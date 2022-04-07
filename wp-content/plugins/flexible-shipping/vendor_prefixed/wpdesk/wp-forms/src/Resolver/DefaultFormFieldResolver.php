<?php

namespace FSVendor\WPDesk\Forms\Resolver;

use FSVendor\WPDesk\View\Renderer\Renderer;
use FSVendor\WPDesk\View\Resolver\DirResolver;
use FSVendor\WPDesk\View\Resolver\Resolver;
/**
 * Use with View to resolver form fields to default templates.
 *
 * @package WPDesk\Forms\Resolver
 */
class DefaultFormFieldResolver implements \FSVendor\WPDesk\View\Resolver\Resolver
{
    /** @var Resolver */
    private $dir_resolver;
    public function __construct()
    {
        $this->dir_resolver = new \FSVendor\WPDesk\View\Resolver\DirResolver(__DIR__ . '/../../templates');
    }
    public function resolve($name, \FSVendor\WPDesk\View\Renderer\Renderer $renderer = null)
    {
        return $this->dir_resolver->resolve($name, $renderer);
    }
}
