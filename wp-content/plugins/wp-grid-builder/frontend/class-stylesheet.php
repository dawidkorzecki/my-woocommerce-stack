<?php
/**
 * StyleSheet
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

use WP_Grid_Builder\Includes\File;
use WP_Grid_Builder\Includes\Loaders;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build grid stylesheet
 *
 * @class WP_Grid_Builder\FrontEnd\StyleSheet
 * @since 1.0.0
 */
final class StyleSheet implements Models\StyleSheet_Interface {

	/**
	 * Holds grid settings
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $settings = [];

	/**
	 * Holds enqueued grid id
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private static $proceeded = [];

	/**
	 * Holds grid CSS
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	public static $css = [
		'global' => '',
		'grids'  => '',
		'cards'  => '',
		'custom' => '',
	];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object Settings $settings Settings class instance.
	 * @param object Cards    $cards Cards class instance.
	 */
	public function __construct( Settings $settings, Cards $cards ) {

		$this->settings = $settings;
		$this->cards    = $cards;

	}

	/**
	 * Check if grid style are already generated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_generated() {

		// If grid style has already been proceeded.
		// It also prevent duplicating CSS if someone is applying the_content filter.
		if ( in_array( $this->settings->id, self::$proceeded, true ) ) {
			return;
		}

		// Save proceeded grid id.
		array_push( self::$proceeded, $this->settings->id );

	}

	/**
	 * Generate dynamic CSS
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function generate() {

		if ( $this->is_generated() ) {
			return;
		}

		// Global styles (only once per page).
		if ( empty( self::$css['global'] ) ) {
			self::$css['global'] = ( new Colors() )->get();
		}

		// Grid styles.
		$this->grid_layout();
		$this->carousel();
		$this->card_colors();
		$this->card_sizes();
		$this->animation();
		$this->lazy_load();
		$this->loader();
		$this->custom();

		return $this;

	}

	/**
	 * Get grid style
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get() {

		$this->get_cards();

		$css  = implode( '', self::$css );
		// Add !important rule to prevent issue with WP Rocket (CSS concatenation).
		$css .= '.wp-grid-builder:not(.wpgb-template){opacity:1!important}';

		return $css;

	}

	/**
	 * Get and merge cards styles
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_cards() {

		$cards = $this->cards->get();

		foreach ( $cards as $id => $card ) {

			$css = File::get_contents( 'cards', $id . '.css' );

			if ( empty( $css ) ) {
				$css = str_replace( '.wpgb-card-preview', '.wpgb-card-' . $id, $card['css'] );
			}

			self::$css['cards'] .= $css;

		}
	}

	/**
	 * Build grid layout Areas styles
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function grid_layout() {

		$layout   = (array) $this->settings->grid_layout;
		$defaults = [
			'style'  => [],
			'facets' => [],
		];

		foreach ( $layout as $area => $args ) {

			$rules = '';
			$class = sanitize_html_class( 'wpgb-' . $area );
			$args  = wp_parse_args( $args, $defaults );

			if ( empty( $args['style'] ) && empty( $args['facets'] ) ) {
				continue;
			}

			// Set flex to 100% if only one facet in area.
			if ( strpos( $area, 'area-top' ) !== false && count( (array) $args['facets'] ) === 1 ) {
				self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .' . $class . '>div{flex:inherit;min-width:25%;max-width:100%}';
			}

			foreach ( $args['style'] as $prop => $val ) {

				if ( '' === $val ) {
					continue;
				}

				$rules .= sanitize_key( $prop ) . ':' . esc_attr( $val ) . ';';

			}

			if ( ! empty( $rules ) ) {
				self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .' . $class . '{' . $rules . '}';
			}
		}
	}

	/**
	 * Add carousel style
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function carousel() {

		if ( 'horizontal' !== $this->settings->layout ) {
			return;
		}

		$facets = array_column( (array) $this->settings->grid_layout, 'facets' );
		$facets = array_filter( (array) $facets );
		$facets = array_reduce( (array) $facets, 'array_merge', [] );

		if ( in_array( 'prev-button', $facets, true ) || isset( $facets['next-button'] ) ) {

			self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .wpgb-prev-button,';
			self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .wpgb-next-button';
			self::$css['grids'] .= '{width:' . esc_attr( $this->settings->prev_next_buttons_size ?: '48px' ) . ';';
			self::$css['grids'] .= 'height:' . esc_attr( $this->settings->prev_next_buttons_size ?: '48px' ) . ';';
			self::$css['grids'] .= 'color:' . esc_attr( $this->settings->prev_next_buttons_color ?: '#333333' ) . ';';
			self::$css['grids'] .= 'background:' . esc_attr( $this->settings->prev_next_buttons_background ?: 'transparent' ) . '}';

		}

		if ( ! in_array( 'page-dots', $facets, true ) ) {
			return;
		}

		// Set page dots background color.
		if ( ! empty( $this->settings->page_dots_color ) ) {

			self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .wpgb-page-dots .wpgb-dot:after';
			self::$css['grids'] .= '{background:' . esc_attr( $this->settings->page_dots_color ) . '}';

		}

		// Set page dots select background color.
		if ( ! empty( $this->settings->page_dots_selected_color ) ) {

			self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .wpgb-page-dots .wpgb-dot[aria-selected=true]:after';
			self::$css['grids'] .= '{background:' . esc_attr( $this->settings->page_dots_selected_color ) . '}';

		}
	}

	/**
	 * Build card colors
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function card_colors() {

		if ( ! empty( $this->settings->content_background ) ) {

			self::$css['grids'] .= '.wp-grid-builder.wpgb-grid-' . $this->settings->id . ' .wpgb-card .wpgb-card-header,';
			self::$css['grids'] .= '.wp-grid-builder.wpgb-grid-' . $this->settings->id . ' .wpgb-card .wpgb-card-body,';
			self::$css['grids'] .= '.wp-grid-builder.wpgb-grid-' . $this->settings->id . ' .wpgb-card .wpgb-card-footer';
			self::$css['grids'] .= '{background:' . esc_attr( $this->settings->content_background ) . '}';

		}

		if ( ! empty( $this->settings->overlay_background ) ) {

			self::$css['grids'] .= '.wp-grid-builder.wpgb-grid-' . $this->settings->id . ' .wpgb-card .wpgb-card-media-overlay';
			self::$css['grids'] .= '{background:' . esc_attr( $this->settings->overlay_background ) . '}';

		}
	}

	/**
	 * Grid/card layout.
	 * Layout fallback if JavaScript disabled in browser
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function card_sizes() {

		$sizes = (array) $this->settings->card_sizes;

		foreach ( $sizes as $width => $size ) {

			if ( $width < 9999 ) {
				self::$css['grids'] .= '@media screen and (max-width: ' . $width . 'px){';
			}

			// Flex layout: IE fallback.
			self::$css['grids'] .= '@media all and (-ms-high-contrast:none), (-ms-high-contrast:active){';
			self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ':not(.wpgb-enabled) .wpgb-viewport>div';
			self::$css['grids'] .= '{margin:0 -' . floor( $size['gutter'] / 4 ) . 'px}';
			self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ':not(.wpgb-enabled) .wpgb-card';
			self::$css['grids'] .= '{width:calc(( 100% - ' . (int) $size['columns'] . ' * ' . (int) $size['gutter'] . 'px ) / ' . (int) $size['columns'] . ' - 0.1px);';
			self::$css['grids'] .= 'margin:' . floor( $size['gutter'] / 2 ) . 'px}';
			self::$css['grids'] .= '}';

			// Grid layout: Modern browsers fallback.
			self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ':not(.wpgb-enabled) .wpgb-viewport>div';
			self::$css['grids'] .= '{grid-template-columns: repeat(' . (int) $size['columns'] . ', 1fr);';
			self::$css['grids'] .= 'grid-gap:' . (int) $size['gutter'] . 'px}';

			// Keep number of columns with masonry and metro layouts.
			if ( $width < 9999 && 'justified' !== $this->settings->type ) {

				// To make sure columns does not stretch.
				for ( $i = $size['columns']; $i < 12; $i++ ) {
					self::$css['grids'] .= '.wp-grid-builder.wpgb-grid-' . $this->settings->id . ':not(.wpgb-enabled) .wpgb-card[data-col="' . $i . '"],';
				}

				self::$css['grids']  = rtrim( self::$css['grids'], ',' );
				self::$css['grids'] .= '{grid-column: span ' . (int) $size['columns'] . '}';

			}

			if ( 'metro' === $this->settings->type ) {

				// Add aspect ratio with padding.
				self::$css['grids'] .= '.wp-grid-builder.wpgb-grid-' . $this->settings->id . ':not(.wpgb-enabled) .wpgb-card-inner:before';
				self::$css['grids'] .= '{padding-bottom:' . 1 / (float) $size['ratio'] * 100 . '%}';

			} elseif ( 'justified' === $this->settings->type ) {

				// Set row height.
				self::$css['grids'] .= '.wp-grid-builder.wpgb-grid-' . $this->settings->id . ':not(.wpgb-enabled) .wpgb-card';
				self::$css['grids'] .= '{height:' . (int) $size['height'] . 'px}';

			}

			if ( $width < 9999 ) {
				self::$css['grids'] .= '}';
			}
		}
	}

	/**
	 * Set card animtion.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function animation() {

		if ( ! isset( $this->settings->animation['visible'], $this->settings->animation['hidden'] ) ) {
			return;
		}

		self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .wpgb-card.wpgb-card-hidden .wpgb-card-wrapper';
		self::$css['grids'] .= '{' . $this->settings->animation['hidden'] . '}';

		self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .wpgb-card:not(.wpgb-card-hidden) .wpgb-card-wrapper';
		self::$css['grids'] .= '{' . $this->settings->animation['visible'] . '}';

	}

	/**
	 * Build Lazy load colors
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function lazy_load() {

		self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .wpgb-card .wpgb-lazy-load';
		self::$css['grids'] .= '{color:' . esc_attr( $this->settings->lazy_load_spinner_color ?: '#0069ff' ) . ';';
		self::$css['grids'] .= 'background:' . esc_attr( $this->settings->lazy_load_background ?: '#e0e4e9' ) . '}';

	}

	/**
	 * Build Loader style
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function loader() {

		if ( ! $this->settings->loader && ! empty( $this->settings->loader_type ) ) {
			return;
		}

		$css = Loaders::get( $this->settings->loader_type, 'css' );

		if ( empty( $css ) ) {
			return;
		}

		// Loader style.
		self::$css['grids'] .= $css;

		// Loader size.
		self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .wpgb-loader .' . sanitize_html_class( $this->settings->loader_type );
		self::$css['grids'] .= '{transform: scale(' . esc_attr( $this->settings->loader_size ?: 1 ) . ' )}';

		// Loader color.
		self::$css['grids'] .= '.wpgb-grid-' . $this->settings->id . ' .wpgb-loader .' . sanitize_html_class( $this->settings->loader_type ) . ' *';
		self::$css['grids'] .= '{color:' . esc_attr( $this->settings->loader_color ?: '#0069ff' ) . ';';
		self::$css['grids'] .= 'background:' . esc_attr( $this->settings->loader_color ?: '#0069ff' ) . '}';

	}

	/**
	 * Get Custom css
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function custom() {

		if ( ! empty( $this->settings->custom_css ) ) {

			// Minify CSS on the fly.
			$custom_css = preg_replace( '/\/\*((?!\*\/).)*\*\//', '', $this->settings->custom_css );
			$custom_css = preg_replace( '/\s{2,}/', ' ', $custom_css );
			$custom_css = preg_replace( '/\s*([:;{}])\s*/', '$1', $custom_css );
			$custom_css = preg_replace( '/;}/', '}', $custom_css );

			self::$css['custom'] .= wp_strip_all_tags( $custom_css );

		}
	}
}
