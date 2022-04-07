<?php
/**
 * I18n handle internalization
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add WPML/Polylang support
 *
 * @class WP_Grid_Builder\Includes\I18n
 * @since 1.2.0 Add strings translation definitions and WPML indexer language.
 * @since 1.0.0
 */
class I18n {

	/**
	 * Constructor
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function __construct() {

		// Register strings definitions.
		add_filter( 'wp_grid_builder_i18n/grid/register_strings', [ $this, 'register_grid_strings' ] );
		add_filter( 'wp_grid_builder_i18n/card/register_strings', [ $this, 'register_card_strings' ] );
		add_filter( 'wp_grid_builder_i18n/facet/register_strings', [ $this, 'register_facet_strings' ] );

		// Handle WPML language when indexing.
		add_filter( 'wp_grid_builder/indexer/query_args', [ $this, 'remove_wpml_filters' ] );
		add_action( 'wp_grid_builder/indexer/facet_indexed', [ $this, 'add_wpml_filters' ] );

		// Prevent WPML to translate attachment IDs from post__in already queried in the right language.
		add_filter( 'wp_grid_builder/attachment/query_args', [ $this, 'remove_wpml_filters' ] );
		add_filter( 'wp_grid_builder/grid/the_objects', [ $this, 'add_wpml_filters' ] );

	}

	/**
	 * Check if Polylang exists
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public static function is_polylang() {

		return function_exists( 'pll_current_language' );

	}

	/**
	 * Check if WPML exists
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public static function is_wpml() {

		global $sitepress;

		if ( function_exists( 'icl_object_id' ) && ! empty( $sitepress ) ) {
			return $sitepress;
		}

		return false;

	}

	/**
	 * Get current lang
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string current language
	 */
	public static function current_lang() {

		$lang = '';
		$wpml = self::is_wpml();

		if ( self::is_polylang() ) {
			$lang = pll_current_language();
		}

		if ( $wpml ) {
			$lang = $wpml->get_current_language();
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( wp_doing_ajax() && ! empty( $_GET['lang'] ) ) {
			$lang = sanitize_key( wp_unslash( $_GET['lang'] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $lang;

	}

	/**
	 * Get default lang
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string default language
	 */
	public static function default_lang() {

		$lang = '';
		$wpml = self::is_wpml();

		if ( self::is_polylang() ) {
			$lang = pll_default_language();
		}

		if ( $wpml ) {
			$lang = $wpml->get_default_language();
		}

		return $lang;

	}

	/**
	 * Register grid string definitions
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array $registry Holds string definitions to translate.
	 * @return array
	 */
	public function register_grid_strings( $registry ) {

		$strings = [
			'no_posts_msg'   => [],
			'no_results_msg' => [],
		];

		return array_merge( $registry, $strings );

	}

	/**
	 * Register card string definitions
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array $registry Holds string definitions to translate.
	 * @return array
	 */
	public function register_card_strings( $registry ) {

		$strings = [
			'raw_content'              => [ 'multiline' => true ],
			'date_format'              => [],
			'author_prefix'            => [],
			'badge_label'              => [],
			'website_text'             => [],
			'meta_prefix'              => [],
			'meta_suffix'              => [],
			'meta_decimal_separator'   => [],
			'meta_thousands_separator' => [],
			'meta_output_date'         => [],
			'link_aria_label'          => [],
		];

		return array_merge( $registry, $strings );

	}

	/**
	 * Register facet string definitions
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array $registry Holds string definitions to translate.
	 * @return array
	 */
	public function register_facet_strings( $registry ) {

		$strings = [
			'title'                 => [],
			'show_more_label'       => [],
			'show_less_label'       => [],
			'select_placeholder'    => [],
			'all_label'             => [],
			'prefix'                => [],
			'suffix'                => [],
			'thousands_separator'   => [],
			'decimal_separator'     => [],
			'reset_range'           => [],
			'date_format'           => [],
			'date_placeholder'      => [],
			'search_placeholder'    => [],
			'prev_text'             => [],
			'next_text'             => [],
			'load_more_text'        => [],
			'loading_text'          => [],
			'result_count_singular' => [],
			'result_count_plural'   => [],
			'label'                 => [],
			'no_results'            => [],
			'loading'               => [],
			'search'                => [],
			'reset_label'           => [],
			'acplt_placeholder'     => [],
			'apply_label'           => [],
			'apply_url'             => [],
			'color_value'           => [],
			'color_label'           => [],
			'alphabetical_index'    => [],
			'numeric_index'         => [],
		];

		return array_merge( $registry, $strings );

	}

	/**
	 * Suppress filters to correctly index terms in all languages
	 *
	 * @since 1.2.2 Added parse_query action to prevent issue with attachment query.
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array $query_args Holds query arguments.
	 * @return array
	 */
	public function remove_wpml_filters( $query_args ) {

		$wpml = self::is_wpml();

		if ( ! $wpml ) {
			return $query_args;
		}

		remove_filter( 'get_terms_args', [ $wpml, 'get_terms_args_filter' ] );
		remove_filter( 'get_term', [ $wpml, 'get_term_adjust_id' ], 1 );
		remove_filter( 'get_pages', [ $wpml, 'get_pages_adjust_ids' ], 1 );
		remove_filter( 'terms_clauses', [ $wpml, 'terms_clauses' ] );
		remove_action( 'parse_query', [ $wpml, 'parse_query' ] );

		return $query_args;

	}

	/**
	 * Re-add filters to prevent to query terms in all languages
	 *
	 * @since 1.2.2 Added parse_query action to prevent issue with attachment query.
	 * @since 1.2.0
	 * @access public
	 *
	 * @param mixed $args Current filter argument (if any).
	 * @return mixed
	 */
	public function add_wpml_filters( $args ) {

		$wpml = self::is_wpml();

		if ( ! $wpml ) {
			return $args;
		}

		add_filter( 'terms_clauses', [ $wpml, 'terms_clauses' ], 10, 4 );
		add_filter( 'get_pages', [ $wpml, 'get_pages_adjust_ids' ], 1, 2 );
		add_filter( 'get_term', [ $wpml, 'get_term_adjust_id' ], 1, 1 );
		add_filter( 'get_terms_args', [ $wpml, 'get_terms_args_filter' ], 10, 2 );
		add_action( 'parse_query', [ $wpml, 'parse_query' ] );

		return $args;

	}
}
