<?php

namespace FSVendor\WPDesk\Mutex;

trait WordpressWpdb
{
    /** @var \wpdb wpdb. */
    private $wpdb;
    /**
     * Get wpdb.
     *
     * @return \wpdb
     */
    private function getWpdbFromGlobal()
    {
        global $wpdb;
        return $wpdb;
    }
}
