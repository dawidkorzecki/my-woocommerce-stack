<?php
/**
 * License
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
 * Fetch plugin license info
 *
 * @class WP_Grid_Builder\Includes\License
 * @since 1.0.0
 */
final class License {

	/**
	 * API uri base
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	protected $api_base = 'https://wpgridbuilder.com/';

	/**
	 * Plugin Name
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	public $name = 'WP Grid Builder';

	/**
	 * Plugin Slug
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	public $slug = WPGB_BASE;

	/**
	 * Plugin option name
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	public $option = WPGB_SLUG;

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	public $version = WPGB_VERSION;

	/**
	 * Plugin license key
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $license_key = '';

	/**
	 * Customer email
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	public $email = '';

	/**
	 * Customer website url
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var string
	 */
	public $url = '';

	/**
	 * API Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $plugin Hold plugin information.
	 */
	public function __construct( $plugin = [] ) {

		$this->url = preg_replace( '(^https?://)', '', home_url() );

		foreach ( $plugin as $key => $value ) {
			$this->$key = $value;
		}

		$this->set_instance();

	}

	/**
	 * Set plugin instance
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_instance() {

		$plugin = get_option( WPGB_SLUG . '_plugin_info', [] );

		// For add-ons from bundle or all access plan.
		if ( ! empty( $plugin ) && WPGB_SLUG !== $this->option ) {

			$option = get_option( $this->option . '_plugin_info', [] );

			unset(
				$plugin['new_version'], // Make sure to not check main plugin version.
				$option['expires'], // To keep expiration date from the main plugin.
				$plugin['info'] // To prevent conflict when fetching add-on details.
			);

			$plugin = wp_parse_args( (array) $option, $plugin );

		}

		$plugin = wp_parse_args(
			apply_filters( 'wp_grid_builder/plugin_info', $plugin, $this->name ),
			[
				'new_version' => '',
				'tested'      => '',
				'icons'       => [],
			]
		);

		foreach ( $plugin as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Build API query args
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $request Request type.
	 * @return string
	 */
	public function build_query( $request ) {

		return add_query_arg(
			urlencode_deep(
				[
					'edd_action' => $request,
					'item_name'  => $this->name,
					'license'    => $this->license_key,
					'email'      => $this->email,
					'url'        => $this->url,
				]
			),
			$this->api_base
		);

	}

	/**
	 * Query the API
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $url URL to request for.
	 * @return mixed
	 */
	public function request( $url ) {

		$response = wp_safe_remote_get(
			$url,
			[
				'timeout'   => 10,
				'sslverify' => false,
			]
		);

		if ( is_wp_error( $response ) ) {

			$response_code = $response->get_error_code();
			$response_message = $response->get_error_message( $response_code );

			return new \WP_Error(
				$response_code,
				$response_message
			);

		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 !== (int) $response_code ) {

			return new \WP_Error(
				$response_code,
				$response_message
			);

		}

		$response = wp_remote_retrieve_body( $response );
		$response = (array) json_decode( $response, true );

		if ( empty( $response ) ) {

			return new \WP_Error(
				'api_error',
				__( 'An unknown error occurred from plugin API.', 'wp-grid-builder' )
			);

		}

		return $this->check_error( $response );

	}

	/**
	 * Check response error
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $response Holds response properties.
	 * @return array
	 */
	public function check_error( $response ) {

		if ( empty( $response['error'] ) || 'expired' === $response['error'] ) {
			return $response;
		}

		switch ( $response['error'] ) {
			case 'license_not_activable':
				$message = __( 'The key you entered belongs to a bundle, please use the product specific license key.', 'wp-grid-builder' );
				break;
			case 'no_activations_left':
				$message = __( 'Your license key has reached its activation limit. You must upgrade your license to use it on this site.', 'wp-grid-builder' );
				break;
			case 'revoked':
			case 'disabled':
				$message = __( 'Your license key has been disabled. Please contact support for more information.', 'wp-grid-builder' );
				break;
			case 'expired':
				$message = __( 'Your license has expired. You must renew your license in order to use it again.', 'wp-grid-builder' );
				break;
			case 'key_mismatch':
			case 'item_name_mismatch':
				$message = __( 'Failed to activate your license, your license key or email does not match.', 'wp-grid-builder' );
				break;
			case 'missing':
				$message = __( 'Invalid license. Please visit your account page and verify it.', 'wp-grid-builder' );
				break;
			case 'invalid':
			case 'site_inactive':
				$message = __( 'Your license is not active for this URL. Please visit your account page to verify it.', 'wp-grid-builder' );
				break;
			default:
				$message = __( 'There was an error with this license key. Please contact support for more information.', 'wp-grid-builder' );
				break;
		}

		return new \WP_Error(
			$response['error'],
			$message
		);

	}

	/**
	 * Activate plugin license
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $license_key Plugin license key.
	 * @param string $email       Plugin license email.
	 * @return mixed
	 */
	public function activate( $license_key = '', $email = '' ) {

		$this->email = trim( $email );
		$this->license_key = trim( $license_key );

		if ( empty( $email ) || empty( $license_key ) ) {

			return new \WP_Error(
				'license_error',
				__( 'Please enter your email and license key.', 'wp-grid-builder' )
			);

		}

		if ( ! is_email( $this->email ) ) {

			return new \WP_Error(
				'email_error',
				__( 'Your email address is not valid.', 'wp-grid-builder' )
			);

		}

		$request  = $this->build_query( 'activate_license' );
		$response = $this->request( $request );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$this->update_plugin_data( $response );

		return true;

	}

	/**
	 * Dectivate plugin license
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return mixed
	 */
	public function deactivate() {

		$request  = $this->build_query( 'deactivate_license' );
		$response = $this->request( $request );

		// If HTTP error, keep license.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// We always deactivate to allow user to re-activate.
		delete_option( $this->option . '_plugin_info' );

		if ( ! empty( $response['license'] ) && 'failed' === $response['license'] ) {

			return new \WP_Error(
				'deactivation_error',
				__( 'En error occured when deactivating your license.', 'wp-grid-builder' )
			);

		}

		return true;

	}

	/**
	 * Get plugins info from its name and author
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return mixed
	 */
	public function get_status() {

		$request  = $this->build_query( 'check_license' );
		$response = $this->request( $request );

		// If HTTP error, keep license.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if (
			! empty( $response['license'] ) &&
			'valid' !== $response['license'] &&
			'expired' !== $response['license']
		) {

			// Delete license only if inactive, invalid or disabled.
			delete_option( $this->option . '_plugin_info' );

			return new \WP_Error(
				'status_error',
				__( 'Your license is not active for this URL or invalid or disabled. Please visit your account page to verify it.', 'wp-grid-builder' )
			);

		} else {
			$this->update_plugin_data( $response );
		}

		return true;

	}

	/**
	 * Get plugin info
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return mixed
	 */
	public function get_info() {

		$request  = $this->build_query( 'get_version' );
		$response = $this->request( $request );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		unset(
			$response['package'],
			$response['download_link']
		);

		if ( isset( $response['sections'] ) ) {
			$response['sections'] = maybe_unserialize( $response['sections'] );
		}

		// Merge plugin info to prevent unecessary requests in plugins_api.
		// Each time we activate, we update status and the transient API check for updates.
		// Plugin info will be cleared and so will be automatically fetched when needed.
		$plugin = get_option( $this->option . '_plugin_info', [] );
		$plugin['info'] = (object) $response;
		$plugin = apply_filters( 'wp_grid_builder/plugin_info', $plugin, $this->name );

		update_option( $this->option . '_plugin_info', $plugin );

		return $plugin['info'];

	}

	/**
	 * Get plugin update
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return mixed
	 */
	public function get_update() {

		$request  = $this->build_query( 'get_version' );
		$response = $this->request( $request );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return (object) $response;

	}

	/**
	 * Refresh plugin license and info
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function refresh() {

		$this->get_status();
		$this->set_instance();

	}

	/**
	 * Update plugin license data
	 *
	 * @since 1.0.2 Unset plugin info.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data Holds plugin info data.
	 */
	public function update_plugin_data( $data ) {

		// Merge and refresh previous license info.
		$plugin_info = get_option( $this->option . '_plugin_info', [] );
		$plugin_info = wp_parse_args( (array) $data, $plugin_info );
		$plugin_info = wp_parse_args(
			$plugin_info,
			[
				'url'         => $this->url,
				'email'       => $this->email,
				'license_key' => $this->license_key,
			]
		);

		// We unset plugin info to force a refresh from plugins_api.
		unset( $plugin_info['info'] );

		update_option( $this->option . '_plugin_info', $plugin_info );

	}
}
