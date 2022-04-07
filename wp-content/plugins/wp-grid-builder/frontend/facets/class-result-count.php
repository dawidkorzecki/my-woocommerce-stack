<?php
/**
 * Result Count facet
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
 * Result Count
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Result_Count
 * @since 1.0.0
 */
class Result_Count {

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

		if (
			empty( $facet['result_count_singular'] ) &&
			empty( $facet['result_count_plural'] )
		) {
			return;
		}

		$query_vars    = wpgb_get_filtered_query_vars();
		$found_objects = wpgb_get_found_objects();

		if (
			( empty( $facet['result_count_singular'] ) && (int) $found_objects < 2 ) ||
			( empty( $facet['result_count_plural'] ) && (int) $found_objects > 1 ) ||
			( ! isset( $query_vars['number'] ) ) ||
			(int) $found_objects < 1
		) {
			return;
		}

		if ( ! isset( $query_vars['offset'] ) ) {
			$query_vars['offset'] = 0;
		}

		$number = max( 0, (int) $query_vars['number'] ) ?: $found_objects;
		$offset = (int) $query_vars['offset'];
		$from   = max( 1, $offset + 1 );
		$to     = min( $from - 1 + $number, $found_objects );

		$output = '<span class="wpgb-result-count">';

			$output .= esc_html(
				str_replace(
					[ '[from]', '[to]', '[total]' ],
					[ $from, $to, $found_objects ],
					(int) $found_objects > 1 ? $facet['result_count_plural'] : $facet['result_count_singular']
				)
			);

		$output .= '</span>';

		return apply_filters( 'wp_grid_builder/facet/result_count', $output, $from, $to, $found_objects );

	}

}
