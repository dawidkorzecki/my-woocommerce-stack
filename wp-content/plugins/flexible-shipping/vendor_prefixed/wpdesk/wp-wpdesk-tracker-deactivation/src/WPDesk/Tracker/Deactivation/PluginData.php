<?php

namespace FSVendor\WPDesk\Tracker\Deactivation;

/**
 * Holds plugin data.
 */
class PluginData
{
    /**
     * @var string
     */
    private $plugin_slug;
    /**
     * @var string
     */
    private $plugin_file;
    /**
     * @var string
     */
    private $plugin_title;
    /**
     * PluginData constructor.
     *
     * @param string $plugin_slug .
     * @param string $plugin_file .
     * @param string $plugin_title .
     */
    public function __construct($plugin_slug, $plugin_file, $plugin_title)
    {
        $this->plugin_slug = $plugin_slug;
        $this->plugin_file = $plugin_file;
        $this->plugin_title = $plugin_title;
    }
    /**
     * @return string
     */
    public function getPluginSlug()
    {
        return $this->plugin_slug;
    }
    /**
     * @param string $plugin_slug
     */
    public function setPluginSlug($plugin_slug)
    {
        $this->plugin_slug = $plugin_slug;
    }
    /**
     * @return string
     */
    public function getPluginFile()
    {
        return $this->plugin_file;
    }
    /**
     * @param string $plugin_file
     */
    public function setPluginFile($plugin_file)
    {
        $this->plugin_file = $plugin_file;
    }
    /**
     * @return string
     */
    public function getPluginTitle()
    {
        return $this->plugin_title;
    }
    /**
     * @param string $plugin_title
     */
    public function setPluginTitle($plugin_title)
    {
        $this->plugin_title = $plugin_title;
    }
}
