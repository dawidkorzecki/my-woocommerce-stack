<?php

/**
 * Method Factory.
 *
 * @package WPDesk\FS\TableRate\Settings
 */
namespace FSVendor\WPDesk\FS\TableRate\Settings;

use FSVendor\WPDesk\FS\Helpers\ShippingMethod;
/**
 * Can create Method.
 */
class MethodSettingsFactory
{
    const FIELD_METHOD_FREE_SHIPPING = 'method_free_shipping';
    /**
     * @param array $shipping_method_array
     * @param bool $method_default_disable Should disable method_default_setting?
     *
     * @return MethodSettings
     */
    public static function create_from_array($shipping_method_array, $method_default_disable = \true)
    {
        $shipping_method_array = self::clean_settings($shipping_method_array);
        $method_default = isset($shipping_method_array['method_default']) ? $shipping_method_array['method_default'] : 'no';
        $method_default = $method_default_disable ? 'no' : $method_default;
        return new \FSVendor\WPDesk\FS\TableRate\Settings\MethodSettingsImplementation($shipping_method_array, isset($shipping_method_array['id']) ? $shipping_method_array['id'] : 'no', isset($shipping_method_array['method_enabled']) ? $shipping_method_array['method_enabled'] : 'yes', isset($shipping_method_array['method_title']) ? $shipping_method_array['method_title'] : '', isset($shipping_method_array['method_description']) ? $shipping_method_array['method_description'] : '', isset($shipping_method_array['tax_status']) ? $shipping_method_array['tax_status'] : '', isset($shipping_method_array['prices_include_tax']) ? $shipping_method_array['prices_include_tax'] : 'no', isset($shipping_method_array['method_free_shipping']) ? $shipping_method_array['method_free_shipping'] : '', isset($shipping_method_array['method_free_shipping_label']) ? $shipping_method_array['method_free_shipping_label'] : '', isset($shipping_method_array['method_free_shipping_cart_notice']) ? $shipping_method_array['method_free_shipping_cart_notice'] : 'no', isset($shipping_method_array['method_calculation_method']) ? $shipping_method_array['method_calculation_method'] : 'sum', isset($shipping_method_array['cart_calculation']) ? $shipping_method_array['cart_calculation'] : 'cart', isset($shipping_method_array['method_visibility']) ? $shipping_method_array['method_visibility'] : 'no', $method_default, isset($shipping_method_array['method_debug_mode']) ? $shipping_method_array['method_debug_mode'] : 'no', isset($shipping_method_array['method_integration']) ? $shipping_method_array['method_integration'] : 'no', \FSVendor\WPDesk\FS\TableRate\Settings\IntegrationSettingsFactory::create_from_shipping_method_settings($shipping_method_array), isset($shipping_method_array['method_rules']) ? $shipping_method_array['method_rules'] : array());
    }
    /**
     * @param array $shipping_method_array
     *
     * @return MethodSettings
     */
    public static function create_from_array_and_tax_status($shipping_method_array, $tax_status)
    {
        $shipping_method_array['tax_status'] = $tax_status;
        return self::create_from_array($shipping_method_array, \false);
    }
    /**
     * If free shipping is 0 (zero) should get same results as empty value.
     *
     * @see https://trello.com/c/UNGnC093/3048-flexible-shipping-307-co%C5%9B-popsu%C5%82o
     *
     * @param array $shipping_method_array .
     *
     * @return array
     */
    private static function clean_settings(array $shipping_method_array)
    {
        if (!isset($shipping_method_array[self::FIELD_METHOD_FREE_SHIPPING]) || '0' === \trim($shipping_method_array[self::FIELD_METHOD_FREE_SHIPPING])) {
            $shipping_method_array[self::FIELD_METHOD_FREE_SHIPPING] = '';
        }
        return $shipping_method_array;
    }
}
