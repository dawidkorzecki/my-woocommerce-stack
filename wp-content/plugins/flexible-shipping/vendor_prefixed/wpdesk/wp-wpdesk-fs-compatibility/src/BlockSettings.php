<?php

/**
 * Block Settings
 *
 * @package WPDesk\FS\Compatibility
 */
namespace FSVendor\WPDesk\FS\Compatibility;

use WC_Shipping_Zones;
use FSVendor\WPDesk_Flexible_Shipping;
/**
 * Can prevent Flexible Shipping settings saving.
 */
class BlockSettings
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
        \add_action('admin_init', array($this, 'block_save_settings'));
        \add_action('flexible-shipping/method-rules-settings/table/before', array($this, 'add_flexible_shipping_method_message'));
        \add_action('flexible_shipping_method_script', array($this, 'add_flexible_shipping_method_message'));
    }
    /**
     * Action when setting are saving.
     */
    public function block_save_settings()
    {
        if (!isset($_POST['save'], $_POST['settings_saving_block'])) {
            return;
        }
        $tab = \filter_input(\INPUT_GET, 'tab');
        $page = \filter_input(\INPUT_GET, 'page');
        if ('wc-settings' !== $page || 'shipping' !== $tab) {
            return;
        }
        $_wpnonce = \filter_input(\INPUT_POST, '_wpnonce');
        if (!\wp_verify_nonce(\wp_unslash($_wpnonce), 'woocommerce-settings')) {
            return;
        }
        $method_title = \filter_input(\INPUT_POST, 'woocommerce_flexible_shipping_method_title');
        $instance_id = \absint(\wp_unslash(\filter_input(\INPUT_GET, 'instance_id')));
        if (!$method_title || !$instance_id) {
            return;
        }
        $shipping_method = \WC_Shipping_Zones::get_shipping_method($instance_id);
        if (!$shipping_method) {
            return;
        }
        \wp_die($this->get_general_message(), '', array('link_url' => $this->get_plugin_update_url(), 'link_text' => $this->get_plugin_update_label(), 'back_link' => \true));
        // WPCS: XSS OK.
    }
    /**
     * Add scripts for FS method.
     */
    public function add_flexible_shipping_method_message()
    {
        $status = (bool) \apply_filters('plugin_compatibility_checker/js_added', \false);
        if ($status) {
            return;
        }
        $action = \filter_input(\INPUT_GET, 'action');
        if (!$action) {
            return;
        }
        \add_filter('plugin_compatibility_checker/js_added', '__return_true');
        $message = $this->get_message_for_settings();
        include \wp_normalize_path(__DIR__ . '/views/html-update-settings-alert.php');
    }
    /**
     * Message for incompatible plugins.
     *
     * @return string
     */
    private function get_message_for_settings()
    {
        return \sprintf('%s %s', $this->get_general_message(), \sprintf('%s%s%s', \sprintf('<a target="_blank" href="%s">', $this->get_plugin_update_url()), $this->get_plugin_update_label(), '</a>'));
    }
    /**
     * @return string
     */
    private function get_general_message()
    {
        $plugins = \implode(', ', $this->plugin_compatibility_checker->get_list_of_incompatible_plugins());
        return \sprintf(\__('In order to prevent any further issues with the plugin configuration or its proper functioning, before saving the changes please update the following: %s.', 'flexible-shipping'), \sprintf('<strong>%s</strong>', $plugins));
    }
    /**
     * @return string
     */
    private function get_plugin_update_label()
    {
        return \__('Go to the plugins list &rarr;', 'flexible-shipping');
    }
    /**
     * @return string
     */
    private function get_plugin_update_url()
    {
        return \add_query_arg('s', 'Flexible Shipping', \admin_url('plugins.php'));
    }
}
