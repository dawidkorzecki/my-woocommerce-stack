<?php
/**
 * Class ItemMeta
 *
 * @package WPDesk\FS\TableRate\Order
 */

namespace WPDesk\FS\TableRate\Order;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WC_Order_Item;
use WC_Order_Item_Shipping;
use WPDesk\FS\TableRate\ShippingMethod\RateCalculator;
use WPDesk\FS\TableRate\ShippingMethodSingle;
use WPDesk_Flexible_Shipping;

/**
 * Can display order meta.
 */
class ItemMeta implements Hookable {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_shipping_meta' ) );

		add_action( 'woocommerce_before_order_itemmeta', array( $this, 'filter_meta_if_flexible_shipping_method' ), 10, 2 );
		add_action( 'woocommerce_after_order_itemmeta', array( $this, 'remove_filter_meta_if_flexible_shipping_method' ) );
	}

	/**
	 * @param int $order_id .
	 */
	public function update_order_shipping_meta( $order_id ) {
		$order = wc_get_order( $order_id );

		foreach ( $order->get_shipping_methods() as $shipping_method ) {
			$shipping_method->delete_meta_data( RateCalculator::DESCRIPTION_BASE64ENCODED );
			$shipping_method->save_meta_data();
		}
	}

	/**
	 * @param int           $item_id .
	 * @param WC_Order_Item $item .
	 */
	public function filter_meta_if_flexible_shipping_method( $item_id, WC_Order_Item $item ) {
		if ( $item instanceof WC_Order_Item_Shipping ) {
			if ( in_array( $item->get_method_id(), array( WPDesk_Flexible_Shipping::METHOD_ID, ShippingMethodSingle::SHIPPING_METHOD_ID ), true ) ) {
				add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_flexible_shipping_item_meta' ) );
				add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'format_display_key' ) );
			}
		}
	}

	/**
	 * @param string $key .
	 *
	 * @return string
	 */
	public function format_display_key( $key ) {
		return RateCalculator::DESCRIPTION === $key ? __( 'Description', 'flexible-shipping' ) : $key;
	}

	/**
	 * @param array $hidden_order_item_meta .
	 *
	 * @return array
	 */
	public function hide_flexible_shipping_item_meta( $hidden_order_item_meta ) {
		$hidden_order_item_meta[] = RateCalculator::FS_INTEGRATION;
		$hidden_order_item_meta[] = RateCalculator::DESCRIPTION_BASE64ENCODED;

		return $hidden_order_item_meta;
	}

	/**
	 * .
	 */
	public function remove_filter_meta_if_flexible_shipping_method() {
		remove_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_flexible_shipping_item_meta' ) );
		remove_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'format_display_key' ) );
	}

}
