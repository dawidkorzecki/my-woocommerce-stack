<?php

/**
 * Class CartCalculationOptions
 *
 * @package WPDesk\FS\TableRate\Settings
 */
namespace FSVendor\WPDesk\FS\TableRate\Settings;

use FSVendor\WPDesk\FS\TableRate\AbstractOptions;
/**
 * Can provide cart calculation options.
 */
class CartCalculationOptions extends \FSVendor\WPDesk\FS\TableRate\AbstractOptions
{
    const CART = 'cart';
    const PACKAGE = 'package';
    /**
     * @return array
     */
    public function get_options()
    {
        return array(self::CART => \__('Cart value', 'flexible-shipping'), self::PACKAGE => \__('Package value', 'flexible-shipping'));
    }
}
