<?php
/**
 * Source functions
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register sources
 *
 * @since 1.0.0
 *
 * @param array $source Holds registered sources.
 * @return array Holds registered sources.
 */
function wpgb_register_sources( $source = [] ) {

	return array_merge(
		[
			'post_type' => 'WP_Grid_Builder\FrontEnd\Sources\Posts',
			'user'      => 'WP_Grid_Builder\FrontEnd\Sources\Users',
			'term'      => 'WP_Grid_Builder\FrontEnd\Sources\Terms',
			'card'      => 'WP_Grid_Builder\FrontEnd\Sources\Cards',
		],
		$source
	);

}
add_filter( 'wp_grid_builder/sources', 'wpgb_register_sources' );

/**
 * Prefilter main query for template mode
 * Happens before WP_Grid_Builder\FrontEnd\Filter filters the query to store unmodified query.
 *
 * @since 1.0.0
 *
 * @param object $query The WP_Query instance.
 */
function wpgb_prefilter_query( $query ) {

	if ( ! wpgb_is_main_query( $query ) ) {
		return;
	}

	wpgb_set_main_query_vars( $query );
	$query->query_vars['wp_grid_builder'] = true;

}
add_action( 'pre_get_posts', 'wpgb_prefilter_query', PHP_INT_MAX - 10 );

/**
 * Check if it's the main query
 *
 * @since 1.2.1
 *
 * @param object $query Holds query object.
 * @return boolean
 */
function wpgb_is_main_query( $query ) {

	if ( empty( $query ) || is_admin() || ( wp_doing_ajax() && ! wpgb_doing_ajax() ) ) {
		return false;
	}

	if ( ! method_exists( $query, 'is_main_query' ) ) {
		return false;
	}

	$is_main_query = $query->is_main_query() && ! $query->is_singular;
	$is_wpgb_query = $query->get( 'wp_grid_builder', false );
	$is_feed_query = $query->is_feed;

	return $is_main_query && ! $is_wpgb_query && ! $is_feed_query;

}

/**
 * Store main query vars
 *
 * @since 1.2.1
 *
 * @param object $query Holds Main query object.
 * @return array
 */
function wpgb_main_query_vars( $query = [] ) {

	static $query_vars = [];

	if ( empty( $query_vars ) && ! empty( $query->query_vars ) ) {

		// To prevent issue on index blog page.
		if ( is_home() && ! isset( $query->query_vars['post_type'] ) ) {
			$query->query_vars['post_type'] = '';
		}

		$query_vars = $query->query_vars;

	}

	return $query_vars;

}

/**
 * Get main query vars
 *
 * @since 1.2.1
 *
 * @return array
 */
function wpgb_get_main_query_vars() {

	return wpgb_main_query_vars();

}

/**
 * Set main query vars
 *
 * @since 1.2.1
 *
 * @param object $query Main query.
 */
function wpgb_set_main_query_vars( $query ) {

	wpgb_main_query_vars( $query );

}

/**
 * Query grid or template content
 *
 * @since 1.5.1
 *
 * @param array $atts Grid/Template attributes.
 */
function wpgb_query_content( $atts = [] ) {

	if ( ! empty( $atts['is_template'] ) ) {
		wpgb_render_template( $atts, 'Query' );
	} else {
		wpgb_render_grid( $atts, 'Query' );
	}
}

/**
 * Refresh grid or template content
 *
 * @since 1.5.1
 *
 * @param array $atts Grid/Template attributes.
 * @return string
 */
function wpgb_refresh_content( $atts = [] ) {

	$content = '';

	if ( ! empty( $atts['is_template'] ) ) {
		$content = wpgb_refresh_template( $atts );
	} else {
		$content = wpgb_refresh_grid( $atts );
	}

	return $content;

}
