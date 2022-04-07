<?php

namespace FSVendor\WPDesk\Tracker\UserFeedback;

class UserFeedbackData
{
    /**
     * @var UserFeedbackOption[]
     */
    private $feedback_options = array();
    /**
     * @var string
     */
    private $thickbox_id;
    /**
     * @var string
     */
    private $thickbox_title;
    /**
     * @var string
     */
    private $heading;
    /**
     * @var string
     */
    private $question;
    /**
     * @var string
     */
    private $hook_suffix;
    /**
     * UserFeedbackData constructor.
     *
     * @param string $thickbox_id
     * @param string $thickbox_title
     * @param string $heading
     * @param string $question
     * @param string $hook_suffix
     */
    public function __construct($thickbox_id, $thickbox_title, $heading, $question, $hook_suffix)
    {
        $this->thickbox_id = $thickbox_id;
        $this->thickbox_title = $thickbox_title;
        $this->heading = $heading;
        $this->question = $question;
        $this->hook_suffix = $hook_suffix;
    }
    /**
     * @param UserFeedbackOption $feedback_option
     *
     * @return UserFeedbackData
     */
    public function add_feedback_option(\FSVendor\WPDesk\Tracker\UserFeedback\UserFeedbackOption $feedback_option)
    {
        $this->feedback_options[] = $feedback_option;
        return $this;
    }
    /**
     * @return UserFeedbackOption[]
     */
    public function get_feedback_options()
    {
        return $this->feedback_options;
    }
    /**
     * @return string
     */
    public function get_thickbox_id()
    {
        return $this->thickbox_id;
    }
    /**
     * @return string
     */
    public function get_thickbox_title()
    {
        return $this->thickbox_title;
    }
    /**
     * @return string
     */
    public function get_heading()
    {
        return $this->heading;
    }
    /**
     * @return string
     */
    public function get_question()
    {
        return $this->question;
    }
    /**
     * @return string
     */
    public function get_hook_suffix()
    {
        return $this->hook_suffix;
    }
}
