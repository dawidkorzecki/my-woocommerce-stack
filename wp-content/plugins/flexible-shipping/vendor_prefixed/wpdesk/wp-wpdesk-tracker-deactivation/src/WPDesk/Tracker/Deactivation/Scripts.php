<?php

namespace FSVendor\WPDesk\Tracker\Deactivation;

/**
 * Can generate deactivation scripts.
 */
class Scripts
{
    use DeactivationContent;
    /**
     * Constructor.
     *
     * @param PluginData  $plugin_data .
     * @param string|null $view_file .
     */
    public function __construct(\FSVendor\WPDesk\Tracker\Deactivation\PluginData $plugin_data, $view_file = null)
    {
        $this->plugin_data = $plugin_data;
        if (!empty($view_file)) {
            $this->view_file = $view_file;
        } else {
            $this->view_file = __DIR__ . '/views/scripts.php';
        }
    }
}
