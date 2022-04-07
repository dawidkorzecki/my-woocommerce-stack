<?php

/**
 * Notice
 *
 * @package WPDesk\FS\Compatibility
 */
namespace FSVendor\WPDesk\FS\Compatibility;

/**
 * Can display notice about incompatible plugins.
 */
class Notice
{
    /**
     * @var PluginCompatibilityChecker .
     */
    private $plugin_compatibility_checker;
    /**
     * Notice constructor.
     *
     * @param PluginCompatibilityChecker $plugin_compatibility_checker .
     */
    public function __construct(\FSVendor\WPDesk\FS\Compatibility\PluginCompatibilityChecker $plugin_compatibility_checker)
    {
        $this->plugin_compatibility_checker = $plugin_compatibility_checker;
    }
    /**
     * Add hooks.
     */
    public function hooks()
    {
        \add_action('admin_notices', array($this, 'admin_notices'));
    }
    /**
     * Display notices in admin.
     */
    public function admin_notices()
    {
        $status = (bool) \apply_filters('plugin_compatibility_checker/notice_added', \false);
        if ($status) {
            return;
        }
        \add_filter('plugin_compatibility_checker/notice_added', '__return_true');
        $checker = $this->plugin_compatibility_checker;
        $additional_info = ' ' . \sprintf(\__('If the WordPress updater hasn\'t informed you about the newer versions available, please %sfollow these instructions &rarr;%s', 'flexible-shipping'), \sprintf('<a href="%s" target="_blank">', \__('https://wpde.sk/fs-2-docs', 'flexible-shipping')), '</a>');
        if (!$checker->is_fs_compatible()) {
            if ($checker->is_active_fs_pro() && $checker->is_fs_pro_compatible() && $checker->is_active_fs_loc() && $checker->is_fs_loc_compatible()) {
                new \FSVendor\WPDesk\Notice\Notice(\sprintf(\__('%sFlexible Shipping%s plugin you are currently using is not compatible with the installed version of Flexible Shipping PRO and Flexible Shipping Locations. Please update the %sFlexible Shipping%s plugin to %s version or newer.', 'flexible-shipping'), '<strong>', '</strong>', '<strong>', '</strong>', $checker->fs->get_required_version()) . $additional_info, 'error');
            } elseif ($checker->is_active_fs_pro() && $checker->is_fs_pro_compatible()) {
                new \FSVendor\WPDesk\Notice\Notice(\sprintf(\__('%sFlexible Shipping%s plugin you are currently using is not compatible with the installed version of Flexible Shipping PRO. Please update the %sFlexible Shipping%s plugin to %s version or newer.', 'flexible-shipping'), '<strong>', '</strong>', '<strong>', '</strong>', $checker->fs->get_required_version()) . $additional_info, 'error');
            } elseif ($checker->is_active_fs_loc() && $checker->is_fs_loc_compatible()) {
                new \FSVendor\WPDesk\Notice\Notice(\sprintf(\__('%sFlexible Shipping%s plugin you are currently using is not compatible with the installed version of Flexible Shipping Locations. Please update the %sFlexible Shipping%s plugin to %s version or newer.', 'flexible-shipping'), '<strong>', '</strong>', '<strong>', '</strong>', $checker->fs->get_required_version()) . $additional_info, 'error');
            }
        } else {
            if (!$checker->is_fs_pro_compatible()) {
                new \FSVendor\WPDesk\Notice\Notice(\sprintf(\__('%sFlexible Shipping PRO%s plugin you are currently using is not compatible with the installed version of Flexible Shipping free. Please update the %sFlexible Shipping PRO%s plugin to %s version or newer.', 'flexible-shipping'), '<strong>', '</strong>', '<strong>', '</strong>', $checker->fs_pro->get_required_version()) . $additional_info, 'error');
            }
            if (!$checker->is_fs_loc_compatible()) {
                new \FSVendor\WPDesk\Notice\Notice(\sprintf(\__('%sFlexible Shipping Locations%s plugin you are currently using is not compatible with the installed version of Flexible Shipping free. Please update the %sFlexible Shipping Locations%s plugin to %s version or newer.', 'flexible-shipping'), '<strong>', '</strong>', '<strong>', '</strong>', $checker->fs_loc->get_required_version()) . $additional_info, 'error');
            }
        }
    }
}
