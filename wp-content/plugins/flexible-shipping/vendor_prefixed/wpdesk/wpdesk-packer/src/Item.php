<?php

namespace FSVendor\WPDesk\Packer;

/**
 * Items can be packed inside a box.
 *
 * @package WPDesk\Packer
 */
interface Item
{
    /** @return float */
    public function get_volume();
    /** @return float */
    public function get_height();
    /** @return float */
    public function get_width();
    /** @return float */
    public function get_length();
    /** @return float */
    public function get_weight();
    /** @return float */
    public function get_value();
    /** @return mixed */
    public function get_internal_data();
}
