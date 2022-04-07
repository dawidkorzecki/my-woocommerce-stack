<?php

namespace FSVendor\WPDesk\Persistence\Adapter;

use FSVendor\WPDesk\Persistence\ElementNotExistsException;
use FSVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Container that uses array as a persistent memory.
 *
 * @package WPDesk\Persistence
 */
class ArrayContainer implements \FSVendor\WPDesk\Persistence\PersistentContainer
{
    /** @var array */
    protected $array;
    public function __construct(array $initial = [])
    {
        $this->array = $initial;
    }
    public function set($id, $value)
    {
        $this->array[$id] = $value;
    }
    public function delete($id)
    {
        unset($this->array[$id]);
    }
    public function has($id)
    {
        return \key_exists($id, $this->array);
    }
    public function get($id)
    {
        if (!isset($this->array[$id])) {
            throw new \FSVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $id));
        }
        return $this->array[$id];
    }
    /**
     * Return array that is used internally to save the data.
     *
     * @return array
     */
    public function get_array()
    {
        return $this->array;
    }
}
