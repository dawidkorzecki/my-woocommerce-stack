<?php
/**
 * Plugin
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\License;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle plugin activation and deactivation
 *
 * @class WP_Grid_Builder\Admin\Plugin
 * @since 1.0.0
 */
final class Plugin extends Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_plugin';

	/**
	 * Render plugin panel
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_panel() {

		ob_start();
		require WPGB_PATH . 'admin/views/panels/plugin.php';
		return ob_get_clean();

	}

	/**
	 * Render add-ons panel
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_addons() {

		ob_start();
		require WPGB_PATH . 'admin/views/pages/add-ons.php';
		return ob_get_clean();

	}

	/**
	 * Activate plugin license
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function activate_plugin() {

		$response = ( new License() )->activate(
			$this->get_var( 'license_key', true ),
			$this->get_var( 'license_email', true )
		);

		$message = __( 'Plugin license successfully activated!', 'wp-grid-builder' );

		if ( is_wp_error( $response ) ) {

			$error    = $response->get_error_code();
			$message  = (string) $response->get_error_message( $error );
			$response = false;

		}

		$this->send_response(
			$response,
			esc_html( $message ),
			$this->render_panel()
		);

	}

	/**
	 * Deactivate plugin license
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function deactivate_plugin() {

		$response = ( new License() )->deactivate();
		$message  = __( 'Your license has been deactivated.', 'wp-grid-builder' );

		if ( is_wp_error( $response ) ) {

			$error    = $response->get_error_code();
			$message  = (string) $response->get_error_message( $error );
			$response = false;

		}

		$this->send_response(
			$response,
			esc_html( $message ),
			$this->render_panel()
		);

	}

	/**
	 * Refresh plugin status
	 *
	 * @since 1.0.2 Throttle refreshing (60s).
	 * @since 1.0.0
	 * @access public
	 */
	public function refresh_status() {

		$message  = __( 'License info refreshed!', 'wp-grid-builder' );
		$response = get_transient( WPGB_SLUG . '_plugin_status' );

		// We allow refresh each minute.
		if ( ! $response ) {

			$response = ( new License() )->get_status();
			set_transient( WPGB_SLUG . '_plugin_status', true, 60 );

		}

		if ( is_wp_error( $response ) ) {

			$error    = $response->get_error_code();
			$message  = (string) $response->get_error_message( $error );
			$response = false;

		}

		$this->send_response(
			$response,
			esc_html( $message ),
			$this->render_panel()
		);

	}

	/**
	 * Install add-on
	 *
	 * @since 1.1.5
	 * @access public
	 */
	public function install_addon() {

		// Include upgrader class.
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		if ( ! current_user_can( 'install_plugins' ) ) {

			$this->send_response(
				false,
				__( 'Sorry, you are not allowed to install plugins on this site.', 'wp-grid-builder' ),
				$this->render_addons()
			);

		}

		$name    = $this->get_var( 'name' );
		$license = ( new License( [ 'name' => $name ] ) )->get_update();

		if ( empty( $license->package ) ) {

			$this->send_response(
				false,
				__( 'Sorry, the plugin package was not found.', 'wp-grid-builder' ),
				$this->render_addons()
			);

		}

		$upgrader = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader( $upgrader );
		$response = $upgrader->install( $license->package );
		$message  = '';

		if ( ! $response ) {

			$message = sprintf(
				/* translators: %s: Add-on name */
				__( 'There was an error installing %s.', 'wp-grid-builder' ),
				esc_html( $name )
			);

		}

		$this->send_response(
			$response,
			$message,
			$this->render_addons()
		);

	}

	/**
	 * Activate add-on
	 *
	 * @since 1.1.5
	 * @access public
	 */
	public function activate_addon() {

		$slug = $this->get_var( 'slug' );
		$name = $this->get_var( 'name' );

		if ( ! current_user_can( 'install_plugins' ) ) {

			$this->send_response(
				false,
				__( 'Sorry, you are not allowed to activate plugins on this site.', 'wp-grid-builder' ),
				$this->render_addons()
			);

		}

		$active   = activate_plugin( $slug );
		$response = true;
		$message  = '';

		if ( is_wp_error( $active ) ) {

			$response = false;
			$message  = sprintf(
				/* translators: %s: Add-on name */
				__( 'There was an error activating %s.', 'wp-grid-builder' ),
				esc_html( $addon )
			);

		}

		$this->send_response(
			$response,
			$message,
			$this->render_addons()
		);

	}
}
