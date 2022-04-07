<?php
/**
 * Layout functions
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Container;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Do loop
 *
 * @since 1.0.0
 */
function wpgb_layout_do_loop() {

	Container::instance( 'Container/Grid' )->get( 'Loop' )->run();

}
add_action( 'wp_grid_builder/layout/do_loop', 'wpgb_layout_do_loop' );

/**
 * Render layout area
 *
 * @since 1.0.0
 *
 * @param string $name Area name.
 */
function wpgb_layout_do_area( $name ) {

	$areas = wpgb_get_grid_settings( 'grid_layout' );

	foreach ( $areas as $area => $args ) {

		if ( false === strpos( $area, $name ) || empty( $args['facets'] ) ) {
			continue;
		}

		Helpers::get_template( 'layout/area', $area );

	}

}
add_action( 'wp_grid_builder/layout/do_area', 'wpgb_layout_do_area' );

/**
 * Render sidebar
 *
 * @since 1.0.0
 *
 * @param string $name Sidebar name.
 */
function wpgb_layout_do_sidebar( $name ) {

	$sidebars = wpgb_get_grid_settings( 'grid_layout' );

	if ( empty( $sidebars[ $name ]['facets'] ) ) {
		return;
	}

	Helpers::get_template( 'layout/sidebar', $name );

}
add_action( 'wp_grid_builder/layout/do_sidebar', 'wpgb_layout_do_sidebar' );

/**
 * Render facets
 *
 * @since 1.0.0
 *
 * @param string $name Area name.
 */
function wpgb_layout_do_facets( $name ) {

	$settings = wpgb_get_grid_settings();
	$areas    = $settings->grid_layout;

	if ( empty( $areas[ $name ]['facets'] ) ) {
		return;
	}

	foreach ( $areas[ $name ]['facets'] as $facet ) {

		// Handle carousel "facets".
		if ( 'prev-button' === $facet || 'next-button' === $facet || 'page-dots' === $facet ) {
			Helpers::get_template( 'carousel/' . $facet );
		} else {

			wpgb_render_facet(
				[
					'id'      => $facet,
					'grid'    => $settings->id,
					'preview' => 'preview' === $settings->id && $settings->is_preview,
				]
			);

		}
	}
}
add_action( 'wp_grid_builder/layout/do_facets', 'wpgb_layout_do_facets' );

/**
 * Include SVG icons if not included (shadow grids/templates)
 *
 * @since 1.4.0
 */
function wpgb_layout_do_svg_icons() {

	if ( empty( wpgb_scripts()->scripts ) ) {
		return;
	}

	Helpers::get_template( 'layout/icons', '', true );

}
add_action( 'wp_footer', 'wpgb_layout_do_svg_icons' );

/**
 * Include SVG icons in block editor
 *
 * @since 1.4.0
 */
function wpgb_block_editor_do_svg_icons() {

	if ( ! wpgb_get_option( 'render_blocks' ) || ! wpgb_is_gutenberg() ) {
		return;
	}

	Helpers::get_template( 'layout/icons', '', true );

}
add_action( 'admin_footer', 'wpgb_block_editor_do_svg_icons' );
