<?php
/**
 * Helpers for Shipping method.
 *
 * @package WPDesk\FS\TableRate\NewRulesTablePointer
 */

namespace WPDesk\FS\Helpers;

/**
 * Collection of helpers for Shipping method.
 */
class ShippingMethod {

	/**
	 * Pattern of option with Flexible Shipping methods.
	 *
	 * @var string
	 */
	const FS_METHODS_OPTION_PREFIX = 'flexible_shipping_methods_%d';

	/**
	 * Checks if there are Flexible Shipping methods in the Shipping Zones.
	 *
	 * @param string $method_name Name of Shipping method.
	 *
	 * @return bool Status.
	 */
	public static function check_if_method_exists_in_zones( $method_name ) {
		$zones = \WC_Shipping_Zones::get_zones();
		foreach ( $zones as $zone ) {
			$zone_instance = \WC_Shipping_Zones::get_zone( $zone['zone_id'] );

			if ( self::check_if_method_exists_in_zone( $method_name, $zone_instance ) === true ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if there are Flexible Shipping methods in the Shipping Zone.
	 *
	 * @param string            $method_name Name of Shipping method.
	 * @param \WC_Shipping_Zone $zone_instance Instance of Shipping Zone.
	 *
	 * @return bool Status.
	 */
	private static function check_if_method_exists_in_zone( $method_name, $zone_instance ) {
		$zone_methods = $zone_instance->get_shipping_methods();
		foreach ( $zone_methods as $zone_method ) {
			if ( $zone_method->id !== $method_name ) {
				continue;
			}

			$option_key       = sprintf( self::FS_METHODS_OPTION_PREFIX, $zone_method->instance_id );
			$shipping_methods = get_option( $option_key, array() );
			if ( $shipping_methods ) {
				return true;
			}
		}

		return false;
	}

}
