<?php

namespace FSVendor\WPDesk\Mutex;

interface MutexStorage
{
    /**
     * @param string $name
     * @param Mutex  $mutex
     */
    public function addToStorage($name, $mutex);
    /**
     * @param string $name
     *
     * @return null|Mutex
     */
    public function getFromStorage($name);
    /**
     * @param string $name
     *
     * @return void
     */
    public function removeFromStorage($name);
}
