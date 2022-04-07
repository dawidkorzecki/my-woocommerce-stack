<?php

namespace FSVendor\WPDesk\Packer\Item;

use FSVendor\WPDesk\Packer\Item;
class ItemImplementation implements \FSVendor\WPDesk\Packer\Item
{
    /** @var float */
    private $weight, $height, $width, $length;
    /** @var float */
    private $volume;
    /** @var float */
    private $value;
    /** @var mixed */
    private $internal_data;
    /**
     * ItemImplementation constructor.
     *
     * @param float      $length .
     * @param float      $width .
     * @param float      $height .
     * @param float      $weight .
     * @param float      $money_value Item money value.
     * @param null|mixed $internal_data .
     */
    public function __construct($length, $width, $height, $weight = 0.0, $money_value = 0.0, $internal_data = null)
    {
        $dimensions = [$length, $width, $height];
        \sort($dimensions);
        $this->length = (float) $dimensions[2];
        $this->width = (float) $dimensions[1];
        $this->height = (float) $dimensions[0];
        $this->volume = (float) ($width * $height * $length);
        $this->weight = (float) $weight;
        $this->value = (float) $money_value;
        $this->internal_data = $internal_data;
    }
    public function get_volume()
    {
        return $this->volume;
    }
    public function get_value()
    {
        return $this->value;
    }
    public function get_height()
    {
        return $this->height;
    }
    public function get_width()
    {
        return $this->width;
    }
    public function get_length()
    {
        return $this->length;
    }
    public function get_weight()
    {
        return $this->weight;
    }
    public function get_internal_data()
    {
        return $this->internal_data;
    }
}
