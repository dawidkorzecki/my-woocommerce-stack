<?php

/**
 * Boxes interface.
 *
 * @package WPDesk\Packer\BoxFactory
 */
namespace FSVendor\WPDesk\Packer\BoxFactory;

use FSVendor\WPDesk\Packer\Box;
/**
 * Boxes as array.
 */
interface Boxes
{
    /**
     * Get boxes array.
     *
     * @return Box[]
     */
    public function get_boxes();
}
