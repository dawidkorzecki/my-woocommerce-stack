<?php

/**
 * Shipping method integration.
 *
 * @package WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency
 */
namespace FSVendor\WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Integrates shipping method (by method_id, ie.: flexible_shipping ) with WooCommerce Multicurrency plugin.
 * @see https://woocommerce.com/products/multi-currency/
 */
class ShippingMethodIntegration implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var string
     */
    private $shipping_method_id;
    /**
     * @var \WOOMC\Integration\Shipping\AbstractController
     */
    private $woocommerce_multicurrency_controller;
    /**
     * @param string $shipping_method_id .
     */
    public function __construct($shipping_method_id)
    {
        $this->shipping_method_id = $shipping_method_id;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('woocommerce_multicurrency_loaded', array($this, 'add_integration_for_shipping_method'));
    }
    /**
     * Add integration to WooCommerce MultiCurrency for shipping method.
     */
    public function add_integration_for_shipping_method()
    {
        $this->woocommerce_multicurrency_controller = new \FSVendor\WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency\ShippingMethodController($this->shipping_method_id, new \FSVendor\WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency\Converter());
        $this->woocommerce_multicurrency_controller->hooks();
    }
}
