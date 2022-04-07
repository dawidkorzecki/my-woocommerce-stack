<?php
/**
 * Facet functions
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\I18n;
use WP_Grid_Builder\FrontEnd\Filter;
use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Database;
use WP_Grid_Builder\Includes\Container;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render facet shortcode
 *
 * @since 1.0.0
 *
 * @param  array  $atts    Shortcode attributes.
 * @param  string $content Shortcode content.
 * @return string Facet markup
 */
function wpgb_facet_shortcode( $atts = [], $content = null ) {

	ob_start();
	wpgb_render_facet( $atts );
	return ob_get_clean();

}
add_shortcode( 'wpgb_facet', 'wpgb_facet_shortcode' );

/**
 * Register facets
 *
 * @since 1.0.0
 *
 * @param array $facets Holds registered facets.
 * @return array Holds registered facets.
 */
function wpgb_register_facets( $facets = [] ) {

	$defaults = [
		'checkbox'     => [
			'name' => __( 'Checkboxes', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'radio'        => [
			'name' => __( 'Radio', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'select'       => [
			'name' => __( 'Dropdown', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'button'       => [
			'name' => __( 'Buttons', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'hierarchy'    => [
			'name' => __( 'Hierarchy', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'range'        => [
			'name' => __( 'Range Slider', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'date'         => [
			'name' => __( 'Date Picker', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'rating'       => [
			'name' => __( 'Rating Picker', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'color'        => [
			'name' => __( 'Color Picker', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'az_index'     => [
			'name' => __( 'A-Z Index', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'search'       => [
			'name' => __( 'Search Field', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'autocomplete' => [
			'name' => __( 'Auto-Complete', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'selection'    => [
			'name' => __( 'Selections', 'wp-grid-builder' ),
			'type' => 'filter',
		],
		'pagination'   => [
			'name' => __( 'Pagination', 'wp-grid-builder' ),
			'type' => 'load',
		],
		'load_more'    => [
			'name' => __( 'Load More', 'wp-grid-builder' ),
			'type' => 'load',
		],
		'per_page'     => [
			'name' => __( 'Per Page', 'wp-grid-builder' ),
			'type' => 'load',
		],
		'result_count' => [
			'name' => __( 'Result Count', 'wp-grid-builder' ),
			'type' => 'load',
		],
		'sort'         => [
			'name' => __( 'Sort', 'wp-grid-builder' ),
			'type' => 'sort',
		],
		'apply'        => [
			'name' => __( 'Apply', 'wp-grid-builder' ),
			'type' => 'apply',
		],
		'reset'        => [
			'name' => __( 'Reset', 'wp-grid-builder' ),
			'type' => 'reset',
		],
	];

	foreach ( $defaults as $slug => $args ) {

		$defaults[ $slug ]['class'] = 'WP_Grid_Builder\FrontEnd\Facets\\' . $slug;
		$defaults[ $slug ]['icons'] = [
			'small' => Helpers::get_icon( $slug . '-facet-small', true ),
			'large' => Helpers::get_icon( $slug . '-facet-large', true ),
		];

	}

	return array_merge( $defaults, $facets );

}
add_filter( 'wp_grid_builder/facets', 'wpgb_register_facets' );

/**
 * Render facet
 *
 * @since 1.0.0
 *
 * @param array $args Facet paramters.
 * @return string
 */
function wpgb_render_facet( $args ) {

	// Normalize arguments.
	$args = apply_filters(
		'wp_grid_builder/facet/render_args',
		wp_parse_args(
			$args,
			[
				'id'        => 0,
				'grid'      => '',
				'align'     => '',
				'class'     => '',
				'className' => '',
			]
		)
	);

	// If invalid facet ID.
	if ( (int) $args['id'] < 1 ) {

		printf(
			// Translators: %1$d facet ID, %2$d grid ID.
			'<pre class="wpgb-error-msg">' . esc_html__( 'The facet ID "%1$d", used to filter the grid "%2$s", is not valid.', 'wp-grid-builder' ) . '</pre>',
			(int) $args['id'],
			esc_html( $args['grid'] )
		);

		return;

	}

	// If missing grid/template ID.
	if ( empty( $args['grid'] ) ) {

		printf(
			// Translators: %1$d facet ID, %2$d grid ID.
			'<pre class="wpgb-error-msg">' . esc_html__( 'The grid ID "%2$d", to be filtered by facet ID "%1$d", is not valid.', 'wp-grid-builder' ) . '</pre>',
			(int) $args['id'],
			(int) $args['grid']
		);

		return;

	}

	$facet_html = '';

	// Get facet html from cache (facet content is unique per grid).
	// We do not cache in grid settings preview mode when not saved.
	if ( empty( $args['preview'] ) ) {

		$language   = I18n::current_lang();
		$transient  = WPGB_SLUG . '_G' . $args['grid'] . 'F' . $args['id'] . $language;
		$facet_html = get_transient( apply_filters( 'wp_grid_builder/facet/transient_name', $transient ) );

	}

	$class_names  = 'wpgb-facet-' . $args['id'];
	$class_names .= ' ' . $args['class'];
	$class_names .= ' ' . $args['className'];

	// Gutenberg align property.
	if ( ! empty( $args['align'] ) && 'none' !== $args['align'] ) {
		$class_names .= ' align' . $args['align'];
	}

	$class_names = Helpers::sanitize_html_classes( $class_names );

	do_action( 'wp_grid_builder/facet/render' );

	printf(
		'<div class="wpgb-facet wpgb-loading %1$s" data-facet="%2$d" data-grid="%3$s">%4$s</div>',
		esc_attr( $class_names ),
		(int) $args['id'],
		esc_attr( trim( $args['grid'] ) ),
		$facet_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);

}

/**
 * Refresh facets asynchronously
 *
 * @since 1.0.0
 *
 * @param array   $args   Facet parameters.
 * @param boolean $render Whether to render facet HTML.
 * @return string|null
 */
function wpgb_refresh_facets( $args, $render = true ) {

	// Define container properties and methods.
	$container = Container::instance( 'Container/Facets' );
	$container->add( 'settings', $args );
	$container->set( 'Facets', 'WP_Grid_Builder\FrontEnd\Facets' );

	return $container->get( 'Facets' )->refresh( $render );

}

/**
 * Search facet choices asynchronously
 *
 * @since 1.4.1
 *
 * @param array $args Facet parameters.
 * @return string|null
 */
function wpgb_search_facet_choices( $args ) {

	// Define container properties and methods.
	$container = Container::instance( 'Container/Facets' );
	$container->add( 'settings', $args );
	$container->set( 'Facets', 'WP_Grid_Builder\FrontEnd\Facets' );

	return $container->get( 'Facets' )->search();

}

/**
 * Get facets instance
 *
 * @since 1.0.0
 *
 * @return object.
 */
function wpgb_get_facets_instance() {

	return Container::instance( 'Container/Facets' )->get( 'Facets' );

}

/**
 * Get Filter instance
 *
 * @since 1.0.0
 *
 * @return object.
 */
function wpgb_get_filter_instance() {

	return Filter::get_instance();

}

/**
 * Get selected facet values
 *
 * @since 1.0.0
 *
 * @param  string $slug Facet slug.
 * @return array Selected facet values.
 */
function wpgb_get_selected_facet_values( $slug = '' ) {

	$filter = wpgb_get_filter_instance();

	if ( empty( $filter->query_string[ $slug ] ) ) {
		return [];
	}

	return $filter->query_string[ $slug ];

}

/**
 * Check if there are at least one facet selected for the current query
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function wpgb_has_selected_facets() {

	$facets = wpgb_get_filter_instance()->facets;

	return ! empty( $facets );

}

/**
 * Get query strings
 *
 * @since 1.0.0
 *
 * @return array
 */
function wpgb_get_query_string() {

	$filter = wpgb_get_filter_instance();

	if ( empty( $filter->query_string ) ) {
		return [];
	}

	return $filter->query_string;

}

/**
 * Get filtered query variables
 *
 * @since 1.0.0
 *
 * @return array
 */
function wpgb_get_filtered_query_vars() {

	$filter = wpgb_get_filter_instance();

	if ( empty( $filter->filtered_query_vars ) ) {
		return [];
	}

	return $filter->filtered_query_vars;

}

/**
 * Get unfiltered query variables
 *
 * @since 1.0.0
 *
 * @return array
 */
function wpgb_get_unfiltered_query_vars() {

	$filter = wpgb_get_filter_instance();

	if ( empty( $filter->unfiltered_query_vars ) ) {
		return [];
	}

	return $filter->unfiltered_query_vars;

}

/**
 * Get current object type (query type)
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_queried_object_type() {

	$filter = wpgb_get_filter_instance();

	if ( empty( $filter->object_type ) ) {
		return 'post';
	}

	return $filter->object_type;

}

/**
 * Get total number of objects from filtered query
 *
 * @since 1.1.5 Change function name
 * @since 1.0.0
 *
 * @return integer
 */
function wpgb_get_found_objects() {

	$query_vars  = wpgb_get_filtered_query_vars();
	$object_type = wpgb_get_queried_object_type();

	switch ( $object_type ) {
		case 'term':
			$objects = Helpers::get_term_ids( $query_vars, '' );
			break;
		case 'user':
			$objects = Helpers::get_user_ids( $query_vars, -1 );
			break;
		default:
			$objects = Helpers::get_post_ids( $query_vars, -1 );
	}

	$found_objects = count( $objects );

	return $found_objects;

}

/**
 * Get queried object ids in grid/template
 *
 * @since 1.1.5
 *
 * @return array Holds all object ids
 */
function wpgb_get_queried_object_ids() {

	$query_vars  = wpgb_get_filtered_query_vars();
	$object_type = wpgb_get_queried_object_type();

	switch ( $object_type ) {
		case 'term':
			$objects = Helpers::get_term_ids( $query_vars, $query_vars['number'] );
			break;
		case 'user':
			$objects = Helpers::get_user_ids( $query_vars, $query_vars['number'] );
			break;
		default:
			$objects = Helpers::get_post_ids( $query_vars, $query_vars['number'] );
	}

	return $objects;

}

/**
 * Build facet orderby clause
 *
 * @since 1.1.6
 *
 * @param  array $facet Holds facet settings.
 * @return string SQL ORDER BY clause.
 */
function wpgb_get_orderby_clause( $facet = [] ) {

	$orderby = 'count DESC, facet_name ASC';
	$allowed = [
		'count'       => true,
		'facet_name'  => true,
		'facet_value' => true,
		'facet_order' => true,
	];

	if (
		isset( $facet['order'], $facet['orderby'] ) &&
		isset( $allowed[ $facet['orderby'] ] )
	) {

		$direction = 'ASC' === $facet['order'] ? 'ASC' : 'DESC';

		// Make sure we get a logical order if duplicate count.
		$orderby  = $facet['orderby'] . ' ' . $direction;
		$orderby .= 'count' === $facet['orderby'] ? ', facet_name ASC' : '';

	}

	return apply_filters( 'wp_grid_builder/facet/orderby', $orderby, $facet );

}

/**
 * Build where clause
 *
 * @since 1.0.0
 *
 * @param array $facet Holds facet settings.
 * @return string SQL filtered where clause.
 */
function wpgb_get_where_clause( $facet = [] ) {

	$is_filtered = wpgb_has_selected_facets();
	$facet_logic = ! empty( $facet['logic'] ) ? $facet['logic'] : 'OR';

	if ( $is_filtered ) {
		return wpgb_get_filtered_where_clause( $facet, $facet_logic );
	}

	return wpgb_get_unfiltered_where_clause();

}

/**
 * Build filtered where clause
 *
 * @since 1.0.0
 *
 * @param array  $facet Holds facet settings.
 * @param string $logic Facet logic operator.
 * @return string SQL filtered where clause.
 */
function wpgb_get_filtered_where_clause( $facet = [], $logic = 'AND' ) {

	// If no selected facets.
	if ( ! wpgb_has_selected_facets() ) {
		return wpgb_get_unfiltered_where_clause();
	}

	// If at least one facet selected and AND logic operator.
	// If current facet not selected there isn't any intersection to match.
	if ( 'AND' === $logic || empty( $facet['selected'] ) ) {
		return ' object_id IN (' . wpgb_get_facets_instance()->implode_filtered_object_ids() . ')';
	}

	$object_ids = wpgb_get_filter_instance()->object_ids;
	// Remove current facet ids from intersection.
	unset( $object_ids[ $facet['slug'] ] );

	// If no intersection possible (no facet to match).
	if ( ! $object_ids ) {
		return wpgb_get_unfiltered_where_clause();
	}

	// Get intersection from selected facet object ids and unfiltered object ids.
	array_push( $object_ids, wpgb_get_unfiltered_object_ids() );
	$object_ids = call_user_func_array( 'array_intersect', array_values( $object_ids ) ) ?: [ 0 ];

	// $object_ids are integers and safe to use for IN clause.
	return ' object_id IN (' . implode( ',', $object_ids ) . ')';

}

/**
 * Get unfiltered where clause
 *
 * @since 1.0.0
 *
 * @return string Sql Unfiltered where clause.
 */
function wpgb_get_unfiltered_where_clause() {

	return ' object_id IN (' . wpgb_get_facets_instance()->implode_unfiltered_object_ids() . ')';

}

/**
 * Get filtered object ids
 *
 * @since 1.0.0
 *
 * @return array Filtered object ids.
 */
function wpgb_get_filtered_object_ids() {

	return wpgb_get_facets_instance()->get_filtered_object_ids();

}

/**
 * Get unfiltered object ids
 *
 * @since 1.0.0
 *
 * @return array Unfiltered object ids.
 */
function wpgb_get_unfiltered_object_ids() {

	return wpgb_get_facets_instance()->get_unfiltered_object_ids();

}

/**
 * Get facet ids from slugs
 *
 * @since 1.5.0
 *
 * @param array $slugs Holds facet slugs.
 * @return array Holds Facet ids.
 */
function wpgb_get_facet_ids( $slugs = [] ) {

	global $wpdb;

	if ( empty( $slugs ) ) {
		return [];
	}

	$holder = rtrim( str_repeat( '%s,', count( (array) $slugs ) ), ',' );

	return $wpdb->get_col(
		$wpdb->prepare(
			"SELECT id FROM {$wpdb->prefix}wpgb_facets
			WHERE slug IN ($holder)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			(array) $slugs
		)
	);

}

/**
 * Get facet id from slug
 *
 * @since 1.5.0
 *
 * @param string $slug Facet slug.
 * @return integer Facet id.
 */
function wpgb_get_facet_id( $slug = '' ) {

	if ( empty( $slug ) ) {
		return 0;
	}

	return current( wpgb_get_facet_ids( (array) $slug ) );

}

/**
 * Get facet instances
 *
 * @since 1.0.0
 *
 * @param array $ids Holds facet ids to query.
 * @return array Facets.
 */
function wpgb_get_facet_instances( $ids = [] ) {

	// Holds instantiated facets.
	static $facets = [];

	// Get queried facet from id and slug.
	$facet_ids = array_column( $facets, 'id' );
	$facet_ids = array_filter( array_diff( (array) $ids, (array) $facet_ids ) );
	$requested = array_fill_keys( (array) $ids, 1 );

	if ( empty( $facet_ids ) ) {
		return array_intersect_key( $facets, $requested );
	}

	// Query facet not already instantiated.
	$results = Database::query_results(
		[
			'select'  => 'id, slug, type, source, settings',
			'from'    => 'facets',
			'orderby' => 'type DESC',
			'id'      => $facet_ids,
		]
	);

	if ( empty( $results ) ) {
		return [];
	}

	$facets = wpgb_normalize_facets( $results, $facets );

	// Order by facet's weight.
	uasort(
		$facets,
		function( $a, $b ) {

			// Selection facet must be rendered at last to correctly lookup in other facet choices.
			if ( 'selection' === $a['type'] ) {
				return 1;
			}

			if ( $a['weight'] === $b['weight'] ) {
				return 0;
			}

			return $a['weight'] < $b['weight'] ? 1 : -1;

		}
	);

	// Only return requested facet ids.
	return array_intersect_key( $facets, $requested );

}

/**
 * Normalize facet settings
 *
 * @since 1.0.0
 *
 * @param  array $results Holds queried facets.
 * @param  array $facets  Holds facets.
 * @return array Facets.
 */
function wpgb_normalize_facets( $results, $facets ) {

	$registered = apply_filters( 'wp_grid_builder/facets', [] );
	$defaults   = require WPGB_PATH . 'admin/settings/defaults/facet.php';

	foreach ( $results as $facet ) {

		// If facet not registered.
		if ( empty( $registered[ $facet['type'] ]['class'] ) ) {
			continue;
		}

		// If facet class does not exist.
		if ( ! class_exists( $registered[ $facet['type'] ]['class'] ) ) {
			continue;
		}

		// We cast the facet ID.
		$facet['id'] = (int) $facet['id'];

		// Normalize settings with defaults.
		$settings   = json_decode( $facet['settings'], true );
		$settings   = apply_filters( 'wp_grid_builder/facet/settings', $settings );
		$normalized = wp_parse_args( $settings, $defaults );

		// Remove settings before merge.
		unset( $facet['settings'] );
		unset( $normalized['common'] );

		// We keep minimal settings for ajax response.
		$normalized['settings'] = array_diff_key( $settings, $defaults['common'] );
		// Add selected value.
		$normalized['selected'] = wpgb_get_selected_facet_values( $facet['slug'] );
		// Add facet Class instance.
		$normalized['instance'] = new $registered[ $facet['type'] ]['class']();
		// Add normalized facet settings.
		$facets[ $facet['id'] ] = array_merge( $normalized, $facet );
		$facets[ $facet['id'] ]['weight'] = apply_filters( 'wp_grid_builder/facet/weight', 0, $facets[ $facet['id'] ] );

	}

	return $facets;

}

/**
 * Get permalink for pagination
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_pagenum_link() {

	$settings = wpgb_get_facets_instance()->settings;

	if ( empty( $settings['permalink'] ) ) {
		return '';
	}

	return $settings['permalink'];

}

/**
 * Get indexable postmeta keys from facets.
 *
 * @since 1.4.0
 *
 * @return array
 */
function wpgb_get_indexable_postmeta_keys() {

	static $meta_keys;

	if ( isset( $meta_keys ) ) {
		return $meta_keys;
	}

	$meta_keys = [];
	$facets    = Database::query_results(
		[
			'select' => 'source',
			'from'   => 'facets',
		]
	);

	foreach ( $facets as $facet ) {

		$keys = explode( '/', $facet['source'] );
		$acf  = explode( '/acf/', $facet['source'] );

		if ( 'post_meta' !== array_shift( $keys ) ) {
			continue;
		}

		if ( ! empty( $acf[1] ) && function_exists( 'get_field_object' ) ) {

			$fields = array_map(
				function( $field ) use ( $meta_keys ) {

					$field = get_field_object( $field );
					return ! empty( $field['name'] ) ? $field['name'] : '';

				},
				explode( '/', $acf[1] )
			);

			// To ensure consistency with field names that may have numbers surrounded by underscores (_1234_).
			$meta_keys[] = implode( '_x_', $fields );

		} else {
			$meta_keys[] = implode( '', $keys );
		}
	}

	return array_unique( array_filter( $meta_keys ) );

}

/**
 * Check if a meta key is indexable
 *
 * @since 1.4.0
 *
 * @param string $key Post meta key name.
 * @return boolean
 */
function wpgb_is_indexable_meta_key( $key = '' ) {

	if ( empty( $key ) ) {
		return false;
	}

	$allowed = wpgb_get_indexable_postmeta_keys();
	// We try to replace repeater index in reverse order to make sure to not override field name.
	$acf_key = strrev( preg_replace( '/_(\d+)_/', '_x_', strrev( $key ) ) );

	return in_array( $key, $allowed, true ) || in_array( $acf_key, $allowed, true );

}

/**
 * Get URL search params
 *
 * @since 1.5.0
 *
 * @return array
 */
function wpgb_get_url_search_params() {

	$params = [];
	$prefix = '_';
	$strlen = strlen( $prefix );

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	foreach ( $_GET as $key => $val ) {

		if ( substr( $key, 0, $strlen ) !== $prefix || ! is_scalar( $val ) ) {
			continue;
		}

		$val = wp_unslash( $val );
		$val = explode( ',', $val );
		$key = substr( $key, $strlen );

		if ( empty( $key ) || empty( $val ) ) {
			continue;
		}

		$params[ $key ] = $val;

	}

	return $params;

}
