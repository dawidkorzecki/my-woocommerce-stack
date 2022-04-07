<?php
/**
 * Class FlexibleShippingMethodsChecker
 *
 * @package WPDesk\FS\Helpers
 */

namespace WPDesk\FS\Helpers;

use WPDesk\FS\TableRate\DefaultRulesSettings;

/**
 * Checker for FS Methods.
 */
class FlexibleShippingMethodsChecker {
	/**
	 * @return bool
	 */
	public function is_new_shipping_method(): bool {
		$shipping_methods = flexible_shipping_get_all_shipping_methods();

		if ( ! isset( $shipping_methods['flexible_shipping'] ) ) {
			return false;
		}

		$flexible_shipping_rates = array_values( $shipping_methods['flexible_shipping']->get_all_rates() );

		if ( empty( $flexible_shipping_rates ) || count( $flexible_shipping_rates ) > 1 ) {
			return false;
		}

		return isset( $flexible_shipping_rates[0]['method_rules'][0][ DefaultRulesSettings::NEW_FIELD ] );
	}
}
