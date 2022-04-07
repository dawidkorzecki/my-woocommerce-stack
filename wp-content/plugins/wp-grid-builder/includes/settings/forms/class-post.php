<?php
/**
 * Post
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings\Forms;

use WP_Grid_Builder\Includes\Settings\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle post fields
 *
 * @class WP_Grid_Builder\Includes\Settings\Forms\Post
 * @since 1.0.0
 */
class Post {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Register post type meta boxes.
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		// Save custom fields for post type.
		add_action( 'save_post', [ $this, 'save_post' ], 10, 3 );
		// Save custom fields for attachment post type.
		add_action( 'edit_attachment', [ $this, 'save_post' ], 10 );

	}

	/**
	 * Register meta box
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_meta_boxes() {

		$settings = Settings::get_instance()->get();

		foreach ( $settings as $setting ) {

			if ( empty( $setting['post_types'] ) ) {
				continue;
			}

			add_meta_box(
				WPGB_SLUG,
				$setting['title'],
				[ $this, 'render_meta_box' ],
				$setting['post_types'],
				'normal',
				'default',
				[ 'id' => $setting['id'] ]
			);

		}
	}

	/**
	 * Render meta box
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_Post $post The post object.
	 * @param array   $metabox Metabox args.
	 */
	public function render_meta_box( $post, $metabox ) {

		$values = get_post_meta( $post->ID, '_' . WPGB_SLUG, true );

		wp_nonce_field( 'wpgb_fields', 'wpgb_fields_nonce', false );
		Settings::get_instance()->render( $metabox['args']['id'], $values );

	}

	/**
	 * Save post fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	public function save_post( $post_id, $post = null, $update = false ) {

		// If post revision.
		if (
			is_int( wp_is_post_revision( $post_id ) ) ||
			is_null( $update )
		) {
			return;
		}

		// If post autosave.
		if (
			( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
			is_int( wp_is_post_autosave( $post_id ) )
		) {
			return;
		}

		// Check user capability.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		Settings::get_instance()->save( $post_id, 'post' );

	}
}
