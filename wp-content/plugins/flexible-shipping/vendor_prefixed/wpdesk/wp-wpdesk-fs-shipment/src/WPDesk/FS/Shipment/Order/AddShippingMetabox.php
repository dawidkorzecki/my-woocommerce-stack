<?php

/**
 * Class WPDesk_Flexible_Shipping_Add_Shipping
 *
 * @package Flexible Shipping
 */
namespace FSVendor\WPDesk\FS\Shipment\Order;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can display maetabox and add new shipment.
 */
class AddShippingMetabox implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * Shipping added?
     *
     * @var bool
     */
    private $shipping_added = \false;
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('add_meta_boxes', array($this, 'add_meta_box'), 20, 2);
        \add_action('admin_init', array($this, 'handle_add_shipping'));
    }
    /**
     * Add shipping.
     *
     * @param string $integration Integration.
     * @param array  $get_data .
     */
    private function add_shipping($integration, $get_data)
    {
        $class_name = \apply_filters('flexible_shipping_shipment_class', 'WPDesk_Flexible_Shipping_Shipment_' . $integration, $integration);
        if (\class_exists($class_name)) {
            $order = \wc_get_order(\sanitize_key($get_data['post']));
            if ($order) {
                $order_id = $order->get_id();
                $integration = \sanitize_key($get_data['fs_add_shipping']);
                // Translators: order id and integration.
                $post_title = \sprintf(\__('Shipment for order %1$s, %2$s', 'flexible-shipping'), $order_id, $integration);
                $shipment_post = array('post_title' => $post_title, 'post_type' => 'shipment', 'post_status' => 'fs-new', 'post_parent' => $order_id);
                $shipment_id = \wp_insert_post($shipment_post);
                \update_post_meta($shipment_id, '_integration', $integration);
                $shipment = fs_get_shipment($shipment_id, $order);
                $shipment->set_created_via_add_shipment();
                if (\method_exists($shipment, 'admin_add_shipment')) {
                    $shipment->admin_add_shipment();
                }
                $shipment->save();
                $this->shipping_added = \true;
            }
        }
    }
    /**
     * Handle add shipping.
     */
    public function handle_add_shipping()
    {
        if (isset($_GET['fs_add_shipping']) && isset($_GET['post']) && isset($_GET['_wpnonce'])) {
            if (\wp_verify_nonce(\sanitize_key(\wp_unslash($_GET['_wpnonce'])), 'fs_add_shipping')) {
                $integration = \sanitize_key($_GET['fs_add_shipping']);
                $this->add_shipping($integration, $_GET);
            }
        }
    }
    /**
     * Add metabox.
     *
     * @param string  $post_type Post type.
     * @param WP_Post $post Post.
     */
    public function add_meta_box($post_type, $post)
    {
        if ('shop_order' === $post_type) {
            $select_options = \apply_filters('flexible_shipping_add_shipping_options', array());
            if (\count($select_options)) {
                $select_options = \array_merge(array('' => \__('Select integration', 'flexible-shipping')), $select_options);
                $args = array('select_options' => $select_options, 'order_id' => $post->ID);
                \add_meta_box('add_shipping_meta_box', \__('Add shipping', 'flexible-shipping'), array($this, 'display_order_metabox'), 'shop_order', 'side', 'default', $args);
            }
        }
    }
    /**
     * Display order metabox.
     *
     * @param WP_Post $post Post.
     * @param array   $args Args.
     */
    public function display_order_metabox($post, $args)
    {
        $select_options = $args['args']['select_options'];
        $order_id = $args['args']['order_id'];
        $add_shipping_url = \admin_url('post.php?post=' . $order_id . '&action=edit');
        $add_shipping_url = \wp_nonce_url($add_shipping_url, 'fs_add_shipping');
        $add_shipping_url = \str_replace('&amp;', '&', $add_shipping_url);
        include 'views/html-order-add_shipping-metabox.php';
    }
}
