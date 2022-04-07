<?php

/**
 * Session interface.
 * @package WPDesk\Session
 */
namespace FSVendor\WPDesk\Session;

/**
 * Session that can store session data.
 */
interface Session
{
    /**
     * Sets session value with specified key.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value);
    /**
     * Returns session value for specified key.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null);
}
