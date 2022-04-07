<?php
/**
 * Styles
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

use WP_Grid_Builder\Includes\Singleton;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle plugin styles
 *
 * @class WP_Grid_Builder\FrontEnd\Styles
 * @since 1.2.1
 */
final class Styles {

	use Singleton;

	/**
	 * Holds plugins styles
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @var array
	 */
	public $styles = [];

	/**
	 * Holds core styles
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @var array
	 */
	public $core_styles = [
		[
			'handle'  => WPGB_SLUG . '-style',
			'source'  => WPGB_URL . 'frontend/assets/css/style.css',
			'version' => WPGB_VERSION,
		],
		[
			'handle'  => WPGB_SLUG . '-facets',
			'source'  => WPGB_URL . 'frontend/assets/css/facets.css',
			'version' => WPGB_VERSION,
		],
		[
			'handle'  => WPGB_SLUG . '-lightbox',
			'source'  => WPGB_URL . 'frontend/assets/css/lightbox.css',
			'version' => WPGB_VERSION,
		],
	];

	/**
	 * Constructor
	 *
	 * @since 1.2.1
	 * @access public
	 */
	public function __construct() {

		add_action( 'wp_head', [ $this, 'noscript' ] );
		add_action( 'wp_footer', [ $this, 'enqueue' ], 11 );
		add_action( 'wp_enqueue_scripts', [ $this, 'critical_css' ] );

	}

	/**
	 * Register core style
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @param string $handle Name of the stylesheet. Should be unique.
	 */
	public function register_style( $handle ) {

		$exists = array_search( $handle, array_column( $this->styles, 'handle' ), true );

		if ( false !== $exists ) {
			return;
		}

		$key = array_search( $handle, array_column( $this->core_styles, 'handle' ), true );

		if ( false === $key ) {
			return;
		}

		$this->styles[] = $this->core_styles[ $key ];

	}

	/**
	 * Deregister core style
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @param string $handle Name of the style.
	 */
	public function deregister_style( $handle ) {

		$key = array_search( $handle, array_column( $this->styles, 'handle' ), true );

		if ( false === $key ) {
			return;
		}

		unset( $this->styles[ $key ] );

	}

	/**
	 * Get Register stylesheets
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @return array
	 */
	public function get_styles() {

		$styles = apply_filters( 'wp_grid_builder/frontend/register_styles', $this->core_styles );

		return array_values( array_filter( $styles ) );

	}

	/**
	 * Enqueue plugin styles
	 *
	 * @since 1.2.1
	 * @access public
	 */
	public function enqueue() {

		if ( empty( $this->styles ) ) {
			return;
		}

		$this->register_styles();
		$this->enqueue_styles();
		$this->inline_style();

	}

	/**
	 * Register stylesheets
	 *
	 * @since 1.2.1
	 * @access public
	 */
	public function register_styles() {

		// Register alias style for dependencies.
		wp_register_style( WPGB_SLUG, false, [], WPGB_VERSION );

		$this->styles = apply_filters( 'wp_grid_builder/frontend/register_styles', $this->styles );
		$this->styles = array_filter( $this->styles );
		$this->styles = array_map(
			function( $style ) {

				if ( ! is_array( $style ) ) {
					return $style;
				}

				wp_register_style( $style['handle'], $style['source'], [ WPGB_SLUG ], $style['version'] );
				return $style['handle'];

			},
			$this->styles
		);

	}

	/**
	 * Enqueue stylesheets
	 *
	 * @since 1.2.1
	 * @access public
	 */
	public function enqueue_styles() {

		$this->styles = array_fill_keys( $this->styles, true );
		$this->styles = apply_filters( 'wp_grid_builder/frontend/enqueue_styles', $this->styles );

		foreach ( $this->styles as $handle => $enqueue ) {
			$enqueue && wp_enqueue_style( $handle );
		}
	}

	/**
	 * Inline style
	 *
	 * @since 1.2.1
	 * @access public
	 */
	public function inline_style() {

		$inline_style = apply_filters( 'wp_grid_builder/frontend/add_inline_style', '', $this->styles );
		$style_handle = ! empty( $this->styles[ WPGB_SLUG . '-style' ] ) ? WPGB_SLUG . '-style' : WPGB_SLUG . '-facets';

		// We inline for main style sheet if available or facets style (template mode).
		wp_add_inline_style( $style_handle, $inline_style );

	}

	/**
	 * Inline critical CSS
	 * Hide as soon as possible grid(s), facet(s) to prevent flickers when loading the page
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function critical_css() {

		// Register & enqueue alias styles.
		wp_register_style( WPGB_SLUG . '-head', false, [], WPGB_VERSION );
		wp_enqueue_style( WPGB_SLUG . '-head' );

		wp_add_inline_style(
			WPGB_SLUG . '-head',
			'.wp-grid-builder:not(.wpgb-template),.wpgb-facet{opacity:0.01}' .
			'.wpgb-facet fieldset{margin:0;padding:0;border:none;outline:none;box-shadow:none}' .
			'.wpgb-facet fieldset:last-child{margin-bottom:40px;}' .
			'.wpgb-facet fieldset legend.wpgb-sr-only{height:1px;width:1px}'
		);

	}

	/**
	 * Add style in noscript tag in head
	 * Allow to reveal grid and cards if JavaScript is disabled.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function noscript() {

		$css  = '<noscript><style>';
		$css .= '.wp-grid-builder .wpgb-card.wpgb-card-hidden .wpgb-card-wrapper{opacity:1!important;visibility:visible!important;transform:none!important}';
		$css .= '.wpgb-facet {opacity:1!important;pointer-events:auto!important}.wpgb-facet *:not(.wpgb-pagination-facet){display:none}';
		$css .= '</style></noscript>';

		echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
}
