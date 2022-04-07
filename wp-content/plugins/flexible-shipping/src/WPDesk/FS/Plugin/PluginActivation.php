<?php
/**
 * Class PluginActivation
 *
 * @package WPDesk\FS\Plugin
 */

namespace WPDesk\FS\Plugin;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can redirect to FS Info tab on first plugin activation.
 */
class PluginActivation implements Hookable {

	const OPTION_NAME        = 'flexible-shipping-activation-redirected';
	const SHIPPING_METHOD_ID = 'flexible_shipping_info';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'redirect_on_first_activation_or_do_nothing' ) );
	}

	/**
	 * .
	 */
	public function redirect_on_first_activation_or_do_nothing() {
		if ( 0 === (int) get_option( self::OPTION_NAME, 1 ) ) {
			if ( wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=shipping&section=' . self::SHIPPING_METHOD_ID ) ) ) {
				update_option( self::OPTION_NAME, 1 );
				$this->terminate();
			}
		}
	}

	/**
	 * .
	 *
	 * @codeCoverageIgnore
	 */
	protected function terminate() {
		die();
	}

	/**
	 * .
	 */
	public function add_activation_option_if_not_present() {
		if ( false === get_option( self::OPTION_NAME, false ) ) {
			add_option( self::OPTION_NAME, 0 );
		}
	}

}
