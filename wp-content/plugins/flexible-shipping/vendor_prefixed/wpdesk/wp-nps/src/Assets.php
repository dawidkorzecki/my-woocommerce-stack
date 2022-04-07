<?php

namespace FSVendor\WPDesk\Nps;

use FSVendor\WPDesk\Nps\DisplayDecisions\DisplayNpsDecision;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can enqueue assets.
 */
class Assets implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var string
     */
    private $assets_url;
    /**
     * @var string
     */
    private $scripts_version;
    /**
     * @var DisplayNpsDecision
     */
    private $display_nps_decision;
    /**
     * Assets constructor.
     *
     * @param string $assets_url
     * @param string $scripts_version
     * @param DisplayNpsDecision $display_nps_decision
     */
    public function __construct($assets_url, $scripts_version, $display_nps_decision)
    {
        $this->assets_url = $assets_url;
        $this->scripts_version = $scripts_version;
        $this->display_nps_decision = $display_nps_decision;
    }
    public function hooks()
    {
        \add_action('admin_enqueue_scripts', array($this, 'enqueue_nps_scripts'));
    }
    /**
     * @internal
     */
    public function enqueue_nps_scripts()
    {
        if ($this->display_nps_decision->should_display_nps()) {
            \wp_register_script('wpdesk_nps', \trailingslashit($this->assets_url) . 'js/nps.js', array('jquery'), $this->scripts_version);
            \wp_enqueue_script('wpdesk_nps');
            \wp_enqueue_style('wpdesk_nps', \trailingslashit($this->assets_url) . 'css/nps.css', array(), $this->scripts_version);
        }
    }
}
