<?php

namespace FSVendor\WPDesk\Tracker\Deactivation;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can handle ajax request with plugin deactivation reason and sends data to WP Desk.
 */
class AjaxDeactivationDataHandler implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const AJAX_ACTION = 'wpdesk_tracker_deactivation_handler_';
    const REQUEST_ADDITIONAL_INFO = 'additional_info';
    /**
     * @var PluginData
     */
    protected $plugin_data;
    /**
     * @var \WPDesk_Tracker_Sender
     */
    private $sender;
    /**
     * DeactivationTracker constructor.
     *
     * @param PluginData $plugin_data .
     * @param \WPDesk_Tracker_Sender $sender
     */
    public function __construct(\FSVendor\WPDesk\Tracker\Deactivation\PluginData $plugin_data, \WPDesk_Tracker_Sender $sender)
    {
        $this->plugin_data = $plugin_data;
        $this->sender = $sender;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('wp_ajax_' . self::AJAX_ACTION . $this->plugin_data->getPluginSlug(), array($this, 'handleAjaxRequest'));
    }
    /**
     * Prepare payload.
     *
     * @param array $request .
     *
     * @return array
     */
    private function preparePayload(array $request)
    {
        $payload = array('click_action' => 'plugin_deactivation', 'plugin' => $this->plugin_data->getPluginFile(), 'plugin_name' => $this->plugin_data->getPluginTitle(), 'reason' => $request['reason']);
        if (!empty($request[self::REQUEST_ADDITIONAL_INFO])) {
            $payload['additional_info'] = $request[self::REQUEST_ADDITIONAL_INFO];
        }
        return \apply_filters('wpdesk_tracker_deactivation_data', $payload);
    }
    /**
     * Send payload to WP Desk.
     *
     * @param array $payload
     */
    private function sendPayloadToWpdesk(array $payload)
    {
        $this->sender->send_payload($payload);
    }
    /**
     * Handle AJAX request.
     */
    public function handleAjaxRequest()
    {
        \check_ajax_referer(self::AJAX_ACTION . $this->plugin_data->getPluginSlug());
        if (isset($_REQUEST['reason'])) {
            $this->sendPayloadToWpdesk($this->preparePayload($_REQUEST));
        }
    }
}
