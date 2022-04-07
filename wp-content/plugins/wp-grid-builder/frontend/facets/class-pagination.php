<?php
/**
 * Pagination facet
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Facets;

use WP_Grid_Builder\Includes\Paginate;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pagination
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Pagination
 * @since 1.0.0
 */
class Pagination {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {}

	/**
	 * Render facet
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return string Facet markup.
	 */
	public function render_facet( $facet ) {

		$query_vars    = wpgb_get_filtered_query_vars();
		$found_objects = wpgb_get_found_objects();

		if ( ! isset( $query_vars['number'] ) ) {
			return;
		}

		$number = (int) $query_vars['number'] ?: $found_objects;
		$paged  = (int) reset( $facet['selected'] ) ?: 1;
		$total  = ceil( (int) $found_objects / $number );

		// Do not show if only one page.
		if ( $total <= 1 ) {
			return;
		}

		$output = '<nav class="wpgb-pagination-facet" aria-label="' . esc_attr__( 'Page navigation', 'wp-grid-builder' ) . '">';

		if ( 'prev_next' !== $facet['pagination'] ) {
			$output .= $this->numbered_pagination( $facet, $paged, $total );
		} else {
			$output .= $this->prev_next_buttons( $facet, $paged, $total );
		}

		$output .= '</nav>';

		return apply_filters( 'wp_grid_builder/facet/pagination', $output, $number, $paged, $total );

	}

	/**
	 * Render numbered pagination
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $paged Current page number.
	 * @param array $total Total number of page.
	 * @return string Pagination markup.
	 */
	public function numbered_pagination( $facet, $paged, $total ) {

		$prefix = '_' . $facet['slug'];
		$base   = wpgb_get_pagenum_link();

		ob_start();

		new Paginate(
			[
				'base'      => $base,
				'format'    => '?' . $prefix,
				'current'   => $paged,
				'total'     => $total,
				'show_all'  => $facet['show_all'],
				'prev_next' => $facet['prev_next'],
				'prev_text' => $facet['prev_text'],
				'next_text' => $facet['next_text'],
				'end_size'  => $facet['end_size'],
				'mid_size'  => $facet['mid_size'],
				'dots_page' => $facet['dots_page'],
				'classes'   => [
					'holder'  => 'wpgb-pagination',
					'page'    => 'wpgb-page',
					'dots'    => 'wpgb-dots-page',
					'current' => 'wpgb-current-page',
				],
			]
		);

		return ob_get_clean();

	}

	/**
	 * Render previous and next buttons
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $paged Current page number.
	 * @param array $total Total number of page.
	 * @return string Prev Next pagination markup.
	 */
	public function prev_next_buttons( $facet, $paged, $total ) {

		$pages = [];
		$base  = wpgb_get_pagenum_link();

		if ( $paged > 1 ) {

			$pages[] = [
				'number'  => $paged - 1,
				'content' => $facet['prev_text'] ?: __( 'Prev', 'wp-grid-builder' ),
			];

		}

		if ( $paged < $total ) {

			$pages[] = [
				'number'  => $paged + 1,
				'content' => $facet['next_text'] ?: __( 'Next', 'wp-grid-builder' ),
			];

		}

		$output = '<ul class="wpgb-pagination">';

		foreach ( $pages as $page ) {

			/* translators: %d: Page number */
			$aria_label = sprintf( __( 'Goto Page %d', 'wp-grid-builder' ), $page['number'] );
			$page_url   = add_query_arg( [ '_' . $facet['slug'] => $page['number'] ], $base );

			$output .= sprintf(
				'<li class="wpgb-page"><a href="%s" aria-label="%s" data-page="%d">%s</a></li>',
				esc_url( $page_url ),
				esc_attr( $aria_label ),
				esc_attr( $page['number'] ),
				esc_html( $page['content'] )
			);

		}

		$output .= '</ul>';

		return $output;

	}

	/**
	 * Query vars
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $query_vars Holds query vars.
	 * @return array Holds unfiltered query vars to override.
	 */
	public function query_vars( $facet, $query_vars ) {

		$paged  = (int) reset( $facet['selected'] ) - 1;
		$offset = $paged * (int) $query_vars['number'];

		return [ 'offset' => $offset ];

	}
}
