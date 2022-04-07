<?php
/**
 * Query Users
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
 * User class.
 *
 * @class WP_Grid_Builder\FrontEnd\Sources\user
 * @since 1.0.0
 */
class Users {

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
	 * Holds queried users
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
	 * Holds user object
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var object
	 */
	protected $user = [];

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
	 * Parse users query args
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function parse_query() {

		$this->build_query();
		$this->run_query();

	}

	/**
	 * Retrieves users based on query variables
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array Queried objects
	 */
	public function get_results() {

		if ( empty( $this->query->get_results() ) ) {
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
		$this->set_role();
		$this->set_role__in();
		$this->set_role__not_in();
		$this->set_user__in();
		$this->set_user__not_in();
		$this->set_has_published_posts();
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

		// Query with all meta.
		$this->query_args['fields'] = 'all_with_meta';
		// Prevent additional query to count total.
		$this->query_args['count_total '] = false;
		// Add WP Grid Builder to query args.
		$this->query_args['wp_grid_builder'] = $this->settings->id;
		// Filter the query args.
		$this->query_args = apply_filters( 'wp_grid_builder/grid/query_args', $this->query_args, $this->settings->id );

		// Run the query.
		$this->query = new \WP_User_Query( $this->query_args );

	}

	/**
	 * Set number parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_number() {

		$number = $this->settings->posts_per_page;

		if ( empty( $number ) ) {
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
		$this->query_args['orderby'] = implode( ' ', (array) $this->settings->user_orderby );

	}

	/**
	 * Set role parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_role() {

		$role = $this->settings->role;
		$role = ! empty( $role ) ? (array) $role : '';

		$this->query_args['role'] = $role;

	}

	/**
	 * Set role__in parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_role__in() {

		if ( empty( $this->settings->role__in ) ) {
			return;
		}

		$this->query_args['role__in'] = (array) $this->settings->role__in;

	}

	/**
	 * Set role__not_in parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_role__not_in() {

		if ( empty( $this->settings->role__not_in ) ) {
			return;
		}

		$this->query_args['role__not_in'] = (array) $this->settings->role__not_in;

	}

	/**
	 * Set include parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_user__in() {

		if ( empty( $this->settings->user__in ) ) {
			return;
		}

		$this->query_args['include'] = (array) $this->settings->user__in;

	}

	/**
	 * Set exclude parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_user__not_in() {

		if ( empty( $this->settings->user__not_in ) ) {
			return;
		}

		$this->query_args['exclude'] = (array) $this->settings->user__not_in;

	}

	/**
	 * Set has_published_posts parameter
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_has_published_posts() {

		if ( empty( $this->settings->has_published_posts ) ) {
			return;
		}

		$this->query_args['has_published_posts'] = $this->settings->has_published_posts;

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

		$results = $this->query->get_results();

		$this->get_users_posts( $results );

		foreach ( $results as $user ) {

			$this->user = $user;
			$this->post = new \stdClass();

			$this->get_post_data();
			$this->get_user_data();
			$this->get_user_meta();
			$this->get_user_permalink();
			$this->get_user_format();
			$this->get_user_media();

			$this->posts[] = apply_filters( 'wp_grid_builder/grid/the_object', $this->post );
			$this->attachment->parse_attachment_ids( $this->post );

		}

	}

	/**
	 * Get users posts count
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $results Holds users object.
	 */
	public function get_users_posts( $results ) {

		$user_ids = array_keys( $results );
		$this->post_count = count_many_users_posts( $user_ids );

	}

	/**
	 * Get assimilated post data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_post_data() {

		$this->post->ID            = $this->user->ID;
		$this->post->object_type   = 'user';
		$this->post->post_date     = mysql2date( 'U', $this->user->user_registered );
		$this->post->post_modified = $this->post->post_date;
		$this->post->post_title    = $this->user->display_name;
		$this->post->post_name     = $this->user->display_name;
		$this->post->post_excerpt  = $this->user->description;
		$this->post->post_content  = $this->user->description;
		$this->post->post_author   = [
			'ID'           => $this->user->ID,
			'display_name' => $this->user->display_name,
			'posts_url'    => get_author_posts_url( $this->user->ID ),
			'avatar'       => get_avatar_data( $this->user->ID ),
		];

	}

	/**
	 * Get user data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_user_data() {

		$this->post->display_name    = $this->user->display_name;
		$this->post->first_name      = $this->user->first_name;
		$this->post->last_name       = $this->user->last_name;
		$this->post->nickname        = $this->user->nickname;
		$this->post->user_login      = $this->user->user_login;
		$this->post->user_email      = $this->user->user_email;
		$this->post->user_url        = $this->user->user_url;
		$this->post->user_roles      = $this->user->roles;
		$this->post->user_caps       = $this->user->caps;
		$this->post->user_locale     = $this->user->locale;
		$this->post->user_post_count = $this->post_count[ $this->user->ID ];

	}

	/**
	 * Get meta data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_user_meta() {

		$meta = array_map( 'current', (array) get_user_meta( $this->user->ID ) );
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
	 * Get user permalink
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_user_permalink() {

		$this->post->permalink = $this->post->metadata[ WPGB_SLUG ]['permalink'];

		if ( empty( $this->post->permalink ) ) {
			$this->post->permalink = get_author_posts_url( $this->user->ID );
		}

	}

	/**
	 * Get user format
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_user_format() {

		$post_format = $this->post->metadata[ WPGB_SLUG ]['post_format'];
		$post_format = empty( $post_format ) ? 'standard' : $post_format;

		// The block API also make this check.
		$format_exists = in_array( $post_format, $this->settings->post_formats, true );
		$this->post->post_format = $format_exists ? $post_format : 'standard';

	}

	/**
	 * Get media content according to user format
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_user_media() {

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

		// Get alternative attachment ID (from user_meta).
		$thumb = (int) $this->post->metadata[ WPGB_SLUG ]['attachment_id'];

		// Get thumbnail id.
		if ( $thumb < 1 ) {

			$thumb = get_avatar_data( $this->user->ID, [ 'size' => 500 ] );

			$thumb = [
				'sizes' => [
					'lightbox'  => get_avatar_data( $this->user->ID, [ 'size' => 50 ] ),
					'thumbnail' => $thumb,
					'full'      => $thumb,
				],
			];

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
