<?php

/**
 * WooCommerce Session Adapter.
 * @package WPDesk\Session
 */
namespace FSVendor\WPDesk\Session\Adapter;

use FSVendor\WPDesk\Session\Session;
/**
 * Can store session data in WooCommerce Session.
 */
class WooCommerceSession implements \FSVendor\WPDesk\Session\Session
{
    /**
     * @var \WC_Session
     */
    private $wc_session_handler;
    /**
     * WooCommerceSessionHandler constructor.
     *
     * @param \WC_Session $wc_session_handler
     */
    public function __construct(\WC_Session $wc_session_handler)
    {
        $this->wc_session_handler = $wc_session_handler;
    }
    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->wc_session_handler->set($key, $value);
    }
    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->wc_session_handler->get($key, $default);
    }
}
