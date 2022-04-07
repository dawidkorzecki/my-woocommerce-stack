<?php

/**
 * Trait CheckboxValue
 *
 * @package WPDesk\FS\TableRate\Settings
 */
namespace FSVendor\WPDesk\FS\TableRate\Settings;

/**
 * Checkbox value methods.
 */
trait CheckboxValue
{
    /**
     * @param $checkbox_value
     *
     * @return string
     */
    protected function get_as_translated_checkbox_value($checkbox_value)
    {
        if (\in_array($checkbox_value, array('yes', 'no'))) {
            return 'yes' === $checkbox_value ? \__('yes', 'flexible-shipping') : \__('no', 'flexible-shipping');
        }
        return $checkbox_value;
    }
}
