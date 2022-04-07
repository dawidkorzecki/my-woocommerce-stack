<?php

namespace FSVendor\WPDesk\Packer\BoxFactory;

use FSVendor\WPDesk\Packer\Box\BoxImplementation;
use FSVendor\WPDesk\Packer\Packer;
/**
 * Appends known boxes to Packer.
 */
abstract class BoxFactory
{
    /**
     * @var array
     */
    protected $boxes = [];
    /**
     * Append boxes.
     *
     * @param Packer $boxpack .
     */
    public function append_all_boxes(\FSVendor\WPDesk\Packer\Packer $boxpack)
    {
        foreach ($this->boxes as $key => $box) {
            $this->append_box($boxpack, $key, $box);
        }
    }
    /**
     * Append single box.
     *
     * @param Packer $boxpack .
     * @param string $key .
     */
    public function append_single_box(\FSVendor\WPDesk\Packer\Packer $boxpack, $key)
    {
        if (isset($this->boxes[$key])) {
            $this->append_box($boxpack, $key, $this->boxes[$key]);
        }
    }
    /**
     * Append box.
     *
     * @param Packer $boxpack .
     * @param string $key .
     * @param array  $box .
     */
    protected function append_box(\FSVendor\WPDesk\Packer\Packer $boxpack, $key, $box)
    {
        $newbox = new \FSVendor\WPDesk\Packer\Box\BoxImplementation($box['length'], $box['width'], $box['height'], 0, $box['weight'], array('id' => $key, 'box' => $box));
        $boxpack->add_box($newbox);
    }
    /**
     * Convert dimension.
     *
     * @param mixed  $dim       Dimension (length, width, or height).
     * @param string $from_unit Base unit to convert dimension from.
     * @param string $to_unit   Target unit to convert dimension to.
     *
     * @return float
     */
    protected function get_dimension($dim, $from_unit = 'in', $to_unit = 'in')
    {
        // Unify all units to cm first.
        if ($from_unit !== $to_unit) {
            switch ($from_unit) {
                case 'in':
                    $dim *= 2.54;
                    break;
                case 'm':
                    $dim *= 100;
                    break;
                case 'mm':
                    $dim *= 0.1;
                    break;
                case 'yd':
                    $dim *= 91.44;
                    break;
            }
            // Output desired unit.
            switch ($to_unit) {
                case 'in':
                    $dim *= 0.3937;
                    break;
                case 'm':
                    $dim *= 0.01;
                    break;
                case 'mm':
                    $dim *= 10;
                    break;
                case 'yd':
                    $dim *= 0.010936133;
                    break;
            }
        }
        return $dim < 0 ? 0 : $dim;
    }
    /**
     * Convert weight.
     *
     * @param float  $weight Weight.
     * @param string $from_unit Unit to convert from.
     * @param string $to_unit   Unit to convert to.
     * @return float
     */
    protected function get_weight($weight, $from_unit = 'lbs', $to_unit = 'lbs')
    {
        $weight = (float) $weight;
        $from_unit = \strtolower($from_unit);
        $to_unit = \strtolower($to_unit);
        // Unify all units to kg first.
        if ($from_unit !== $to_unit) {
            switch ($from_unit) {
                case 'g':
                    $weight *= 0.001;
                    break;
                case 'lbs':
                    $weight *= 0.453592;
                    break;
                case 'oz':
                    $weight *= 0.0283495;
                    break;
            }
            // Output desired unit.
            switch ($to_unit) {
                case 'g':
                    $weight *= 1000;
                    break;
                case 'lbs':
                    $weight *= 2.20462;
                    break;
                case 'oz':
                    $weight *= 35.274;
                    break;
            }
        }
        return $weight < 0 ? 0 : $weight;
    }
}
