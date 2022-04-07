<?php
/**
 * User
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
 * Handle user fields
 *
 * @class WP_Grid_Builder\Includes\Settings\Forms\User
 * @since 1.0.0
 */
class User {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Render user fields on profile page.
		add_action( 'edit_user_profile', [ $this, 'add_meta_boxes' ] );
		// Save user fields from profile page.
		add_action( 'edit_user_profile_update', [ $this, 'save_user' ] );

	}

	/**
	 * Get settings for users
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array User settings
	 */
	public function get_user_settings() {

		return array_filter(
			Settings::get_instance()->get(),
			function( $setting ) {
				return ! empty( $setting['users'] );
			}
		);

	}

	/**
	 * Render meta box
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $user A WP_User object.
	 */
	public function add_meta_boxes( $user ) {

		$settings = $this->get_user_settings();

		foreach ( $settings as $setting ) {

			$values = get_user_meta( $user->ID, '_' . WPGB_SLUG, true );

			wp_nonce_field( 'wpgb_fields', 'wpgb_fields_nonce', false );
			Settings::get_instance()->render( $setting['id'], $values );

		}
	}

	/**
	 * Save user fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $user_id User ID.
	 */
	public function save_user( $user_id ) {

		// Check user capability.
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		Settings::get_instance()->save( $user_id, 'user' );

	}
}
