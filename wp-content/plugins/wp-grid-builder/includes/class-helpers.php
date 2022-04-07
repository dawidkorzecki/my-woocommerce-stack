<?php
/**
 * Helpers
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helpers methods
 *
 * @class WP_Grid_Builder\Includes\Helpers
 * @since 1.0.0
 */
class Helpers {

	/**
	 * Convert php.ini number notation (e.g.: '2M') to an integer
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $size Size value.
	 * @return integer
	 */
	public static function let_to_num( $size ) {

		$let = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );

		switch ( strtoupper( $let ) ) {
			case 'P':
				$ret *= 1024;
				// no break.
			case 'T':
				$ret *= 1024;
				// no break.
			case 'G':
				$ret *= 1024;
				// no break.
			case 'M':
				$ret *= 1024;
				// no break.
			case 'K':
				$ret *= 1024;
		}

		return $ret;

	}

	/**
	 * Shorten long numbers (K/M/B)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $n Number to shorten.
	 * @param integer $precision Number precison.
	 * @return integer
	 */
	public static function shorten_number_format( $n, $precision = 1 ) {

		if ( $n < 1000 ) {

			$shorten  = '';
			$n_format = $n;

		} elseif ( $n >= 1000 && $n <= 999999 ) {

			$shorten  = 'k';
			$n_format = $n / 1000;

		} elseif ( $n <= 1000000000 ) {

			$shorten  = 'M';
			$n_format = $n / 1000000;

		} else {

			$shorten  = 'B';
			$n_format = $n / 1000000000;

		}

		$whole = floor( $n_format );
		$float = (int) $n_format - (int) $whole > 0 ? str_replace( '0.', '', $n_format - $whole ) : '';
		$float = isset( $float[0] ) && $float[0] > 0 ? '.' . $float[0] : '';

		return (int) $n_format . $float . $shorten;

	}

	/**
	 * Handles recursively HTML entity decoding multi-dimensional array values.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $array Array of data which should be decoded.
	 * @return array Array with decoded values.
	 */
	public static function array_entity_decode( $array ) {

		$data = [];

		foreach ( (array) $array as $key => $value ) {

			// Recursively process array.
			if ( is_array( $value ) ) {
				$data[ $key ] = self::array_entity_decode( $value );
			}

			// Non-array, non-scalar values should not be added.
			if ( ! is_scalar( $value ) ) {
				continue;
			}

			// Pass strings (no scalar) through html_entity_decode.
			if ( is_string( $value ) ) {
				$data[ $key ] = html_entity_decode( $value, ENT_QUOTES, 'UTF-8' );
			} else {
				$data[ $key ] = $value;
			}
		}

		return $data;

	}

	/**
	 * Test if given object is a JSON string or not.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  mixed $object Given object.
	 * @return bool
	 */
	public static function is_json( $object ) {

		return is_string( $object )
			&& is_array( json_decode( $object, true ) )
			&& json_last_error() === JSON_ERROR_NONE;

	}

	/**
	 * Maybe JSON decode string.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  mixed $string The json string being decoded.
	 * @param  bool  $assoc  When TRUE, returned objects will be converted into associative arrays.
	 * @return string
	 */
	public static function maybe_json_decode( $string, $assoc = false ) {

		if ( is_array( $string ) ) {

			return array_map(
				function( $val ) {
					return self::maybe_json_decode( $val );
				},
				$string
			);

		}

		return self::is_json( $string ) ? json_decode( $string, $assoc ) : $string;

	}

	/**
	 * Maybe JSON encode string.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  mixed $obj Content being JSON encoded.
	 * @return mixed
	 */
	public static function maybe_json_encode( $obj ) {

		if ( ! is_array( $obj ) && ! is_object( $obj ) ) {
			return $obj;
		}

		return array_map(
			function( $item ) {

				if ( is_array( $item ) || is_object( $item ) ) {
					return wp_json_encode( $item );
				}

				return $item;

			},
			$obj
		);

	}

	/**
	 * Sanitize multiple HTML classes in one pass.
	 *
	 * @since 1.0.4 Preg_split string by whitespaces.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  mixed $classes Classes to be sanitized.
	 * @return string Sanitized class names.
	 */
	public static function sanitize_html_classes( $classes = '' ) {

		if ( empty( $classes ) ) {
			return '';
		}

		if ( ! is_array( $classes ) ) {
			$classes = preg_split( '/\s+/', $classes );
		}

		$classes = array_map( 'sanitize_html_class', (array) $classes );
		$classes = implode( ' ', $classes );
		$classes = preg_replace( '!\s+!', ' ', $classes );
		$classes = trim( $classes );

		return $classes;

	}

	/**
	 * Get debug mode state
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public static function get_debug_mode() {

		return defined( 'WP_DEBUG' ) && WP_DEBUG;

	}

	/**
	 * Get PHP memory limit
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_memory_limit() {

		// Set default limit in case.
		$memory_limit = '128M';

		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		}

		// If unlimited memory.
		if ( ! $memory_limit || -1 === (int) $memory_limit ) {
			return '&infin;';
		}

		$memory_limit = self::let_to_num( $memory_limit );
		$memory_limit = size_format( $memory_limit );

		return $memory_limit;

	}

	/**
	 * Get PHP memory usage
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_memory_usage() {

		$memory_usage = __( 'unknown', 'wp-grid-builder' );

		if ( function_exists( 'memory_get_usage' ) ) {

			$memory_usage = memory_get_usage();
			$memory_usage = size_format( $memory_usage );

		}

		return $memory_usage;

	}

	/**
	 * Get WordPress max upload size
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_max_upload_size() {

		$max_upload_size = wp_max_upload_size();
		$max_upload_size = size_format( $max_upload_size );

		return $max_upload_size;

	}

	/**
	 * Get PHP software
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_server_software() {

		$server_software = __( 'unknown', 'wp-grid-builder' );

		if ( ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
			$server_software = wp_unslash( $_SERVER['SERVER_SOFTWARE'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		return $server_software;

	}

	/**
	 * Get PHP post max size
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_post_max_size() {

		$post_max_size = __( 'unknown', 'wp-grid-builder' );

		if ( function_exists( 'ini_get' ) ) {

			$post_max_size = ini_get( 'post_max_size' );
			$post_max_size = self::let_to_num( $post_max_size );
			$post_max_size = size_format( $post_max_size );

		}

		return $post_max_size;

	}

	/**
	 * Get PHP max execution time
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return number
	 */
	public static function get_max_execution_time() {

		$max_execution_time = __( 'unknown', 'wp-grid-builder' );

		if ( function_exists( 'ini_get' ) ) {
			$max_execution_time = ini_get( 'max_execution_time' );
		}

		return $max_execution_time;

	}

	/**
	 * Get PHP max input vars
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return number
	 */
	public static function get_max_input_vars() {

		$max_input_vars = 1000;

		if ( version_compare( PHP_VERSION, '5.3.9', '>=' ) && function_exists( 'ini_get' ) ) {
			$max_input_vars = ini_get( 'max_input_vars' );
		}

		return $max_input_vars;

	}

	/**
	 * Get activated plugins
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return number
	 */
	public static function get_active_plugins() {

		$active_plugins = (array) get_option( 'active_plugins' );

		if ( is_multisite() ) {

			$network_plugins = (array) get_site_option( 'active_sitewide_plugins' );
			$network_plugins = array_keys( $network_plugins );
			$active_plugins  = array_merge( $active_plugins, $network_plugins );

		}

		return $active_plugins;

	}

	/**
	 * Get roles
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function get_roles() {

		$roles = wp_roles();
		$roles = $roles->get_names();
		$roles = array_map( 'translate_user_role', $roles );

		return $roles;

	}

	/**
	 * Get users from ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $ids IDs of authors/users.
	 * @return array
	 */
	public static function get_users( $ids = '' ) {

		$users = [];

		if ( ! $ids || ! is_array( $ids ) ) {
			return $users;
		}

		$query = new \WP_User_Query(
			[
				'include'     => $ids,
				'number'      => count( $ids ),
				'orderby'     => 'include',
				'fields'      => [ 'ID', 'display_name' ],
				'count_total' => false,
			]
		);

		$query = $query->get_results();

		foreach ( $query as $user ) {
			$users[ $user->ID ] = esc_html( $user->display_name );
		}

		return $users;

	}

	/**
	 * Query user ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $query_vars Holds query arguments.
	 * @param integer $number Number of users to query.
	 * @return array Holds user ids.
	 */
	public static function get_user_ids( $query_vars, $number ) {

		$query_vars = array_merge(
			$query_vars,
			[
				'number'      => $number,
				'fields'      => 'ID',
				'count_total' => false,
			]
		);

		return (array) ( new \WP_User_Query( $query_vars ) )->results;

	}

	/**
	 * Get user capability
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_user_capability() {

		return apply_filters( 'wp_grid_builder/user_capability', 'manage_options' );

	}

	/**
	 * Check user capability
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public static function current_user_can() {

		$capability = self::get_user_capability();

		return current_user_can( $capability );

	}

	/**
	 * Get post types
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function get_post_types() {

		global $wp_post_types;

		$post_types = [];

		if ( empty( $wp_post_types ) ) {
			return $post_types;
		}

		foreach ( $wp_post_types as $post_type ) {

			if ( $post_type->public ) {
				$post_types[ $post_type->name ] = ucfirst( $post_type->label );
			}
		}

		return $post_types;

	}

	/**
	 * Get posts from ids
	 *
	 * @since 1.1.8 Added fallback to default post ID if missing from pll_get_post.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $ids Post IDs.
	 * @return array
	 */
	public static function get_posts( $ids ) {

		$posts = [];

		if ( empty( $ids ) || ! is_array( $ids ) ) {
			return $posts;
		}

		if ( function_exists( 'pll_get_post' ) ) {

			foreach ( $ids as $index => $id ) {
				$ids[ $index ] = pll_get_post( $id ) ?: $id;
			}
		}

		$query = new \WP_Query(
			[
				'post_type'              => 'any',
				'post__in'               => $ids,
				'posts_per_page'         => count( $ids ),
				'orderby'                => 'post__in',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'no_found_rows'          => true,
			]
		);

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {

				$query->the_post();

				$post_title = get_the_title();

				if ( empty( $post_title ) ) {
					$post_title = get_post_field( 'post_name' );
				}

				$posts[ get_the_ID() ] = wp_strip_all_tags( $post_title );

			}

			wp_reset_postdata();

		}

		return $posts;

	}

	/**
	 * Get post ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $query_vars Holds query arguments.
	 * @param integer $number Number of posts to query.
	 * @return array Holds post ids.
	 */
	public static function get_post_ids( $query_vars, $number ) {

		$query_vars = array_merge(
			$query_vars,
			[
				'paged'                  => 1,
				'posts_per_page'         => $number,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'cache_results'          => false,
				'no_found_rows'          => true,
				'fields'                 => 'ids',
			]
		);

		$post_ids = (array) ( new \WP_Query( $query_vars ) )->posts;

		wp_reset_postdata();

		return $post_ids;

	}

	/**
	 * Get post status
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function get_post_status() {

		global $wp_post_statuses;

		$post_status = [
			'any' => __( 'Any', 'wp-grid-builder' ),
		];

		if ( ! empty( $wp_post_statuses ) ) {

			foreach ( $wp_post_statuses as $status ) {
				$post_status[ $status->name ] = ucfirst( $status->label );
			}
		}

		return $post_status;

	}

	/**
	 * Get Taxonomies
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $post_types Holds post types.
	 * @return array
	 */
	public static function get_taxonomies( $post_types = [] ) {

		global $wp_taxonomies;

		$taxonomies = [];

		foreach ( (array) $wp_taxonomies as $taxonomy => $args ) {

			$matched = true;

			if ( ! empty( $post_types ) && is_array( $post_types ) ) {
				$matched = array_intersect( $args->object_type, $post_types );
			}

			if (
				empty( $matched ) ||
				(
					// Exception for WooCommerce taxonomy.
					'product_visibility' !== $taxonomy &&
					! $args->publicly_queryable &&
					! $args->show_tagcloud &&
					! $args->show_ui &&
					! $args->public
				)
			) {
				continue;
			}

			// Fallback to taxonomy name if empty label.
			$taxonomies[ $taxonomy ] = ucfirst( $args->label ?: $taxonomy );

		}

		return $taxonomies;

	}

	/**
	 * Get Taxonomies list
	 *
	 * @since 1.4.2
	 * @access public
	 *
	 * @param array $post_types Holds post types.
	 * @return array
	 */
	public static function get_taxonomies_list( $post_types = [] ) {

		$taxonomies = self::get_taxonomies( $post_types );
		$duplicates = array_diff_key( $taxonomies, array_unique( $taxonomies ) );

		foreach ( $duplicates as $taxonomy => $label ) {
			$taxonomies[ $taxonomy ] .= ' (' . $taxonomy . ')';
		}

		return $taxonomies;

	}

	/**
	 * Get terms from term ids attached to post types.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $ids Term IDs.
	 * @param  array $post_types Post type(s) to search in taxonomy terms.
	 * @return array
	 */
	public static function get_terms( $ids, $post_types = [ 'post' ] ) {

		$terms = [];

		if ( empty( $ids ) || ! is_array( $ids ) ) {
			return $terms;
		}

		$ids = array_map(
			function ( $id ) {

				if ( function_exists( 'pll_get_term' ) ) {
					$id = pll_get_term( $id );
				}

				return $id;

			},
			$ids
		);

		$taxonomies = self::get_taxonomies( $post_types );
		$taxonomies = array_keys( $taxonomies );

		if ( empty( $taxonomies ) ) {
			return $terms;
		}

		$query = get_terms(
			[
				'taxonomy'               => $taxonomies,
				'include'                => $ids,
				'number'                 => count( $ids ),
				'orderby'                => 'include',
				'hide_empty'             => false,
				'update_term_meta_cache' => false,
			]
		);

		if ( is_wp_error( $query ) || empty( $query ) ) {
			return $terms;
		}

		foreach ( $query as $term ) {

			$terms[ $term->term_id ] = esc_html(
				sprintf(
					/* translators: %s: term name, %d: number of posts */
					__( '%1$s (%2$d)', 'wp-grid-builder' ),
					$term->name,
					$term->count
				)
			);

		}

		return $terms;

	}

	/**
	 * Get terms from term taxonomy ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $ids Term taxonomy ids.
	 * @return array
	 */
	public static function get_taxonomy_terms( $ids ) {

		$terms = [];

		if ( empty( $ids ) || ! is_array( $ids ) ) {
			return $terms;
		}

		$query = new \WP_Term_Query(
			[
				'number'                 => count( $ids ),
				'term_taxonomy_id'       => $ids,
				'orderby'                => 'name',
				'order'                  => 'ASC',
				'hide_empty'             => false,
				'update_term_meta_cache' => false,
			]
		);

		if ( is_wp_error( $query ) || empty( $query ) ) {
			return $terms;
		}

		foreach ( (array) $query->terms as $term ) {

			$terms[ $term->term_taxonomy_id ] = esc_html(
				sprintf(
					/* translators: %s: term name, %d: number of posts */
					__( '%1$s (%2$d)', 'wp-grid-builder' ),
					$term->name,
					$term->count
				)
			);

		}

		return $terms;

	}

	/**
	 * Query term ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $query_vars Holds query arguments.
	 * @param integer $number Number of terms to query.
	 * @return array Holds term ids.
	 */
	public static function get_term_ids( $query_vars, $number ) {

		$query_vars = array_merge(
			$query_vars,
			[
				'number' => $number,
				'fields' => 'ids',
			]
		);

		return (array) ( new \WP_Term_Query( $query_vars ) )->terms;

	}

	/**
	 * Get facets from ids.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $ids IDs of facets.
	 * @return array
	 */
	public static function get_facets( $ids ) {

		$facets  = [];
		$results = Database::query_results(
			[
				'select' => 'id, name',
				'from'   => 'facets',
				'ids'    => (array) $ids,
			]
		);

		foreach ( (array) $results as $facet ) {
			$facets[ $facet['id'] ] = $facet['name'];
		}

		return $facets;

	}

	/**
	 * Delete facet from index table.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $slug Facet slug.
	 */
	public static function delete_index( $slug ) {

		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s",
				$slug
			)
		);

	}

	/**
	 * Get indexable/filterable facets.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $ids IDs of facets.
	 * @return array
	 */
	public static function get_indexable_facets( $ids = -1 ) {

		$facet_ids = -1 === (int) $ids ? '' : (array) $ids;
		$defaults  = require WPGB_PATH . 'admin/settings/defaults/facet.php';

		$facets = Database::query_results(
			[
				'select' => 'id, slug, type, source, settings',
				'from'   => 'facets',
				'id'     => $facet_ids,
			]
		);

		$facets = array_filter(
			(array) $facets,
			function( $facet ) {

				$can_filter = 'selection' !== $facet['type'] && 'search' !== $facet['type'];
				return ! empty( $facet['source'] ) && $can_filter;

			}
		);

		return array_map(
			function( $facet ) use ( $defaults ) {

				// We cast the facet ID.
				$facet['id'] = (int) $facet['id'];

				$settings = json_decode( $facet['settings'], true );
				$settings = wp_parse_args( $settings, $defaults );

				// Remove settings before merge.
				unset( $facet['settings'] );
				// Add facet normalized settings.
				$settings = array_merge( $settings, $facet );

				return $settings;

			},
			$facets
		);

	}

	/**
	 * Get list of image sizes
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function get_image_sizes() {

		$sizes = [ 'full', 'thumbnail', 'medium', 'medium_large', 'large' ];
		$sizes = array_combine( $sizes, $sizes );
		$sizes = array_merge( $sizes, wp_get_additional_image_sizes() );

		foreach ( $sizes as $key => $args ) {

			if ( ! isset( $args['width'], $args['height'] ) ) {

				$args = [
					'width'  => get_option( $args . '_size_w' ),
					'height' => get_option( $args . '_size_h' ),
				];

			}

			unset( $args['crop'] );
			$size = array_filter( $args );
			$size = join( ' x ', $size );

			$sizes[ $key ] = $key . ( $size ? ' (' . $size . ')' : '' );

		}

		return $sizes;

	}

	/**
	 * Get SVG icon markup (<use>)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string  $name Icon name.
	 * @param  boolean $link Return url only or not.
	 * @param  boolean $echo Echo or Return markup.
	 * @return string
	 */
	public static function get_icon( $name = null, $link = false, $echo = true ) {

		global $is_IE;

		if ( ! $name ) {
			return;
		}

		$icon = 'wpgb-' . $name . '-icon';

		if ( $is_IE ) {
			$url = '#' . $icon;
		} else {
			$url = WPGB_URL . 'admin/assets/svg/sprite.svg?v=' . WPGB_VERSION . '#' . $icon;
		}

		if ( $link ) {
			return esc_url( $url );
		}

		if ( ! $echo ) {
			ob_start();
		}

		echo '<svg class="' . sanitize_html_class( $icon ) . '">';
			echo '<use xlink:href="' . esc_url( $url ) . '"></use>';
		echo '</svg>';

		if ( ! $echo ) {
			return ob_get_clean();
		}
	}

	/**
	 * Get plugin page
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_plugin_page() {

		global $plugin_page;

		$page = $plugin_page;

		if ( wp_doing_ajax() ) {

			$path = wp_parse_url( wp_get_referer() );

			if ( isset( $path['query'] ) ) {
				wp_parse_str( $path['query'], $output );
			}

			if ( ! isset( $output['page'] ) ) {
				return '';
			}

			$page = $output['page'];

		}

		if ( strpos( $page, 'wpgb-' ) !== 0 ) {
			return '';
		}

		return str_replace( 'wpgb-', '', $page );

	}

	/**
	 * Get file contents
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string $file File to read.
	 * @return string
	 */
	public static function file_get_contents( $file ) {

		// To make sur we get content from plugin file.
		$file = wp_normalize_path( WPGB_PATH . $file );

		if ( ! file_exists( $file ) ) {
			return false;
		}

		// Some shared hosting disable file access.
		if ( function_exists( 'ini_get' ) && ini_get( 'allow_url_fopen' ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			return file_get_contents( $file );
		}

		// Fallback to include.
		// This helper is only used with .json from the plugin folder.
		// There is nothing to execute from these files.
		ob_start();
		require $file;
		return ob_get_clean();

	}

	/**
	 * Get Google Fonts
	 *
	 * @since 1.0.0
	 */
	public static function get_google_fonts() {

		$fonts_json = self::file_get_contents( 'admin/assets/json/google-fonts.json' );
		return json_decode( $fonts_json, true );

	}

	/**
	 * Delete transient
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $name Transient partial name.
	 */
	public static function delete_transient( $name = '' ) {

		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options}
				WHERE option_name LIKE %s
				OR option_name LIKE %s",
				$wpdb->esc_like( '_site_transient_wpgb_' . $name ) . '%',
				$wpdb->esc_like( '_transient_wpgb_' . $name ) . '%'
			)
		);

	}

	/**
	 * Get template part
	 *
	 * @since 1.2.1 Support of require_once
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string  $template      Template name.
	 * @param mixed   $wpgb_template Var to pass in the template.
	 * @param boolean $require_once  Whether to require_once or require.
	 */
	public static function get_template( $template = '', $wpgb_template = '', $require_once = false ) {

		if ( empty( $template ) ) {
			return;
		}

		$folder     = 'wp-grid-builder';
		$template   = '/templates/' . ltrim( $template . '.php', '/' );
		$child_dir  = trailingslashit( get_stylesheet_directory() );
		$parent_dir = trailingslashit( get_template_directory() );
		$plugin_dir = trailingslashit( WPGB_PATH . 'frontend/' );

		if ( file_exists( $child_dir . $folder . $template ) ) {
			// Child theme.
			$located = $child_dir . $folder . $template;
		} elseif ( file_exists( $parent_dir . $folder . $template ) ) {
			// Parent theme.
			$located = $parent_dir . $folder . $template;
		} else {
			// Native Plugin template.
			$located = $plugin_dir . $template;
		}

		$located = wp_normalize_path( $located );

		if ( ! is_file( $located ) ) {
			return;
		}

		if ( $require_once ) {
			require_once $located;
		} else {
			require $located;
		}
	}

	/**
	 * Return list of oembed providers
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function get_embed_providers() {

		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned, WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow
		return [
			'#https?://?(?:www\.|m\.)?youtube\.com/?(?:watch\?v=|embed/)?([\w\-_]+)+#i'                            => 'youtube',
			'#https?://youtu\.be/?([\w\-_]+)+#i'                                                                   => 'youtube',
			'#https?://?player.vimeo\.com/video/?([\w\-_]+)+#i'                                                    => 'vimeo',
			'#https?://?(?:www\.)?vimeo\.com/?([\w\-_]+)+#i'                                                       => 'vimeo',
			'#https?://?(?:.+)?(?:wistia\.com|wistia\.net|wi\.st)/?(?:embed/)?(?:iframe|playlists)/?([\w\-_]+)+#i' => 'wistia',
		];
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned, WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow

	}

	/**
	 * Retrieve oembed data.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $provider  Provider type (youtube, vimeo, wistia).
	 * @param string $video_id  Video id to retrieve oembed data.
	 * @return object
	 */
	public static function get_oembed_data( $provider, $video_id ) {

		$providers = [
			'youtube' => 'https://www.youtube.com/watch?v=%s',
			'vimeo'   => 'https://vimeo.com/%d',
			'wistia'  => 'https://fast.wistia.com/embed/iframe/%s',
		];

		if ( ! isset( $providers[ $provider ] ) ) {
			return;
		}

		if ( ! class_exists( 'WP_oEmbed' ) ) {
			include ABSPATH . WPINC . '/class-oembed.php';
		}

		$url   = sprintf( $providers[ $provider ], $video_id );
		$embed = _wp_oembed_get_object();
		$embed = $embed->get_data( $url );

		if ( empty( $embed ) ) {
			return;
		}

		return $embed;

	}

	/**
	 * Sanitize facet value (for query string value in URL)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $str String to sanitize.
	 * @return string Sanitized string.
	 */
	public static function sanitize_facet_value( $str ) {

		if ( is_numeric( $str ) && ! is_int( $str ) ) {
			return (float) $str + 0;
		}

		$str = remove_accents( $str );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
		$str = strip_tags( $str );
		// Convert nbsp, ndash, mdash and its entities to hyphens.
		$str = str_replace( [ '%c2%a0', '%e2%80%93', '%e2%80%94' ], '-', $str );
		$str = str_replace( [ '&nbsp;', '&#160;', '&ndash;', '&#8211;', '&mdash;', '&#8212;' ], '-', $str );
		// kill entities.
		$str = preg_replace( '/&.+?;/', '', $str );
		$str = preg_replace( '/\s+/', '-', $str );
		$str = preg_replace( '|-+|', '-', $str );
		$str = str_replace( [ ',', '.' ], '-', $str );
		$str = strtolower( $str );

		// Facet_value column accept 191 chars
		// Url is also limited in length (2,083 chars).
		if ( 80 < strlen( $str ) ) {
			$str = md5( $str );
		}

		return $str;

	}

	/**
	 * SVG definition for allowed HTML tags when escaping
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function allowed_svg_tags() {

		return [
			'svg'     => [
				'id'          => true,
				'class'       => true,
				'viewbox'     => true,
				'x'           => true,
				'y'           => true,
				'xmlns'       => true,
				'xmlns:xlink' => true,
			],
			'use'     => [
				'id'          => true,
				'class'       => true,
				'x'           => true,
				'y'           => true,
				'transform'   => true,
				'xmlns:xlink' => true,
				'xlink:href'  => true,
			],
			'symbol'  => [
				'id'       => true,
				'class'    => true,
				'viewbox'  => true,
				'overflow' => true,
				'x'        => true,
				'y'        => true,
			],
			'g'       => [
				'id'        => true,
				'class'     => true,
				'style'     => true,
				'clip-path' => true,
				'clip-rule' => true,
				'transform' => true,
			],
			'path'    => [
				'id'        => true,
				'class'     => true,
				'd'         => true,
				'fill'      => true,
				'style'     => true,
				'path'      => true,
				'transform' => true,
			],
			'rect'    => [
				'id'        => true,
				'class'     => true,
				'x'         => true,
				'y'         => true,
				'width'     => true,
				'height'    => true,
				'transform' => true,
			],
			'polygon' => [
				'id'        => true,
				'class'     => true,
				'points'    => true,
				'transform' => true,
			],
		];

	}

	/**
	 * Split string into words (to use in SQL LIKE clauses)
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @param string $string Holds string to split.
	 * @return array Splitted terms from string
	 */
	public static function split_into_words( $string = '' ) {

		$terms = [];

		if ( ! preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $string, $matches ) ) {
			return [ $string ];
		}

		foreach ( $matches[0] as $term ) {

			// We remove all types of quote to prevent missing match.
			$term = str_replace( [ "'", '"', '＂', '‘', '’', '`' ], ' ', $term );
			$term = trim( $term );

			if ( empty( $term ) ) {
				continue;
			}

			$terms = array_merge( $terms, preg_split( '/\s+/', $term ) );

		}

		return $terms ?: [ $string ];

	}
}
