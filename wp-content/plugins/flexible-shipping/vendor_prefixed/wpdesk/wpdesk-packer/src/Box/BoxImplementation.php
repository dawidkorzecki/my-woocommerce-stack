<?php

namespace FSVendor\WPDesk\Packer\Box;

use FSVendor\WPDesk\Packer\Box;
use FSVendor\WPDesk\Packer\Item\ItemImplementation;
class BoxImplementation extends \FSVendor\WPDesk\Packer\Item\ItemImplementation implements \FSVendor\WPDesk\Packer\Box
{
    /** @var float|null */
    private $max_weight;
    /** @var string */
    private $name;
    /** @var string */
    private $id;
    /**
     * BoxImplementation constructor.
     *
     * @param float $length
     * @param float $width
     * @param float $height
     * @param float $box_weight
     * @param float|null $max_weight
     * @param string $id
     * @param string $name
     * @param mixed $internal_data
     */
    public function __construct($length, $width, $height, $box_weight, $max_weight, $id, $name = '', $internal_data = null)
    {
        parent::__construct($length, $width, $height, $box_weight, 0, $internal_data);
        $this->max_weight = $max_weight;
        $this->id = $id;
        $this->name = $name;
    }
    /**
     * @return float|null
     */
    public function get_max_weight()
    {
        return $this->max_weight;
    }
    /**
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }
    /**
     * @return string
     */
    public function get_unique_id()
    {
        return $this->id;
    }
}
