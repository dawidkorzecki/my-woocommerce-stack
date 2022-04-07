<?php
/**
 * Apply facet
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
 * Apply
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Apply
 * @since 1.4.0
 */
class Apply {

	/**
	 * Constructor
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function __construct() {}

	/**
	 * Render facet
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return string Facet markup.
	 */
	public function render_facet( $facet ) {

		if ( empty( $facet['apply_label'] ) ) {
			return;
		}

		$output  = '<button type="button" class="wpgb-button wpgb-apply" name="' . esc_attr( $facet['slug'] ) . '">';
		$output .= esc_html( $facet['apply_label'] );
		$output .= '</button>';

		return apply_filters( 'wp_grid_builder/facet/apply', $output, $facet );

	}
}
