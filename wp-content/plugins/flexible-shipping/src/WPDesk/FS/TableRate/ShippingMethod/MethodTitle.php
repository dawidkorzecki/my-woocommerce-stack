<?php
/**
 * Class MethodTitle
 *
 * @package WPDesk\FS\TableRate\ShippingMethod
 */

namespace WPDesk\FS\TableRate\ShippingMethod;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WC_Shipping_Method;
use WPDesk\FS\TableRate\ShippingMethodSingle;

/**
 * Can change method title.
 */
class MethodTitle implements Hookable {
	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'woocommerce_settings_shipping', array( $this, 'add_hook_on_settings_page' ), 10, 2 );
	}

	/**
	 * .
	 */
	public function add_hook_on_settings_page() {
		add_filter( 'woocommerce_shipping_method_title', array( $this, 'modify_shipping_method_title' ), 10, 2 );
	}

	/**
	 * @param string             $title           .
	 * @param WC_Shipping_Method $shipping_method .
	 *
	 * @return string
	 */
	public function modify_shipping_method_title( $title, $shipping_method ) {
		if ( $shipping_method instanceof ShippingMethodSingle ) {
			return $shipping_method->get_instance_option( 'method_title' );
		}

		return $title;
	}
}
