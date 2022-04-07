<?php

namespace FSVendor\WPDesk\Packer;

use FSVendor\WPDesk\Packer\Exception\NoItemsException;
/**
 * Can pack items into boxes.
 *
 * Put some items using add_item(), add some boxes using add_box() and get packed items by get_packages() or get_items_cannot_pack()
 *
 * @package WPDesk\Packer
 */
class Packer
{
    /** @var Box[] */
    protected $boxes;
    /** @var Item[] */
    protected $items;
    /** @var Item[] */
    private $items_cannot_pack;
    /** @var PackedBox[] */
    protected $packages;
    /**
     * @param Item $item
     */
    public function add_item(\FSVendor\WPDesk\Packer\Item $item)
    {
        $this->items[] = $item;
    }
    /**
     * @param Box $box
     */
    public function add_box(\FSVendor\WPDesk\Packer\Box $box)
    {
        $this->boxes[] = $box;
    }
    /**
     * @return PackedBox[]
     */
    public function get_packages()
    {
        return $this->packages ?: [];
    }
    /**
     * @return Item[]
     */
    public function get_items_cannot_pack()
    {
        return $this->items_cannot_pack;
    }
    /**
     * Pack items to boxes creating packages.
     *
     * @throws NoItemsException
     */
    public function pack()
    {
        if (\sizeof($this->items) === 0) {
            throw new \FSVendor\WPDesk\Packer\Exception\NoItemsException('No items to pack!');
        }
        $this->packages = [];
        $this->boxes = $this->order_boxes_by_volume($this->boxes);
        if (!$this->boxes) {
            $this->items_cannot_pack = $this->items;
            $this->items = [];
        }
        // Keep looping until packed
        while (\sizeof($this->items) > 0) {
            $this->items = $this->order_items($this->items);
            $best_package = $this->find_best_packed_box();
            if ($best_package->get_success_percent() === 0.0) {
                $this->items_cannot_pack = $this->items;
                $this->items = [];
            } else {
                $this->items = $best_package->get_nofit_items();
                $this->packages[] = $best_package;
            }
        }
    }
    /**
     * Pack all items to all boxes and try to find one best success package
     *
     * @return PackedBox Best packed package possible
     */
    private function find_best_packed_box()
    {
        $packages = [];
        foreach ($this->boxes as $box) {
            $packages[] = new \FSVendor\WPDesk\Packer\PackedBox($box, $this->items);
        }
        // Find the best success rate
        $best_percent = 0;
        $best_package = null;
        /** @var PackedBox $package */
        foreach ($packages as $package) {
            if ($package->get_success_percent() >= $best_percent) {
                $best_percent = $package->get_success_percent();
                $best_package = $package;
            }
        }
        return $best_package;
    }
    /**
     * Order boxes by weight and volume
     *
     * @param array $sort
     *
     * @return array
     */
    private function order_boxes_by_volume($sort)
    {
        if (!empty($sort)) {
            \uasort($sort, static function (\FSVendor\WPDesk\Packer\Box $a, \FSVendor\WPDesk\Packer\Box $b) {
                if ($a->get_volume() === $b->get_volume()) {
                    if ($a->get_max_weight() === $b->get_max_weight()) {
                        return 0;
                    }
                    return $a->get_max_weight() < $b->get_max_weight() ? 1 : -1;
                }
                return $a->get_volume() < $b->get_volume() ? 1 : -1;
            });
        }
        return $sort;
    }
    /**
     * Order items by weight and volume
     *
     * @param array $sort
     *
     * @return array
     */
    private function order_items($sort)
    {
        if (!empty($sort)) {
            \uasort($sort, static function (\FSVendor\WPDesk\Packer\Item $a, \FSVendor\WPDesk\Packer\Item $b) {
                if ($a->get_volume() === $b->get_volume()) {
                    if ($a->get_weight() === $b->get_weight()) {
                        return 0;
                    }
                    return $a->get_weight() < $b->get_weight() ? 1 : -1;
                }
                return $a->get_volume() < $b->get_volume() ? 1 : -1;
            });
        }
        return $sort;
    }
}
