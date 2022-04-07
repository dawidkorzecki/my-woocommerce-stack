<?php

namespace FSVendor\WPDesk\Packer;

use FSVendor\WPDesk\Packer\Exception\NoItemsException;
/**
 * Can pack items into boxes. Each item is packed to separate box with item dimensions and weight.
 *
 * Put some items using add_item()
 *
 * @package WPDesk\Packer
 */
class PackerSeparately extends \FSVendor\WPDesk\Packer\Packer
{
    /**
     * Box name.
     *
     * @var string
     */
    private $box_name;
    /**
     * PackerSeparately constructor.
     *
     * @param string $box_name .
     */
    public function __construct($box_name = 'Custom box for item')
    {
        $this->box_name = $box_name;
    }
    /**
     * Pack items to boxes creating packages.
     *
     * @throws NoItemsException .
     */
    public function pack()
    {
        if (0 === \count($this->items)) {
            throw new \FSVendor\WPDesk\Packer\Exception\NoItemsException('No items to pack!');
        }
        $this->packages = [];
        // Pack items.
        foreach ($this->items as $item) {
            $packed_box = new \FSVendor\WPDesk\Packer\PackedBox(new \FSVendor\WPDesk\Packer\Box\BoxImplementation($item->get_length(), $item->get_width(), $item->get_height(), 0, null, $this->box_name, $this->box_name, ['name' => $this->box_name, 'box' => ['name' => $this->box_name, 'length' => $item->get_length(), 'width' => $item->get_width(), 'height' => $item->get_height()]]), [$item]);
            $packed_box->get_packed_items();
            // Calculates weight!
            $this->packages[] = $packed_box;
        }
        $this->items = [];
    }
}
