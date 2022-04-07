<?php
/**
 * Order item meta.
 *
 * @package Flexible Shipping.
 */

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Manages hidden order item meta.
 */
class WPDesk_Flexible_Shipping_Order_Item_Meta implements Hookable {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'add_hidden_order_itemmeta' ) );
	}

	/**
	 * @param array $hidden_order_itemmeta .
	 *
	 * @return array
	 */
	public function add_hidden_order_itemmeta( $hidden_order_itemmeta ) {
		$hidden_order_itemmeta[] = WPDesk_Flexible_Shipping::META_DEFAULT;

		return $hidden_order_itemmeta;
	}
}
