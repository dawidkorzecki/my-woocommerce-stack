<?php
/**
 * Class RateCalculatorFactory
 *
 * @package WPDesk\FS\TableRate\ShippingMethod
 */

namespace WPDesk\FS\TableRate\ShippingMethod;

use WC_Cart;
use WC_Shipping_Method;
use WPDesk\FS\TableRate\Rule\Condition\ConditionsFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleAdditionalCostFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleCostFieldsFactory;
use WPDesk\FS\TableRate\Rule\ShippingContents\DestinationAddressFactory;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContentsImplementation;
use WPDesk\FS\TableRate\Rule\SpecialAction\SpecialActionFactory;

/**
 * Can create rates calculator.
 */
class RateCalculatorFactory {
	/**
	 * @param WC_Shipping_Method $shipping_method .
	 * @param array               $package         .
	 *
	 * @return RateCalculator
	 */
	public static function create_for_shipping_method( WC_Shipping_Method $shipping_method, array $package ) {
		$shop_currency = get_option( 'woocommerce_currency' );
		$cart_currency = get_woocommerce_currency();

		$available_conditions       = ( new ConditionsFactory() )->get_conditions();
		$cost_fields                = ( new RuleCostFieldsFactory() )->get_fields();
		$available_additional_costs = ( new RuleAdditionalCostFactory() )->get_additional_costs();
		$available_special_actions  = ( new SpecialActionFactory() )->get_special_actions();
		$cost_rounding_precision    = wc_get_price_decimals();
		$cart                       = WC()->cart;
		$prices_includes_tax        = self::prices_include_tax( $cart );

		$cart_contents = new ShippingContentsImplementation(
			$cart->cart_contents,
			$prices_includes_tax,
			$cost_rounding_precision,
			DestinationAddressFactory::create_from_package_destination( $package['destination'] ),
			$cart_currency
		);

		$free_shipping_calculator = new FreeShippingCalculator();

		return new RateCalculator(
			$shipping_method,
			$shop_currency,
			$cart_currency,
			$available_conditions,
			$cost_fields,
			$available_additional_costs,
			$available_special_actions,
			$cost_rounding_precision,
			$prices_includes_tax,
			$cart,
			$cart_contents,
			$package,
			$free_shipping_calculator
		);
	}

	/**
	 * @param WC_Cart $cart .
	 *
	 * @return bool
	 */
	private static function prices_include_tax( WC_Cart $cart ) {
		return (bool) apply_filters( 'flexible_shipping_prices_include_tax', $cart->display_prices_including_tax() );
	}
}
