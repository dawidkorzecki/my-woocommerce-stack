<?php

namespace FSVendor\WPDesk\PluginBuilder\Builder;

use FSVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use FSVendor\WPDesk\PluginBuilder\Storage\PluginStorage;
abstract class AbstractBuilder
{
    /**
     * Create plugin class
     */
    public function build_plugin()
    {
    }
    /**
     * Store plugin class in some kind of storage
     */
    public function store_plugin(\FSVendor\WPDesk\PluginBuilder\Storage\PluginStorage $storage)
    {
    }
    /**
     * Init plugin internal structure
     */
    public function init_plugin()
    {
    }
    /**
     * Return built plugin
     * @return AbstractPlugin
     */
    abstract function get_plugin();
    /**
     * Set settings class in plugin
     *
     * @param $settings
     */
    public function set_settings($settings)
    {
    }
    /**
     * Set view class in plugin
     *
     * @param $view
     */
    public function set_view($view)
    {
    }
    /**
     * Set tracker class in plugin
     *
     * @param $tracker
     */
    public function set_tracker($tracker)
    {
    }
    /**
     * Set helper class in plugin
     *
     * @param $helper
     */
    public function set_helper($helper)
    {
    }
}
