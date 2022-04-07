<?php

namespace FSVendor\WPDesk\ABTesting\ABVariant;

use FSVendor\WPDesk\ABTesting\ABVariant;
/**
 * Base class for variants. Nothing special.
 *
 * @package WPDesk\ABTesting\ABVariant
 */
abstract class BasicABVariant implements \FSVendor\WPDesk\ABTesting\ABVariant
{
    /** @var string */
    private $variant_id;
    /**
     * @param string $variant_id
     */
    public function __construct($variant_id)
    {
        $this->variant_id = (string) $variant_id;
    }
    public abstract function is_on($functionality);
    public function get_variant_id()
    {
        return $this->variant_id;
    }
}
