<?php

/**
 * Plugin Compatibility Checker
 *
 * @package WPDesk\FS\TableRate
 */
namespace FSVendor\WPDesk\FS\Compatibility;

/**
 * Class PluginCompatibilityChecker
 *
 * @package WPDesk\FS\TableRate
 */
class PluginCompatibilityChecker
{
    /**
     * @var PluginDetails .
     */
    public $fs;
    /**
     * @var PluginDetails .
     */
    public $fs_pro;
    /**
     * @var PluginDetails .
     */
    public $fs_loc;
    /**
     * PluginCompatibilityChecker constructor.
     */
    public function __construct()
    {
        $this->fs = new \FSVendor\WPDesk\FS\Compatibility\PluginDetails('flexible-shipping/flexible-shipping.php', 'FLEXIBLE_SHIPPING_VERSION', '4.0.0');
        $this->fs_pro = new \FSVendor\WPDesk\FS\Compatibility\PluginDetails('flexible-shipping-pro/flexible-shipping-pro.php', 'FLEXIBLE_SHIPPING_PRO_VERSION', '2.0.0');
        $this->fs_loc = new \FSVendor\WPDesk\FS\Compatibility\PluginDetails('flexible-shipping-locations/flexible-shipping-locations.php', 'FLEXIBLE_SHIPPING_LOCATIONS_VERSION', '2.0.0');
    }
    /**
     * @return bool
     */
    public function is_active_fs()
    {
        return $this->fs->is_active();
    }
    /**
     * @return bool
     */
    public function is_active_fs_pro()
    {
        return $this->fs_pro->is_active();
    }
    /**
     * @return bool
     */
    public function is_active_fs_loc()
    {
        return $this->fs_loc->is_active();
    }
    /**
     * @return bool
     */
    public function are_plugins_compatible()
    {
        return $this->is_fs_compatible() && $this->is_fs_pro_compatible() && $this->is_fs_loc_compatible();
    }
    /**
     * @return bool
     */
    public function is_fs_compatible()
    {
        return $this->check_plugin($this->fs);
    }
    /**
     * @return bool
     */
    public function is_fs_pro_compatible()
    {
        return $this->check_plugin($this->fs_pro);
    }
    /**
     * @return bool
     */
    public function is_fs_loc_compatible()
    {
        return $this->check_plugin($this->fs_loc);
    }
    /**
     * @return array
     */
    public function get_list_of_incompatible_plugins()
    {
        $plugins_list = [];
        if (!$this->is_fs_compatible()) {
            $plugins_list[] = \__('Flexible Shipping', 'flexible-shipping');
        }
        if (!$this->is_fs_pro_compatible()) {
            $plugins_list[] = \__('Flexible Shipping PRO', 'flexible-shipping');
        }
        if (!$this->is_fs_loc_compatible()) {
            $plugins_list[] = \__('Flexible Shipping Locations', 'flexible-shipping');
        }
        return $plugins_list;
    }
    /**
     * @param PluginDetails $plugin .
     *
     * @return bool
     */
    private function check_plugin($plugin)
    {
        if (!$plugin->is_active()) {
            return \true;
        }
        return $plugin->is_compatible();
    }
}
