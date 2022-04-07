<?php

namespace FSVendor\WPDesk\Tracker\UserFeedback;

/**
 * Can generate user feedback thickbox content.
 */
class Thickbox
{
    use UserFeedbackContent;
    /**
     * Constructor.
     *
     * @param UserFeedbackData $user_feedback_data .
     * @param string|null $view_file If null given default thickbox file is used.
     */
    public function __construct(\FSVendor\WPDesk\Tracker\UserFeedback\UserFeedbackData $user_feedback_data, $view_file = null)
    {
        $thickbox_id = $user_feedback_data->get_thickbox_id();
        $this->user_feedback_data = $user_feedback_data;
        if (!empty($view_file)) {
            $this->view_file = $view_file;
        } else {
            $this->view_file = __DIR__ . '/views/thickbox.php';
        }
    }
}
