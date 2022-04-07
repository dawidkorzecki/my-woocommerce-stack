<?php

/**
 * Interface Converter
 * @package WPDesk\WooCommerce\CurrencySwitchers
 */
namespace FSVendor\WPDesk\WooCommerce\CurrencySwitchers;

/**
 * Interface for currency switchers converters.
 */
interface SwitcherConverter
{
    /**
     * Convert value from shop currency to current currency.
     *
     * @param float $value Value in shop currency.
     *
     * @return float
     */
    public function convert($value);
    /**
     * Convert an array of prices.
     *
     * @return array
     * @example convert_array( ['price' => '10', 'sale_price' => 5] ) --> ['price' => '12.4', 'sale_price' => 6.2]
     */
    public function convert_array($values);
}
