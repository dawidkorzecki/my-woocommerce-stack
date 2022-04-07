<?php
/**
 * TinyMCE
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add tinyMCE support and button for shortcode
 *
 * @class WP_Grid_Builder\Admin\TinyMCE
 * @since 1.0.0
 */
class TinyMCE {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'admin_init', [ $this, 'tinymce' ] );

	}

	/**
	 * Add TinyMCE support
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function tinymce() {

		// Check user permissions.
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Check if WYSIWYG is enabled.
		if ( get_user_option( 'rich_editing' ) !== 'true' ) {
			return;
		}

		// Add TinyMCE button.
		add_filter( 'mce_buttons', [ $this, 'mce_buttons' ] );
		// Add TinyMCE script.
		add_filter( 'mce_external_plugins', [ $this, 'mce_external_plugins' ] );
		// Localize TinyMCE.
		add_action( 'admin_enqueue_scripts', [ $this, 'localize_settings' ] );

	}

	/**
	 * Add button in TinyMCE
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $buttons First-row list of buttons.
	 * @return array.
	 */
	public function mce_buttons( $buttons ) {

		array_push( $buttons, WPGB_SLUG );
		return $buttons;

	}

	/**
	 * Enqueue script in TinyMCE
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $plugin_array An array of external TinyMCE plugins.
	 * @return array.
	 */
	public function mce_external_plugins( $plugin_array ) {

		$plugin_array[ WPGB_SLUG ] = WPGB_URL . 'admin/assets/js/tinymce.js';
		return $plugin_array;

	}

	/**
	 * Localize TinyMCE.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function localize_settings() {

		$localize = [
			'icon'      => WPGB_URL . 'admin/assets/svg/icon.svg',
			'templates' => array_keys( apply_filters( 'wp_grid_builder/templates', [] ) ),
			'settings'  => [
				[
					'type'   => 'listbox',
					'name'   => 'type',
					'label'  => esc_html__( 'Shortcode Type', 'wp-grid-builder' ),
					'values' => [
						[
							'text'     => esc_html__( 'Grid', 'wp-grid-builder' ),
							'value'    => 'grid',
							'selected' => true,
						],
						[
							'text'  => esc_html__( 'Facet', 'wp-grid-builder' ),
							'value' => 'facet',
						],
					],
				],
				[
					'type'   => 'listbox',
					'name'   => 'facet',
					'label'  => esc_html__( 'Select a Facet', 'wp-grid-builder' ),
					'values' => [
						[
							'text'  => esc_html__( 'None', 'wp-grid-builder' ),
							'value' => '',
						],
					],
				],
				[
					'type'   => 'listbox',
					'name'   => 'grid',
					'label'  => esc_html__( 'Select a Grid', 'wp-grid-builder' ),
					'values' => [
						[
							'text'  => esc_html__( 'None', 'wp-grid-builder' ),
							'value' => '',
						],
					],
				],
			],
		];

		wp_localize_script( 'wp-tinymce', WPGB_SLUG . '_tinymce', $localize );

	}
}
