<?php
/**
 * Assets
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

use WP_Grid_Builder\Includes\File;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register assets
 *
 * @class WP_Grid_Builder\FrontEnd\Assets
 * @since 1.0.0
 */
final class Assets implements Models\Assets_Interface {

	/**
	 * Holds grid settings
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $settings = [];

	/**
	 * Stylesheet name
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	protected $sheet_name = '';

	/**
	 * Holds custom Javascript
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array
	 */
	protected static $javascript = [];

	/**
	 * Holds registered grid id
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private static $registered = [];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object Settings   $settings Settings class instance.
	 * @param object Stylesheet $stylesheet StyleSheet class instance.
	 * @param object Cards      $cards Cards class instance.
	 */
	public function __construct( Settings $settings, Stylesheet $stylesheet, Cards $cards ) {

		$this->settings   = $settings;
		$this->stylesheet = $stylesheet;
		$this->cards      = $cards;

	}

	/**
	 * Check if stylesheets are already registered..
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_registered() {

		// ADD each registered grid id.
		array_push( self::$registered, $this->settings->id );
		// ADD custom JS for each grid.
		array_push( self::$javascript, $this->settings->custom_js );

		return count( self::$registered ) > 1;

	}

	/**
	 * Register stylesheets.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register() {

		// Generate grid style.
		$this->stylesheet->generate();

		// We register only once for several grids.
		if ( $this->is_registered() ) {
			return;
		}

		add_filter( 'wp_grid_builder/frontend/register_styles', [ $this, 'register_styles' ], 0 );
		add_filter( 'wp_grid_builder/frontend/add_inline_style', [ $this, 'inline_style' ] );
		add_filter( 'wp_grid_builder/frontend/add_inline_script', [ $this, 'inline_script' ] );

	}

	/**
	 * Register stylesheets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $styles Holds stylesheet to register.
	 * @return array
	 */
	public function register_styles( $styles ) {

		$this->get_stylesheet_name();
		$this->generate_stylesheet();

		$styles[] = $this->get_style();
		$styles[] = $this->get_fonts();

		return $styles;

	}

	/**
	 * Build stylesheet name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_stylesheet_name() {

		// Check if admin to prevent issue with wp_reset_postdata not working properly in admin pages.
		// https://core.trac.wordpress.org/ticket/18408.
		if ( is_admin() || ! empty( $this->settings->inline_style ) || ! empty( $this->settings->is_gutenberg ) ) {
			return;
		}

		$grid_ids = implode( 'G', self::$registered );
		$card_ids = array_keys( $this->cards->get() );
		$card_ids = implode( 'C', $card_ids );

		// Generate an unique name from combination of post, grid, and card ids.
		$this->sheet_name = 'G' . $grid_ids . 'C' . $card_ids;

	}

	/**
	 * Generate stylesheet if missing
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function generate_stylesheet() {

		$url = File::get_url( 'grids', $this->sheet_name . '.css' );

		if ( ! empty( $url ) && ! is_admin() ) {
			return;
		}

		if ( empty( $this->sheet_name ) ) {
			return;
		}

		$css = $this->stylesheet->get();
		$css = wp_strip_all_tags( $css );

		// Unset sheet_name if an error occured when generating it to force inline style.
		if ( ! File::put_contents( 'grids', $this->sheet_name . '.css', $css ) ) {
			$this->sheet_name = '';
		}
	}

	/**
	 * Inline style if no stylesheet generated
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $style Style to inline.
	 * @return string
	 */
	public function inline_style( $style ) {

		if ( empty( $this->sheet_name ) ) {

			$grids = $this->stylesheet->get();
			$grids = wp_strip_all_tags( $grids );
			// We inline first (global CSS).
			$style = $grids . $style;

		}

		return $style;

	}

	/**
	 * Register stylesheet
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_style() {

		if ( empty( $this->sheet_name ) ) {
			return [];
		}

		return [
			'handle'  => WPGB_SLUG . '-grids',
			'source'  => esc_url_raw( File::get_url( 'grids', $this->sheet_name . '.css' ) ),
			'version' => filemtime( File::get_path( 'grids', $this->sheet_name . '.css' ) ),
		];

	}

	/**
	 * Register Google Fonts
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_fonts() {

		$query  = [];
		$cards  = $this->cards->get();
		$fonts  = array_column( $cards, 'fonts' );
		$google = array_column( $fonts, 'google' );

		if ( empty( $google ) ) {
			return [];
		}

		// Merge all font families.
		$families = call_user_func_array( 'array_merge_recursive', $google );
		$families = array_map( 'array_unique', $families );

		if ( empty( $families ) ) {
			return [];
		}

		array_walk(
			$families,
			function( $val, $key ) use ( &$query ) {
				$query[] = $key . ':' . implode( ',', $val );
			}
		);

		$query = implode( '|', $query );

		$url = add_query_arg(
			[ 'family' => rawurlencode( $query ) ],
			'https://fonts.googleapis.com/css'
		);

		return [
			'handle'  => WPGB_SLUG . '-fonts',
			'source'  => esc_url_raw( $url ),
			'version' => null,
		];

	}

	/**
	 * Add inline javascript code.
	 * Each grid has an unique instance (JS) which corresponds to the DOM order.
	 *
	 * @since 1.1.5 Added wpgb.loaded event to support defer and async scripts.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $script Script to inline.
	 * @return string
	 */
	public function inline_script( $script ) {

		$script .= 'window.addEventListener(\'wpgb.loaded\',function(){';

		array_walk(
			self::$javascript,
			function( $custom, $instance ) use ( &$script ) {

				$script .= '(function(){';
				$script .= 'var wpgb=WP_Grid_Builder.instance(' . ( (int) $instance + 1 ) . ');';
				$script .= 'if(!wpgb.init){return}';

				// Custom JS script from user.
				if ( ! empty( $custom ) ) {

					$custom  = wp_kses_decode_entities( $custom );
					$custom  = html_entity_decode( $custom );
					$script .= rtrim( $custom, ';' ) . ';';

				}

				$script .= 'wpgb.init()';
				$script .= '})();';

			}
		);

		$script .= '});';

		return apply_filters( 'wp_grid_builder/grid/inline_script', $script );

	}
}
