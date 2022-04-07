<?php

namespace FSVendor\WPDesk\License\Page\License\Action;

/**
 * Action error.
 */
class ActionError implements \JsonSerializable
{
    /**
     * @var string
     */
    private $message;
    /**
     * @var string
     */
    private $type;
    /**
     * @param string $message .
     * @param string $type .
     */
    public function __construct(string $message, string $type)
    {
        $this->message = $message;
        $this->type = $type;
    }
    /**
     * @return string
     */
    public function get_message() : string
    {
        return $this->message;
    }
    /**
     * @return string
     */
    public function get_type() : string
    {
        return $this->type;
    }
    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return ['message' => $this->message, 'type' => $this->type];
    }
}
