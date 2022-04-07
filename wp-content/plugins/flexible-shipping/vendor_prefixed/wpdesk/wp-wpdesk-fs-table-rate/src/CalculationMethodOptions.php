<?php

/**
 * Class CartCalculationOptions
 *
 * @package WPDesk\FSPro\TableRate
 */
namespace FSVendor\WPDesk\FS\TableRate;

/**
 * Can provide calculation method options.
 */
class CalculationMethodOptions extends \FSVendor\WPDesk\FS\TableRate\AbstractOptions
{
    /**
     * @return array
     */
    public function get_options()
    {
        return array('sum' => \__('Sum', 'flexible-shipping'));
    }
}
