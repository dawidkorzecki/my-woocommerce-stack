<?php

namespace FSVendor\WPDesk\View\Resolver;

use FSVendor\WPDesk\View\Renderer\Renderer;
use FSVendor\WPDesk\View\Resolver\Exception\CanNotResolve;
/**
 * This resolver never finds the file
 *
 * @package WPDesk\View\Resolver
 */
class NullResolver implements \FSVendor\WPDesk\View\Resolver\Resolver
{
    public function resolve($name, \FSVendor\WPDesk\View\Renderer\Renderer $renderer = null)
    {
        throw new \FSVendor\WPDesk\View\Resolver\Exception\CanNotResolve("Null Cannot resolve");
    }
}
