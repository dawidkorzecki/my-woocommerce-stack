<?php

/**
 * Shipping method controler.
 *
 * @package WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency
 */
namespace FSVendor\WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency;

use FSVendor\WP_Mock\Hook;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Converts shipping costs and taxes from shop currency to current currency.
 */
class ShippingMethodController implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var string
     */
    private $shipping_method_id;
    /**
     *
     * @var Converter
     */
    protected $converter;
    /**
     * @param string    $shipping_method_id .
     * @param Converter $converter .
     */
    public function __construct($shipping_method_id, $converter)
    {
        $this->shipping_method_id = $shipping_method_id;
        $this->converter = $converter;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_filter('woocommerce_shipping_rate_cost', array($this, 'convert_shipping_rate_costs_for_shipping_method'), 10, 2);
        \add_filter('woocommerce_shipping_rate_taxes', array($this, 'convert_shipping_rate_taxes_for_shipping_method'), 10, 2);
    }
    /**
     * Filter the rate cost.
     *
     * @param float|int|string  $cost                 The shipping rate cost.
     * @param \WC_Shipping_Rate $shipping_rate_object The shipping rate object.
     *
     * @return float|int|string
     * @internal filter.
     */
    public function convert_shipping_rate_costs_for_shipping_method($cost, $shipping_rate_object)
    {
        if ($this->is_shipping_method($shipping_rate_object)) {
            $cost = $this->converter->convert($cost);
        }
        return $cost;
    }
    /**
     * Filter the taxes.
     *
     * @param float[]|int[]|string[] $taxes                The shipping rate taxes array.
     * @param \WC_Shipping_Rate      $shipping_rate_object The shipping rate object.
     *
     * @return float[]|int[]|string[]
     * @internal filter.
     */
    public function convert_shipping_rate_taxes_for_shipping_method($taxes, $shipping_rate_object)
    {
        if ($this->is_shipping_method($shipping_rate_object)) {
            $taxes = $this->converter->convert_array($taxes);
        }
        return $taxes;
    }
    /**
     * Check if the shipping rate object's method ID is relevant to this class.
     *
     * @param \WC_Shipping_Rate $shipping_rate_object The shipping rate object.
     *
     * @return bool
     */
    private function is_shipping_method($shipping_rate_object)
    {
        return $shipping_rate_object instanceof \WC_Shipping_Rate && $this->shipping_method_id === $shipping_rate_object->get_method_id();
    }
}
