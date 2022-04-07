<?php

namespace FSVendor\WPDesk\Packer;

/**
 * Have all info about items packed in the given box
 *
 * @package WPDesk\Packer
 */
final class PackedBox
{
    /** @var float Dimension is stored here if adjusted during packing */
    private $packed_height;
    private $maybe_packed_height;
    /** @var float Dimension is stored here if adjusted during packing */
    private $packed_width;
    private $maybe_packed_width;
    /** @var float Dimension is stored here if adjusted during packing */
    private $packed_length;
    private $maybe_packed_length;
    /** @var float */
    private $packed_volume;
    /** @var float */
    private $packed_weight;
    /** @var float */
    private $packed_value;
    /** @var Box */
    private $box;
    /** @var Item[] */
    private $items_to_pack;
    /** @var Item[] */
    private $packed_items = [];
    /** @var Item[] */
    private $nofit_items = [];
    /** @var float */
    private $success_percent = 0.0;
    /**
     * @param Box    $box
     * @param Item[] $items
     */
    public function __construct(\FSVendor\WPDesk\Packer\Box $box, array $items)
    {
        $this->box = $box;
        $this->items_to_pack = $items;
    }
    /**
     * @return Box
     */
    public function get_box()
    {
        return $this->box;
    }
    /**
     * @return Item[]
     */
    public function get_packed_items()
    {
        $this->try_to_pack();
        return $this->packed_items;
    }
    /**
     * Get packed weight.
     *
     * @return float
     */
    public function get_packed_weight()
    {
        return $this->packed_weight;
    }
    /**
     * Get packed value.
     *
     * @return float
     */
    public function get_packed_value()
    {
        return $this->packed_value;
    }
    /**
     * @return Item[]
     */
    public function get_nofit_items()
    {
        $this->try_to_pack();
        return $this->nofit_items;
    }
    /**
     * How good is this box in packing given items. Higher is better.
     *
     * @return float
     */
    public function get_success_percent()
    {
        $this->try_to_pack();
        return $this->success_percent;
    }
    /**
     * Try to pack/fit all items info the box. Packed can be accessed via get_packed_items(); Unpacked can be accessed via get_nofit_items()
     *
     * @return void
     */
    private function try_to_pack()
    {
        if (\sizeof($this->items_to_pack) === 0) {
            return;
        }
        $packed = [];
        $unpacked = [];
        $packed_weight = $this->box->get_weight();
        $packed_volume = 0;
        $packed_value = 0;
        /** @var Item $item */
        foreach ($this->items_to_pack as $item) {
            if ($this->can_be_packed($item, $packed_weight, $packed_volume)) {
                $packed[] = $item;
                $packed_volume += $item->get_volume();
                $packed_weight += $item->get_weight();
                $packed_value += $item->get_value();
                // Adjust dimensions if needed, after this item has been packed inside
                if (null !== $this->maybe_packed_height) {
                    $this->packed_height = $this->maybe_packed_height;
                    $this->packed_length = $this->maybe_packed_length;
                    $this->packed_width = $this->maybe_packed_width;
                    $this->maybe_packed_height = null;
                    $this->maybe_packed_length = null;
                    $this->maybe_packed_width = null;
                }
            } else {
                $unpacked[] = $item;
            }
        }
        $this->packed_items = $packed;
        $this->nofit_items = $unpacked;
        $this->packed_weight = $packed_weight;
        $this->packed_volume = $packed_volume;
        $this->packed_value = $packed_value;
        $this->calculate_packing_success_rate();
    }
    /**
     * See if an item fits into the box at all
     *
     * @param Item $item
     *
     * @return bool
     */
    private function can_fit_to_empty_box($item)
    {
        return $this->box->get_length() >= $item->get_length() && $this->box->get_width() >= $item->get_width() && $this->box->get_height() >= $item->get_height() && $item->get_volume() <= $this->box->get_volume();
    }
    /**
     * If item can still fit to the box regarding weight and volume
     *
     * @param Item  $item
     * @param float $current_weight
     * @param float $current_volume
     *
     * @return bool
     */
    private function can_be_packed(\FSVendor\WPDesk\Packer\Item $item, $current_weight, $current_volume)
    {
        // Check dimensions
        if (!$this->can_fit_to_empty_box($item)) {
            return \false;
        }
        // Check max weight
        if ($this->box->get_max_weight() > 0) {
            if ($current_weight + $item->get_weight() > $this->box->get_max_weight()) {
                return \false;
            }
        }
        return !($current_volume + $item->get_volume() > $this->box->get_volume());
    }
    /**
     * Calculate success_percent
     *
     * @return void
     */
    private function calculate_packing_success_rate()
    {
        // Get weight of unpacked items
        $unpacked_weight = 0;
        $unpacked_volume = 0;
        foreach ($this->nofit_items as $item) {
            $unpacked_weight += $item->get_weight();
            $unpacked_volume += $item->get_volume();
        }
        // Calculate packing success % based on % of weight and volume of all items packed
        $packed_weight_ratio = null;
        $packed_volume_ratio = null;
        $packed_weight_to_compare = $this->packed_weight - $this->box->get_weight();
        if ($packed_weight_to_compare + $unpacked_weight > 0) {
            $packed_weight_ratio = $packed_weight_to_compare / ($packed_weight_to_compare + $unpacked_weight);
        }
        if ($this->packed_volume + $unpacked_volume) {
            $packed_volume_ratio = $this->packed_volume / ($this->packed_volume + $unpacked_volume);
        }
        if (null === $packed_weight_ratio && null === $packed_volume_ratio) {
            // Fallback to amount packed
            $this->success_percent = \sizeof($this->packed_items) / (\sizeof($this->nofit_items) + \sizeof($this->packed_items)) * 100;
        } elseif (null === $packed_weight_ratio) {
            // Volume only
            $this->success_percent = $packed_volume_ratio * 100;
        } elseif (null === $packed_volume_ratio) {
            // Weight only
            $this->success_percent = $packed_weight_ratio * 100;
        } else {
            $this->success_percent = $packed_weight_ratio * $packed_volume_ratio * 100;
        }
    }
}
