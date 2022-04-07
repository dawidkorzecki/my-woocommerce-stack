<?php
/**
 * Scripts
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

use WP_Grid_Builder\Includes\I18n;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Localize plugin data
 *
 * @class WP_Grid_Builder\FrontEnd\Localize
 * @since 1.1.5
 */
final class Localize {

	/**
	 * Holds all identifiers to filter
	 *
	 * @since 1.4.0
	 * @access protected
	 * @var string
	 */
	protected $to_filter = [];

	/**
	 * Holds all identifiers to render
	 *
	 * @since 1.4.0
	 * @access protected
	 * @var string
	 */
	protected $to_render = [];

	/**
	 * Constructor
	 *
	 * @since 1.1.5
	 * @access public
	 */
	public function __construct() {

		add_filter( 'wp_grid_builder/facet/render_args', [ $this, 'get_identifiers' ], PHP_INT_MAX );
		add_filter( 'wp_grid_builder/grid/settings', [ $this, 'get_identifiers' ], PHP_INT_MAX );
		add_filter( 'wp_grid_builder/template/args', [ $this, 'get_identifiers' ], PHP_INT_MAX );
		add_filter( 'wp_grid_builder/frontend/localize_script', [ $this, 'localize_data' ] );

	}

	/**
	 * Get all grid identifiers to be rendered and/or filtered.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param array $args Holds facet, grid or template args.
	 * @return array
	 */
	public function get_identifiers( $args ) {

		if ( strpos( current_filter(), 'facet' ) !== false ) {
			$this->to_filter[] = $args['grid'];
		} else {
			$this->to_render[] = $args['id'];
		}

		return $args;

	}

	/**
	 * Localize data
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @param array $data Holds data to localize.
	 * @return array
	 */
	public function localize_data( $data ) {

		return array_merge(
			$data,
			$this->globals(),
			$this->helpers(),
			$this->lightbox(),
			$this->combobox(),
			$this->autocomplete(),
			$this->range_slider(),
			$this->vendors()
		);

	}

	/**
	 * Localize globals
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @return array
	 */
	public function globals() {

		$filter = count( array_unique( $this->to_filter ) );
		$render = count( $this->to_render );
		$unique = $filter < 2 && $render < 2;

		return [
			'lang'      => I18n::current_lang(),
			'ajaxUrl'   => Async::get_endpoint(),
			'history'   => wpgb_get_option( 'history' ) && $unique,
			'mainQuery' => wpgb_get_main_query_vars(),
			'permalink' => preg_replace( '/\?.*/', '', get_pagenum_link() ),
		];

	}

	/**
	 * Checks loaded scripts (mainly to for async/defer scripts)
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @return array
	 */
	public function helpers() {

		$scripts = wpgb_scripts()->scripts;

		return [
			'hasGrids'    => ! empty( $scripts['wpgb-layout'] ),
			'hasFacets'   => ! empty( $scripts['wpgb-facets'] ),
			'hasLightbox' => ! empty( $scripts['wpgb-lightbox'] ),
			'shadowGrids' => array_values( array_unique( array_diff( $this->to_filter, $this->to_render ) ) ),
		];

	}

	/**
	 * Localize lightbox strings
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @return array
	 */
	public function lightbox() {

		$options = wpgb_get_options();

		return [
			'lightbox' => [
				'plugin'     => $options['lightbox_plugin'],
				'counterMsg' => esc_html( $options['lightbox_counter_message'] ),
				'errorMsg'   => esc_html( $options['lightbox_error_message'] ),
				'prevLabel'  => esc_html( $options['lightbox_previous_label'] ),
				'nextLabel'  => esc_html( $options['lightbox_next_label'] ),
				'closeLabel' => esc_html( $options['lightbox_close_label'] ),
			],
		];

	}

	/**
	 * Localize combobox strings
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @return array
	 */
	public function combobox() {

		return [
			'combobox' => [
				'search'     => esc_html__( 'Please enter 1 or more characters.', 'wp-grid-builder' ),
				'loading'    => esc_html__( 'Loading...', 'wp-grid-builder' ),
				'cleared'    => esc_html__( 'options cleared.', 'wp-grid-builder' ),
				'expanded'   => esc_html__( 'Use Up and Down to choose options, press Enter to select the currently focused option, press Escape to collapse the list.', 'wp-grid-builder' ),
				'noResults'  => esc_html__( 'No Results Found.', 'wp-grid-builder' ),
				'collapsed'  => esc_html__( 'Press Enter or Space to expand the list.', 'wp-grid-builder' ),
				'clearLabel' => esc_html__( 'Clear', 'wp-grid-builder' ),
				/* translators: %s: Selected option name */
				'selected'   => esc_html__( 'option %s, selected.', 'wp-grid-builder' ),
				/* translators: %s: Deselected option name */
				'deselected' => esc_html__( 'option %s, deselected.', 'wp-grid-builder' ),
			],
		];

	}

	/**
	 * Localize autocomplete strings
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @return array
	 */
	public function autocomplete() {

		return [
			'autocomplete' => [
				'open'       => esc_html__( 'Use Up and Down to choose suggestions and press Enter to select suggestion.', 'wp-grid-builder' ),
				'input'      => esc_html__( 'Type to search or press Escape to clear the input.', 'wp-grid-builder' ),
				'clear'      => esc_html__( 'Field cleared.', 'wp-grid-builder' ),
				'noResults'  => esc_html__( 'No suggestions found.', 'wp-grid-builder' ),
				'loading'    => esc_html__( 'Loading suggestions...', 'wp-grid-builder' ),
				'clearLabel' => esc_html__( 'Clear', 'wp-grid-builder' ),
				/* translators: %s: Selected suggestion name */
				'select'     => esc_html__( '%s suggestion was selected.', 'wp-grid-builder' ),
			],
		];

	}

	/**
	 * Localize range slider strings
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array
	 */
	public function range_slider() {

		return [
			'range' => [
				'minLabel' => esc_html__( 'Minimum value', 'wp-grid-builder' ),
				'maxLabel' => esc_html__( 'Maximum value', 'wp-grid-builder' ),
			],
		];

	}

	/**
	 * Localize vendors
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @return array
	 */
	public function vendors() {

		return [
			'vendors' => [
				[
					'type'    => 'js',
					'handle'  => WPGB_SLUG . '-date',
					'source'  => WPGB_URL . 'frontend/assets/js/vendors/date.js',
					'version' => filemtime( WPGB_PATH . 'frontend/assets/js/vendors/date.js' ),
				],
				[
					'type'    => 'css',
					'handle'  => WPGB_SLUG . '-date-css',
					'source'  => WPGB_URL . 'frontend/assets/css/vendors/date.css',
					'version' => filemtime( WPGB_PATH . 'frontend/assets/css/vendors/date.css' ),
				],
				[
					'type'    => 'js',
					'handle'  => WPGB_SLUG . '-range',
					'source'  => WPGB_URL . 'frontend/assets/js/vendors/range.js',
					'version' => filemtime( WPGB_PATH . 'frontend/assets/js/vendors/range.js' ),
				],
				[
					'type'    => 'js',
					'handle'  => WPGB_SLUG . '-select',
					'source'  => WPGB_URL . 'frontend/assets/js/vendors/select.js',
					'version' => filemtime( WPGB_PATH . 'frontend/assets/js/vendors/select.js' ),
				],
				[
					'type'    => 'js',
					'handle'  => WPGB_SLUG . '-autocomplete',
					'source'  => WPGB_URL . 'frontend/assets/js/vendors/autocomplete.js',
					'version' => filemtime( WPGB_PATH . 'frontend/assets/js/vendors/autocomplete.js' ),
				],
			],
		];

	}
}
