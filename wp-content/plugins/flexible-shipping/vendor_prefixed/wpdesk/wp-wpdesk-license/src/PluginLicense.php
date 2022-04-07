<?php

namespace FSVendor\WPDesk\License;

use FSVendor\WPDesk_Plugin_Info;
/**
 * Provides plugin license information.
 */
class PluginLicense
{
    /**
     * @var WPDesk_Plugin_Info
     */
    private $plugin_info;
    /**
     * @param WPDesk_Plugin_Info $info
     */
    public function __construct(\FSVendor\WPDesk_Plugin_Info $info)
    {
        $this->plugin_info = $info;
    }
    /**
     * @return bool
     */
    public function is_active()
    {
        return \get_option($this->prepare_option_is_active()) === 'Activated';
    }
    /**
     * @return string
     */
    public function get_api_key()
    {
        $api_option = $this->get_api_option();
        $field_key = $this->prepare_option_name('key');
        return isset($api_option[$field_key]) ? $api_option[$field_key] : '';
    }
    /**
     * @return string
     */
    public function get_activation_email()
    {
        $api_option = $this->get_api_option();
        $field_key = $this->prepare_option_name('activation_email');
        return isset($api_option[$field_key]) ? $api_option[$field_key] : '';
    }
    /**
     * @return array
     */
    private function get_api_option()
    {
        $option_value = \get_option(\sprintf('api_%1$s', \basename($this->plugin_info->get_plugin_dir())), []);
        return \is_array($option_value) ? $option_value : [];
    }
    /**
     * @return string
     */
    private function prepare_option_is_active()
    {
        return $this->prepare_option_name('activated');
    }
    /**
     * @param string $field .
     *
     * @return string
     */
    private function prepare_option_name($field)
    {
        return \sprintf('api_%1$s_%2$s', \basename($this->plugin_info->get_plugin_slug()), $field);
    }
}
