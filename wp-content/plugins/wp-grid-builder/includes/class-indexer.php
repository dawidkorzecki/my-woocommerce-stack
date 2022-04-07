<?php
/**
 * Indexer
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes;

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Cron\Process;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle taxonomy terms index
 *
 * @class WP_Grid_Builder\Includes\Indexer
 * @since 1.0.0
 */
final class Indexer extends Process {

	/**
	 * Post parent state to check wp_insert_post
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var boolean
	 */
	public $saving_post = false;

	/**
	 * Heartbeat tick
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @var boolean
	 */
	public $heartbeat_tick = false;

	/**
	 * Meta keys to exclude from indexer
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @var boolean
	 */
	public $excluded_keys = [
		'_encloseme',
		'_edit_lock',
		'_edit_last',
		'_wp_page_template',
		'_wp_trash_meta_status',
		'_wp_trash_meta_time',
		'_wp_desired_post_slug',
	];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct();

		// Post actions.
		add_action( 'save_post', [ $this, 'save_post' ] );
		add_action( 'delete_post', [ $this, 'delete_post' ] );
		add_filter( 'wp_insert_post_parent', [ $this, 'insert_post' ], 10, 4 );

		// Index post meta.
		add_action( 'heartbeat_tick', [ $this, 'heartbeat_tick' ] );
		add_action( 'updated_post_meta', [ $this, 'updated_post_meta' ], 10, 4 );
		add_action( 'deleted_post_meta', [ $this, 'updated_post_meta' ], 10, 4 );

		// Attachment actions.
		add_action( 'edit_attachment', [ $this, 'edit_attachment' ] );
		add_action( 'add_attachment', [ $this, 'add_attachment' ] );

		// User actions.
		add_action( 'profile_update', [ $this, 'save_user' ], 10, 1 );
		add_action( 'user_register', [ $this, 'save_user' ], 10, 1 );
		add_action( 'delete_user', [ $this, 'delete_user' ] );

		// Term actions.
		add_action( 'edited_term', [ $this, 'edit_term' ], 10, 3 );
		add_action( 'delete_term', [ $this, 'delete_term' ], 10, 4 );
		add_action( 'set_object_terms', [ $this, 'set_object_terms' ] );

	}

	/**
	 * Handle post update
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id Holds post id.
	 */
	public function save_post( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( false !== wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'auto-draft' === get_post_status( $post_id ) ) {
			return;
		}

		$this->index_object_id( $post_id, 'post' );
		$this->saving_post = false;

	}

	/**
	 * Handle post deletion
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id Holds post id.
	 */
	public function delete_post( $post_id ) {

		global $wpdb;

		// Query post facets.
		$facets = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT slug FROM {$wpdb->prefix}wpgb_facets
				WHERE source LIKE %s
				OR source LIKE %s
				OR source LIKE %s",
				'%' . $wpdb->esc_like( 'post_field/' ) . '%',
				'%' . $wpdb->esc_like( 'post_meta/' ) . '%',
				'%' . $wpdb->esc_like( 'taxonomy/' ) . '%'
			)
		);

		if ( empty( $facets ) ) {
			return;
		}

		$placeholders = rtrim( str_repeat( '%s,', count( $facets ) ), ',' );

		$query = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}wpgb_index
				WHERE object_id = %d
				AND slug IN ($placeholders)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				array_merge( [ $post_id ], $facets )
			)
		);

	}

	/**
	 * Prevent set_object_terms() to index wp_insert_post.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int   $post_parent Post parent ID.
	 * @param int   $post_id     Post ID.
	 * @param array $new_postarr Array of parsed post data.
	 * @param array $postarr     Array of sanitized, but otherwise unmodified post data.
	 */
	public function insert_post( $post_parent, $post_id, $new_postarr, $postarr ) {

		$this->saving_post = true;
		return $post_parent;

	}

	/**
	 * Fires when Heartbeat ticks in logged-in environments.
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function heartbeat_tick() {

		$this->heartbeat_tick = true;

	}

	/**
	 * Handle post meta update
	 *
	 * @since 1.4.0
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $object_id  Post ID.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value.
	 */
	public function updated_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {

		if ( $this->saving_post || $this->heartbeat_tick ) {
			return;
		}

		if ( in_array( $meta_key, $this->excluded_keys, true ) || ! wpgb_is_indexable_meta_key( $meta_key ) ) {
			return;
		}

		$this->index_object_id( $object_id, 'post' );

	}

	/**
	 * Unset $saving_post state when updating or saving attachment.
	 * wp_insert_post action is never reached in these cases.
	 * Otherwise set_object_terms will never index terms from attachments.
	 *
	 * @since 1.1.8
	 * @access public
	 *
	 * @param int $post_id Holds post id.
	 */
	public function edit_attachment( $post_id ) {

		$this->saving_post = false;

	}

	/**
	 * Index newly uploaded attachment
	 *
	 * @since 1.1.9
	 * @access public
	 *
	 * @param int $post_id Holds post id.
	 */
	public function add_attachment( $post_id ) {

		$this->index_object_id( $post_id, 'post' );

	}

	/**
	 * Handle user register/update
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $user_id User ID.
	 */
	public function save_user( $user_id ) {

		$this->index_object_id( $user_id, 'user' );

	}

	/**
	 * Handle user deletion
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $user_id User ID.
	 */
	public function delete_user( $user_id ) {

		global $wpdb;

		// Query facets.
		$facets = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT slug FROM {$wpdb->prefix}wpgb_facets
				WHERE source LIKE %s
				OR source LIKE %s",
				'%' . $wpdb->esc_like( 'user_field/' ) . '%',
				'%' . $wpdb->esc_like( 'user_meta/' ) . '%'
			)
		);

		if ( empty( $facets ) ) {
			return;
		}

		$placeholders = rtrim( str_repeat( '%s,', count( $facets ) ), ',' );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}wpgb_index
				WHERE object_id = %d
				AND slug IN ($placeholders)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				array_merge( [ $user_id ], $facets )
			)
		);

	}

	/**
	 * Handle term changes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int    $term_id  Term id.
	 * @param int    $tt_id    Term taxonomy  id.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function edit_term( $term_id, $tt_id, $taxonomy ) {

		global $wpdb;

		// For term object type.
		$this->index_object_id( $term_id, 'term' );

		// Query facets.
		$facets = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT slug FROM {$wpdb->prefix}wpgb_facets
				WHERE source = %s",
				"taxonomy/$taxonomy"
			)
		);

		if ( empty( $facets ) ) {
			return;
		}

		$placeholders = rtrim( str_repeat( '%s,', count( $facets ) ), ',' );
		$term = get_term( $term_id, $taxonomy );
		$slug = sanitize_title( $term->slug );

		// For post types.
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}wpgb_index
				SET facet_value = %s, facet_name = %s
				WHERE facet_id = %d
				AND slug IN ($placeholders)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				array_merge( [ $slug, $term->name, $term_id ], $facets )
			)
		);

	}

	/**
	 * Handle term deletion
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int    $term_id  Term id.
	 * @param int    $tt_id    Term taxonomy id.
	 * @param string $taxonomy Taxonomy slug.
	 * @param mixed  $deleted_term Copy of the already-deleted term, in the form specified by the parent function.
	 */
	public function delete_term( $term_id, $tt_id, $taxonomy, $deleted_term ) {

		global $wpdb;

		// Query post facets.
		$post_facets = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT slug FROM {$wpdb->prefix}wpgb_facets
				WHERE source = %s",
				"taxonomy/$taxonomy"
			)
		);

		if ( ! empty( $post_facets ) ) {

			$placeholders = rtrim( str_repeat( '%s,', count( $post_facets ) ), ',' );

			// For post types.
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}wpgb_index
					WHERE facet_id = %d
					AND slug IN ($placeholders)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					array_merge( [ $term_id ], $post_facets )
				)
			);
		}

		// Query term facets.
		$term_facets = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT slug FROM {$wpdb->prefix}wpgb_facets
				WHERE source LIKE %s
				OR source LIKE %s",
				'%' . $wpdb->esc_like( 'term_field/' ) . '%',
				'%' . $wpdb->esc_like( 'term_meta/' ) . '%'
			)
		);

		if ( ! empty( $term_facets ) ) {

			$placeholders = rtrim( str_repeat( '%s,', count( $term_facets ) ), ',' );

			// For terms.
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}wpgb_index
					WHERE object_id = %d
					AND slug IN ($placeholders)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					array_merge( [ $term_id ], $term_facets )
				)
			);
		}

	}

	/**
	 * Support for manual taxonomy associations
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $object_id Term id.
	 */
	public function set_object_terms( $object_id ) {

		if ( $this->saving_post ) {
			return;
		}

		$this->index_object_id( $object_id, 'post' );

	}

	/**
	 * Index object ids for all facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed  $object_id Holds post ids to index.
	 * @param string $type      Object type (post/user/term).
	 */
	public function index_object_id( $object_id = '', $type = '' ) {

		global $wpdb;

		if ( empty( $object_id ) ) {
			return;
		}

		$facets = Helpers::get_indexable_facets();

		if ( empty( $facets ) ) {
			return;
		}

		foreach ( $facets as $facet ) {

			$object_type = $this->get_object_type( $facet );

			if ( $type !== $object_type ) {
				continue;
			}

			// Delete object_id rows from current facet.
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}wpgb_index
					WHERE object_id = %d
					AND slug = %s",
					$object_id,
					$facet['slug']
				)
			);

			$this->process_objects( (array) $object_id, $facet );

		}
	}

	/**
	 * Index array of facets for all posts
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $facet_ids Holds facet ids to index.
	 */
	public function index_facets( $facet_ids = '' ) {

		$state  = false;
		$facets = Helpers::get_indexable_facets( $facet_ids );

		if ( empty( $facets ) ) {
			return false;
		}

		foreach ( $facets as $facet ) {
			$state = $this->add_to_queue( $facet['id'], $facet );
		}

		$this->dispatch();

		return $state;

	}

	/**
	 * Run task to index
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $key   Facet key in queue (cron task).
	 */
	protected function task( $facet, $key ) {

		if ( $this->queue->is_canceled( $key ) ) {
			return true;
		}

		if ( isset( $facet['objects'] ) ) {
			$object_ids = $facet['objects'];
		} else {

			$object_ids = $this->query_objects( $facet );
			Helpers::delete_index( $facet['slug'] );

		}

		if ( empty( $object_ids ) ) {
			return true;
		}

		return $this->process_objects( (array) $object_ids, $facet, $key );

	}

	/**
	 * Process object ids to index
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $object_ids Holds Object ids to index.
	 * @param array $facet      Holds facet settings.
	 * @param array $key        Facet key in queue (cron task).
	 */
	public function process_objects( $object_ids, $facet, $key = '' ) {

		global $wpdb;

		$counter = 0;
		$offset  = 0;
		$length  = count( $object_ids );
		$offset  = isset( $facet['offset'] ) ? $facet['offset'] : 0;

		$facet['objects'] = $object_ids;

		if ( $offset ) {
			$object_ids = $this->offset_objects( $object_ids, $facet );
		} elseif ( $key ) {
			$this->set_progress( 0, true );
		}

		foreach ( $object_ids as $index => $object_id ) {

			// At each 10 indexed object ids set progress.
			if ( $key && 0 === $index % 10 ) {

				// Set progress value.
				$progress = ( $offset + $index ) / $length * 100;
				$this->set_progress( $progress );

			}

			// If canceled.
			if ( $key && $this->queue->is_canceled( $key ) ) {

				$this->set_progress( 0, true );
				return true;

			}

			// If we reach limit while indexing.
			if ( $key && ( $this->time_exceeded() || $this->memory_exceeded() ) ) {

				$facet['offset'] = $offset + $index;
				$facet['length'] = $length;

				$this->queue->update_item( $key, $facet );
				return false;

			}

			// To index content from 3rd party plugins (ACF, WOO, EDD, etc...).
			// If not empty, it allows to bypass the default index (get_rows()).
			$rows = apply_filters( 'wp_grid_builder/indexer/index_object', [], $object_id, $facet );

			// Index if no custom rows set.
			if ( empty( $rows ) ) {
				$rows = $this->get_rows( $object_id, $facet );
			}

			$rows = $this->set_upper_values( $rows, $object_id, $facet );

			foreach ( $rows as $row ) {

				$row = apply_filters( 'wp_grid_builder/indexer/row', $row, $object_id, $facet );
				$row = $this->normalize_row( $row, $object_id, $facet );
				$this->insert_row( $row );

			}
		}

		if ( $key ) {
			$this->set_progress( 100, true );
		}

		do_action( 'wp_grid_builder/indexer/facet_indexed', $facet );

		return true;

	}

	/**
	 * Offset object ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $object_ids Holds Object ids to index.
	 * @param array $facet      Holds facet settings.
	 */
	public function offset_objects( $object_ids, $facet ) {

		global $wpdb;

		// We start from the previous object id to be sure we do not miss some data to index.
		$object_ids = array_slice( $object_ids, max( 0, $facet['offset'] - 1 ) );

		// Remove previously indexed object id.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				AND object_id = %d",
				$facet['slug'],
				$object_ids[0]
			)
		);

		return $object_ids;

	}

	/**
	 * Set facet index progression
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $progress Progression value.
	 * @param boolean $instant  Instantly set progression.
	 */
	public function set_progress( $progress, $instant = false ) {

		set_site_transient( 'wpgb_cron_progress', (float) $progress, 60 );

	}

	/**
	 * On indexer complete
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function complete() {

		parent::complete();

		$this->set_progress( 0, true );

	}

	/**
	 * Get facet object type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 */
	public function get_object_type( $facet ) {

		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		switch ( $source ) {
			case 'user_meta':
			case 'user_field':
				return 'user';
			case 'term_meta':
			case 'term_field':
				return 'term';
			default:
				return 'post';
		}
	}

	/**
	 * Query object ids to index.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 */
	public function query_objects( $facet ) {

		$type   = $this->get_object_type( $facet );
		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		switch ( $type ) {
			case 'user':
				return $this->query_users( $facet );
			case 'term':
				return $this->query_terms( $facet );
			default:
				return $this->query_posts( $facet, $source );
		}
	}

	/**
	 * Query post ids to index.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $facet  Holds facet settings.
	 * @param string $source Facet source type.
	 * @return array of post ids
	 */
	public function query_posts( $facet, $source ) {

		global $wp_taxonomies;

		$post_types = 'any';

		if ( 'taxonomy' === $source && isset( $wp_taxonomies[ $facet['taxonomy'] ] ) ) {

			$taxonomy   = $wp_taxonomies[ $facet['taxonomy'] ];
			$post_types = $taxonomy->object_type;

		}

		$query_args = [
			'post_type'        => $post_types,
			'post_status'      => 'any',
			'posts_per_page'   => -1,
			'fields'           => 'ids',
			'orderby'          => 'ID',
			'cache_results'    => false,
			'no_found_rows'    => true,
			'suppress_filters' => true,
			'lang'             => '',
		];

		$query_args = apply_filters( 'wp_grid_builder/indexer/query_args', $query_args, 'post', $facet );

		$posts = (array) ( new \WP_Query( $query_args ) )->posts;

		wp_reset_postdata();

		return $posts;

	}

	/**
	 * Query user ids to index.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array of user ids
	 */
	public function query_users( $facet ) {

		$query_args = [
			'number'  => -1,
			'fields'  => 'ID',
			'orderby' => 'ID',
		];

		$query_args = apply_filters( 'wp_grid_builder/indexer/query_args', $query_args, 'user', $facet );

		return (array) ( new \WP_User_Query( $query_args ) )->results;

	}

	/**
	 * Query term ids to index.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array of term ids
	 */
	public function query_terms( $facet ) {

		$query_args = [
			'number'     => '',
			'fields'     => 'ids',
			'orderby'    => 'id',
			'hide_empty' => false,
			'lang'       => '',
		];

		$query_args = apply_filters( 'wp_grid_builder/indexer/query_args', $query_args, 'term', $facet );

		return (array) ( new \WP_Term_Query( $query_args ) )->terms;

	}

	/**
	 * Get data for a table row
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Holds facet settings.
	 */
	public function get_rows( $object_id, $facet ) {

		$rows   = [];
		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		switch ( $source ) {
			case 'taxonomy':
				$rows = $this->index_taxonomy_terms( $object_id, $facet );
				break;
			case 'post_field':
				$rows = $this->index_post_field( $object_id, $facet );
				break;
			case 'user_field':
				$rows = $this->index_user_field( $object_id, $facet );
				break;
			case 'term_field':
				$rows = $this->index_term_field( $object_id, $facet );
				break;
			case 'post_meta':
			case 'user_meta':
			case 'term_meta':
				$rows = $this->index_metadata( $object_id, $facet );
				break;
		}

		return $rows;

	}

	/**
	 * Get and set upper values
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array   $rows     Holds lower row to index.
	 * @param integer $object_id Object id.
	 * @param array   $facet     Holds facet settings.
	 */
	public function set_upper_values( $rows, $object_id, $facet ) {

		if ( empty( $rows ) || empty( $facet['meta_key_upper'] ) ) {
			return $rows;
		}

		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		if ( ! in_array( $source, [ 'post_meta', 'term_meta', 'user_meta' ], true ) ) {
			return $rows;
		}

		$upper_facet = array_merge(
			$facet,
			[
				'source'   => $source . '/' . $facet['meta_key_upper'],
				'meta_key' => $facet['meta_key_upper'],
			]
		);

		$upper_rows = apply_filters( 'wp_grid_builder/indexer/index_object', [], $object_id, $upper_facet );

		if ( empty( $upper_rows ) ) {
			$upper_rows = $this->get_rows( $object_id, $upper_facet );
		}

		foreach ( $rows as $key => $row ) {

			if ( ! empty( $upper_rows[ $key ] ) ) {
				$rows[ $key ]['facet_name'] = $upper_rows[ $key ]['facet_value'];
			}
		}

		return $rows;

	}

	/**
	 * Index taxonomy terms
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Holds facet settings.
	 */
	public function index_taxonomy_terms( $object_id, $facet ) {

		$added  = [];
		$output = [];

		$query_args = apply_filters(
			'wp_grid_builder/indexer/term_query_args',
			[
				'object_ids' => $object_id,
				'taxonomy'   => $facet['taxonomy'],
				'include'    => array_map( 'intval', (array) $facet['include'] ),
				'exclude'    => array_map( 'intval', (array) $facet['exclude'] ),
				'parent'     => $facet['parent'] ? (int) $facet['parent'] : '',
				'lang'       => '',
			],
			$object_id,
			$facet
		);

		$terms = (array) ( new \WP_Term_Query( $query_args ) )->terms;

		foreach ( $terms as $term ) {

			// Prevent duplicate terms.
			if ( isset( $added[ $term->term_id ] ) ) {
				continue;
			}

			// Do not index parent term.
			if ( $term->term_id === $query_args['parent'] ) {
				continue;
			}

			// Set parent id to root parent if children of parent.
			if ( $term->parent === $query_args['parent'] ) {
				$term->parent = 0;
			}

			// Set parent id to root parent if included term without included parent.
			if (
				in_array( $term->term_id, $query_args['include'], true ) &&
				! in_array( $term->parent, $query_args['include'], true )
			) {
				$term->parent = 0;
			}

			$added[ $term->term_id ] = true;

			$output[] = [
				'facet_value'  => $term->slug,
				'facet_name'   => $term->name,
				'facet_id'     => $term->term_id,
				'facet_parent' => $term->parent,
				'facet_order'  => $term->term_order,
			];

			$parent_terms = $this->get_parent_terms( $term, $query_args, $facet );

			// Index child parents to count all childs attached to a parent.
			foreach ( $parent_terms as $parent_term ) {

				if ( isset( $added[ $parent_term->term_id ] ) ) {
					continue;
				}

				$added[ $parent_term->term_id ] = true;

				$output[] = [
					'facet_value'  => $parent_term->slug,
					'facet_name'   => $parent_term->name,
					'facet_id'     => $parent_term->term_id,
					'facet_parent' => $parent_term->parent,
					'facet_order'  => $parent_term->term_order,
				];

			}
		}

		return $output;

	}

	/**
	 * Get parent terms.
	 *
	 * @since 1.0.1 Prevent hierarchical list for asynchronous select facet.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $term       Child term.
	 * @param array  $query_args Holds WP_Term_Query arguments.
	 * @param array  $facet      Holds facet settings.
	 */
	public function get_parent_terms( $term, $query_args, $facet ) {

		// if no parent continue.
		if ( ! $term->parent ) {
			return [];
		}

		// If parent terms but not hierarchical type.
		if ( ! $facet['hierarchical'] && 'hierarchy' !== $facet['type'] ) {
			return [];
		}

		// If asynchronous select facet, prevent hierarchical list.
		if ( 'select' === $facet['type'] && ! empty( $facet['searchable'] ) && ! empty( $facet['async'] ) ) {
			return [];
		}

		$ancestors = get_ancestors( $term->term_id, $query_args['taxonomy'] );

		// Take into account included/excluded terms from facet settings.
		if ( ! empty( $query_args['exclude'] ) ) {
			$ancestors = array_diff( $ancestors, $query_args['exclude'] );
		} elseif ( ! empty( $query_args['include'] ) ) {
			$ancestors = array_intersect( $ancestors, $query_args['include'] );
		}

		if ( empty( $ancestors ) ) {
			return [];
		}

		$parent_terms = get_terms(
			[
				'taxonomy'   => $query_args['taxonomy'],
				'include'    => $ancestors,
				'hide_empty' => false,
			]
		);

		if ( is_wp_error( $parent_terms ) ) {
			return [];
		}

		return $parent_terms;

	}

	/**
	 * Index post field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Holds facet settings.
	 */
	public function index_post_field( $object_id, $facet ) {

		$post = get_post( $object_id );

		if ( ! isset( $post->{$facet['post_field']} ) ) {
			return [];
		}

		$value = $post->{$facet['post_field']};
		$name  = $value;

		if ( 'post_author' === $facet['post_field'] ) {

			$name = '';
			$user = get_userdata( $value );

			if ( isset( $user->display_name ) ) {
				$name = $user->display_name;
			}
		} elseif ( 'post_type' === $facet['post_field'] ) {

			$name = '';
			$type = get_post_type_object( $value );

			if ( isset( $type->labels->name ) ) {
				$name = $type->labels->name;
			}
		}

		return [
			[
				'facet_value' => $value,
				'facet_name'  => $name,
			],
		];

	}

	/**
	 * Index user field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Holds facet settings.
	 */
	public function index_user_field( $object_id, $facet ) {

		$rows = [];
		$user = get_user_by( 'id', $object_id );

		if ( ! isset( $user->{$facet['user_field']} ) ) {
			return $rows;
		}

		$value = $user->{$facet['user_field']};
		$name  = $value;

		if ( 'roles' === $facet['user_field'] ) {

			$wp_roles = Helpers::get_roles();

			foreach ( (array) $value as $role ) {

				if ( ! isset( $wp_roles[ $role ] ) ) {
					continue;
				}

				$rows[] = [
					'facet_value' => $role,
					'facet_name'  => $wp_roles[ $role ],
				];

			}

			return $rows;

		} else {

			$rows[] = [
				'facet_value' => $value,
				'facet_name'  => $name,
			];

		}

		return $rows;

	}

	/**
	 * Index term field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Holds facet settings.
	 */
	public function index_term_field( $object_id, $facet ) {

		$term = get_term_by( 'term_taxonomy_id', $object_id );

		if ( ! isset( $term->{$facet['term_field']} ) ) {
			return [];
		}

		$value = $term->{$facet['term_field']};
		$name  = $value;

		if ( 'taxonomy' === $facet['term_field'] ) {

			$taxonomies = Helpers::get_taxonomies();
			$name = isset( $taxonomies[ $value ] ) ? $taxonomies[ $value ] : $value;

		}

		return [
			[
				'facet_value' => $value,
				'facet_name'  => $name,
			],
		];

	}

	/**
	 * Index metadata (post, user, term)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $object_id Object id.
	 * @param array   $facet     Holds facet settings.
	 */
	public function index_metadata( $object_id, $facet ) {

		$output = [];
		$values = get_metadata( $facet['field_type'], $object_id, $facet['meta_key'] );

		foreach ( (array) $values as $value ) {

			$output[] = [
				'facet_value' => $value,
				'facet_name'  => $value,
			];

		}

		return $output;

	}

	/**
	 * Normalize row column values
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $columns   Holds row columns.
	 * @param integer $object_id Object id to index.
	 * @param array   $facet     Holds facet settings.
	 */
	public function normalize_row( $columns, $object_id, $facet ) {

		return wp_parse_args(
			$columns,
			[
				'object_id'    => $object_id,
				'slug'         => $facet['slug'],
				'facet_value'  => '',
				'facet_name'   => '',
				'facet_id'     => 0,
				'facet_parent' => 0,
				'facet_order'  => 0,
			]
		);

	}

	/**
	 * Save a facet value to DB
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $columns Holds columns to insert into row.
	 */
	public function insert_row( $columns ) {

		global $wpdb;

		if ( ! is_array( $columns ) ) {
			return;
		}

		// Only accept scalar values.
		if ( '' === $columns['facet_value'] || ! is_scalar( $columns['facet_value'] ) ) {
			return;
		}

		$columns['facet_value'] = Helpers::sanitize_facet_value( $columns['facet_value'] );

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}wpgb_index
				(object_id, slug, facet_value, facet_name, facet_id, facet_parent, facet_order)
				VALUES (%d, %s, %s, %s, %d, %d, %d)",
				$columns['object_id'],
				$columns['slug'],
				$columns['facet_value'],
				$columns['facet_name'],
				$columns['facet_id'],
				$columns['facet_parent'],
				$columns['facet_order']
			)
		);

	}
}
