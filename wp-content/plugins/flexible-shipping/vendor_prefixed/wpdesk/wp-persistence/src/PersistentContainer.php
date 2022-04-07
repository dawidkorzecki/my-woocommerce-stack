<?php

namespace FSVendor\WPDesk\Persistence;

use Psr\Container\ContainerInterface;
/**
 * Container that you can use to save some values.
 * When class require only read capabilities use ContainerInterface. When requires to write use this interface.
 *
 * @package WPDesk\Persistence
 */
interface PersistentContainer extends \Psr\Container\ContainerInterface
{
    /**
     * Set value for a given key.
     *
     * @param string $id Identifier of the entry to look for.
     * @param array|int|string|float $value Value should not be an object or callable.
     *
     * @return void
     */
    public function set($id, $value);
    /**
     * Clear value from a given key.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return void
     */
    public function delete($id);
}
