<?php

/**
 * Class IntegrationSettingsImplementation
 * @package WPDesk\FS\TableRate\Settings
 */
namespace FSVendor\WPDesk\FS\TableRate\Settings;

use FSVendor\WPDesk\FS\TableRate\Logger\CanFormatForLog;
/**
 * Integration settings implementation.
 */
class IntegrationSettingsImplementation implements \FSVendor\WPDesk\FS\TableRate\Settings\IntegrationSettings, \FSVendor\WPDesk\FS\TableRate\Logger\CanFormatForLog
{
    /**
     * @var string
     */
    private $name;
    /**
     * IntegrationSettingsImplementation constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    /**
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }
    /**
     * @return string
     */
    public function format_for_log()
    {
        $integrations_options = \apply_filters('flexible_shipping_integration_options', array('' => \__('None', 'flexible-shipping')));
        return \sprintf(\__('Integration: %1$s', 'flexible-shipping'), isset($integrations_options[$this->name]) ? $integrations_options[$this->name] : $this->name) . "\n";
    }
}
