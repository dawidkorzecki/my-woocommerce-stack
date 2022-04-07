<?php

namespace FSVendor\WPDesk\License;

use FSVendor\WPDesk_Plugin_Info;
/**
 * Replaces WPDesk_Helper_Plugin. Gets info from plugin and sends it to subscription/update integrations
 *
 * @package WPDesk\License
 */
class PluginRegistrator
{
    /** @var WPDesk_Plugin_Info */
    private $plugin_info;
    /**
     * @var PluginLicense
     */
    private $plugin_license;
    public function __construct(\FSVendor\WPDesk_Plugin_Info $plugin_info)
    {
        $this->plugin_info = $plugin_info;
        $this->plugin_license = new \FSVendor\WPDesk\License\PluginLicense($plugin_info);
    }
    /**
     * @return bool
     */
    public function is_active()
    {
        return $this->plugin_license->is_active();
    }
    /**
     * Initializes license manager.
     */
    public function initialize_license_manager()
    {
        $license_manager = new \FSVendor\WPDesk\License\LicenseManager($this->plugin_info);
        $license_manager->init_activation_form();
        $license_manager->create_api_manager();
        $license_manager->hooks();
    }
}
