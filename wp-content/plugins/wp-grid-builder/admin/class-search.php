<?php
/**
 * Search
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle ajax search requests
 *
 * @class WP_Grid_Builder\Admin\Search
 * @since 1.0.0
 */
final class Search extends Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_search';

	/**
	 * Search users
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function search_users() {

		$users = [];
		$roles = Helpers::get_roles();

		$query = new \WP_User_Query(
			[
				'number'         => 10,
				'orderby'        => 'display_name',
				'order'          => 'ASC',
				'search'         => '*' . trim( $this->get_var( 'search' ) ) . '*',
				'search_columns' => [
					'display_name',
				],
			]
		);

		foreach ( $query->get_results() as $user ) {

			$posts = count_user_posts( $user->ID );
			/* translators: %d: number of user posts */
			$count = sprintf( _n( '%d post', '%d posts', max( 1, $posts ), 'wp-grid-builder' ), (int) $posts );
			$role  = isset( $user->roles[0] ) ? $user->roles[0] : get_option( 'default_role' );
			$role  = isset( $roles[ $role ] ) ? $roles[ $role ] : $role;

			$users[] = [
				'value'   => esc_attr( $user->ID ),
				'option'  => esc_html( $user->display_name ),
				'content' => sprintf(
					'%s<div class="wpgb-author-detail">
						<span class="wpgb-user-name">%s</span>
						<span class="wpgb-user-role">%s, %s</span>
					</div>',
					get_avatar( $user->ID, 46 ),
					esc_html( $user->display_name ),
					esc_html( $role ),
					esc_html( $count )
				),
			];

		}

		$this->send_response( true, null, Helpers::array_entity_decode( $users ) );

	}

	/**
	 * Search terms (fetch by default the term_id as value)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $key Value to fetch from term.
	 */
	public function search_terms( $key = 'term_id' ) {

		$terms_list = [];
		$post_types = $this->get_var( 'data', false );
		$post_types = explode( ',', $post_types );
		$post_types = array_filter( $post_types );
		$taxonomies = Helpers::get_taxonomies_list( $post_types );

		if ( empty( $taxonomies ) ) {
			$this->send_response();
		}

		add_filter( 'get_terms_orderby', [ $this, 'order_terms_by_relevance' ], 10, 3 );

		$query = new \WP_Term_Query(
			[
				'lang'       => $this->get_var( 'lang', false ),
				'taxonomy'   => array_keys( $taxonomies ),
				'number'     => 10,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => false,
				'fields'     => 'all',
				'name__like' => trim( $this->get_var( 'search' ) ),
			]
		);

		remove_filter( 'get_terms_orderby', [ $this, 'order_terms_by_relevance' ], 10 );

		if ( empty( $query->terms ) ) {
			$this->send_response();
		}

		foreach ( $query->terms as $term ) {

			$label = $taxonomies[ $term->taxonomy ];

			$terms_list[ $label ][] = [
				'value'   => esc_attr( $term->{$key} ),
				'option'  => esc_html(
					sprintf(
						/* translators: %s: term name, %d: number of posts */
						__( '%1$s (%2$d)', 'wp-grid-builder' ),
						$term->name,
						$term->count
					)
				),
				'content' => esc_html(
					sprintf(
						/* translators: %s: term name, %d: number of posts */
						'<span>' . _n( '%1$s <span>(%2$d post)</span>', '%1$s <span>(%2$d posts)</span>', max( 1, $term->count ), 'wp-grid-builder' ) . '</span>',
						$term->name,
						$term->count
					)
				),
			];

		}

		$this->send_response( true, null, Helpers::array_entity_decode( $terms_list ) );

	}

	/**
	 * Order terms search by relevance
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @param string $orderby    ORDERBY clause of the terms query.
	 * @param array  $query_vars An array of term query arguments.
	 * @param array  $taxonomies An array of taxonomy names.
	 * @return string
	 */
	public function order_terms_by_relevance( $orderby, $query_vars, $taxonomies ) {

		global $wpdb;

		$name = $wpdb->esc_like( $query_vars['name__like'] );

		return $wpdb->prepare(
			'CASE
				WHEN t.name LIKE %s THEN 0
				WHEN t.name LIKE %s THEN 1
				WHEN t.name LIKE %s THEN 2
				ELSE 3
			END, CHAR_LENGTH(t.name), t.name',
			$name . '%',
			$name . ' %',
			'% ' . $name . '%'
		);

	}

	/**
	 * Search taxonomy terms (fetch term_taxonomy_id as value)
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function search_taxonomy_terms() {

		$this->search_terms( 'term_taxonomy_id' );

	}

	/**
	 * Search post by title
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function search_posts() {

		add_filter( 'posts_search', [ $this, 'search_by_title' ], 500, 2 );

		$query = new \WP_Query(
			[
				'lang'                   => $this->get_var( 'lang', false ),
				'post_type'              => 'any',
				'posts_per_page'         => 10,
				'orderby'                => 'title',
				'order'                  => 'ASC',
				's'                      => $this->get_var( 'search' ),
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'no_found_rows'          => true,
			]
		);

		remove_filter( 'posts_search', [ $this, 'search_by_title' ], 500 );

		if ( ! $query->have_posts() ) {
			$this->send_response();
		}

		$posts = [];

		while ( $query->have_posts() ) {

			$query->the_post();

			$post_id    = get_the_ID();
			$post_type  = get_post_type();
			$post_type  = get_post_type_object( $post_type );
			$post_type  = $post_type->labels->name;
			$post_title = get_the_title();
			$post_title = $post_title ? $post_title : get_post_field( 'post_name', get_post() );

			$posts[ $post_type ][] = [
				'value'   => esc_attr( $post_id ),
				'option'  => wp_strip_all_tags( $post_title ),
				'content' => wp_kses_post(
					sprintf(
						/* translators: %s: page title, %d: page ID */
						'<span>' . __( '%1$s <span>(ID %2$d)</span>', 'wp-grid-builder' ) . '</span>',
						wp_strip_all_tags( $post_title ),
						$post_id
					)
				),
			];

		}

		wp_reset_postdata();

		$this->send_response( true, null, Helpers::array_entity_decode( $posts ) );

	}

	/**
	 * Filter search to search by title only
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $search String to search.
	 * @param object $wp_query WP query object.
	 */
	public function search_by_title( $search, $wp_query ) {

		global $wpdb;

		$q = $wp_query->query_vars;

		if ( empty( $search ) || empty( $q['s'] ) ) {
			return;
		}

		$search = $wpdb->prepare(
			"{$wpdb->posts}.post_title LIKE %s",
			'%' . $wpdb->esc_like( trim( $q['s'] ) ) . '%'
		);

		return ' AND ' . $search;

	}

	/**
	 * Search cards
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function search_cards() {

		$search = $this->get_var( 'search' );
		$search = remove_accents( $search );
		$search = trim( $search );

		try {

			$results = Database::query_results(
				[
					'select'  => 'id, name',
					'from'    => 'cards',
					'orderby' => 'name ASC',
					's'       => $search,
					'limit'   => 10,
				]
			);

		} catch ( \Exception $e ) {
			$this->send_response( false, $e->getMessage() );
		}

		$array  = [];
		$custom = (array) apply_filters( 'wp_grid_builder/cards', [] );

		array_walk(
			$custom,
			function( &$val, $card ) use ( &$array, &$search ) {

				$name = remove_accents( $val['name'] );

				if ( stripos( $name, $search ) !== false ) {

					$array[] = [
						'id'   => $card,
						'name' => $val['name'],
					];

				}

			}
		);

		$results = array_merge( $results, $array );

		usort(
			$results,
			function( $a, $b ) {
				return strcmp( $a['name'], $b['name'] );
			}
		);

		array_splice( $results, 10 );

		$cards = [];

		array_walk(
			$results,
			function( &$val ) use ( &$cards ) {

				$cards[] = [
					'value'   => esc_attr( $val['id'] ),
					'option'  => wp_kses_decode_entities( $val['name'] ),
					'content' => esc_html( $val['name'] ),
				];

			}
		);

		$this->send_response( true, null, Helpers::array_entity_decode( $cards ) );

	}

	/**
	 * Search facets
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function search_facets() {

		$search = $this->get_var( 'search' );
		$search = remove_accents( $search );
		$search = trim( $search );
		$facets = apply_filters( 'wp_grid_builder/facets', [] );

		try {

			$results = Database::query_results(
				[
					'select'  => 'id, type, name',
					'from'    => 'facets',
					'orderby' => 'name ASC',
					's'       => $search,
					'limit'   => 10,
				]
			);

		} catch ( \Exception $e ) {
			$this->send_response( false, $e->getMessage() );
		}

		$results = array_map(
			function( $item ) use ( $facets ) {

				$facet_type = ! empty( $item['type'] ) ? $item['type'] : 'filter';
				$facet_icon = ! empty( $facets[ $facet_type ]['icons']['small'] );
				$facet_icon = $facet_icon ? $facets[ $facet_type ]['icons']['small'] : Helpers::get_icon( 'filter-action-small', true );
				$facet_icon = '<svg class="wpgb-facet-icon"><use xlink:href="' . esc_url( $facet_icon ) . '"></use></svg>';

				if ( ! isset( $facets[ $facet_type ] ) ) {
					return false;
				}

				return [
					'value'   => esc_attr( $item['id'] ),
					'option'  => wp_kses_decode_entities( $item['name'] ),
					'content' => $facet_icon . esc_html( $item['name'] ),
				];

			},
			$results
		);

		$results = array_values( array_filter( $results ) );
		$this->send_response( true, null, Helpers::array_entity_decode( $results ) );

	}

	/**
	 * Search grids
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function search_grids() {

		$search = $this->get_var( 'search' );
		$search = remove_accents( $search );
		$search = trim( $search );

		try {

			$results = Database::query_results(
				[
					'select'  => 'id, name',
					'from'    => 'grids',
					'orderby' => 'name ASC',
					's'       => $search,
					'limit'   => 10,
				]
			);

		} catch ( \Exception $e ) {
			$this->send_response( false, $e->getMessage() );
		}

		$results = array_map(
			function( $item ) {

				return [
					'value'   => (int) $item['id'],
					'option'  => wp_kses_decode_entities( $item['name'] ),
					'content' => esc_html( $item['name'] ),
				];

			},
			$results
		);

		$this->send_response( true, null, Helpers::array_entity_decode( $results ) );

	}

	/**
	 * Search custom fields
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function search_custom_fields() {

		$search = $this->get_var( 'search' );
		$search = trim( remove_accents( $search ) );
		$fields = $this->search_registered_fields( $search, [] );
		$fields = $this->search_wordpress_fields( $search, $fields );

		$this->send_response( true, null, Helpers::array_entity_decode( $fields ) );

	}

	/**
	 * Search in registered custom fields
	 *
	 * @since 1.1.5
	 * @access public
	 *
	 * @param array $search Searched string.
	 * @param array $fields Holds meta keys.
	 */
	public function search_registered_fields( $search, $fields ) {

		$type  = $this->get_var( 'data', false ) ?: 'name';
		$types = apply_filters( 'wp_grid_builder/custom_fields', [], $type );

		foreach ( $types as $type => $args ) {

			foreach ( $args as $key => $name ) {

				if (
					stripos( $key, $search ) !== false ||
					stripos( $name, $search ) !== false ||
					stripos( $type, $search ) !== false
				) {

					$fields[ $type ][] = [
						'value'   => esc_attr( $key ),
						'content' => esc_html( $name ),
					];

				}
			}
		}

		return $fields;

	}

	/**
	 * Search for native custom fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $search Searched string.
	 * @param array $fields Holds meta keys.
	 */
	public function search_wordpress_fields( $search, $fields ) {

		global $wpdb;

		$meta_keys = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT meta_key FROM {$wpdb->termmeta}
					WHERE meta_key LIKE %s
				UNION
					SELECT DISTINCT meta_key FROM {$wpdb->usermeta}
						WHERE meta_key LIKE %s
				UNION
					SELECT DISTINCT meta_key FROM {$wpdb->postmeta}
						WHERE meta_key LIKE %s
						AND meta_key NOT LIKE %s
						AND meta_key != %s
						AND meta_key != %s
				ORDER BY CHAR_LENGTH(meta_key) ASC, meta_key ASC
				LIMIT 20",
				[
					'%' . $wpdb->esc_like( $search ) . '%',
					'%' . $wpdb->esc_like( $search ) . '%',
					'%' . $wpdb->esc_like( $search ) . '%',
					'%_oembed_%',
					'_edit_last',
					'_edit_lock',
				]
			)
		);

		if ( $wpdb->last_error ) {
			$this->unknown_error();
		}

		if ( empty( $meta_keys ) ) {
			return $fields;
		}

		$label = esc_html__( 'WordPress Custom Fields', 'wp-grid-builder' ) ?: 'WordPress Custom Fields';

		foreach ( $meta_keys as $meta_key ) {

			$fields[ $label ][] = [
				'value'   => esc_attr( $meta_key ),
				'content' => esc_html( $meta_key ),
			];

		}

		return $fields;

	}
}
