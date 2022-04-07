<?php

namespace FSVendor\WPDesk\View\Renderer;

use FSVendor\WPDesk\View\Resolver\Resolver;
/**
 * Can render templates
 */
class SimplePhpRenderer implements \FSVendor\WPDesk\View\Renderer\Renderer
{
    /** @var Resolver */
    private $resolver;
    public function __construct(\FSVendor\WPDesk\View\Resolver\Resolver $resolver)
    {
        $this->set_resolver($resolver);
    }
    /**
     * @param Resolver $resolver
     *
     * @return void|Resolver
     */
    public function set_resolver(\FSVendor\WPDesk\View\Resolver\Resolver $resolver)
    {
        $this->resolver = $resolver;
    }
    /**
     * @param string $template
     * @param array|null $params
     *
     * @return string
     */
    public function render($template, array $params = null)
    {
        if ($params !== null) {
            \extract($params, \EXTR_SKIP);
        }
        \ob_start();
        include $this->resolver->resolve($template . '.php');
        return \ob_get_clean();
    }
}
