<?php
/**
 * Layout
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build grid layout
 *
 * @class WP_Grid_Builder\FrontEnd\Layout
 * @since 1.0.0
 */
final class Layout implements Models\Layout_Interface {

	/**
	 * Holds grid settings
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Holds assets instance
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $assets;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object Settings $settings Settings class instance.
	 * @param object Assets   $assets Assets class instance.
	 */
	public function __construct( Settings $settings, Assets $assets ) {

		$this->settings = $settings;
		$this->assets   = $assets;

	}

	/**
	 * Generate main templates
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {

		$this->assets->register();
		$this->set_classes();
		$this->set_options();
		$this->do_templates();

	}

	/**
	 * Get main grid wrapper class
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_classes() {

		$class  = 'wp-grid-builder wpgb-grid-' . $this->settings->id;
		$class .= ' ' . $this->settings->class;
		// Gutenberg custom class name.
		$class .= ' ' . $this->settings->className;

		// Gutenberg align class name (only if not in Gutenberg editor).
		if (
			! empty( $this->settings->align ) &&
			'none' !== $this->settings->align &&
			empty( $this->settings->is_gutenberg )
		) {
			$class .= ' align' . $this->settings->align;
		}

		$this->settings->class = Helpers::sanitize_html_classes( $class );

	}

	/**
	 * Prepare data attribute JSON options for JS.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_options() {

		$js_options = $this->get_options();

		$this->settings->js_options = wp_json_encode( $js_options );

	}

	/**
	 * Get grid and global options
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_options() {

		$options = [];

		foreach ( $this->settings->js_options as $key => $val ) {

			if ( ! isset( $this->settings->{$key} ) ) {
				continue;
			}

			// To be compliant with JS syntax.
			$js_key = ucwords( str_replace( '_', ' ', $key ) );
			$js_key = lcfirst( str_replace( ' ', '', $js_key ) );

			$options[ $js_key ] = $this->settings->{$key};

		}

		$options['rightToLeft'] = is_rtl();

		return $options;

	}

	/**
	 * Do templates
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function do_templates() {

		do_action( 'wp_grid_builder/grid/render' );

		Helpers::get_template( 'layout/wrapper-start' );
		Helpers::get_template( 'layout/layout' );
		Helpers::get_template( 'layout/wrapper-end' );

	}
}
