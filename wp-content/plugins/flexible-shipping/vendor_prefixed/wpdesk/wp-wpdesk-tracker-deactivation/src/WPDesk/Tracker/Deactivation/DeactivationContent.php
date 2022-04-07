<?php

namespace FSVendor\WPDesk\Tracker\Deactivation;

/**
 * Can generate content.
 */
trait DeactivationContent
{
    /**
     * @var PluginData
     */
    private $plugin_data;
    /**
     * @var string
     */
    private $view_file = __DIR__ . '/views/abstract.php';
    /**
     * Returns HTML content.
     *
     * @return string
     */
    public function getContent()
    {
        $plugin_title = $this->plugin_data->getPluginTitle();
        $plugin_file = $this->plugin_data->getPluginFile();
        $plugin_slug = $this->plugin_data->getPluginSlug();
        $thickbox_id = 'tracker-tb-' . $this->plugin_data->getPluginSlug();
        $ajax_action = \FSVendor\WPDesk\Tracker\Deactivation\AjaxDeactivationDataHandler::AJAX_ACTION . $this->plugin_data->getPluginSlug();
        $ajax_nonce = \wp_create_nonce($ajax_action);
        \ob_start();
        include $this->view_file;
        return \ob_get_clean();
    }
}
