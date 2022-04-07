<?php

namespace FSVendor\WPDesk\Plugin\Flow\Initialization;

use FSVendor\WPDesk\PluginBuilder\Plugin\Activateable;
use FSVendor\WPDesk\PluginBuilder\Plugin\Deactivateable;
use FSVendor\WPDesk\PluginBuilder\Plugin\SlimPlugin;
use FSVendor\WPDesk\PluginBuilder\Storage\StorageFactory;
/**
 * Helps with plugin building concepts.
 *
 * @package WPDesk\Plugin\Flow\Initialization
 */
trait BuilderTrait
{
    /**
     * Build plugin from info.
     *
     * @param \WPDesk_Plugin_Info $plugin_info
     *
     * @return SlimPlugin
     */
    private function build_plugin(\FSVendor\WPDesk_Plugin_Info $plugin_info)
    {
        $class_name = \apply_filters('wp_builder_plugin_class', $plugin_info->get_class_name());
        /** @var SlimPlugin $plugin */
        $plugin = new $class_name($plugin_info);
        return $plugin;
    }
    /**
     * Initialize WP register hooks that have to be fire before any other.
     *
     * @param \WPDesk_Plugin_Info $plugin_info
     * @param SlimPlugin $plugin
     *
     * @return SlimPlugin
     */
    private function init_register_hooks(\FSVendor\WPDesk_Plugin_Info $plugin_info, \FSVendor\WPDesk\PluginBuilder\Plugin\SlimPlugin $plugin)
    {
        if ($plugin instanceof \FSVendor\WPDesk\PluginBuilder\Plugin\Activateable) {
            \register_activation_hook($plugin_info->get_plugin_file_name(), [$plugin, 'activate']);
        }
        if ($plugin instanceof \FSVendor\WPDesk\PluginBuilder\Plugin\Deactivateable) {
            \register_deactivation_hook($plugin_info->get_plugin_file_name(), [$plugin, 'deactivate']);
        }
        return $plugin;
    }
    /**
     * Store plugin for others to use.
     *
     * @param SlimPlugin $plugin
     */
    private function store_plugin(\FSVendor\WPDesk\PluginBuilder\Plugin\SlimPlugin $plugin)
    {
        $storageFactory = new \FSVendor\WPDesk\PluginBuilder\Storage\StorageFactory();
        $storageFactory->create_storage()->add_to_storage(\get_class($plugin), $plugin);
    }
    /**
     * Init integration layer of the plugin.
     *
     * @param SlimPlugin $plugin
     */
    private function init_plugin(\FSVendor\WPDesk\PluginBuilder\Plugin\SlimPlugin $plugin)
    {
        \do_action('wp_builder_before_plugin_init', $plugin);
        $plugin->init();
        \do_action('wp_builder_before_init', $plugin);
    }
}
