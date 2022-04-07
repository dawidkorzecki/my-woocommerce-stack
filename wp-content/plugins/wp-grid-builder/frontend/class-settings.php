<?php
/**
 * Settings
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query grid settings
 *
 * @class WP_Grid_Builder\FrontEnd\Settings
 * @since 1.0.0
 */
final class Settings implements Models\Settings_Interface {

	/**
	 * Holds grid settings
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $grid = [];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $grid Holds grid parameters.
	 */
	public function __construct( $grid ) {

		$this->grid = $grid;

	}

	/**
	 * If has grid id.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @throws \Exception Error missing id.
	 */
	public function is_valid() {

		// Make it work with grid id passed as argument.
		if ( isset( $this->grid ) && is_scalar( $this->grid ) ) {
			$this->grid = [ 'id' => $this->grid ];
		}

		// If grid id is not valid.
		if ( empty( $this->grid['id'] ) || ! is_numeric( $this->grid['id'] ) ) {

			$error_msg = __( 'Sorry, no grids were found for the requested grid id.', 'wp-grid-builder' );
			throw new \Exception( $error_msg );

		}

		$this->grid['id'] = (int) $this->grid['id'];

	}

	/**
	 * Handle dynamic grid (like card overview).
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_dynamic() {

		if ( ! isset( $this->grid['is_dynamic'], $this->grid['id'] ) ) {
			return false;
		}

		$this->grid['id'] = sanitize_html_class( $this->grid['id'] );

		// Mainly to correctly hook in preview mode from grid id (if grid is saved).
		if ( ! empty( $this->grid['is_preview'] ) && 'preview' !== $this->grid['id'] ) {
			$this->grid['id'] = (int) $this->grid['id'];
		}

		$this->assing_vars( $this->grid );

		return true;

	}

	/**
	 * Query grid settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @throws \Exception Error missing grid settings.
	 */
	public function query() {

		if ( $this->is_dynamic() ) {
			return;
		}

		$this->is_valid();

		$settings = Database::query_var(
			[
				'select' => 'settings',
				'from'   => 'grids',
				'id'     => $this->grid['id'],
			]
		);

		$settings = json_decode( $settings, true );

		if ( empty( $settings ) ) {

			/* translators: %d: grid id */
			$error_msg = sprintf( __( 'No settings found for the grid #%d.', 'wp-grid-builder' ), $this->grid['id'] );
			throw new \Exception( $error_msg );

		}

		$settings = wp_parse_args( $this->grid, $settings );
		$this->assing_vars( $settings );

	}

	/**
	 * Assign settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds grid settings.
	 */
	public function assing_vars( $settings ) {

		$settings = apply_filters( 'wp_grid_builder/grid/settings', $settings );

		foreach ( $settings as $key => $val ) {
			$this->$key = $val;
		}

		unset( $this->grid );

	}
}
