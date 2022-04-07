<?php
/**
 * Grids
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\File;
use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle grid actions
 *
 * @class WP_Grid_Builder\Admin\Grids
 * @since 1.0.0
 */
final class Grids extends Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_grid';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct();

		add_action( 'wp_grid_builder/delete/grids', [ $this, 'delete' ] );
		add_action( 'wp_grid_builder/import/grids', [ $this, 'import' ] );

	}

	/**
	 * Get grid settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $id Holds grid id to query to import.
	 * @return string
	 */
	public function get_settings( $id ) {

		if ( (int) $id <= 0 ) {
			return;
		}

		return Database::query_var(
			[
				'select' => 'settings',
				'from'   => 'grids',
				'id'     => (int) $id,
			]
		);

	}

	/**
	 * Save grid settings
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function save() {

		// Load grid setting fields.
		require WPGB_PATH . 'admin/settings/grid.php';

		// Process settings.
		$settings = $this->get_var( 'settings' );
		$settings = json_decode( $settings, true );
		$settings = wp_grid_builder()->settings->sanitize( $settings );
		$settings = $this->merge( $settings );
		$settings = Helpers::maybe_json_encode( $settings );

		// If grid name empty.
		if ( empty( $settings['name'] ) ) {
			$this->send_response( false, __( 'Please, enter a grid name', 'wp-grid-builder' ) );
		}

		try {
			$id = Database::save_row( 'grids', $settings, $this->get_var( 'id' ) );
		} catch ( \Exception $e ) {
			$this->send_response( false, $e->getMessage() );
		}

		do_action( 'wp_grid_builder/save/grid', $id );

		$this->delete_stylesheets( $id );
		$this->send_response( true, __( 'Settings Saved!', 'wp-grid-builder' ), $id );

	}

	/**
	 * Merge previous settings
	 * Allows to preserve field with dedicated capability.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds Grid settings.
	 * @return array Grid settings
	 */
	public function merge( $settings ) {

		if ( current_user_can( 'edit_plugins' ) ) {
			return $settings;
		}

		// Get previous settings.
		$grid = $this->get_settings( $this->get_var( 'id' ) );
		$grid = json_decode( $grid, true );

		// Keep custom JS if user can't edit plugin (like WordPress editor).
		if ( isset( $grid['custom_js'] ) ) {
			$settings['settings']['custom_js'] = $grid['custom_js'];
		}

		return $settings;

	}

	/**
	 * Delete grids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $ids Holds deleted grid ids.
	 */
	public function delete( $ids ) {

		array_map(
			function( $id ) {
				$this->delete_stylesheets( $id );
			},
			(array) $ids
		);

	}

	/**
	 * Import grids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $grids Holds grids to import.
	 */
	public function import( $grids ) {

		// Load grid setting fields.
		require WPGB_PATH . 'admin/settings/grid.php';

		array_map(
			function( $grid ) {

				$grid = wp_grid_builder()->settings->sanitize( $grid );
				$grid = Helpers::maybe_json_encode( $grid );

				try {
					Database::import_row( 'grids', $grid );
				} catch ( \Exception $e ) {
					$this->send_response( false, $e->getMessage() );
				}

			},
			(array) $grids
		);

	}

	/**
	 * Delete stylesheet from grids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $id Card id to match in stylesheet.
	 */
	public function delete_stylesheets( $id ) {

		$files = File::get_files( 'grids' );

		if ( empty( $files ) ) {
			return;
		}

		// Delete matching stylesheet.
		array_map(
			function( $file ) use ( $id ) {

				if ( stripos( $file['name'], 'G' . $id ) !== false ) {
					File::delete( 'grids', $file['name'] );
				}

			},
			(array) $files
		);

		// Delete facet html output.
		Helpers::delete_transient( 'G' . $id );

	}

	/**
	 * Check grid and skin types.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function check_type() {

		$type  = $this->get_var( 'type' );
		$cards = Database::query_results(
			[
				'select' => 'type, name',
				'from'   => 'cards',
				'id'     => (array) $this->get_var( 'cards' ),
			]
		);

		// merge custom cards.
		$cards = array_merge( $cards, apply_filters( 'wp_grid_builder/cards', [] ) );

		// Get invalid card name.
		$cards = array_map(
			function( $card ) use ( $type ) {
				if ( 'masonry' === $card['type'] && 'masonry' !== $type ) {
					return $card['name'];
				}

				return '';
			},
			$cards
		);

		$cards   = array_filter( $cards );
		$message = null;

		if ( ! empty( $cards ) ) {

			$i18n = [
				'metro'     => __( 'Metro', 'wp-grid-builder' ),
				'masonry'   => __( 'Masonry', 'wp-grid-builder' ),
				'justified' => __( 'Justified', 'wp-grid-builder' ),
			];

			$message  = '<p class="wpgb-nota-bene wpgb-nota-grid-type wpgb-warning"><span>';
			$message .= Helpers::get_icon( 'warning', false, false );
			$message .= wp_kses_post(
				sprintf(
					/* Translators: %1$s: grid type, %2$s: card names. */
					_n(
						'The following Masonry card is not compatible with the current grid layout (%1$s): <strong>%2$s</strong>.',
						'The following Masonry cards are not compatible with the current grid layout (%1$s): <strong>%2$s</strong>.',
						count( $cards ),
						'wp-grid-builder'
					),
					isset( $i18n[ $type ] ) ? $i18n[ $type ] : '',
					implode( ', ', $cards )
				)
			);
			$message .= '<br>' . wp_kses_post( __( 'You need to change cards set under <strong>Card Styles</strong> panel or to change the type of layout under <strong>Grid Layout</strong> panel.', 'wp-grid-builder' ) );
			$message .= '<br>' . esc_html__( 'A default card will be used in the grid instead.', 'wp-grid-builder' );
			$message .= '</span></p>';

		}

		$this->send_response( true, null, $message );

	}
}
