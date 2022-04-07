<?php
/**
 * Search facet
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
 * Search
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Search
 * @since 1.0.0
 */
class Search {

	/**
	 * Render facet
	 *
	 * @since 1.0.0
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
			</label>%6$s',
			esc_html( $label ),
			esc_attr( $facet['slug'] ),
			esc_attr( $facet['search_placeholder'] ),
			esc_attr( $value ),
			$this->search_icon(),
			$this->clear_button()
		);

		$output  = '<div class="wpgb-search-facet">';
		$output .= $input;
		$output .= '</div>';

		return apply_filters( 'wp_grid_builder/facet/search', $output, $facet );

	}

	/**
	 * Search icon
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Search icon.
	 */
	public function search_icon() {

		$output  = '<svg class="wpgb-input-icon" viewBox="0 0 24 24" height="16" width="16" aria-hidden="true" focusable="false">';
		$output .= '<path fill="currentColor" d="M18.932 16.845a10.206 10.206 0 0 0 2.087-6.261A10.5 10.5 0 0 0 10.584 0a10.584 10.584 0 0 0 0 21.168 9.9 9.9 0 0 0 6.261-2.087l4.472 4.472a1.441 1.441 0 0 0 2.087 0 1.441 1.441 0 0 0 0-2.087zm-8.348 1.193a7.508 7.508 0 0 1-7.6-7.453 7.6 7.6 0 0 1 15.2 0 7.508 7.508 0 0 1-7.6 7.452z"></path>';
		$output .= '</svg>';

		return $output;

	}

	/**
	 * Clear button
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @return string Clear button.
	 */
	public function clear_button() {

		$output  = '<button type="button" class="wpgb-clear-button" hidden>';
		$output .= '<span class="wpgb-sr-only">' . esc_html__( 'Clear', 'wp-grid-builder' ) . '</span>';
		$output .= '<svg viewBox="0 0 20 20" height="20" width="100%" aria-hidden="true" focusable="false">';
		$output .= '<path fill="currentColor" d="M12.549 14.737l-2.572-2.958-2.57 2.958a1.2 1.2 0 01-1.812-1.574L8.387 9.95 5.594 6.737a1.2 1.2 0 01.119-1.693 1.2 1.2 0 011.693.119l2.571 2.958 2.571-2.958a1.2 1.2 0 011.693-.119 1.2 1.2 0 01.119 1.693L11.567 9.95l2.793 3.213a1.2 1.2 0 11-1.811 1.574z"></path>';
		$output .= '</svg>';
		$output .= '</button>';

		return $output;

	}

	/**
	 * Query object ids
	 *
	 * @since 1.1.9 Add post_status in search query.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array Holds queried facet object ids.
	 */
	public function query_objects( $facet ) {

		$object = wpgb_get_queried_object_type();
		$search = $this->get_facet_value( $facet );
		$number = $facet['search_number'];

		switch ( $object ) {
			case 'post':
				$query_vars = wpgb_get_unfiltered_query_vars();
				$query['s'] = $search;
				$query['post_type'] = isset( $query_vars['post_type'] ) ? $query_vars['post_type'] : 'any';
				$query['post_status'] = isset( $query_vars['post_status'] ) ? $query_vars['post_status'] : 'any';
				$query = apply_filters( 'wp_grid_builder/facet/search_query_args', $query, $facet );
				return Helpers::get_post_ids( $query, $number );
			case 'term':
				$query['search'] = $search;
				$query = apply_filters( 'wp_grid_builder/facet/search_query_args', $query, $facet );
				return Helpers::get_term_ids( $query, $number );
			case 'user':
				$query['search'] = '*' . trim( $search ) . '*';
				$query = apply_filters( 'wp_grid_builder/facet/search_query_args', $query, $facet );
				return Helpers::get_user_ids( $query, $number );
		}

	}

	/**
	 * Get string to search.
	 *
	 * @since 1.0.0
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

	/**
	 * Query vars
	 *
	 * @since 1.1.5
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $query_vars Holds query vars.
	 * @return array Holds query vars to override.
	 */
	public function query_vars( $facet, $query_vars ) {

		if ( ! $facet['search_relevancy'] || ! empty( $query_vars['orderby'] ) ) {
			return;
		}

		return [
			'orderby' => 'post__in',
			'order'   => '',
		];

	}
}
