<?php

namespace FSVendor\WPDesk\Tracker;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can enqueue assets.
 */
class Assets implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var string
     */
    private $script_version = '1';
    /**
     * @var string
     */
    private $plugin_slug;
    /**
     * @param string $plugin_slug
     */
    public function __construct($plugin_slug)
    {
        $this->plugin_slug = $plugin_slug;
    }
    public function hooks()
    {
        \add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }
    public function admin_enqueue_scripts()
    {
        $screen = \get_current_screen();
        if ($screen->id == 'admin_page_wpdesk_tracker_' . $this->plugin_slug) {
            $handle = 'wpdesk-helper-tracker_' . $this->plugin_slug;
            \wp_register_style($handle, \plugin_dir_url(__FILE__) . '../../../assets/css/tracker.css', array(), $this->script_version);
            \wp_enqueue_style($handle);
        }
    }
}
