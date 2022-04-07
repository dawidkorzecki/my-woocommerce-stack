<?php
/**
 * Class FlexibleShippingRates
 *
 * @package WPDesk\FS\TableRate\Rates
 */

namespace WPDesk\FS\TableRate\Rates;

use WC_Shipping_Zone;
use WC_Shipping_Zones;
use WPDesk\FS\TableRate\ShippingMethodSingle;
use WPDesk_Flexible_Shipping;

/**
 * Can get all Flexible Shipping rates.
 */
class FlexibleShippingRates {

	/**
	 * @var array
	 */
	private static $flexible_shipping_rates;

	/**
	 * @return array
	 */
	public static function get_flexible_shipping_rates() {
		if ( null === self::$flexible_shipping_rates ) {
			self::$flexible_shipping_rates = self::get_flexible_shipping_rates_from_configuration();
		}

		return self::$flexible_shipping_rates;
	}

	/**
	 * @return array
	 */
	private static function get_flexible_shipping_rates_from_configuration() {
		$rates = array();
		foreach ( self::get_shipping_zones() as $zone ) {
			foreach ( $zone['shipping_methods'] as $instance_id => $woo_shipping_method ) {
				if ( WPDesk_Flexible_Shipping::METHOD_ID === $woo_shipping_method->id ) {
					$rates = self::append_flexible_shipping_group_rates( $rates, $woo_shipping_method );
				}
				if ( ShippingMethodSingle::SHIPPING_METHOD_ID === $woo_shipping_method->id ) {
					$rates = self::append_flexible_shipping_single_rate( $rates, $woo_shipping_method );
				}
			}
		}

		return $rates;
	}

	/**
	 * @return WC_Shipping_Zone[]
	 */
	private static function get_shipping_zones() {
		$zones                               = WC_Shipping_Zones::get_zones();
		$zone0                               = WC_Shipping_Zones::get_zone( 0 );
		$zones[0]                            = $zone0->get_data();
		$zones[0]['formatted_zone_location'] = $zone0->get_formatted_location();
		$zones[0]['shipping_methods']        = $zone0->get_shipping_methods();

		return $zones;
	}

	/**
	 * @param array                    $rates               .
	 * @param WPDesk_Flexible_Shipping $woo_shipping_method .
	 *
	 * @return array
	 */
	private static function append_flexible_shipping_group_rates( array $rates, WPDesk_Flexible_Shipping $woo_shipping_method ) {
		$shipping_methods = $woo_shipping_method->get_shipping_methods();
		foreach ( $shipping_methods as $shipping_method ) {
			$id                             = $woo_shipping_method->prepare_rate_id( $shipping_method );
			$shipping_method['instance_id'] = $woo_shipping_method->instance_id;
			$rates[ $id ]                   = $shipping_method;
			$rates[ $id ]['method_id']      = WPDesk_Flexible_Shipping::METHOD_ID;
		}

		return $rates;
	}

	/**
	 * @param array                $rates               .
	 * @param ShippingMethodSingle $woo_shipping_method .
	 *
	 * @return array
	 */
	private static function append_flexible_shipping_single_rate( array $rates, ShippingMethodSingle $woo_shipping_method ) {
		$shipping_method                                                = $woo_shipping_method->instance_settings;
		$shipping_method['instance_id']                                 = $woo_shipping_method->instance_id;
		$rates[ $woo_shipping_method->get_rate_id() ]                   = $shipping_method;
		$rates[ $woo_shipping_method->get_rate_id() ]['method_id']      = ShippingMethodSingle::SHIPPING_METHOD_ID;
		$rates[ $woo_shipping_method->get_rate_id() ]['method_enabled'] = $woo_shipping_method->enabled;

		return $rates;
	}
}
