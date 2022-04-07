<?php
/**
 * Autocomplete facet
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
 * Autocomplete
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Autocomplete
 * @since 1.3.0
 */
class Autocomplete {

	/**
	 * Render facet
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @param array   $facet  Holds facet settings.
	 * @param array   $items  Holds facet items.
	 * @param integer $parent Parent id to process children.
	 * @return string Facet markup.
	 */
	public function render_facet( $facet, $items, $parent = 0 ) {

		$label = $facet['title'] ?: __( 'Search content', 'wp-grid-builder' );
		$value = $this->get_facet_value( $facet );
		$input = sprintf(
			'<label>
				<span class="wpgb-sr-only">%1$s</span>
				<input class="wpgb-input" type="search" name="%2$s" placeholder="%3$s" value="%4$s" autocomplete="off">
				%5$s
			</label>',
			esc_html( $label ),
			esc_attr( $facet['slug'] ),
			esc_attr( $facet['acplt_placeholder'] ),
			esc_attr( $value ),
			$this->search_icon()
		);

		$output  = '<div class="wpgb-autocomplete-facet">';
		$output .= $input;
		$output .= '</div>';

		return apply_filters( 'wp_grid_builder/facet/autocomplete', $output, $facet );

	}

	/**
	 * Search icon
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @return string Select icon.
	 */
	public function search_icon() {

		$output  = '<svg class="wpgb-input-icon" viewBox="0 0 24 24" height="16" width="16" aria-hidden="true" focusable="false">';
		$output .= '<path fill="currentColor" d="M18.932 16.845a10.206 10.206 0 0 0 2.087-6.261A10.5 10.5 0 0 0 10.584 0a10.584 10.584 0 0 0 0 21.168 9.9 9.9 0 0 0 6.261-2.087l4.472 4.472a1.441 1.441 0 0 0 2.087 0 1.441 1.441 0 0 0 0-2.087zm-8.348 1.193a7.508 7.508 0 0 1-7.6-7.453 7.6 7.6 0 0 1 15.2 0 7.508 7.508 0 0 1-7.6 7.452z"></path>';
		$output .= '</svg>';

		return $output;

	}

	/**
	 * Query object ids
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array Holds queried facet object ids.
	 */
	public function query_objects( $facet ) {

		global $wpdb;

		// Because we escape HTML and decode entities on search.
		$search_value = $this->get_facet_value( $facet );
		$search_value = wp_specialchars_decode( $search_value, ENT_QUOTES );
		$search_value = wp_kses_normalize_entities( $search_value );
		$like_clause  = $this->parse_search( $search_value );

		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT object_id
				FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				AND $like_clause", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$facet['slug']
			)
		);

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
	 * Get string to search.
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return string Selected facet value.
	 */
	public function get_facet_value( $facet ) {

		// Revert array to string.
		$value = (array) $facet['selected'];
		$value = implode( ',', $value );

		return $value;

	}
}


