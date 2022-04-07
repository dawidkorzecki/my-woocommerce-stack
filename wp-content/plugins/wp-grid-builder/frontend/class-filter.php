<?php
/**
 * Filter
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Singleton;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter query vars
 *
 * @class WP_Grid_Builder\FrontEnd\Filter
 * @since 1.0.0
 */
final class Filter implements Models\Filter_Interface {

	use Singleton;

	/**
	 * Holds queried facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $facets = [];

	/**
	 * Holds queried object ids from facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $object_ids = [];

	/**
	 * Holds faceted query string
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $query_string = [];

	/**
	 * Holds unfiltered query variables
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $unfiltered_query_vars = [];

	/**
	 * Holds filtered query variables
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $filtered_query_vars = [];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'pre_get_posts', [ $this, 'update' ], PHP_INT_MAX - 9 );
		add_action( 'pre_get_users', [ $this, 'update' ], PHP_INT_MAX - 9 );
		add_action( 'pre_get_terms', [ $this, 'update' ], PHP_INT_MAX - 9 );

	}

	/**
	 * Update query variables
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $query Holds WP query object.
	 */
	public function update( $query ) {

		if ( ! $query || empty( $query->query_vars['wp_grid_builder'] ) ) {
			return;
		}

		$grid_id = $query->query_vars['wp_grid_builder'];

		// Unset wp_grid_builder to prevent running checks on additional queries.
		unset( $query->query_vars['wp_grid_builder'] );

		$this->get_object_type();
		$this->set_number_var( $query );
		$this->convert_offset_var( $query );

		$this->unfiltered_query_vars = $query->query_vars;

		$this->filter_query_string( $grid_id );
		$this->get_selected_facets();

		if ( ! empty( $this->facets ) ) {

			$this->set_queried_vars( $query );
			$this->set_queried_objects( $query );

		}

		$this->fix_include( $query );
		$this->filtered_query_vars = $query->query_vars;
		$this->optimize_query( $query );

	}

	/**
	 * Get queried object type.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_object_type() {

		$filter = current_filter();
		$filter = str_replace( 'pre_get_', '', $filter );
		$filter = rtrim( $filter, 's' );

		$this->object_type = $filter;
		$this->include_key = 'post' === $filter ? 'post__in' : 'include';
		$this->exclude_key = 'post' === $filter ? 'post__not_in' : 'exclude';

	}

	/**
	 * Set and unify number variable across object types
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $query Holds WP query object.
	 */
	public function set_number_var( $query ) {

		// Add default posts_per_page from WordPress option.
		if ( 'post' === $this->object_type && ! isset( $query->query_vars['posts_per_page'] ) ) {
			$query->query_vars['posts_per_page'] = get_option( 'posts_per_page', 10 );
		}

		// Add number to query vars to use same property in facets.
		if ( ! isset( $query->query_vars['number'] ) ) {
			$query->query_vars['number'] = $query->query_vars['posts_per_page'];
		}

		// Add default number from WordPress option.
		if ( 'post' === $this->object_type && empty( $query->query_vars['number'] ) ) {
			$query->query_vars['number'] = get_option( 'posts_per_page', 10 );
		}

		// In case -1 number is set for terms.
		if ( 'term' === $this->object_type && $query->query_vars['number'] < 0 ) {
			$query->query_vars['number'] = 0;
		}
	}

	/**
	 * Convert offset parameter to post__in/include or post__not_in/exclude
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $query Holds WP query object.
	 */
	public function convert_offset_var( $query ) {

		if ( ! isset( $query->query_vars['offset'] ) ) {
			return;
		}

		$offset = (int) $query->query_vars['offset'];

		if ( $offset < 1 ) {
			return;
		}

		// Unset offset to prevent conflict with pagination.
		$query->query_vars['offset'] = 0;
		// Get offsetted object ids.
		$object_ids = $this->get_offset_ids( $query, $offset );

		if ( empty( $object_ids ) ) {
			return;
		}

		$object_ids = array_values( $object_ids );

		if ( ! empty( $query->query_vars[ $this->include_key ] ) ) {

			// Remove offsetted ids from included ids.
			$query->query_vars[ $this->include_key ] = array_diff(
				$query->query_vars[ $this->include_key ],
				$object_ids
			);

			// Make sure to include nothing if offset matches all included ids.
			if ( empty( $query->query_vars[ $this->include_key ] ) ) {
				$query->query_vars[ $this->include_key ] = [ 0 ];
			}
		} else {

			// Merge offsetted ids with exluded ids.
			$query->query_vars[ $this->exclude_key ] = array_merge(
				$query->query_vars[ $this->exclude_key ],
				$object_ids
			);

		}
	}

	/**
	 * Get offsetted object ids.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object  $query  Holds WP query object.
	 * @param integer $offset Offset number.
	 * @return array Holds object ids.
	 */
	public function get_offset_ids( $query, $offset ) {

		switch ( $this->object_type ) {
			case 'post':
				return Helpers::get_post_ids( $query->query_vars, $offset );
			case 'term':
				return Helpers::get_term_ids( $query->query_vars, $offset );
			case 'user':
				return Helpers::get_user_ids( $query->query_vars, $offset );
		}
	}

	/**
	 * Allows to filter query string parameters dynamically.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int|string $grid_id Grid id.
	 */
	public function filter_query_string( $grid_id ) {

		$action = 'render';

		if ( wpgb_is_refreshing_facets() ) {
			$action = 'refresh';
		} elseif ( wpgb_is_searching_facet_choices() ) {
			$action = 'search';
		}

		// We reset query string.
		$this->query_string = wpgb_get_url_search_params();
		$this->query_string = apply_filters( 'wp_grid_builder/facet/query_string', $this->query_string, $grid_id, $action );

	}

	/**
	 * Get selected facets from query strings
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_selected_facets() {

		$slugs  = array_keys( $this->query_string );
		$facets = wpgb_get_facet_ids( $slugs );
		$facets = wpgb_get_facet_instances( $facets );

		// We reset facets.
		$this->facets = [];

		if ( empty( $facets ) ) {
			return;
		}

		foreach ( (array) $facets as $facet ) {

			// If facet not exists.
			if ( ! in_array( $facet['slug'], $slugs, true ) ) {
				continue;
			}

			if ( empty( $facet['selected'] ) ) {
				continue;
			}

			$this->facets[ $facet['slug'] ] = $facet;

		}
	}

	/**
	 * Get queried variables
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $query Holds query object.
	 */
	public function set_queried_vars( $query ) {

		foreach ( $this->facets as $facet ) {

			if ( ! method_exists( $facet['instance'], 'query_vars' ) ) {
				continue;
			}

			$vars = $facet['instance']->query_vars( $facet, $query->query_vars );

			// Set posts_per_page for post types.
			if ( isset( $vars['number'] ) ) {
				$vars['posts_per_page'] = $vars['number'];
			}

			if ( ! empty( $vars ) && is_array( $vars ) ) {
				$query->query_vars = array_merge( $query->query_vars, $vars );
			}
		}
	}

	/**
	 * Get queried objects ids
	 *
	 * @since 1.1.5 Keep search object ids order.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $query Holds WP query object.
	 */
	public function set_queried_objects( $query ) {

		$object_ids = [];

		// To do intersection with included object ids.
		if ( ! empty( $query->query_vars[ $this->include_key ] ) ) {
			$object_ids = $query->query_vars[ $this->include_key ];
		}

		foreach ( $this->facets as $facet ) {

			if ( ! method_exists( $facet['instance'], 'query_objects' ) ) {
				continue;
			}

			// Allows custom queried objects for each facet.
			$queried_ids = apply_filters( 'wp_grid_builder/facet/query_objects', false, $facet, $query->query_vars );

			if ( false === $queried_ids ) {
				$queried_ids = $facet['instance']->query_objects( $facet, $query->query_vars ) ?: [ 0 ];
			}

			// On first iteration.
			if ( empty( $object_ids ) ) {
				$object_ids = $queried_ids;
			} else {
				// Make sure to preserve search relevance (post order when intersecting).
				if ( 'search' === $facet['type'] ) {
					$object_ids = array_intersect( $queried_ids, $object_ids ) ?: [ 0 ];
				} else {
					$object_ids = array_intersect( $object_ids, $queried_ids ) ?: [ 0 ];
				}
			}

			$this->object_ids[ $facet['slug'] ] = $queried_ids;

		}

		// If no facets have been queried.
		if ( empty( $this->object_ids ) ) {
			return;
		}

		// Keep exluded object ids ('post__not_in' or 'exclude' vars).
		if ( ! empty( $query->query_vars[ $this->exclude_key ] ) ) {

			$object_ids = array_diff( $object_ids, $query->query_vars[ $this->exclude_key ] );
			$query->query_vars[ $this->exclude_key ] = [];

		}

		$query->query_vars[ $this->include_key ] = array_values( $object_ids ) ?: [ 0 ];

	}

	/**
	 * Fix include parameter issue from Wp_Term_Query.
	 *
	 * Fix WP_Term_Query 'include' with [ 0 ].
	 * If 'include' equals [ 0 ], it returns all terms.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param object $query Holds query object.
	 */
	public function fix_include( $query ) {

		if ( 'term' !== $this->object_type ) {
			return;
		}

		if ( empty( $query->query_vars['include'] ) ) {
			return;
		}

		// We set a large number to query nothing.
		// Passing negative value in include will not work.
		if ( [ 0 ] === $query->query_vars['include'] ) {
			$query->query_vars['include'] = [ PHP_INT_MAX ];
		}
	}

	/**
	 * Speed up query when we do not need to render queried content.
	 *
	 * @since 1.4.2
	 * @access public
	 *
	 * @param object $query Holds WP query object.
	 */
	public function optimize_query( $query ) {

		if ( ! wpgb_is_shadow_grid() && ! wpgb_is_rendering_facets() && ! wpgb_is_searching_facet_choices() ) {
			return;
		}

		$query->query_vars['no_found_rows']  = true;
		$query->query_vars['count_total']    = false;
		$query->query_vars['posts_per_page'] = 1;
		$query->query_vars['number']         = 1;

	}
}
