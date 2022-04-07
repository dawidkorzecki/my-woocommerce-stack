<?php

/**
 * Based on options.
 *
 * @package WPDesk\FS\TableRate
 */
namespace FSVendor\WPDesk\FS\TableRate;

/**
 * Can provide Based On options.
 */
class BasedOnOptions extends \FSVendor\WPDesk\FS\TableRate\AbstractOptions
{
    /**
     * @return array
     */
    public function get_options()
    {
        return \apply_filters('flexible_shipping_method_rule_options_based_on', array('none' => \__('None', 'flexible-shipping'), 'value' => \__('Price', 'flexible-shipping'), 'weight' => \__('Weight', 'flexible-shipping')));
    }
}
