<?php

namespace FSVendor\WPDesk\Nps;

/**
 * Handles feedback option.
 */
class FeedbackOption implements \JsonSerializable
{
    /**
     * @var string
     */
    private $label;
    /**
     * @var string
     */
    private $additional_question;
    /**
     * @param string $label
     * @param string $additional_question
     */
    public function __construct(string $label, string $additional_question = '')
    {
        $this->label = $label;
        $this->additional_question = $additional_question;
    }
    /**
     * @return string
     */
    public function get_label() : string
    {
        return $this->label;
    }
    /**
     * @return string
     */
    public function get_additional_question() : string
    {
        return $this->additional_question;
    }
    public function jsonSerialize()
    {
        return ['label' => $this->label, 'additional_question' => $this->additional_question];
    }
}
