<?php
/**
 * Async
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle asynchronous requets
 *
 * @class WP_Grid_Builder\FrontEnd\Async
 * @since 1.0.0
 */
abstract class Async {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'define_ajax' ], 0 );
		add_action( 'template_redirect', [ $this, 'intercept_request' ], 0 );

	}

	/**
	 * Get custom async endpoint
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public static function get_endpoint() {

		$home_url = home_url( '/', 'relative' );
		$endpoint = add_query_arg( 'wpgb-ajax', 'action', $home_url );
		$endpoint = apply_filters( 'wp_grid_builder/async/get_endpoint', $endpoint );

		return esc_url_raw( $endpoint );

	}


	/**
	 * Set Ajax constant and headers
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function define_ajax() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( empty( $_GET['wpgb-ajax'] ) ) {
			return;
		}

		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		// Turn off display_errors to prevent malformed JSON.
		if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
			// phpcs:ignore WordPress.PHP.IniSet.display_errors_Blacklisted, WordPress.PHP.NoSilencedErrors.Discouraged
			@ini_set( 'display_errors', 0 );
		}

		$GLOBALS['wpdb']->hide_errors();

	}

	/**
	 * Send headers for async request
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function ajax_headers() {

		send_origin_headers();
		send_nosniff_header();
		$this->nocache_headers();

		// phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		// phpcs:enable WordPress.PHP.NoSilencedErrors.Discouraged

		status_header( 200 );

	}

	/**
	 * Set nocache_headers to disable page caching.
	 * Set constants to prevent caching by some plugins.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private function nocache_headers() {

		nocache_headers();

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
			define( 'DONOTCACHEOBJECT', true );
		}

		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( 'DONOTCACHEDB', true );
		}
	}

	/**
	 * Intercept async request.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function intercept_request() {

		if ( ! wpgb_doing_ajax() ) {
			return;
		}

		$this->ajax_headers();

		$action  = $this->get_action();
		$request = $this->get_request();

		if ( apply_filters( 'wp_grid_builder/async/intercept', false, $action, $request ) ) {
			return;
		}

		do_action( 'wp_grid_builder/async/' . $action, $request );
		$this->$action( $request );
		wp_die();

	}

	/**
	 * Get requested action
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_action() {

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.NonceVerification.Recommended
		$action  = sanitize_text_field( wp_unslash( $_GET['wpgb-ajax'] ) );
		$allowed = [ 'render', 'refresh', 'search' ];

		// Make sure only allowed actions can be ran.
		if ( ! in_array( $action, $allowed, true ) ) {
			$this->unknown_error();
		}

		return $action;

	}

	/**
	 * Get requested data
	 *
	 * Nonce is not necessary in our case and does not improve security at this stage.
	 * Logged out users all have the same nonce and it simply not improves security in our case.
	 * Not testing against a nonce for logged out users also prevents caching issue due to nonce lifetime.
	 * Anyone can filter and query grid content, so there isn't any user capability check.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_request() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( empty( $_REQUEST[ WPGB_SLUG ] ) ) {
			$this->unknown_error();
		}

		$request = wp_unslash( $_REQUEST[ WPGB_SLUG ] );
		$request = json_decode( $request, true );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return $request;

	}

	/**
	 * Handle unknown errors
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function unknown_error() {

		wp_send_json(
			[
				'success' => false,
				'message' => esc_html__( 'Sorry, an unknown error occurred.', 'wp-grid-builder' ),
			]
		);

	}

	/**
	 * Handle render action
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $request Holds requested data.
	 */
	abstract protected function render( $request );

	/**
	 * Handle refresh action
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $request Holds requested data.
	 */
	abstract protected function refresh( $request );

	/**
	 * Handle search action
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $request Holds requested data.
	 */
	abstract protected function search( $request );
}
