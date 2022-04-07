<?php

namespace FSVendor\WPDesk\Tracker\UserFeedback;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can handle ajax request with user feedback data and sends data to WP Desk.
 */
class AjaxUserFeedbackDataHandler implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const AJAX_ACTION = 'wpdesk_tracker_user_feedback_handler_';
    const REQUEST_ADDITIONAL_INFO = 'additional_info';
    const REQUEST_SELECTED_OPTION = 'selected_option';
    const FEEDBACK_ID = 'feedback_id';
    /**
     * @var UserFeedbackData
     */
    protected $user_feedback_data;
    /**
     * @var \WPDesk_Tracker_Sender
     */
    private $sender;
    /**
     * @param UserFeedbackData $user_feedback_data .
     * @param \WPDesk_Tracker_Sender $sender
     */
    public function __construct(\FSVendor\WPDesk\Tracker\UserFeedback\UserFeedbackData $user_feedback_data, $sender)
    {
        $this->user_feedback_data = $user_feedback_data;
        $this->sender = $sender;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('wp_ajax_' . self::AJAX_ACTION . $this->user_feedback_data->get_thickbox_id(), array($this, 'handle_ajax_request'));
    }
    /**
     * Prepare payload.
     *
     * @param array $request .
     *
     * @return array
     */
    private function prepare_payload(array $request)
    {
        $payload = array('click_action' => 'user_feedback', self::FEEDBACK_ID => $this->user_feedback_data->get_thickbox_id(), 'selected_option' => $request[self::REQUEST_SELECTED_OPTION]);
        if (!empty($request[self::REQUEST_ADDITIONAL_INFO])) {
            $payload['additional_info'] = $request[self::REQUEST_ADDITIONAL_INFO];
        }
        return \apply_filters('wpdesk_tracker_user_feedback_data', $payload);
    }
    /**
     * Send payload to WP Desk.
     *
     * @param array $payload
     */
    private function send_payload_to_wpdesk(array $payload)
    {
        $this->sender->send_payload($payload);
    }
    /**
     * Handle AJAX request.
     */
    public function handle_ajax_request()
    {
        \check_ajax_referer(self::AJAX_ACTION . $this->user_feedback_data->get_thickbox_id());
        if (isset($_REQUEST[self::REQUEST_SELECTED_OPTION])) {
            $payload = $this->prepare_payload($_REQUEST);
            $this->send_payload_to_wpdesk($this->prepare_payload($_REQUEST));
            \do_action('wpdesk_tracker_user_feedback_data_handled', $payload);
        }
    }
}
