<?php
/**
 * Preview
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preview grid in settings and overview panels
 *
 * @class WP_Grid_Builder\Admin\Preview
 * @since 1.0.0
 */
final class Preview {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'admin_post_' . WPGB_SLUG . '_preview', [ $this, 'preview' ] );
		add_action( 'wp_grid_builder/card/wrapper_start', [ $this, 'card_header' ] );

	}

	/**
	 * Preview grid
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function preview() {

		// Check user role capability.
		if ( ! Helpers::current_user_can() ) {
			exit;
		}

		// Check referer.
		if ( check_ajax_referer( WPGB_SLUG . '_preview_grid', 'nonce', false ) === false ) {
			exit;
		}

		// Remove slashes from Global.
		$request = wp_unslash( $_POST );

		// Validate method.
		if ( ! isset( $request['method'] ) || 'preview' !== $request['method'] ) {
			exit;
		}

		// Prepare assets.
		$this->dequeue();
		$this->enqueue();

		// Get requested settings.
		$settings = $this->get_settings( $request );
		// Render iframe content.
		require_once WPGB_PATH . 'admin/views/modules/iframe-preview.php';

		exit;

	}

	/**
	 * Get grid data
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $request Holds request arguments.
	 * @return array .
	 */
	public function get_settings( $request ) {

		if ( ! isset( $request['settings'] ) ) {
			return;
		}

		$settings = $request['settings'];

		// Cards overview.
		if ( isset( $request['type'] ) && 'overview' === $request['type'] ) {

			$defaults = json_decode( $settings, true );
			$settings = require WPGB_PATH . 'admin/settings/defaults/preview.php';
			$settings = wp_parse_args( $settings, $defaults );

			return $settings;

		}

		// Grids overview preview.
		if ( is_numeric( $settings ) ) {

			return [
				'id'           => $settings,
				'is_preview'   => true,
				'inline_style' => true,
			];

		}

		// Load grid settings fields.
		require WPGB_PATH . 'admin/settings/grid.php';

		$settings = json_decode( $settings, true );
		$settings = wp_grid_builder()->settings->sanitize( $settings );

		// Adjust settings for preview mode.
		$settings['is_dynamic']   = true;
		$settings['is_preview']   = true;
		$settings['inline_style'] = true;

		return $settings;

	}

	/**
	 * Add card header
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function card_header() {

		if ( ! wpgb_is_preview() ) {
			return;
		}

		$type = wpgb_get_object_type();

		if ( 'post' !== $type && 'term' !== $type && 'user' !== $type ) {
			return;
		}

		$id    = (int) wpgb_get_the_id();
		$label = __( 'Edit Card', 'wp-grid-builder' );
		$nonce = wp_create_nonce( 'wpgb_fields_edit_' . $id );

		echo '<button type="button" class="wpgb-button wpgb-edit-card" data-id="' . esc_attr( $id ) . '" data-type="' . esc_attr( $type ) . '" data-nonce="' . esc_attr( $nonce ) . '" aria-label="' . esc_attr( $label ) . '">';
			Helpers::get_icon( 'pencil' );
		echo '</button>';

	}

	/**
	 * Dequeue all scripts/styles to prevent any conflict
	 * Enqueued 3rd party scripts and styles should be empty at this stage.
	 *
	 * @since 1.0.1 Prevent to dequeue jQuery script.
	 * @since 1.0.0
	 * @access public
	 */
	public function dequeue() {

		global $wp_scripts, $wp_styles;

		// Dequeue scripts.
		if ( isset( $wp_scripts->queue ) ) {

			foreach ( $wp_scripts->queue as $handle ) {

				// We preserve jquery script.
				if ( 'jquery' !== $handle ) {
					wp_scripts()->remove( $handle );
				}
			}
		}

		// Dequeue styles.
		if ( isset( $wp_styles->queue ) ) {

			foreach ( $wp_styles->queue as $handle ) {
				wp_styles()->remove( $handle );
			}
		}

	}

	/**
	 * Enqueue scripts/styles
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue() {

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( WPGB_SLUG . '-preview', WPGB_URL . 'admin/assets/js/preview.js', [ 'jquery', WPGB_SLUG ], WPGB_VERSION, true );
		wp_enqueue_style( WPGB_SLUG . '-preview', WPGB_URL . 'admin/assets/css/preview.css', [], WPGB_VERSION );

	}
}
