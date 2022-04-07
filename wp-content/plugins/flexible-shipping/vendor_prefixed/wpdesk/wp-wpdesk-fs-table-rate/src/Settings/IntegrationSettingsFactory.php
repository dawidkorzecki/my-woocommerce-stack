<?php

/**
 * Integration settings Factory.
 *
 * @package WPDesk\FS\TableRate\Settings
 */
namespace FSVendor\WPDesk\FS\TableRate\Settings;

/**
 * Can create Integration settings.
 */
class IntegrationSettingsFactory
{
    const INTEGRATION_NONE = 'none';
    /**
     * @param array $shipping_method_array
     *
     * @return IntegrationSettingsImplementation
     */
    public static function create_from_shipping_method_settings($shipping_method_array)
    {
        return new \FSVendor\WPDesk\FS\TableRate\Settings\IntegrationSettingsImplementation(isset($shipping_method_array['method_integration']) ? $shipping_method_array['method_integration'] : self::INTEGRATION_NONE);
    }
}
