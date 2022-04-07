<?php

/**
 * Class Assets
 * @package WPDesk\FS\TableRate\Logger
 */
namespace FSVendor\WPDesk\FS\TableRate\Logger;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can enqueue assets.
 */
class Assets implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const HANDLE = 'flexible_shipping_notices';
    private $assets_url;
    private $scripts_version;
    /**
     * Assets constructor.
     *
     * @param $assets_url
     * @param $scripts_version
     */
    public function __construct($assets_url, $scripts_version)
    {
        $this->assets_url = $assets_url;
        $this->scripts_version = $scripts_version;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    /**
     * Enqueue assets.
     *
     * @internal
     */
    public function enqueue_assets()
    {
        if (\current_user_can('manage_woocommerce')) {
            \wp_register_script(self::HANDLE, \trailingslashit($this->assets_url) . 'js/notices.js', array('jquery'), $this->scripts_version, \true);
            \wp_enqueue_script(self::HANDLE);
            \wp_register_style(self::HANDLE, \trailingslashit($this->assets_url) . 'css/notices.css', array(), $this->scripts_version);
            \wp_enqueue_style(self::HANDLE);
        }
    }
}
