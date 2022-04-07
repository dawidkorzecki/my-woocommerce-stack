<?php

namespace FSVendor\WPDesk\License\Page\License\Action;

use FSVendor\WPDesk\License\Page\Action;
/**
 * Do nothing.
 *
 * @package WPDesk\License\Page\License\Action
 */
class Nothing implements \FSVendor\WPDesk\License\Page\Action
{
    public function execute(array $plugin)
    {
        // NOOP
    }
}
