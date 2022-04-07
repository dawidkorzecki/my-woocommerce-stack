<?php

/**
 * Null sender.
 *
 * @package WPDesk\Tracker\Sender
 */
namespace FSVendor\WPDesk\Tracker\Sender;

/**
 * Can send data to nowhere.
 */
class NullSender implements \WPDesk_Tracker_Sender
{
    /**
     * Does nothing.
     *
     * @param array $payload .
     *
     * @return array|void
     */
    public function send_payload(array $payload)
    {
        // Do nothing
    }
}
