<?php
/**
 * Template functions
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\FrontEnd\Template;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render template shortcode
 *
 * @since 1.4.0
 *
 * @param  array  $atts    Shortcode attributes.
 * @param  string $content Shortcode content.
 * @return string Template markup
 */
function wpgb_template_shortcode( $atts = [], $content = null ) {

	// Check atts against allowed atts for security reason.
	$args = array_fill_keys( [ 'id', 'is_main_query' ], 0 );
	$atts = array_filter( (array) $atts );
	$atts = wp_parse_args( $atts, $args );
	$atts = array_intersect_key( $atts, $args );

	ob_start();
	wpgb_render_template( $atts );
	return ob_get_clean();

}
add_shortcode( 'wpgb_template', 'wpgb_template_shortcode' );

/**
 * Render template
 *
 * @since 1.0.0
 *
 * @param array  $args     Template paramters.
 * @param string $abstract Abstract class method to call.
 */
function wpgb_render_template( $args, $abstract = 'render' ) {

	$template = new Template( $args );

	if ( 'render' === $abstract ) {
		$template->render();
	} else {
		$template->query();
	}

}

/**
 * Refresh template asynchronously
 *
 * @since 1.0.0
 *
 * @param array $args Template paramters.
 * @return string
 */
function wpgb_refresh_template( $args ) {

	$template = new Template( $args );

	ob_start();
	$template->refresh();
	return ob_get_clean();

}
