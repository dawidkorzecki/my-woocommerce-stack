<?php

namespace FSVendor\WPDesk\Tracker\UserFeedback;

/**
 * Can generate content.
 */
trait UserFeedbackContent
{
    /**
     * @var UserFeedbackData
     */
    private $user_feedback_data;
    /**
     * @var string
     */
    private $view_file = __DIR__ . '/views/abstract.php';
    /**
     * Returns HTML content.
     *
     * @return string
     */
    public function get_content()
    {
        $thickbox_id = $this->user_feedback_data->get_thickbox_id();
        $thickbox_title = $this->user_feedback_data->get_thickbox_title();
        $thickbox_heading = $this->user_feedback_data->get_heading();
        $thickbox_question = $this->user_feedback_data->get_question();
        $thickbox_feedback_options = $this->user_feedback_data->get_feedback_options();
        $thickbox_all_options = \count($thickbox_feedback_options);
        $button_send_text = \__('Proceed', 'flexible-shipping');
        $ajax_action = \FSVendor\WPDesk\Tracker\UserFeedback\AjaxUserFeedbackDataHandler::AJAX_ACTION . $thickbox_id;
        $ajax_nonce = \wp_create_nonce($ajax_action);
        \ob_start();
        include $this->view_file;
        return \ob_get_clean();
    }
}
