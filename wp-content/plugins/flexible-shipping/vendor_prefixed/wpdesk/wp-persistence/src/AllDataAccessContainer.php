<?php

namespace FSVendor\WPDesk\Persistence;

use Psr\Container\ContainerInterface;
/**
 * Container that allows to get all data stored by container.
 *
 * @package WPDesk\Persistence
 */
interface AllDataAccessContainer extends \Psr\Container\ContainerInterface
{
    /**
     * Get all values.
     *
     * @return array
     */
    public function get_all();
}
