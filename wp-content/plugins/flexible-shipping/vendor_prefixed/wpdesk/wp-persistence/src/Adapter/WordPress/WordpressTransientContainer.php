<?php

namespace FSVendor\WPDesk\Persistence\Adapter\WordPress;

use FSVendor\WPDesk\Persistence\ElementNotExistsException;
use FSVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Can store data using WordPress transients.
 * Warning: stored false is considered unset.
 *
 * @package WPDesk\Persistence\Wordpress
 */
final class WordpressTransientContainer implements \FSVendor\WPDesk\Persistence\PersistentContainer
{
    /** @var int */
    private $expiration;
    /** @var string */
    private $namespace;
    /**
     * @param string $namespace Namespace so transients in different containers would not conflict.
     * @param float|int $expiration Expire transient after xx seconds.
     */
    public function __construct($namespace = '', $expiration = DAY_IN_SECONDS)
    {
        $this->expiration = (int) $expiration;
        $this->namespace = $namespace;
    }
    public function set($id, $value)
    {
        \set_transient($this->prepare_key_name($id), $value, $this->expiration);
    }
    /**
     * Warning: stored false is considered unset.
     *
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return \get_transient($this->prepare_key_name($id)) !== \false;
    }
    public function delete($id)
    {
        \delete_transient($this->prepare_key_name($id));
    }
    /**
     * Prepare transient name for key.
     *
     * @param string $key Key.
     *
     * @return string
     */
    private function prepare_key_name($key)
    {
        return \sanitize_key($this->namespace . $key);
    }
    public function get($id)
    {
        $value = \get_transient($this->prepare_key_name($id));
        if (\false === $value) {
            throw new \FSVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $id));
        }
        return $value;
    }
}
