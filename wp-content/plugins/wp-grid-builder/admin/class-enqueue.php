<?php
/**
 * Enqueue
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Loaders;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue admin assets
 *
 * @class WP_Grid_Builder\Admin\Enqueue
 * @since 1.0.0
 */
class Enqueue {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'plugin_page_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'plugin_post_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'plugin_term_scripts' ] );

	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function plugin_page_scripts() {

		$plugin_page = Helpers::get_plugin_page();

		if ( empty( $plugin_page ) ) {
			return;
		}

		$this->enqueue_wp_scripts();
		$this->enqueue_main_script();

		switch ( $plugin_page ) {
			case 'grids':
			case 'settings':
				$this->enqueue_settings();
				break;
			case 'grid-settings':
				$this->enqueue_codemirror();
				$this->enqueue_settings();
				Loaders::add_inline_styles();
				break;
			case 'facet-settings':
				$this->enqueue_codemirror();
				$this->enqueue_settings();
				break;
			case 'card-builder':
				$this->enqueue_codemirror();
				$this->enqueue_settings();
				$this->enqueue_builder();
				break;
		}

		$this->enqueue_rtl_style();

	}

	/**
	 * Register Meta Boxes (custom fields) for single edit post pages
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function plugin_post_scripts() {

		global $pagenow;

		if ( ! Helpers::current_user_can() ) {
			return;
		}

		if ( ! wpgb_get_option( 'post_meta' ) ) {
			return;
		}

		if ( 'post-new.php' === $pagenow || 'post.php' === $pagenow ) {

			$this->enqueue_wp_scripts();
			$this->enqueue_admin_post();
			$this->enqueue_rtl_style();

		}
	}

	/**
	 * Register Meta Boxes (custom fields) for term add/edit pages
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function plugin_term_scripts() {

		global $pagenow;

		if ( ! Helpers::current_user_can() ) {
			return;
		}

		if ( ! wpgb_get_option( 'term_meta' ) ) {
			return;
		}

		if ( 'edit-tags.php' === $pagenow || 'term.php' === $pagenow ) {

			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
			// Inline init colorpickers to prevent loading the whole settings/helper scripts.
			wp_add_inline_script( 'wp-color-picker', 'jQuery( ".wpgb-color-picker" ).wpColorPicker( { color: this.value } );' );

		}
	}

	/**
	 * Enqueue WordPress scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_wp_scripts() {

		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

	}

	/**
	 * Enqueue main admin panel scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_main_script() {

		wp_enqueue_script( WPGB_SLUG . '-helpers', WPGB_URL . 'admin/assets/js/helpers.js', [], WPGB_VERSION, true );
		wp_enqueue_script( WPGB_SLUG . '-admin', WPGB_URL . 'admin/assets/js/admin.js', [ 'jquery' ], WPGB_VERSION, true );
		wp_enqueue_style( WPGB_SLUG . '-admin', WPGB_URL . 'admin/assets/css/admin.css', [], WPGB_VERSION );

	}

	/**
	 * Enqueue Codemirror scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_codemirror() {

		wp_enqueue_script( WPGB_SLUG . '-code-script', WPGB_URL . 'admin/assets/js/codemirror.js', [], WPGB_VERSION, true );
		wp_enqueue_style( WPGB_SLUG . '-code-style', WPGB_URL . 'admin/assets/css/codemirror.css', [], WPGB_VERSION );

	}

	/**
	 * Enqueue settings scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_settings() {

		wp_enqueue_script( WPGB_SLUG . '-settings', WPGB_URL . 'admin/assets/js/settings.js', [ 'jquery' ], WPGB_VERSION, true );

	}

	/**
	 * Enqueue builder script and style
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_builder() {

		$handle  = WPGB_SLUG . '-builder';
		$styles  = apply_filters( 'wp_grid_builder/builder/register_styles', [] );
		$scripts = apply_filters( 'wp_grid_builder/builder/register_scripts', [] );

		wp_enqueue_script( $handle, WPGB_URL . 'admin/assets/js/builder.js', [], WPGB_VERSION, true );
		wp_enqueue_style( $handle, WPGB_URL . 'admin/assets/css/builder.css', [], WPGB_VERSION );

		foreach ( $scripts as $script ) {
			wp_enqueue_script( $script['handle'], $script['source'], [ $handle ], $script['version'], true );
		}

		foreach ( $styles as $style ) {
			wp_enqueue_style( $style['handle'], $style['source'], [ $handle ], $style['version'] );
		}

		$this->add_inline_builder_styles();

	}

	/**
	 * Enqueue RTL styles
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_rtl_style() {

		if ( is_rtl() ) {
			wp_enqueue_style( WPGB_SLUG . '-admin-rtl', WPGB_URL . 'admin/assets/css/admin-rtl.css', [], WPGB_VERSION );
		}

	}

	/**
	 * Enqueue admin edit post page scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_admin_post() {

		wp_enqueue_style( WPGB_SLUG . '-admin', WPGB_URL . 'admin/assets/css/admin-post.css', [], WPGB_VERSION );
		wp_enqueue_script( WPGB_SLUG . '-helpers', WPGB_URL . 'admin/assets/js/helpers.js', [], WPGB_VERSION, true );
		wp_enqueue_script( WPGB_SLUG . '-admin', WPGB_URL . 'admin/assets/js/settings.js', [ 'jquery' ], WPGB_VERSION, true );

	}


	/**
	 * Add inline builder styles
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_inline_builder_styles() {

		$style   = '';
		$options = wpgb_get_options();

		// Accent color.
		if ( ! empty( $options['accent_scheme_1'] ) ) {

			$style .= '[class*="wpgb-block-"].wpgb-idle-accent-1,';
			$style .= '.wpgb-preview [class*="wpgb-block-"].wpgb-hover-accent-1:hover';
			$style .= '{color:' . esc_attr( $options['accent_scheme_1'] ) . '}';

		}

		foreach ( [ 'dark', 'light' ] as $scheme ) {

			for ( $i = 1; $i < 4; $i++ ) {

				$color = $options[ $scheme . '_scheme_' . $i ];

				if ( empty( $color ) ) {
					continue;
				}

				// Idle and hover scheme.
				$style .= '.wpgb-scheme-' . $scheme . ' .wpgb-idle-scheme-' . $i . ',';
				$style .= '.wpgb-preview .wpgb-scheme-' . $scheme . ' [class*="wpgb-block-"].wpgb-hover-scheme-' . $i . ':hover';
				$style .= '{color:' . esc_attr( $color ) . '}';

			}
		}

		wp_add_inline_style( WPGB_SLUG . '-builder', $style );

	}
}
