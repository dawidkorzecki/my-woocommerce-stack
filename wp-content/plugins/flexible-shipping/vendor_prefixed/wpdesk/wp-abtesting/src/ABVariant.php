<?php

namespace FSVendor\WPDesk\ABTesting;

/**
 * Specific variant should know what functionalities should be on/off.
 *
 * @package WPDesk\ABTesting
 */
interface ABVariant
{
    /**
     * Checks if a variant does have a given functionality working.
     *
     * @param string $functionality
     *
     * @return bool
     */
    public function is_on($functionality);
    /**
     * Returns the variant id (can be numeric). For example for standard AB testing it would be A or B.
     *
     * @return string
     */
    public function get_variant_id();
}
