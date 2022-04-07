<?php
/**
 * Add SearchWP support
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Third_Party;

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle SearchWP search feature
 *
 * @class WP_Grid_Builder\Includes\Third_Party\SearchWP
 * @since 1.0.0
 */
class SearchWP {

	/**
	 * Holds searched keywords
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $keywords = '';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		if ( ! class_exists( 'SWP_Query' ) ) {
			return;
		}

		add_filter( 'wp_grid_builder/facet/search_engines', [ $this, 'register_engines' ] );
		add_filter( 'wp_grid_builder/facet/search_query_args', [ $this, 'search_terms' ], 10, 2 );
		add_filter( 'wp_grid_builder/facet/query_objects', [ $this, 'query_objects' ], 10, 2 );
		add_filter( 'wp_grid_builder/async/get_endpoint', [ $this, 'add_search_query' ] );
		add_filter( 'searchwp\native\short_circuit', [ $this, 'short_circuit' ], 0, 2 );
		add_filter( 'get_search_query', [ $this, 'restore_search_query' ] );
		add_filter( 'posts_results', [ $this, 'highlight' ] );

	}

	/**
	 * Register SearchWP engines
	 *
	 * @since 1.5.2
	 * @access public
	 *
	 * @param  array $engines Holds registered search engines.
	 * @return array
	 */
	public function register_engines( $engines ) {

		if ( ! method_exists( '\SearchWP\Settings', '_get_engines_settings' ) ) {
			return $engines;
		}

		$swp_engines = \SearchWP\Settings::_get_engines_settings();

		unset( $swp_engines['default'] );

		if ( ! empty( $swp_engines ) ) {
			$engines['searchwp'] .= ' (default)';
		}

		foreach ( $swp_engines as $engine => $args ) {
			$engines[ 'searchwp/' . $engine ] = 'SearchWP (' . $args['label'] . ')';
		}

		return $engines;

	}

	/**
	 * Prevent running SearchWP if enabled
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $query_args Holds WP query args.
	 * @param  array $facets Holds facet settings.
	 * @return array WP Query args.
	 */
	public function search_terms( $query_args, $facets ) {

		$query_args['suppress_filters'] = true;
		return $query_args;

	}

	/**
	 * Query object ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $match Match state.
	 * @param array $facet Holds facet settings.
	 * @return array Holds queried facet object ids.
	 */
	public function query_objects( $match, $facet ) {

		if (
			empty( $facet['type'] ) ||
			'search' !== $facet['type'] ||
			empty( $facet['search_engine'] ) ||
			0 !== stripos( $facet['search_engine'], 'searchwp' )
		) {
			return $match;
		}

		$object = wpgb_get_queried_object_type();
		$engine = substr( $facet['search_engine'], 9 );
		$number = (int) $facet['search_number'];

		if ( 'post' !== $object ) {
			return $match;
		}

		$this->keywords = (array) $facet['selected'];
		$this->keywords = wp_unslash( implode( ' ', $this->keywords ) );

		return $this->query_posts( $this->keywords, $engine, $number );

	}

	/**
	 * Query post ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string  $keywords Searched keywords.
	 * @param string  $engine   SearchWP engine.
	 * @param integer $number   Number to search for.
	 * @return array Queried post ids.
	 */
	public function query_posts( $keywords = '', $engine = '', $number = 200 ) {

		if ( empty( $engine ) ) {
			$engine = 'default';
		}

		return (array) (
			new \SWP_Query(
				[
					's'              => $keywords,
					'engine'         => $engine,
					'posts_per_page' => $number,
					'fields'         => 'ids',
				]
			)
		)->posts ?: [ 0 ];

	}

	/**
	 * Add search query parameter to endpoint if missing
	 *
	 * @access public
	 * @since 1.5.4
	 *
	 * @param string $endpoint Async endpoint url.
	 * @return string
	 */
	public function add_search_query( $endpoint ) {

		$query = get_search_query();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['s'] ) && ! empty( $query ) ) {
			$endpoint = add_query_arg( 's', $query, $endpoint );
		}

		return $endpoint;

	}

	/**
	 * Prevent SearchWP to modify the main query directly
	 *
	 * @since 1.5.4
	 * @access public
	 *
	 * @param boolean   $short_circuit Whether to be short circuit SearchWP.
	 * @param \WP_Query $query         Holds query instance.
	 * @return boolean
	 */
	public function short_circuit( $short_circuit, $query ) {

		if ( ! $query instanceof \WP_Query ) {
			return $short_circuit;
		}

		if ( ! wpgb_doing_ajax() || empty( $query->get( 'wp_grid_builder' ) ) ) {
			return $short_circuit;
		}

		$keywords = trim( $query->get( 's' ) );

		if ( empty( $keywords ) ) {
			return $short_circuit;
		}

		$this->keywords = $keywords;

		$post__in = array_diff(
			$this->query_posts( $this->keywords ),
			$query->get( 'post__not_in' ) ?: []
		);

		$query->set( 's', '' );
		$query->set( '_s', $keywords );
		$query->set( 'post__not_in', [] );
		$query->set( 'post__in', $post__in );

		if ( empty( $query->get( 'orderby' ) ) ) {
			$query->set( 'orderby', 'post__in' );
		}

		return true;

	}

	/**
	 * Restore search query
	 *
	 * @since 1.5.4
	 * @access public
	 *
	 * @param mixed $search Contents of the search query variable.
	 * @return mixed
	 */
	public function restore_search_query( $search ) {

		if ( empty( trim( $search ) ) ) {
			$search = get_query_var( '_s' );
		}

		return $search;

	}

	/**
	 * Highlight keywords in filtered content
	 *
	 * @since 1.5.2
	 * @access public
	 *
	 * @param array $posts Holds posts.
	 * @return array
	 */
	public function highlight( $posts ) {

		if ( empty( $this->keywords ) ) {
			return $posts;
		}

		if (
			! method_exists( '\SearchWP\Settings', 'get' ) ||
			! method_exists( '\SearchWP\Highlighter', 'apply' )
		) {
			return $posts;
		}

		if ( ! \SearchWP\Settings::get( 'highlighting', 'boolean' ) ) {
			return $posts;
		}

		$highlighter = new \SearchWP\Highlighter();
		$keywords    = Helpers::split_into_words( trim( $this->keywords ) );

		$posts = array_map(
			function( $post ) use ( $highlighter, $keywords ) {

				$post->post_title   = $highlighter::apply( $post->post_title, $keywords );
				$post->post_excerpt = $highlighter::apply( $post->post_excerpt, $keywords );

				return $post;

			},
			$posts
		);

		if ( ! wpgb_doing_ajax() ) {
			$this->keywords = '';
		}

		return $posts;

	}
}
