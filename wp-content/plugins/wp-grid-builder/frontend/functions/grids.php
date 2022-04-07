<?php
/**
 * Grid functions
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Admin\Preview;
use WP_Grid_Builder\Includes\Container;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render grid shortcode
 *
 * @since 1.0.3 Allow is_main_query in shortcode attribute
 * @since 1.0.0
 *
 * @param  array  $atts    Shortcode attributes.
 * @param  string $content Shortcode content.
 * @return string Grid markup
 */
function wpgb_grid_shortcode( $atts = [], $content = null ) {

	// Check atts against allowed atts for security reason.
	$args = array_fill_keys( [ 'id', 'is_main_query' ], 0 );
	$atts = array_filter( (array) $atts );
	$atts = wp_parse_args( $atts, $args );
	$atts = array_intersect_key( $atts, $args );

	ob_start();
	wpgb_render_grid( $atts );
	return ob_get_clean();

}
add_shortcode( 'wpgb_grid', 'wpgb_grid_shortcode' );

/**
 * Output Grid
 *
 * @since 1.0.0
 *
 * @param  mixed  $args Holds grid paramters or grid ID.
 * @param  string $abstract Container abstract class to call.
 * @return mixed
 */
function wpgb_render_grid( $args, $abstract = 'Layout' ) {

	$object    = null;
	$abstract  = ucfirst( $abstract );
	$namespace = 'WP_Grid_Builder\FrontEnd\\';
	$container = Container::instance( 'Container/Grid', $namespace );

	// Define container properties and methods.
	$container
		->add( 'grid', $args )
		->set( 'Settings' )
		->set( 'Normalize' )
		->set( 'Query' )
		->set( 'Cards' )
		->set( 'Loop' )
		->set( 'Layout' )
		->set( 'StyleSheet' )
		->set( 'Assets' );

	try {

		// Query grid settings and normalize.
		$container->get( 'Normalize' )->parse();
		// Get resolved method.
		$class = $container->get( $abstract );

		// Provide main container abstract methods.
		switch ( $abstract ) {
			case 'Settings':
				$object = get_object_vars( $class );
				break;
			case 'Cards':
				$object = $class->query()->get();
				break;
			case 'Query':
				$class->parse_query();
				break;
			case 'Loop':
				$class->run();
				break;
			case 'Layout':
				$class->render();
				break;
			case 'StyleSheet':
				$object = $class->generate()->get();
				break;
			case 'Assets':
				do_action( 'wp_grid_builder/grid/render' );
				$class->cards->query();
				$class->stylesheet->generate();
				$class->register();
				break;
		}
	} catch ( \Exception $e ) {

		$grid_id = is_numeric( $args ) ? $args : 0;
		$grid_id = isset( $args['id'] ) ? $args['id'] : $grid_id;

		// Only output error on first load (no grids found and no sources found).
		! wp_doing_ajax() && printf(
			'<pre class="wpgb-error-msg" data-id="%s">%s</pre>',
			esc_attr( $grid_id ),
			wp_kses_post( $e->getMessage() )
		);

	}

	$container->destroy( 'Container/Grid' );
	$container = null;

	return $object;

}

/**
 * Refresh Grid asynchronously
 *
 * @since 1.0.0
 *
 * @param  mixed $args Holds grid paramters or grid ID.
 * @return string
 */
function wpgb_refresh_grid( $args ) {

	// To handle edit card button in admin preview mode.
	if ( isset( $args['is_preview'] ) ) {
		new Preview();
	}

	if ( ! empty( $args['is_shadow'] ) ) {

		wpgb_render_grid( $args, 'Query' );
		return '';

	}

	ob_start();
	wpgb_render_grid( $args, 'Loop' );
	return ob_get_clean();

}
