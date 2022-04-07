<?php

/**
 * Interface CanFormatForLog
 * @package WPDesk\FS\TableRate\Logger
 */
namespace FSVendor\WPDesk\FS\TableRate\Logger;

/**
 * Can format for log.
 */
interface CanFormatForLog
{
    /**
     * @return string
     */
    public function format_for_log();
}
