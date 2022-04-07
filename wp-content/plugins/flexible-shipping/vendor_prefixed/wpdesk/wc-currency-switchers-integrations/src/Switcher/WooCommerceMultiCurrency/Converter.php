<?php

/**
 * Currency converter.
 *
 * @package WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency
 */
namespace FSVendor\WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency;

use FSVendor\WPDesk\WooCommerce\CurrencySwitchers\AbstractConverter;
/**
 * Can convert currency using WooCommerce MultiCurrency plugin.
 * @see https://woocommerce.com/products/multi-currency/
 */
class Converter extends \FSVendor\WPDesk\WooCommerce\CurrencySwitchers\AbstractConverter
{
    /**
     * @inheritDoc
     */
    public function convert($value)
    {
        $rate_storage = new \WOOMC\Rate\Storage();
        $price_rounder = new \WOOMC\Price\Rounder();
        $currency_detector = new \WOOMC\Currency\Detector();
        $price_calculator = new \WOOMC\Price\Calculator($rate_storage, $price_rounder);
        $price_controller = new \WOOMC\Price\Controller($price_calculator, $currency_detector);
        return $price_controller->convert($value);
    }
}
