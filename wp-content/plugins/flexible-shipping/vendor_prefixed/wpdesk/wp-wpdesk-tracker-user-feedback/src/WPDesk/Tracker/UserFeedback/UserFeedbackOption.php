<?php

namespace FSVendor\WPDesk\Tracker\UserFeedback;

class UserFeedbackOption
{
    /**
     * @var string
     */
    private $option_name;
    /**
     * @var string
     */
    private $option_text;
    /**
     * @var bool
     */
    private $has_additional_info;
    /**
     * @var string
     */
    private $additional_info_placeholder;
    /**
     * UserFeedbackOption constructor.
     *
     * @param string $option_name
     * @param string $option_text
     * @param bool $has_additional_info
     * @param string $additional_info_placeholder
     */
    public function __construct($option_name, $option_text, $has_additional_info = \false, $additional_info_placeholder = '')
    {
        $this->option_name = $option_name;
        $this->option_text = $option_text;
        $this->has_additional_info = $has_additional_info;
        $this->additional_info_placeholder = $additional_info_placeholder;
    }
    /**
     * @return string
     */
    public function get_option_name()
    {
        return $this->option_name;
    }
    /**
     * @return string
     */
    public function get_option_text()
    {
        return $this->option_text;
    }
    /**
     * @return bool
     */
    public function has_additional_info()
    {
        return $this->has_additional_info;
    }
    /**
     * @return string
     */
    public function get_additional_info_placeholder()
    {
        return $this->additional_info_placeholder;
    }
}
