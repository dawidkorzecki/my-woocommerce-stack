<?php
/**
 * Cards
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
 * Handle card actions
 *
 * @class WP_Grid_Builder\Admin\Cards
 * @since 1.0.0
 */
final class Cards extends Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_card';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct();

		add_action( 'wp_grid_builder/duplicate/cards', [ $this, 'duplicate' ] );
		add_action( 'wp_grid_builder/delete/cards', [ $this, 'delete' ] );
		add_action( 'wp_grid_builder/import/cards', [ $this, 'import' ] );

	}

	/**
	 * Get builder blocks
	 *
	 * @since 1.0.0
	 */
	public function get_blocks() {

		$types = apply_filters(
			'wp_grid_builder/block/types',
			[
				'post_blocks'    => esc_html__( 'Post Blocks', 'wp-grid-builder' ),
				'product_blocks' => esc_html__( 'Product Blocks', 'wp-grid-builder' ),
				'user_blocks'    => esc_html__( 'User Blocks', 'wp-grid-builder' ),
				'term_blocks'    => esc_html__( 'Term Blocks', 'wp-grid-builder' ),
				'media_blocks'   => esc_html__( 'Buttons &#38; Icons Blocks', 'wp-grid-builder' ),
				'custom_blocks'  => esc_html__( 'Custom Blocks', 'wp-grid-builder' ),
			]
		);

		$blocks = Helpers::file_get_contents( 'admin/assets/json/default-blocks.json' );
		$blocks = (array) json_decode( $blocks, true );
		$blocks = $this->get_custom_blocks( $blocks );

		// Remove product blocks if WC and EDD not activated.
		if ( ! class_exists( 'WooCommerce' ) && ! class_exists( 'Easy_Digital_Downloads' ) ) {
			unset( $types['product_blocks'] );
		}

		return array_map(
			function( $type, $label ) use ( $blocks ) {

				return [
					'label'  => $label,
					'blocks' => ! empty( $blocks[ $type ] ) ?
					$blocks[ $type ] : [],
				];

			},
			array_keys( $types ),
			$types
		);

	}
	/**
	 * Get custom registered blocks
	 *
	 * @since 1.0.0
	 *
	 * @param array $blocks Holds all defaults blocks.
	 * @return array
	 */
	public function get_custom_blocks( $blocks ) {

		$custom_blocks = apply_filters( 'wp_grid_builder/blocks', [] );

		foreach ( (array) $custom_blocks as $slug => $args ) {

			$type = 'custom_blocks';

			if ( empty( $args['name'] ) ) {
				continue;
			}

			if ( isset( $args['type'] ) ) {
				$type = $args['type'];
			}

			if ( ! isset( $blocks[ $type ] ) ) {
				$blocks[ $type ] = [];
			}

			// Add custom block to default blocks.
			$blocks[ $type ][ $slug ] = wp_parse_args(
				$args,
				[
					'settings' => [
						'content' => [
							'source' => 'custom_block',
						],
						'style'   => [
							'idle'  => [],
							'hover' => [],
						],
						'action'  => [],
					],
				]
			);
		}

		return $blocks;

	}

	/**
	 * Get builder settings
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_settings() {

		$layers   = null;
		$card_id  = $this->get_var( 'id', false );
		$content  = require WPGB_PATH . 'admin/settings/builder.php';
		$settings = require WPGB_PATH . 'admin/settings/defaults/builder.php';

		if ( $card_id ) {

			$columns = Database::query_row(
				[
					'select' => 'name, layout, settings',
					'from'   => 'cards',
					'id'     => $card_id,
				]
			);

			if ( ! empty( $columns ) ) {

				// Get card layers.
				$layout = json_decode( $columns['layout'] );
				$layers = $layout->layers;
				// Reassign card name.
				$settings = json_decode( $columns['settings'] );
				$settings->general->name = $columns['name'];

			}
		}

		$this->send_response(
			true,
			null,
			[
				'blocks'   => Helpers::array_entity_decode( $this->get_blocks() ),
				'content'  => Helpers::array_entity_decode( $content ),
				'fonts'    => Helpers::get_google_fonts(),
				'settings' => $settings,
				'layers'   => $layers,
			]
		);

	}

	/**
	 * Save card settings
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function save() {

		// Load builder setting fields.
		require WPGB_PATH . 'admin/settings/builder.php';

		// Process settings.
		$settings = $this->get_var( 'settings' );
		$settings = json_decode( $settings, true );
		$settings = wp_grid_builder()->settings->sanitize( $settings );
		$settings = Helpers::maybe_json_encode( $settings );

		// If card name empty.
		if ( empty( $settings['name'] ) ) {
			$this->send_response( false, __( 'Please, enter a card name', 'wp-grid-builder' ) );
		}

		try {
			$id = Database::save_row( 'cards', $settings, $this->get_var( 'id' ) );
		} catch ( \Exception $e ) {
			$this->send_response( false, $e->getMessage() );
		}

		do_action( 'wp_grid_builder/save/card', $id );

		$this->delete_stylesheets( $id );
		$this->generate_stylesheet( $id, $settings );
		$this->send_response( true, __( 'Settings Saved!', 'wp-grid-builder' ), $id );

	}

	/**
	 * Delete cards
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $ids Holds deleted card ids.
	 */
	public function delete( $ids ) {

		array_map(
			function( $id ) {

				File::delete( 'cards', $id . '.css' );
				$this->delete_stylesheets( $id );

			},
			(array) $ids
		);

	}

	/**
	 * Duplicate cards
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $ids Holds duplicated card ids.
	 */
	public function duplicate( $ids ) {

		$cards = Database::query_results(
			[
				'select' => 'id, css',
				'from'   => 'cards',
				'id'     => $ids,
			]
		);

		array_map(
			function( $card ) {
				$this->generate_stylesheet( $card['id'], $card );
			},
			$cards
		);

	}

	/**
	 * Import cards
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $cards Holds cards to import.
	 */
	public function import( $cards ) {

		// Load card setting fields.
		require WPGB_PATH . 'admin/settings/builder.php';

		array_map(
			function( $card ) {

				$card = wp_grid_builder()->settings->sanitize( $card );
				$card = Helpers::maybe_json_encode( $card );

				try {

					$id = Database::import_row( 'cards', $card );
					$this->generate_stylesheet( $id, $card );

				} catch ( \Exception $e ) {
					$this->send_response( false, $e->getMessage() );
				}

			},
			(array) $cards
		);

	}

	/**
	 * Generate card stylesheet
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $id Row id.
	 * @param array   $card Holds items to import.
	 */
	public function generate_stylesheet( $id, $card ) {

		$css = str_replace( '.wpgb-card-preview', '.wpgb-card-' . $id, $card['css'] );
		file::put_contents( 'cards', $id . '.css', $css );

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

				if ( stripos( $file['name'], 'C' . $id ) !== false ) {
					File::delete( 'grids', $file['name'] );
				}

			},
			(array) $files
		);

	}
}
