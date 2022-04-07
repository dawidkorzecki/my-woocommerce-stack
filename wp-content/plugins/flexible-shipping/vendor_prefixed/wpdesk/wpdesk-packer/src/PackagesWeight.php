<?php

/**
 * Packages Weight.
 *
 * @package WPDesk\Packer
 */
namespace FSVendor\WPDesk\Packer;

/**
 * Can get packages weight.
 */
class PackagesWeight
{
    /**
     * Packages.
     *
     * @var PackedBox[]
     */
    private $packages;
    /**
     * Packages constructor.
     *
     * @param PackedBox[] $packages .
     */
    public function __construct($packages)
    {
        $this->packages = $packages;
    }
    /**
     * Get total packages weight.
     *
     * @return float
     */
    public function get_total_weight()
    {
        $total_weight = 0.0;
        foreach ($this->packages as $package) {
            $total_weight += $package->get_packed_weight();
        }
        return $total_weight;
    }
}
