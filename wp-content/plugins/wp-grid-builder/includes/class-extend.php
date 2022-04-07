<?php
/**
 * Extend
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
 * Extend WordPress support and localize
 *
 * @class WP_Grid_Builder\Includes\Extend
 * @since 1.0.0
 */
class Extend {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Add post formats for any (custom) post types.
		add_action( 'after_setup_theme', [ $this, 'extend_theme_support' ], PHP_INT_MAX );
		// Register additionnal image sizes.
		add_action( 'after_setup_theme', [ $this, 'add_image_sizes' ] );
		// Add post thumbnail for attachments.
		add_action( 'after_setup_theme', [ $this, 'add_attachment_thumbnail' ] );
		// Add post thumbnail for attachments image type.
		add_action( 'admin_init', [ $this, 'remove_attachment_thumbnail' ] );
		// Display thumbnail in media library (for unsupported mime types).
		add_filter( 'wp_mime_type_icon', [ $this, 'change_mime_icon' ], 10, 3 );
		// Register widget.
		add_action( 'widgets_init', [ $this, 'register_widget' ] );
		// Register cron for image resize.
		add_action( 'wpgb_resizer_cron', [ $this, 'generate_blurred_attachment' ], 1, 2 );

	}

	/**
	 * Extend Theme Support
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function extend_theme_support() {

		if ( ! wpgb_get_option( 'post_formats_support' ) ) {
			return;
		}

		$std_formats = (array) get_theme_support( 'post-formats' );
		$std_formats = array_shift( $std_formats );
		$new_formats = [ 'gallery', 'video', 'audio' ];
		$new_formats = $std_formats ? wp_parse_args( $new_formats, $std_formats ) : $new_formats;

		// Add theme support for post formats supported by the plugin.
		add_theme_support( 'post-formats', array_unique( $new_formats ) );
		// Add Post Formats support.
		add_action( 'init', [ $this, 'add_post_formats' ] );

	}

	/**
	 * Add Post Formats support to any post types
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_post_formats() {

		// Get all public post types.
		$post_types = get_post_types(
			[
				'public' => true,
			]
		);

		// Unset attachment.
		unset( $post_types['attachment'] );

		foreach ( $post_types as $slug => $name ) {

			// Add post formats support.
			add_post_type_support( $slug, 'post-formats' );
			// Register post format taxonomy.
			register_taxonomy_for_object_type( 'post_format', $slug );

		}
	}

	/**
	 * Add image sizes to WordPress
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_image_sizes() {

		$img_sizes = wpgb_get_option( 'image_sizes' );

		if ( empty( $img_sizes ) || ! is_array( $img_sizes ) ) {
			return;
		}

		foreach ( $img_sizes as $size => $args ) {

			$width  = isset( $args['width'] ) ? (int) $args['width'] : 0;
			$height = isset( $args['height'] ) ? (int) $args['height'] : 0;
			$crop   = isset( $args['crop'] ) ? (bool) $args['crop'] : false;

			if ( $width > 0 || $height > 0 ) {
				add_image_size( 'wp_grid_builder_size_' . ( $size + 1 ), $width, $height, $crop );
			}
		}
	}

	/**
	 * Add featured image field for attachment
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_attachment_thumbnail() {

		add_post_type_support( 'attachment', 'thumbnail' );

	}

	/**
	 * Remove featured image field for attachment image type
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function remove_attachment_thumbnail() {

		global $pagenow;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( 'post.php' !== $pagenow || ! isset( $_GET['post'] ) ) {
			return;
		}

		$post_type = get_post_type( (int) $_GET['post'] );

		if ( 'attachment' !== $post_type ) {
			return;
		}

		$mime_type = get_post_mime_type( (int) $_GET['post'] );

		if ( strpos( $mime_type, 'image' ) !== false ) {
			remove_post_type_support( 'attachment', 'thumbnail' );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Display thumbnail in media library to non support post thumbnail.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string  $icon Path to the mime type icon.
	 * @param string  $mime Mime type.
	 * @param integer $post_id Attachment ID. Will equal 0 if the function passed the mime type..
	 */
	public function change_mime_icon( $icon, $mime = null, $post_id = null ) {

		// Attachment types which support thumbnail.
		$supported = [ 'image', 'video', 'audio' ];

		// Check if attachment support thumbnail.
		$supported = array_filter(
			$supported,
			function( $type ) use ( $post_id ) {
				return wp_attachment_is( $type, $post_id );
			}
		);

		// Replace icon with thumbnail to unsupported mime type.
		if ( ! $supported ) {

			$thumb = get_the_post_thumbnail_url( $post_id, 'medium' );

			if ( ! empty( $thumb ) ) {
				return $thumb;
			}
		}

		return $icon;

	}

	/**
	 * Register widgets
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widget() {

		register_widget( __NAMESPACE__ . '\Widgets\Grid_Widget' );
		register_widget( __NAMESPACE__ . '\Widgets\Facet_Widget' );

		if ( ! empty( apply_filters( 'wp_grid_builder/templates', [] ) ) ) {
			register_widget( __NAMESPACE__ . '\Widgets\Template_Widget' );
		}
	}

	/**
	 * Generate blurred attachment.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id   Image attachment ID.
	 * @param array   $size Image size arguments.
	 */
	public function generate_blurred_attachment( $id, $size ) {

		LQIP_Resizer::generate_attachment_metadata( $id, $size );

	}
}
