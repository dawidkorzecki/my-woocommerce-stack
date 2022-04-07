<?php

namespace FSVendor\WPDesk\Pointer;

/**
 * WordPress admin pointer message position.
 *
 * @package WPDesk\Pointer
 */
class PointerPosition
{
    const TOP = 'top';
    const RIGHT = 'right';
    const BOTTOM = 'bottom';
    const LEFT = 'left';
    /**
     * @var string
     */
    private $edge = \false;
    /**
     * @var string
     */
    private $align;
    public function __construct($edge = 'left', $align = 'top')
    {
        $this->edge = $edge;
        $this->align = $align;
    }
    /**
     * @return string
     */
    public function getEdge()
    {
        return $this->edge;
    }
    /**
     * @param string $edge
     */
    public function setEdge($edge)
    {
        $this->edge = $edge;
    }
    /**
     * @return string
     */
    public function getAlign()
    {
        return $this->align;
    }
    /**
     * @param string $align
     */
    public function setAlign($align)
    {
        $this->align = $align;
    }
    /**
     * Render as JSON.
     */
    public function render()
    {
        echo \json_encode(array('edge' => $this->edge, 'align' => $this->align));
    }
}
