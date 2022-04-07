<?php
/**
 * Metabox
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle metaboxes
 *
 * @class WP_Grid_Builder\Admin\MetaBox
 * @since 1.0.0
 */
final class MetaBox extends Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_fields';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct();

		add_action( 'admin_init', [ $this, 'add_metaboxes' ] );
		add_action( 'created_term', [ $this, 'add_metaboxes' ], 1, 3 );

	}

	/**
	 * Check iff current user can edit meta
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_metaboxes() {

		if ( ! Helpers::current_user_can() ) {
			return;
		}

		if ( wpgb_get_option( 'post_meta' ) ) {
			$this->post_metabox();
		}

		if ( wpgb_get_option( 'term_meta' ) ) {
			$this->term_metabox();
		}
	}

	/**
	 * Register post metabox
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function post_metabox() {

		global $pagenow;

		if ( 'post-new.php' === $pagenow || 'post.php' === $pagenow ) {
			require_once WPGB_PATH . 'admin/settings/post.php';
		}
	}

	/**
	 * Register term metabox
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function term_metabox() {

		global $pagenow;

		if ( 'term.php' === $pagenow || 'edit-tags.php' === $pagenow || current_filter() === 'created_term' ) {
			require_once WPGB_PATH . 'admin/settings/term.php';
		}
	}

	/**
	 * Render custom fields popup
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_fields() {

		$object_id   = $this->get_var( 'id' );
		$object_type = $this->get_var( 'type' );

		if ( (int) $object_id < 1 ) {
			$this->unknown_error();
		}

		ob_start();
		require_once WPGB_PATH . 'admin/views/modules/post-fields.php';

		$this->send_response(
			true,
			null,
			ob_get_clean()
		);

	}

	/**
	 * Save custom fields popup
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function save_fields() {

		// Load post fields.
		require WPGB_PATH . 'admin/settings/post.php';

		// Get metadata type.
		$type = $this->get_var( 'type' );

		// If metadata not supported.
		if ( 'post' !== $type && 'user' !== $type && 'term' !== $type ) {
			return;
		}

		// Get object id.
		$id = $this->get_var( 'id' );

		// Sanitize fields.
		$meta = $this->get_var( 'meta' );
		$meta = json_decode( $meta, true );
		$meta = wp_grid_builder()->settings->sanitize( $meta );
		$meta = apply_filters( 'wp_grid_builder/settings/save_fields', $meta, $type, $id );
		$meta = wp_slash( $meta );

		// Keep other metadata.
		$old  = get_metadata( $type, $id, '_' . WPGB_SLUG, true );
		$meta = wp_parse_args( $meta, $old );

		update_metadata( $type, $id, '_' . WPGB_SLUG, $meta );

		$this->send_response( true, __( 'Settings Saved!', 'wp-grid-builder' ) );

	}
}
