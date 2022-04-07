<?php

namespace FSVendor\WPDesk\Mutex;

class StaticMutexStorage implements \FSVendor\WPDesk\Mutex\MutexStorage
{
    /**
     * @var Mutex[]
     */
    public static $mutexStorage;
    /**
     * Add to storage.
     *
     * @param string $name
     * @param Mutex $mutex
     */
    public function addToStorage($name, $mutex)
    {
        self::$mutexStorage[$name] = $mutex;
    }
    /**
     * @param string $name
     *
     * @return null|Mutex
     */
    public function getFromStorage($name)
    {
        return isset(self::$mutexStorage[$name]) ? self::$mutexStorage[$name] : null;
    }
    /**
     * @param string $name
     *
     * @return void
     */
    public function removeFromStorage($name)
    {
        if (isset(self::$mutexStorage[$name])) {
            unset(self::$mutexStorage[$name]);
        } else {
            throw new \FSVendor\WPDesk\Mutex\MutexNotFoundInStorage();
        }
    }
}
