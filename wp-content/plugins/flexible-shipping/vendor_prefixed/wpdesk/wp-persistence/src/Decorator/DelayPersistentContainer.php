<?php

namespace FSVendor\WPDesk\Persistence\Decorator;

use FSVendor\WPDesk\Persistence\DeferredPersistentContainer;
use FSVendor\WPDesk\Persistence\ElementNotExistsException;
use FSVendor\WPDesk\Persistence\PersistentContainer;
/**
 * You can use this class to delay write access to any PersistenceContainer.
 *
 * @package WPDesk\Persistence
 */
class DelayPersistentContainer implements \FSVendor\WPDesk\Persistence\DeferredPersistentContainer
{
    /**
     * Container with deferred access.
     *
     * @var PersistentContainer
     */
    protected $container;
    /**
     * Data that has been set but not yet saved to $container.
     *
     * @var array
     */
    protected $internal_data = [];
    /**
     * The keys that was changed in using internal data.
     *
     * @var bool[]
     */
    protected $changed = [];
    public function __construct(\FSVendor\WPDesk\Persistence\PersistentContainer $container)
    {
        $this->container = $container;
    }
    public function get($id)
    {
        if (isset($this->changed[$id]) && $this->changed[$id]) {
            if (isset($this->internal_data[$id])) {
                return $this->internal_data[$id];
            }
            throw new \FSVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $id));
        }
        return $this->container->get($id);
    }
    public function has($id)
    {
        if (isset($this->changed[$id]) && $this->changed[$id]) {
            return isset($this->internal_data[$id]);
        }
        return $this->container->has($id);
    }
    public function save()
    {
        foreach ($this->changed as $key => $value) {
            $this->container->set($key, $this->internal_data[$key]);
        }
        $this->reset();
    }
    public function is_changed()
    {
        return !empty($this->changed);
    }
    public function reset()
    {
        $this->changed = [];
    }
    public function set($id, $value)
    {
        $this->changed[$id] = \true;
        $this->internal_data[$id] = $value;
    }
    public function delete($id)
    {
        $this->changed[$id] = \true;
        unset($this->internal_data[$id]);
    }
}
