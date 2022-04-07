<?php
/**
 * Class DuplicateNotice
 *
 * @package WPDesk\FS\TableRate\ShippingMethod\Duplicate
 */

namespace WPDesk\FS\TableRate\ShippingMethod\Duplicate;

use FSVendor\WPDesk\Notice\Notice;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Display admin notices.
 */
class DuplicateNotice implements Hookable {
	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'woocommerce_settings_shipping', [ $this, 'add_admin_notice' ] );
	}

	/**
	 * Add admin notices.
	 */
	public function add_admin_notice() {
		if ( ! isset( $_GET['status'], $_GET[ DuplicateAction::ACTION ], $_GET['method_title'] ) ) {
			return;
		}

		$status = in_array( $_GET['status'], [ 'success', 'error' ] ) ? $_GET['status'] : 'warning'; // phpcs:ignore
		$title  = sanitize_text_field( urldecode( wp_unslash( $_GET['method_title'] ) ) ); // phpcs:ignore

		if ( $status === 'success' ) {
			$message = __( '%1$s%2$s%3$s successfully duplicated.', 'flexible-shipping' ); // phpcs:ignore
		} else {
			$message = __( '%1$s%2$s%3$s shipping method duplication error. Please try again later.', 'flexible-shipping' ); // phpcs:ignore
		}

		$this->display_notice( sprintf( $message, '<strong>', $title, '</strong>' ), $status );
	}

	/**
	 * @param string $content .
	 * @param string $type    .
	 *
	 * @codeCoverageIgnore
	 */
	protected function display_notice( string $content, string $type ) {
		new Notice( $content, $type, true );
	}
}
