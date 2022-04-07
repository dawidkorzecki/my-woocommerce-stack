<?php
/**
 * Class TaxCalculator
 *
 * @package WPDesk\FS\TableRate\Tax
 */

namespace WPDesk\FS\TableRate\Tax;

use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettingsImplementation;

/**
 * Can calculate taxes for rates.
 */
class TaxCalculator {

	const TAXABLE = 'taxable';
	const COST    = 'cost';

	/**
	 * @var MethodSettingsImplementation
	 */
	private $method_settings;

	/**
	 * @var array
	 */
	private $tax_rates;

	/**
	 * TaxCalculator constructor.
	 *
	 * @param MethodSettingsImplementation $method_settings .
	 */
	public function __construct( MethodSettingsImplementation $method_settings, array $tax_rates ) {
		$this->method_settings = $method_settings;
		$this->tax_rates = $tax_rates;
	}

	/**
	 * @param array $rate .
	 * @param bool $is_customer_vat_exempt .
	 */
	public function append_taxes_to_rate_if_enabled( array $rate, $is_customer_vat_exempt ) {
		if ( wc_tax_enabled() && 'yes' === $this->method_settings->get_prices_include_tax()
			&& self::TAXABLE === $this->method_settings->get_tax_status()
			&& isset( $rate[ self::COST ] ) && 0.0 !== (float) $rate[ self::COST ]
		) {
			return $this->append_taxes_to_rate( $rate, $is_customer_vat_exempt );
		}

		return $rate;
	}

	/**
	 * @param array $rate .
	 * @param bool $is_customer_vat_exempt .
	 *
	 * @return array
	 */
	private function append_taxes_to_rate( array $rate, bool $is_customer_vat_exempt ) {
		$total_cost = $rate[ self::COST ];
		$taxes = \WC_Tax::calc_tax( $total_cost, $this->tax_rates, true );

		$rate[ self::COST ] = $total_cost - array_sum( $taxes );

		$rate['taxes'] = $is_customer_vat_exempt ? [] : \WC_Tax::calc_shipping_tax( $rate[ self::COST ], $this->tax_rates );

		$rate['price_decimals'] = '4'; // Prevent the cost from being rounded before the tax is added.

		return $rate;
	}

}
