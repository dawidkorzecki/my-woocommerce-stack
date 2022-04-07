<?php

/**
 * Based on options.
 *
 * @package WPDesk\FS\TableRate
 */
namespace FSVendor\WPDesk\FS\TableRate;

/**
 * Can provide options.
 */
abstract class AbstractOptions
{
    /**
     * @return array
     */
    public abstract function get_options();
    /**
     * @param $option_value
     *
     * @return string
     */
    public function get_option_label($option_value)
    {
        $options = $this->get_options();
        return isset($options[$option_value]) ? $options[$option_value] : $option_value;
    }
}
