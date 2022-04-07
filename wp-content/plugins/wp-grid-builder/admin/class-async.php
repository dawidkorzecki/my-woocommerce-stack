<?php
/**
 * Async
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
 * Handle asynchronous requests.
 *
 * @class WP_Grid_Builder\Admin\Async
 * @since 1.0.0
 */
abstract class Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_async';

	/**
	 * Holds post
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $post = [];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'wp_ajax_' . $this->action, [ $this, 'maybe_handle' ] );

	}

	/**
	 * Check ajax request method
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function maybe_handle() {

		$this->capability();
		$this->referer();
		$this->handle();

		// If no methods sent response.
		wp_die();

	}

	/**
	 * Check capability
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function capability() {

		if ( ! Helpers::current_user_can() ) {

			$this->send_response(
				false,
				__( 'You are not allowed to perform this action. Please contact site administrator for further information.', 'wp-grid-builder' )
			);

		}

	}

	/**
	 * Check referer
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function referer() {

		// Get unique nonce token from current action and method (action is prefixed by plugin slug).
		// Symbol is usually an object type ID but can be anything to improve security.
		$action = isset( $_POST['action'] ) ? sanitize_key( $_POST['action'] ) : '';
		$object = isset( $_POST['object'] ) ? sanitize_key( $_POST['object'] ) : '';
		$symbol = isset( $_POST['symbol'] ) ? sanitize_key( $_POST['symbol'] ) : '';
		$token  = $action . '_' . $object . ( $symbol ? '_' . $symbol : '' );

		if ( check_ajax_referer( $token, 'nonce', false ) === false ) {

			$this->send_response(
				false,
				__( 'An error occurred. Please try to refresh the page or logout and login again.', 'wp-grid-builder' )
			);

		}

		$this->post = wp_unslash( $_POST );

	}

	/**
	 * Handle unknown errors
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function unknown_error() {

		$this->send_response(
			false,
			__( 'Sorry, an unknown error occurred.', 'wp-grid-builder' )
		);

	}

	/**
	 * Send response
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param boolean $success Success state.
	 * @param string  $message Holds message for backend.
	 * @param string  $content Holds content for backend.
	 */
	protected function send_response( $success = true, $message = '', $content = '' ) {

		wp_send_json(
			[
				'success' => (bool) $success,
				'message' => wp_strip_all_tags( $message ),
				// Content is already escaped in methods & templates.
				'content' => $content,
			]
		);

	}

	/**
	 * Helper to get var from $post
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param  string  $key Key name to get from $post array.
	 * @param  boolean $throw Throw error.
	 * @return mixed
	 */
	protected function get_var( $key = '', $throw = true ) {

		if ( empty( $key ) ) {
			return;
		}

		if ( ! isset( $this->post[ $key ] ) ) {

			if ( $throw ) {
				$this->unknown_error();
			}

			return;

		}

		return $this->post[ $key ];

	}


	/**
	 * Handle method to trigger.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function handle() {

		$method = $this->get_var( 'method' );

		if ( ! method_exists( $this, $method ) ) {
			$this->unknown_error();
		}

		call_user_func( [ $this, $method ] );

	}
}
