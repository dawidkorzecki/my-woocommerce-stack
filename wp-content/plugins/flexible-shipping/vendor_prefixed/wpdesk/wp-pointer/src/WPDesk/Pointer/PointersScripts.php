<?php

namespace FSVendor\WPDesk\Pointer;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Pointers handler.
 *
 * @package WPDesk\Pointer
 */
class PointersScripts implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var array
     */
    private $enqueueOnScreens = array();
    /**
     * PointersScripts constructor.
     *
     * @param null|string|array $enqueueOnScreens Empty for all screens.
     */
    public function __construct($enqueueOnScreens = array())
    {
        if (null === $enqueueOnScreens) {
            $enqueueOnScreens = array();
        }
        if (!\is_array($enqueueOnScreens)) {
            $enqueueOnScreens = array($enqueueOnScreens);
        }
        $this->enqueueOnScreens = $enqueueOnScreens;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }
    /**
     * Enqueue scripts.
     *
     * @param string $hook
     */
    public function enqueueScripts($hook)
    {
        if (\count($this->enqueueOnScreens) === 0 || \in_array($hook, $this->enqueueOnScreens, \true)) {
            \wp_enqueue_style('wp-pointer');
            \wp_enqueue_script('wp-pointer');
        }
    }
}
