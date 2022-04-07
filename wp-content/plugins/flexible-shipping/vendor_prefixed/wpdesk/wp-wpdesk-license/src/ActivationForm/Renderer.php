<?php

namespace FSVendor\WPDesk\License\ActivationForm;

use FSVendor\WPDesk_Plugin_Info;
class Renderer
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
    /**
     * @return string
     */
    public function render()
    {
        \ob_start();
        $this->output_render();
        return \ob_get_clean();
    }
    public function output_render()
    {
        $form_content = new \FSVendor\WPDesk\License\ActivationForm\FormContentRenderer($this->plugin_info);
        $plugin_slug = $this->plugin_info->get_plugin_slug();
        $plugin_file = $this->plugin_info->get_plugin_file_name();
        include __DIR__ . '/views/activation-form.php';
    }
}
