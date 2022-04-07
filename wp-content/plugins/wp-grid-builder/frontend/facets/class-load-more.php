<?php
/**
 * Load More facet
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Facets;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load More
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Load_more
 * @since 1.0.0
 */
class Load_More {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_filter( 'wp_grid_builder/facet/response', [ $this, 'set_settings' ], 10, 3 );

	}

	/**
	 * Filter facet response to set date settings
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @param array $response Holds facet response.
	 * @param array $facet    Holds facet settings.
	 * @param array $items    Holds facet items.
	 * @return array
	 */
	public function set_settings( $response, $facet, $items ) {

		// Skip other facets or if already set.
		if ( 'load_more' !== $facet['type'] || isset( $facet['settings']['offset'] ) ) {
			return $response;
		}

		$query_vars = wpgb_get_filtered_query_vars();
		$response['settings']['number'] = ! empty( $query_vars['number'] ) ? (int) $query_vars['number'] : 0;
		$response['settings']['offset'] = ! empty( $query_vars['offset'] ) ? (int) $query_vars['offset'] : 0;

		return $response;

	}

	/**
	 * Render facet
	 *
	 * @since 1.2.0 Handle shortcode [number] in button text.
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

		if ( ! isset( $query_vars['offset'] ) ) {
			$query_vars['offset'] = 0;
		}

		$number = (int) $query_vars['number'];
		$offset = (int) $query_vars['offset'];
		$remain = (int) $found_objects - $number - $offset;

		// if no more items to load or all items are loaded.
		// Post types never have 0 as value (default value is always set in pre_get_post @see Filter class).
		// Terms/users set to empty represent all (-1).
		if ( $remain <= 0 || $number <= 0 ) {
			return;
		}

		// Prevent to display items number twice if already enabled and present in button text.
		$button_text = str_replace( '[number]', $remain, $facet['load_more_text'] );
		$show_number = $button_text === $facet['load_more_text'] && $facet['load_more_remain'];

		$output = sprintf(
			'<button type="button" class="wpgb-button wpgb-load-more">%1$s%2$s</button>',
			$button_text,
			$show_number ? ' (' . $remain . ')' : ''
		);

		return apply_filters( 'wp_grid_builder/facet/load_more', $output, $facet, $number, $offset, $remain );

	}


	/**
	 * Query vars
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $query_vars Holds query vars.
	 * @return array Holds query vars to override.
	 */
	public function query_vars( $facet, $query_vars ) {

		$offset = reset( $facet['selected'] );

		return [
			'offset' => (int) $offset,
			'number' => (int) $facet['load_posts_number'],
		];

	}
}
