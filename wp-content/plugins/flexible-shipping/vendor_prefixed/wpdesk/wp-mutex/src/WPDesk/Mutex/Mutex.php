<?php

namespace FSVendor\WPDesk\Mutex;

interface Mutex
{
    /**
     * Tries to set lock and returns true if successful
     *
     * @return bool
     */
    public function acquireLock();
    /**
     * Releases lock
     *
     * @return void
     */
    public function releaseLock();
}
