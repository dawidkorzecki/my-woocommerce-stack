<?php

/**
 * Session factory.
 *
 * @package WPDesk\Session
 */
namespace FSVendor\WPDesk\Session;

use FSVendor\WPDesk\Session\Adapter\WooCommerceSession;
/**
 * Can create session adapters.
 */
class SessionFactory
{
    /**
     * @var WooCommerceSession
     */
    private $woocommerce_session_adapter;
    /**
     * Creates WooCommerce session adapter.
     *
     * @return Session
     */
    public function get_woocommerce_session_adapter()
    {
        if (null === $this->woocommerce_session_adapter) {
            \WC()->initialize_session();
            $this->woocommerce_session_adapter = new \FSVendor\WPDesk\Session\Adapter\WooCommerceSession(\WC()->session);
        }
        return $this->woocommerce_session_adapter;
    }
}
