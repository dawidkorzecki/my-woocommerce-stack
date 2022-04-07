<?php
/**
 * Class FreeShippingCalculator
 *
 * @package WPDesk\FS\TableRate\ShippingMethod
 */

namespace WPDesk\FS\TableRate\ShippingMethod;

use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettings;
use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettingsFactory;
use WPDesk_Flexible_Shipping;

/**
 * Can calculate free shipping.
 */
class FreeShippingCalculator {

	/**
	 * @param MethodSettings $shipping_method .
	 * @param float          $cart_contents_cost .
	 *
	 * @return bool
	 */
	public function is_free_shipping( MethodSettings $shipping_method, $cart_contents_cost ) {
		/**
		 * Can provide free shipping callback.
		 * For internal use.
		 *
		 * @internal
		 */
		$free_shipping_calculation_callback = apply_filters(
			'flexible-shipping/shipping-method/free-shipping-callback',
			array( $this, 'is_free_shipping_callback' ),
			$shipping_method->get_raw_settings()
		);

		$is_free_shipping = $free_shipping_calculation_callback( $shipping_method->get_raw_settings(), $cart_contents_cost );

		$is_free_shipping = apply_filters_deprecated(
			'flexible_shipping_is_free_shipping',
			array( $is_free_shipping, $shipping_method->get_raw_settings(), $cart_contents_cost ),
			'4.0.0',
			'flexible-shipping/shipping-method/is-free-shipping'
		);

		/**
		 * Can modify free shipping.
		 *
		 * @param bool  $is_free_shipping Current is_free_shipping value based on method settings.
		 * @param array $shipping_method Flexible shipping method settings.
		 * @param float $cart_contents_cost Shipping contents cost.
		 *
		 * @return bool
		 */
		return apply_filters( 'flexible-shipping/shipping-method/is-free-shipping', $is_free_shipping, $shipping_method->get_raw_settings(), $cart_contents_cost );
	}

	/**
	 * Is free shipping?
	 *
	 * @param array $shipping_method_settings .
	 * @param float $cart_contents_cost .
	 *
	 * @return bool
	 */
	public function is_free_shipping_callback( $shipping_method_settings, $cart_contents_cost ) {
		$shipping_method = MethodSettingsFactory::create_from_array( $shipping_method_settings );
		$is_free_shipping = false;
		$free_shipping = $shipping_method->get_free_shipping();
		if ( isset( $free_shipping ) && '' !== $free_shipping ) {
			$free_shipping = trim( $free_shipping );
			if ( is_numeric( $free_shipping ) ) {
				if ( apply_filters( 'flexible_shipping_value_in_currency', floatval( $free_shipping ) ) <= floatval( $cart_contents_cost ) ) {
					$is_free_shipping = true;
				}
			}
		}

		return $is_free_shipping;
	}

}
