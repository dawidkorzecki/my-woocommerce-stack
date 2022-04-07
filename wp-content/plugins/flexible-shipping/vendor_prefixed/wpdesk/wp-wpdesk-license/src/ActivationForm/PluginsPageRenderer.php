<?php

namespace FSVendor\WPDesk\License\ActivationForm;

use FSVendor\WPDesk\License\PluginLicense;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk_Plugin_Info;
/**
 * Can render activation form on plugins page.
 */
class PluginsPageRenderer implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var WPDesk_Plugin_Info
     */
    private $plugin_info;
    /**
     * @param WPDesk_Plugin_Info $plugin_info .
     */
    public function __construct(\FSVendor\WPDesk_Plugin_Info $plugin_info)
    {
        $this->plugin_info = $plugin_info;
    }
    public function hooks()
    {
        \add_action('after_plugin_row_' . $this->plugin_info->get_plugin_file_name(), [$this, 'display_activation_form_in_table_row'], 10, 3);
    }
    /**
     * Displays activation form.
     *
     * @param string $plugin_file .
     * @param array  $plugin_data .
     * @param string $status .
     */
    public function display_activation_form_in_table_row($plugin_file, $plugin_data, $status)
    {
        $this->output_render($plugin_file);
    }
    /**
     * @param string $plugin_file
     *
     * @return string
     */
    public function render(string $plugin_file)
    {
        \ob_start();
        $this->output_render($plugin_file);
        return \ob_get_clean();
    }
    public function output_render(string $plugin_file)
    {
        $plugin_license = new \FSVendor\WPDesk\License\PluginLicense($this->plugin_info);
        $plugin_slug = $this->plugin_info->get_plugin_slug();
        $form_content = new \FSVendor\WPDesk\License\ActivationForm\Renderer($this->plugin_info);
        $is_active = $plugin_license->is_active();
        include __DIR__ . '/views/plugins-page-row.php';
    }
}
