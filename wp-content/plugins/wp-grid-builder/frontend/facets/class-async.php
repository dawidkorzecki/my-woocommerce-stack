<?php
/**
 * Handle asynchronous facet
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Facets;

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Async
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Async
 * @since 1.0.0
 */
class Async {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {}

	/**
	 * Query facet choices
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $facet  Holds facet settings.
	 * @param string $search Searched string.
	 * @return array Holds facet items.
	 */
	public function query_facet( $facet, $search = '' ) {

		if ( ! wpgb_has_selected_facets() ) {
			$items = $this->get_facet_items( $facet, $search, false );
		} elseif ( ! $facet['show_empty'] ) {
			$items = $this->get_facet_items( $facet, $search );
		} else {

			$items = $this->merge_facets(
				$this->get_facet_items( $facet, $search ),
				$this->get_facet_items( $facet, $search, false )
			);

		}

		return $this->normalize( $facet, $items );

	}

	/**
	 * Query facet items from object ids
	 *
	 * @since 1.3.0 Split search string into terms
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $facet    Holds facet settings.
	 * @param string  $search   Searched string.
	 * @param boolean $filtered Where clause state.
	 * @return array Holds facet items.
	 */
	public function get_facet_items( $facet, $search, $filtered = true ) {

		global $wpdb;

		// Make sure we can select any value if single selection.
		if ( ! $facet['multiple'] ) {
			$facet['logic'] = 'OR';
		}

		$child_clause = '';
		$order_clause = wpgb_get_orderby_clause( $facet );

		// Because we escape HTML and decode entities on search.
		$search_value = wp_specialchars_decode( $search, ENT_QUOTES );
		$search_value = wp_kses_normalize_entities( $search_value );

		if ( ! $facet['children'] ) {
			$child_clause = 'AND facet_parent = 0';
		}

		if ( $filtered ) {
			$where_clause = wpgb_get_filtered_where_clause( $facet, $facet['logic'] );
		} else {
			$where_clause = wpgb_get_unfiltered_where_clause();
		}

		$like_clause = $this->parse_search( $search_value );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT facet_name, facet_value, facet_id, facet_parent, COUNT(DISTINCT object_id) AS count
				FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				$child_clause
				AND $where_clause
				AND $like_clause
				GROUP BY facet_name
				ORDER BY $order_clause
				LIMIT %d",
				$facet['slug'],
				$facet['limit']
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	}

	/**
	 * Parse search string
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @param string $string Searched string to parse.
	 * @return array Search clauses
	 */
	public function parse_search( $string ) {

		global $wpdb;

		$terms = Helpers::split_into_words( $string );
		$terms = array_map(
			function( $term ) use ( $wpdb ) {
				return $wpdb->prepare( 'facet_name LIKE %s', '%' . $wpdb->esc_like( $term ) . '%' );
			},
			array_unique( $terms )
		);

		return implode( ' AND ', $terms );

	}

	/**
	 * Normalize facet length and name
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $facet Holds facet settings.
	 * @param boolean $items Hold facet items.
	 * @return array Holds facet items.
	 */
	public function normalize( $facet, $items ) {

		$count = $facet['show_count'];
		$items = array_slice( $items, 0, $facet['limit'] );

		return array_map(
			function( $item ) use ( $count ) {

				// Item value is used in combobox and added with .textContent method in JS so we need to decode.
				$item->facet_name   = html_entity_decode( esc_html( $item->facet_name ), ENT_QUOTES, 'UTF-8' );
				$item->facet_parent = (int) $item->facet_parent;
				$item->count        = $count ? (int) $item->count : false;

				return $item;

			},
			$items
		);

	}

	/**
	 * Merge facet items
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $filtered_facet   Holds filtered facet items.
	 * @param array $unfiltered_facet Holds unfiltered facet items.
	 * @return array Holds facet items.
	 */
	public function merge_facets( $filtered_facet, $unfiltered_facet ) {

		$filtered   = [];
		$unfiltered = [];

		// Rebuild filtered facet items with value as key.
		foreach ( $filtered_facet as $item ) {
			// Key as string (in case it's an integer) to preserve order.
			$filtered[ '_' . $item->facet_value ] = $item;
		}

		// Rebuild unfiltered facet items with value as key.
		foreach ( $unfiltered_facet as $item ) {
			$unfiltered[ '_' . $item->facet_value ] = $item;
		}

		// Remove filtered values.
		$unfiltered = array_filter(
			$unfiltered,
			function( $item ) use ( $filtered ) {
				return ! isset( $filtered[ $item->facet_value ] );
			}
		);

		$facet = $filtered + $unfiltered;
		$facet = array_map(
			function( $facet ) use ( $filtered ) {

				$is_filtered     = isset( $filtered[ '_' . $facet->facet_value ] );
				$facet->count    = $is_filtered ? $facet->count : 0;
				$facet->disabled = ! $facet->count;

				return $facet;

			},
			$facet
		);

		return array_values( $facet );

	}

	/**
	 * Query selected facet choices
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array Holds facet items.
	 */
	public function query_selected( $facet ) {

		if ( empty( $facet['selected'] ) ) {
			return [];
		}

		// Make sure we can select any value if single selection.
		if ( ! $facet['multiple'] ) {
			$facet['logic'] = 'OR';
		}

		$selected = $this->get_selected_items( $facet );

		// If no selected values found with current query.
		// Get selected values from unfiltered query.
		if ( empty( $selected ) ) {

			$selected = $this->get_selected_items( $facet, false );
			// Set count to 0 because the values do not exist initially (because of a search for example).
			$selected = array_map(
				function( $item ) {

					$item->count = 0;
					return $item;

				},
				$selected
			);

		}

		return $selected;

	}

	/**
	 * Query selected facet items from object ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $facet Holds facet settings.
	 * @param boolean $filtered Where clause state.
	 * @return array Holds facet items.
	 */
	public function get_selected_items( $facet, $filtered = true ) {

		global $wpdb;

		if ( wpgb_has_selected_facets() && $filtered ) {
			$where_clause = wpgb_get_filtered_where_clause( $facet, $facet['logic'] );
		} else {
			$where_clause = wpgb_get_unfiltered_where_clause();
		}

		$placeholders = rtrim( str_repeat( '%s,', count( $facet['selected'] ) ), ',' );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT facet_name, facet_value, facet_id, facet_parent, COUNT(DISTINCT object_id) AS count
				FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				AND $where_clause
				AND facet_value IN($placeholders)
				GROUP BY facet_value
				ORDER BY FIELD(facet_value, $placeholders)
				LIMIT %d",
				array_merge(
					(array) $facet['slug'],
					$facet['selected'],
					$facet['selected'],
					(array) count( $facet['selected'] )
				)
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	}
}
