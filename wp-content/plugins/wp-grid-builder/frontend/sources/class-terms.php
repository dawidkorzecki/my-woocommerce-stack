<?php
/**
 * Query Terms
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Sources;

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Term class.
 *
 * @class WP_Grid_Builder\FrontEnd\Sources\Term
 * @since 1.0.0
 */
class Terms {

	/**
	 * Holds grid settings
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Holds queried terms
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $posts = [];

	/**
	 * Holds post object
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var object
	 */
	public $post = [];

	/**
	 * Holds term object
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var object
	 */
	protected $term = [];

	/**
	 * Holds term parents id and name
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var object
	 */
	protected $parents = [];

	/**
	 * WP_Query args
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $query_args;

	/**
	 * WP_Query
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var object
	 */
	private $query;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds grid settings.
	 */
	public function __construct( $settings ) {

		$this->settings   = $settings;
		$this->attachment = new Attachment( $settings );

	}

	/**
	 * Parse terms query args
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function parse_query() {

		$this->build_query();
		$this->run_query();

	}

	/**
	 * Retrieves terms based on query variables
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array Queried objects
	 */
	public function get_results() {

		if ( empty( $this->query->get_terms() ) ) {
			return [];
		}

		$this->do_loop();
		$this->get_attachments();

		return apply_filters( 'wp_grid_builder/grid/the_objects', $this->posts );

	}

	/**
	 * Get attachment
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_attachments() {

		if ( empty( $this->attachment->ids ) ) {
			return;
		}

		$this->attachment->query( $this->posts );

	}

	/**
	 * Build custom query
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function build_query() {

		$this->set_number();
		$this->set_offset();
		$this->set_orderby();
		$this->set_taxonomy();
		$this->set_hide_empty();
		$this->set_childless();
		$this->set_term__in();
		$this->set_term__not_in();
		$this->set_meta_key();
		$this->set_meta_query();

	}

	/**
	 * Run custom query
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function run_query() {

		// Add language to prevent issue when querying asynchronously.
		$this->query_args['lang'] = $this->settings->lang;
		// Returns an array of term objects with the 'object_id' param.
		$this->query_args['fields'] = 'all_with_object_id';
		// Add WP Grid Builder to query args.
		$this->query_args['wp_grid_builder'] = $this->settings->id;
		// Filter the query args.
		$this->query_args = apply_filters( 'wp_grid_builder/grid/query_args', $this->query_args, $this->settings->id );

		// Run the query.
		$this->query = new \WP_Term_Query( $this->query_args );

	}

	/**
	 * Set number parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_number() {

		$number = $this->settings->posts_per_page;

		if ( $number < 0 ) {
			$number = 0;
		} elseif ( empty( $number ) ) {
			$number = get_option( 'posts_per_page', 10 );
		}

		$this->query_args['number'] = (int) $number;

	}

	/**
	 * Set offset parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_offset() {

		$this->query_args['offset'] = (int) $this->settings->offset;

	}

	/**
	 * Set order and orderby parameters
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_orderby() {

		$this->query_args['order'] = $this->settings->order;

		if ( empty( $this->settings->term_orderby ) ) {
			return;
		}

		$this->query_args['orderby'] = implode( ' ', (array) $this->settings->term_orderby );

	}

	/**
	 * Set taxonomy parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_taxonomy() {

		$taxonomy = (array) $this->settings->taxonomy;

		if ( empty( $taxonomy ) ) {

			$taxonomy = Helpers::get_taxonomies();
			$taxonomy = array_keys( $taxonomy );

		}

		$this->query_args['taxonomy'] = $taxonomy;

	}

	/**
	 * Set hide_empty parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_hide_empty() {

		$this->query_args['hide_empty'] = (bool) $this->settings->hide_empty;

	}

	/**
	 * Set childless parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_childless() {

		$this->query_args['childless'] = (bool) $this->settings->childless;

	}

	/**
	 * Set include parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_term__in() {

		if ( empty( $this->settings->term__in ) ) {
			return;
		}

		$this->query_args['include'] = (array) $this->settings->term__in;

	}

	/**
	 * Set exclude parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_term__not_in() {

		if ( empty( $this->settings->term__not_in ) ) {
			return;
		}

		$this->query_args['exclude'] = (array) $this->settings->term__not_in;

	}

	/**
	 * Set meta_key parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_meta_key() {

		if ( empty( $this->query_args['orderby'] ) ) {
			return;
		}

		if (
			strpos( $this->query_args['orderby'], 'meta_value' ) === false &&
			strpos( $this->query_args['orderby'], 'meta_value_num' ) === false
		) {
			return;
		}

		$this->query_args['meta_key'] = $this->settings->meta_key;

	}

	/**
	 * Set meta_query parameter
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $meta_query Holds meta query args.
	 */
	public function set_meta_query( $meta_query = false ) {

		if ( empty( $this->settings->meta_query ) ) {
			return;
		}

		$this->query_args['meta_query'] = $this->process_meta_query( (array) $this->settings->meta_query );

	}

	/**
	 * Process meta_query arguments
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $meta_query Holds meta query args.
	 */
	public function process_meta_query( $meta_query ) {

		return array_map(
			function( $clause ) {

				if ( isset( $clause['value'], $clause['type'] ) && in_array( $clause['type'], [ 'DATE', 'DATETIME', 'TIME' ], true ) ) {

					$dates = explode( ' ', $clause['value'] );
					$dates = array_map( 'date', $dates );
					$clause['value'] = implode( ' ', $dates );

				}

				if ( isset( $clause['value'], $clause['compare'] ) && in_array( $clause['compare'], [ 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ], true ) ) {

					$values = explode( ',', $clause['value'] );
					// Set at least 2 values to match wpdb::prepare placeholders.
					$values = array_pad( $values, 2, '' );
					$clause['value'] = $values;

				} elseif ( is_array( $clause ) ) {
					$clause = $this->process_meta_query( $clause );
				}

				return $clause;

			},
			$meta_query
		);

	}

	/**
	 * Custom query loop
	 *
	 * @since 1.2.0 Parse attachment ids after the object is filtered.
	 * @since 1.0.0
	 * @access public
	 */
	public function do_loop() {

		$results = $this->query->get_terms();

		$this->get_terms_parent( $results );

		foreach ( $results as $term ) {

			$this->term = $term;
			$this->post = new \stdClass();

			$this->get_post_data();
			$this->get_term_data();
			$this->get_term_meta();
			$this->get_term_parent();
			$this->get_term_permalink();
			$this->get_term_format();
			$this->get_term_media();

			$this->posts[] = apply_filters( 'wp_grid_builder/grid/the_object', $this->post );
			$this->attachment->parse_attachment_ids( $this->post );

		}

	}

	/**
	 * Get terms parents
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $results Holds term objects.
	 */
	public function get_terms_parent( $results ) {

		$parent_ids = [];

		foreach ( $results as $term ) {
			array_push( $parent_ids, $term->parent );
		}

		$parent_ids = array_filter( $parent_ids );

		if ( empty( $parent_ids ) ) {
			return;
		}

		$query = new \WP_Term_Query(
			[
				'include' => $parent_ids,
				'fields'  => 'id=>name',
			]
		);

		$parents = $query->get_terms();

		if ( ! empty( $parents ) ) {
			$this->parents = $parents;
		}

	}

	/**
	 * Get assimilated post data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_data() {

		$this->post->ID             = $this->term->term_id;
		$this->post->object_type    = 'term';
		$this->post->post_title     = $this->term->name;
		$this->post->post_name      = $this->term->slug;
		$this->post->post_excerpt   = $this->term->description;
		$this->post->post_content   = $this->term->description;
		$this->post->post_date      = '';
		$this->post->post_modified  = '';
		$this->post->post_author    = '';
		$this->post->post_thumbnail = '';

	}

	/**
	 * Get term data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_term_data() {

		$this->post->term_id          = $this->term->term_id;
		$this->post->term_name        = $this->term->name;
		$this->post->term_slug        = $this->term->slug;
		$this->post->term_group       = $this->term->term_group;
		$this->post->term_taxonomy_id = $this->term->term_taxonomy_id;
		$this->post->term_taxonomy    = $this->term->taxonomy;
		$this->post->term_count       = $this->term->count;

	}

	/**
	 * Get meta data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_term_meta() {

		$meta = array_map( 'current', (array) get_term_meta( $this->post->ID ) );
		$meta = array_map( 'maybe_unserialize', $meta );

		if ( isset( $meta[ '_' . WPGB_SLUG ] ) ) {

			$meta[ WPGB_SLUG ] = $meta[ '_' . WPGB_SLUG ];
			unset( $meta[ '_' . WPGB_SLUG ] );

		} else {
			$meta[ WPGB_SLUG ] = [];
		}

		$defaults = require WPGB_PATH . 'admin/settings/defaults/post.php';
		$meta[ WPGB_SLUG ] = wp_parse_args( $meta[ WPGB_SLUG ], $defaults );

		$this->post->metadata = $meta;

	}

	/**
	 * Get term parent
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_term_parent() {

		$parent = $this->term->parent;

		if ( isset( $this->parents[ $parent ] ) ) {
			$parent = $this->parents[ $parent ];
		}

		$this->post->term_parent = $parent;

	}

	/**
	 * Get term permalink
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_term_permalink() {

		$this->post->permalink = $this->post->metadata[ WPGB_SLUG ]['permalink'];

		if ( ! empty( $this->post->permalink ) ) {
			return;
		}

		$term_link = get_term_link( $this->post->ID, $this->term->taxonomy );

		if ( is_wp_error( $term_link ) ) {
			return;
		}

		$this->post->permalink = $term_link;

	}

	/**
	 * Get term format
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_term_format() {

		$post_format = $this->post->metadata[ WPGB_SLUG ]['post_format'];
		$post_format = empty( $post_format ) ? 'standard' : $post_format;

		// The block API also make this check.
		$format_exists = in_array( $post_format, $this->settings->post_formats, true );
		$this->post->post_format = $format_exists ? $post_format : 'standard';

	}

	/**
	 * Get media content according to term format
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_term_media() {

		$source = '';
		$format = $this->post->post_format;

		// Add post_media key to post.
		$this->post->post_media = null;

		// Fetch media from metadata.
		switch ( $format ) {
			case 'gallery':
				$source = $this->get_gallery_format();
				break;
			case 'video':
				$source = $this->get_video_format();
				break;
			case 'audio':
				$source = $this->get_audio_format();
				break;
		}

		// Get thumbnail whatever the post format.
		$this->get_thumbnail();

		if ( 'standard' === $format ) {
			return;
		}

		// If not content found set post to standard format.
		if ( empty( $source ) ) {
			return;
		}

		$source['format'] = $format;
		$this->post->post_media = $source;

	}

	/**
	 * Get thumbnail ID or data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_thumbnail() {

		// Get alternative attachment ID (from term_meta).
		$thumb = (int) $this->post->metadata[ WPGB_SLUG ]['attachment_id'];

		// Get Woocommerce thumbnail id.
		if ( $thumb < 1 && isset( $this->post->metadata['thumbnail_id'] ) ) {
			$thumb = (int) $this->post->metadata['thumbnail_id'];
		}

		if ( empty( $thumb ) ) {
			return;
		}

		$this->post->post_thumbnail = $thumb;

	}

	/**
	 * Get alternative gallery IDs
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_gallery_format() {

		if ( empty( $this->post->metadata[ WPGB_SLUG ]['gallery_ids'] ) ) {
			return;
		}

		return [
			'sources' => $this->post->metadata[ WPGB_SLUG ]['gallery_ids'],
		];

	}

	/**
	 * Get alternative audio content
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_audio_format() {

		$sources = [
			$this->post->metadata[ WPGB_SLUG ]['mp3_url'],
			$this->post->metadata[ WPGB_SLUG ]['ogg_url'],
		];

		$sources = array_filter( $sources );

		if ( empty( $sources ) ) {
			return;
		}

		return [
			'type'    => 'hosted',
			'sources' => $sources,
		];

	}

	/**
	 * Get alternative video content
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_video_format() {

		$sources = [
			$this->post->metadata[ WPGB_SLUG ]['mp4_url'],
			$this->post->metadata[ WPGB_SLUG ]['ogv_url'],
			$this->post->metadata[ WPGB_SLUG ]['webm_url'],
		];

		$sources = array_filter( $sources );

		if ( ! empty( $sources ) ) {

			return [
				'type'    => 'hosted',
				'sources' => $sources,
			];

		}

		$embed = $this->post->metadata[ WPGB_SLUG ]['embed_video_url'];

		if ( empty( $embed ) ) {
			return;
		}

		$providers = Helpers::get_embed_providers();

		foreach ( $providers as $provider => $media ) {

			if ( ! preg_match( $provider, $embed, $match ) ) {
				continue;
			}

			return [
				'type'    => 'embedded',
				'sources' => [
					'provider' => $media,
					'url'      => $match[0],
					'id'       => $match[1],
				],
			];
		}

	}
}
