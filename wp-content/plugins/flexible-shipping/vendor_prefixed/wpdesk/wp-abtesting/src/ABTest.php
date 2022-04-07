<?php

namespace FSVendor\WPDesk\ABTesting;

/**
 * Test should know what variant should be used and that is all.
 *
 * @package WPDesk\ABTesting
 */
interface ABTest
{
    /**
     * Return info about what variant of AB test should be used.
     *
     * @return ABVariant
     */
    public function get_variant();
}
