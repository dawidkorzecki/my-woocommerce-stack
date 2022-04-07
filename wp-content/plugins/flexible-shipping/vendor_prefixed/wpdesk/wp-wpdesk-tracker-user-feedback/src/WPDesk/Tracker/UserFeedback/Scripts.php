<?php

namespace FSVendor\WPDesk\Tracker\UserFeedback;

/**
 * Can generate user feedback scripts.
 */
class Scripts
{
    use UserFeedbackContent;
    /**
     * Constructor.
     *
     * @param UserFeedbackData $user_feedback_data .
     * @param string|null $view_file If null given default scrips file is used.
     */
    public function __construct(\FSVendor\WPDesk\Tracker\UserFeedback\UserFeedbackData $user_feedback_data, $view_file = null)
    {
        $this->user_feedback_data = $user_feedback_data;
        if (!empty($view_file)) {
            $this->view_file = $view_file;
        } else {
            $this->view_file = __DIR__ . '/views/scripts.php';
        }
    }
}
