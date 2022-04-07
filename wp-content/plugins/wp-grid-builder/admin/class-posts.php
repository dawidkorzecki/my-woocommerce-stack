<?php
/**
 * Posts
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle post actions
 *
 * @class WP_Grid_Builder\Admin\Posts
 * @since 1.0.0
 */
final class Posts {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Clear attachment ids cache on add/udpate/delete.
		add_action( 'add_attachment', [ $this, 'clear_attachment_cache' ] );
		add_action( 'delete_attachment', [ $this, 'clear_attachment_cache' ] );

	}

	/**
	 * Clear attachment ids in cache
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id Attachment ID.
	 */
	public function clear_attachment_cache( $post_id ) {

		$post_type = get_post_type( $post_id );

		if ( 'attachment' === $post_type ) {
			wp_cache_delete( 'wpgb_all_attachment_ids' );
		}

	}
}
