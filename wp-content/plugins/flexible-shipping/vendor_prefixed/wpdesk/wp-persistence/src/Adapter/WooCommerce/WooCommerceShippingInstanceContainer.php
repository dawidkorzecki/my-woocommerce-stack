<?php

namespace FSVendor\WPDesk\Persistence\Adapter\WooCommerce;

use FSVendor\WPDesk\Persistence\ElementNotExistsException;
use FSVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Can store data using WooCommerce shipping instance settings options.
 * Use when want the abstract access to \WC_Shipping_Method.
 *
 * @package WPDesk\Persistence\WooCommerce
 */
final class WooCommerceShippingInstanceContainer implements \FSVendor\WPDesk\Persistence\PersistentContainer
{
    /** @var \WC_Shipping_Method */
    private $method;
    public function __construct(\WC_Shipping_Method $method)
    {
        $this->method = $method;
    }
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new \FSVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $id));
        }
        return $this->method->get_instance_option($id);
    }
    public function has($id)
    {
        return isset($this->method->instance_settings[$id]);
    }
    public function set($id, $value)
    {
        $this->method->instance_settings[$id] = $value;
        /** @see \WC_Shipping_Method::process_admin_options */
        \update_option($this->method->get_instance_option_key(), \apply_filters('woocommerce_shipping_' . $this->method->id . '_instance_settings_values', $this->method->instance_settings, $this->method), 'yes');
    }
    public function delete($id)
    {
        $form_fields = $this->method->get_instance_form_fields();
        $empty_value = isset($form_fields[$id]) ? $this->method->get_field_default($form_fields[$id]) : null;
        $this->set($id, $empty_value);
    }
}
