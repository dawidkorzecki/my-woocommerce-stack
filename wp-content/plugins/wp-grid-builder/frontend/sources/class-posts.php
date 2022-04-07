<?php
/**
 * Query Posts
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Sources;

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\First_Media;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post class.
 *
 * @class WP_Grid_Builder\FrontEnd\Sources\Post
 * @since 1.0.0
 */
class Posts {

	/**
	 * Holds settings instance
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $settings;

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
	 * Holds queried posts
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
	 * Parse posts query args
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function parse_query() {

		if ( $this->settings->is_main_query ) {
			$this->main_query();
		} else {

			$this->build_query();
			$this->run_query();

		}
	}

	/**
	 * Retrieves posts based on query variables
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array Queried objects
	 */
	public function get_results() {

		if ( ! $this->query || ! $this->query->have_posts() ) {
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
	 * Run main WP query
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function main_query() {

		global $wp_query;

		if ( wp_doing_ajax() && ! empty( $this->settings->main_query ) ) {

			// Add language to prevent issue when querying asynchronously.
			$this->settings->main_query['lang'] = $this->settings->lang;
			// Turns off SQL_CALC_FOUND_ROWS even when limits are present.
			$this->settings->main_query['no_found_rows'] = true;
			// Add WP Grid Builder to query args.
			$this->settings->main_query['wp_grid_builder'] = $this->settings->id;

			$this->query = new \WP_Query( $this->settings->main_query );

		} elseif ( is_main_query() && ! is_admin() ) {
			$this->query = $wp_query;
		}

	}

	/**
	 * Build custom query
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function build_query() {

		$this->set_post_type();
		$this->set_post_status();
		$this->set_posts_per_page();
		$this->set_offset();
		$this->set_orderby();
		$this->set_author__in();
		$this->set_post__in();
		$this->set_post__not_in();
		$this->set_attachments();
		$this->set_mime_types();
		$this->set_meta_key();
		$this->set_meta_query();
		$this->set_taxonomies();
		$this->set_tax_query();

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
		// Turns off SQL_CALC_FOUND_ROWS even when limits are present.
		$this->query_args['no_found_rows'] = true;
		// Add WP Grid Builder to query args.
		$this->query_args['wp_grid_builder'] = $this->settings->id;
		// Filter the query args.
		$this->query_args = apply_filters( 'wp_grid_builder/grid/query_args', $this->query_args, $this->settings->id );

		// Run the query.
		$this->query = new \WP_Query( $this->query_args );

	}

	/**
	 * Set post_type parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_post_type() {

		$post_type = $this->settings->post_type;
		$post_type = ! empty( $post_type ) ? $post_type : 'any';

		$this->query_args['post_type'] = (array) $post_type;

	}

	/**
	 * Set post_status parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_post_status() {

		$post_status = $this->settings->post_status;

		if ( empty( $post_status ) ) {

			$post_status = [ 'publish' ];

			if ( is_user_logged_in() ) {
				$post_status[] = 'private';
			}
		}

		$this->query_args['post_status'] = (array) $post_status;

	}

	/**
	 * Set posts_per_page parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_posts_per_page() {

		$this->query_args['posts_per_page'] = (int) $this->settings->posts_per_page;

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

		$this->query_args['order']   = $this->settings->order;
		$this->query_args['orderby'] = implode( ' ', (array) $this->settings->orderby );

	}

	/**
	 * Set author__in parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_author__in() {

		$this->query_args['author__in'] = (array) $this->settings->author__in;

	}

	/**
	 * Set post__in parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_post__in() {

		$this->query_args['post__in'] = (array) $this->settings->post__in;

	}

	/**
	 * Set post__not_in parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_post__not_in() {

		$this->query_args['post__not_in'] = (array) $this->settings->post__not_in;

	}

	/**
	 * Set attachments ids in post__in or post__not_in
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_attachments() {

		global $wpdb;

		$attachment_ids = (array) $this->settings->attachment_ids;
		$attachment_ids = array_filter( $attachment_ids );

		// If no attachment post type selected.
		if ( ! in_array( 'attachment', $this->query_args['post_type'], true ) ) {
			return;
		}

		// Add "inherit" status to retrieve attachments.
		array_push( $this->query_args['post_status'], 'publish' );
		array_push( $this->query_args['post_status'], 'inherit' );
		$this->query_args['post_status'] = array_unique( $this->query_args['post_status'] );

		if ( empty( $attachment_ids ) ) {
			return;
		}

		// Merge post__in if not empty.
		if ( ! empty( $this->query_args['post__in'] ) ) {

			$this->query_args['post__in'] = array_merge( $this->query_args['post__in'], $attachment_ids );
			return;

		}

		// If several post types set.
		if ( count( $this->query_args['post_type'] ) > 1 ) {

			$all_attachment_ids = wp_cache_get( 'wpgb_all_attachment_ids' );

			if ( ! is_array( $all_attachment_ids ) ) {

				$all_attachment_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment'" );
				wp_cache_add( 'wpgb_all_attachment_ids', $all_attachment_ids );

			}

			$post__not_in = array_diff( $all_attachment_ids, $attachment_ids );
			$this->query_args['post__not_in'] = array_merge( $this->query_args['post__not_in'], $post__not_in );

		} else {

			if ( array_search( 'rand', (array) $this->query_args['orderby'], true ) === false ) {
				$this->query_args['orderby'] .= $this->query_args['orderby'] ? ' post__in' : 'post__in';
			}

			$this->query_args['post__in'] = $attachment_ids;

		}

	}


	/**
	 * Set post mime types
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_mime_types() {

		// If no attachment post type selected or media selected.
		if ( ! in_array( 'attachment', $this->query_args['post_type'], true )
			|| ! empty( $this->settings->attachment_ids )
			|| count( $this->query_args['post_type'] ) > 1 ) {
			return;
		}

		$this->query_args['post_mime_type'] = (array) $this->settings->post_mime_type;

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
	 * Set taxonomies from term IDs if children are included in tax_query
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function set_taxonomies() {

		$this->term_taxonomies = [];

		if ( empty( $this->settings->tax_query_children ) || empty( $this->settings->tax_query ) ) {
			return;
		}

		$query = new \WP_Term_Query(
			[
				'term_taxonomy_id' => $this->settings->tax_query,
				'hide_empty'       => false,
			]
		);

		if ( is_wp_error( $query ) || empty( $query ) ) {
			return;
		}

		foreach ( (array) $query->terms as $term ) {
			$this->term_taxonomies[ $term->term_taxonomy_id ] = $term->taxonomy;
		}
	}

	/**
	 * Set tax_query parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_tax_query() {

		$tax_query = $this->settings->tax_query;

		if ( empty( $tax_query ) ) {
			return;
		}

		$this->query_args['tax_query'] = array_map(
			function( $term_id ) {

				$clause = [
					'field'            => 'term_taxonomy_id',
					'terms'            => (array) $term_id,
					'operator'         => $this->settings->tax_query_operator,
					'include_children' => (bool) $this->settings->tax_query_children,
				];

				// We add taxonomy parameter if available.
				if ( ! empty( $this->term_taxonomies[ $term_id ] ) ) {
					$clause['taxonomy'] = $this->term_taxonomies[ $term_id ];
				}

				return $clause;

			},
			(array) $tax_query
		);

		$this->query_args['tax_query']['relation'] = $this->settings->tax_query_relation;

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

		while ( $this->query->have_posts() ) {

			$this->query->the_post();

			$this->get_post();
			$this->get_post_meta();
			$this->get_permalink();
			$this->get_post_terms();
			$this->get_post_format();
			$this->get_post_author();
			$this->get_post_media();
			$this->get_product_data();

			$this->posts[] = apply_filters( 'wp_grid_builder/grid/the_object', $this->post );
			$this->attachment->parse_attachment_ids( $this->post );

		}

		wp_reset_postdata();

	}

	/**
	 * Build item array
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post() {

		global $post;

		$this->post                = clone $post;
		$this->post->object_type   = 'post';
		$this->post->post_sticky   = is_sticky();
		$this->post->post_status   = get_post_status();
		$this->post->post_date     = get_the_date( 'U' );
		$this->post->post_modified = get_the_modified_date( 'U' );
		$this->post->post_title    = get_the_title();
		$this->post->post_content  = get_the_content( null, false, $post->ID );

	}

	/**
	 * Get meta data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_meta() {

		$meta = array_map( 'current', (array) get_post_meta( $this->post->ID ) );
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
	 * Get post permalink
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_permalink() {

		$this->post->permalink = $this->post->metadata[ WPGB_SLUG ]['permalink'];

		if ( empty( $this->post->permalink ) ) {
			$this->post->permalink = get_the_permalink( $this->post->ID );
		}

	}

	/**
	 * Get taxonomy terms
	 *
	 * @since 1.2.0 Exclude language taxonomy (Polylang)
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_terms() {

		$post_terms = [];
		$taxonomies = Helpers::get_taxonomies( (array) $this->post->post_type );

		foreach ( $taxonomies as $taxonomy => $args ) {

			// Ignore Polylang taxonomy.
			if ( 'language' === $taxonomy ) {
				continue;
			}

			$terms = get_the_terms( $this->post->ID, $taxonomy );

			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				continue;
			}

			foreach ( $terms as $term ) {

				if ( empty( $term ) ) {
					continue;
				}

				$link = get_term_link( $term->term_id );
				$meta = get_term_meta( $term->term_id, '_' . WPGB_SLUG, true );

				$term->link = ! is_wp_error( $link ) ? $link : '';
				$term->color = isset( $meta['color'] ) ? $meta['color'] : '';
				$term->background = isset( $meta['background'] ) ? $meta['background'] : '';

				$post_terms[] = $term;

			}
		}

		$this->post->post_terms = $post_terms;

	}

	/**
	 * Get post format
	 *
	 * @since 1.1.8 Preserve unsupported post formats.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_format() {

		$post_format = $this->post->metadata[ WPGB_SLUG ]['post_format'];
		$post_format = empty( $post_format ) ? get_post_format() : $post_format;
		$post_format = empty( $post_format ) ? 'standard' : $post_format;

		$supported_formats = [ 'gallery', 'audio', 'video' ];
		$format_supported = in_array( $post_format, $supported_formats, true );
		$format_allowed = in_array( $post_format, $this->settings->post_formats, true );

		// We keep not supported formats (to assign cards to them) and we exclude support for not allowed formats.
		$this->post->post_format = ! $format_supported || $format_allowed ? $post_format : 'standard';

	}

	/**
	 * Get author data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_author() {

		$author = get_the_author_meta( 'ID' );
		$avatar = get_avatar_data( $author );

		$this->post->post_author = [
			'ID'           => $author,
			'display_name' => get_the_author_meta( 'display_name' ),
			'posts_url'    => get_author_posts_url( $author ),
			'avatar'       => $avatar,
		];

	}

	/**
	 * Get media content according to post format
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_media() {

		$source = '';
		$format = $this->post->post_format;

		// Add post_media key to post.
		$this->post->post_media = null;

		// Fetch media from post_meta.
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

		// If no alternative format available, get default attachement format and source.
		if ( empty( $source ) && 'attachment' === $this->post->post_type ) {

			$format = $this->get_attachment_format();
			$source = $this->get_attachment_url();

		}

		// Get thumbnail whatever the post format.
		$this->get_thumbnail();

		if ( 'standard' === $format ) {
			return;
		}

		// Try to fetch first media (audio, video & gallery) if missng.
		if ( empty( $source ) && $this->settings->first_media ) {
			$source = ( new First_Media( $this->post ) )->get( $format );
		}

		// If not content found set post to standard format.
		if ( empty( $source ) || ! is_array( $source ) ) {
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

		// Get alternative attachment ID (from metadata).
		$thumb = (int) $this->post->metadata[ WPGB_SLUG ]['attachment_id'];

		// If image attachment directly get image.
		if ( $thumb < 1 && 'attachment' === $this->post->post_type && wp_attachment_is_image( $this->post->ID ) ) {

			$this->post->post_thumbnail = $this->attachment->get_attachment( $this->post );
			return;

		}

		// Get thumbnail id.
		if ( $thumb < 1 ) {
			$thumb = get_post_thumbnail_id();
		}

		// Try to get first image in post content if thumb missing.
		if ( empty( $thumb ) && $this->settings->first_media && 'attachment' !== $this->post->post_type ) {
			$thumb = ( new First_Media( $this->post ) )->get();
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
	 * @since 1.0.1 Fix condition to fetch video attachment.
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

	/**
	 * Get attachment media format (audi or video)
	 *
	 * @since 1.1.8
	 * @access public
	 */
	public function get_attachment_format() {

		$format  = wp_attachment_is( 'video' ) ? 'video' : 'standard';
		$format  = wp_attachment_is( 'audio' ) ? 'audio' : $format;
		$allowed = in_array( $format, $this->settings->post_formats, true );

		// We exclude support for not allowed formats.
		$this->post->post_format = $allowed ? $format : 'standard';

		return $format;

	}

	/**
	 * Get attachment url for audio or video format
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_attachment_url() {

		if (
			'video' !== $this->post->post_format &&
			'audio' !== $this->post->post_format
		) {
			return;
		}

		$file_url = wp_get_attachment_url( $this->post->ID );

		if ( empty( $file_url ) ) {
			return;
		}

		return [
			'type'    => 'hosted',
			'sources' => (array) $file_url,
		];

	}

	/**
	 * Get product data
	 *
	 * @since 1.2.0 Add support for WooCommerce product_variation post type.
	 * @since 1.1.5 Add first product gallery in attachment.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_product_data() {

		if (
			(
				'product' === $this->post->post_type ||
				'product_variation' === $this->post->post_type
			) &&
			class_exists( 'WooCommerce' )
		) {

			$woo = new Woo();
			$this->post->product = $woo->post;
			$this->attachment->ids[] = $this->post->product->first_gallery_image;

		}

		if ( 'download' === $this->post->post_type && class_exists( 'Easy_Digital_Downloads' ) ) {

			$edd = new EDD( $this->post->ID );
			$this->post->product = $edd->post;

		}

	}
}
