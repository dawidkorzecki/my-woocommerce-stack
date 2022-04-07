<?php
/**
 * Asset functions
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\FrontEnd\Colors;
use WP_Grid_Builder\FrontEnd\Styles;
use WP_Grid_Builder\FrontEnd\Scripts;
use WP_Grid_Builder\Includes\File;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Scripts instance
 *
 * @since 1.2.1
 *
 * @return Scripts WP_Grid_Builder\FrontEnd\Scripts instance.
 */
function wpgb_scripts() {

	return Scripts::get_instance();

}

/**
 * Get styles instance
 *
 * @since 1.2.1
 *
 * @return Styles WP_Grid_Builder\FrontEnd\Styles instance.
 */
function wpgb_styles() {

	return Styles::get_instance();

}

/**
 * Register core plugin script
 *
 * @since 1.2.1
 *
 * @param string $handle Script handle name.
 */
function wpgb_register_script( $handle ) {

	$instance = wpgb_scripts();
	$instance->register_script( $handle );

}

/**
 * Register core plugin style
 *
 * @since 1.2.1
 *
 * @param string $handle Style handle name.
 */
function wpgb_register_style( $handle ) {

	$instance = wpgb_styles();
	$instance->register_style( $handle );

}

/**
 * Deregister core plugin script
 *
 * @since 1.2.1
 *
 * @param string $handle Script handle name.
 */
function wpgb_deregister_script( $handle ) {

	$instance = wpgb_scripts();
	$instance->deregister_script( $handle );

}

/**
 * Deregister core plugin style
 *
 * @since 1.2.1
 *
 * @param string $handle Style handle name.
 */
function wpgb_deregister_style( $handle ) {

	$instance = wpgb_styles();
	$instance->deregister_style( $handle );

}

/**
 * Enqueue all registered scripts
 *
 * @since 1.2.1
 */
function wpgb_enqueue_scripts() {

	$instance = wpgb_scripts();
	$instance->enqueue();

}

/**
 * Enqueue all registered styles
 *
 * @since 1.2.1
 */
function wpgb_enqueue_styles() {

	$instance = wpgb_styles();
	$instance->enqueue();

}

/**
 * Get registered scripts
 *
 * @since 1.2.1
 *
 * @return array
 */
function wpgb_get_scripts() {

	$instance = wpgb_scripts();
	return $instance->get_scripts();

}

/**
 * Get registered styles
 *
 * @since 1.2.1
 *
 * @return array
 */
function wpgb_get_styles() {

	$instance = wpgb_styles();
	return $instance->get_styles();

}

/**
 * Get core scripts
 *
 * @since 1.2.1
 *
 * @return array
 */
function wpgb_get_core_scripts() {

	$instance = wpgb_scripts();
	return $instance->core_scripts;

}

/**
 * Get core styles
 *
 * @since 1.2.1
 *
 * @return array
 */
function wpgb_get_core_styles() {

	$instance = wpgb_styles();
	return $instance->core_styles;

}

/**
 * Get CSS of facet colors
 *
 * @since 1.4.0
 *
 * @return string
 */
function wpgb_get_facet_colors_css() {

	return wp_strip_all_tags( ( new Colors() )->facets() );

}

/**
 * Generate facet colors stylesheet
 *
 * @since 1.4.0
 *
 * @return boolean
 */
function wpgb_generate_facet_colors_stylesheet() {

	if ( File::get_url( 'facets', 'colors.css' ) ) {
		return true;
	}

	return File::put_contents( 'facets', 'colors.css', wpgb_get_facet_colors_css() );

}

/**
 * Generate and get facet colors stylesheet
 *
 * @since 1.4.0
 *
 * @return array
 */
function wpgb_get_facet_colors_stylesheet() {

	if ( ! wpgb_generate_facet_colors_stylesheet() ) {
		return [];
	}

	return [
		'handle'  => WPGB_SLUG . '-colors',
		'source'  => esc_url_raw( File::get_url( 'facets', 'colors.css' ) ),
		'version' => filemtime( File::get_path( 'facets', 'colors.css' ) ),
	];

}

/**
 * Register facet colors stylsheet in block editor
 *
 * @since 1.4.0
 *
 * @param array $styles Holds registered stylsheets.
 * @return array
 */
function wpgb_editor_register_facet_colors_style( $styles ) {

	if ( wpgb_get_option( 'render_blocks' ) && wpgb_is_gutenberg() ) {
		$styles[] = wpgb_get_facet_colors_stylesheet();
	}

	return $styles;

}
add_action( 'wp_grid_builder/frontend/register_styles', 'wpgb_editor_register_facet_colors_style' );

/**
 * Register facet colors stylsheet on frontend
 *
 * @since 1.4.0
 *
 * @param array $styles Holds registered stylsheets.
 * @return array
 */
function wpgb_register_facet_colors_style( $styles ) {

	$handles = array_column( $styles, 'handle' );

	if ( ! in_array( WPGB_SLUG . '-style', $handles, true ) ) {
		$styles[] = wpgb_get_facet_colors_stylesheet();
	}

	return $styles;

}
add_filter( 'wp_grid_builder/frontend/register_styles', 'wpgb_register_facet_colors_style' );

/**
 * Inline facet colors CSS if stylesheet missing (fallback)
 *
 * @since 1.4.0
 *
 * @param string $styles Holds inline styles.
 * @param array  $handles Holds enqueued stylesheet handles.
 * @return string
 */
function wpgb_inline_facet_colors_style( $styles, $handles ) {

	if ( ! isset( $handles[ WPGB_SLUG . '-style' ] ) && ! isset( $handles[ WPGB_SLUG . '-colors' ] ) ) {
		$styles .= wpgb_get_facet_colors_css();
	}

	return $styles;

}
add_filter( 'wp_grid_builder/frontend/add_inline_style', 'wpgb_inline_facet_colors_style', 10, 2 );

/**
 * Dequeue facets stylesheet if main stylesheet is already registered
 *
 * @since 1.4.0
 *
 * @param array $handles Holds handles to enqueue.
 * @return array
 */
function wpgb_dequeue_facets_style( $handles ) {

	if ( isset( $handles[ WPGB_SLUG . '-facets' ], $handles[ WPGB_SLUG . '-style' ] ) ) {
		unset( $handles[ WPGB_SLUG . '-facets' ] );
	}

	return $handles;

}
add_filter( 'wp_grid_builder/frontend/enqueue_styles', 'wpgb_dequeue_facets_style' );

/**
 * Prevent to defer/async polyfills script
 * Polyfills must be loaded before all other plugin/add-on scripts to prevent any error.
 *
 * @since 1.2.1
 *
 * @param string $tag    The `<script>` tag for the enqueued script.
 * @param string $handle The script's registered handle.
 * @param string $src    The script's source URL.
 * @return string Script tag
 */
function wpgb_polyfill_script_tag( $tag, $handle, $src ) {

	if ( WPGB_SLUG . '-polyfills' === $handle ) {

		$tag = preg_replace(
			'(<script.*?\ssrc=.*?><\/script>)',                // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
			'<script src="' . esc_url( $src ) . '"></script>', // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
			$tag
		);

	}

	return $tag;

}
add_filter( 'script_loader_tag', 'wpgb_polyfill_script_tag', PHP_INT_MAX, 3 );
