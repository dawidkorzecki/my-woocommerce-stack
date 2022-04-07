<?php

namespace FSVendor\WPDesk\Persistence\Adapter\WordPress;

use FSVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Can store data using WordPress Post metadata.
 * Warning: stored string '' is considered unset.
 *
 * @package WPDesk\Persistence\Wordpress
 */
final class WordpressPostMetaContainer implements \FSVendor\WPDesk\Persistence\PersistentContainer
{
    /** @var int */
    private $post_id;
    /**
     * @param int $post_id Id of the WordPress post.
     */
    public function __construct($post_id)
    {
        $this->post_id = (int) $post_id;
    }
    public function set($key, $value)
    {
        \update_post_meta($this->post_id, $key, $value);
    }
    public function get($key)
    {
        return \get_post_meta($this->post_id, $key, \true);
    }
    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($key)
    {
        return \metadata_exists('post', $this->post_id, $key);
    }
    public function delete($key)
    {
        \delete_post_meta($this->post_id, $key);
    }
}
