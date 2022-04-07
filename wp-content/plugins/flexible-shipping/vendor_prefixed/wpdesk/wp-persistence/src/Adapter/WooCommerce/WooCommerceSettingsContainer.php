<?php

namespace FSVendor\WPDesk\Persistence\Adapter\WooCommerce;

use FSVendor\WPDesk\Persistence\ElementNotExistsException;
use FSVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Can store data using WooCommerce settings options.
 * Use when want the abstract access to \WC_Settings_API.
 *
 * @package WPDesk\Persistence\WooCommerce
 */
final class WooCommerceSettingsContainer implements \FSVendor\WPDesk\Persistence\PersistentContainer
{
    /** @var \WC_Settings_API */
    private $settings;
    public function __construct(\WC_Settings_API $settings)
    {
        $this->settings = $settings;
    }
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new \FSVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $id));
        }
        return $this->settings->get_option($id);
    }
    public function has($id)
    {
        return isset($this->settings->settings[$id]);
    }
    public function set($id, $value)
    {
        if (\version_compare(\WC_VERSION, '3.4', '>=')) {
            $this->settings->update_option($id, $value);
        } else {
            $this->settings->settings[$id] = $value;
            $this->update_db_using_wordpress();
        }
    }
    /**
     * WC_Settings_API is so great that sometimes we have to manually update the data using WP update_option.
     *
     * @see \WC_Settings_API::process_admin_options
     */
    private function update_db_using_wordpress()
    {
        \update_option($this->settings->get_option_key(), \apply_filters('woocommerce_settings_api_sanitized_fields_' . $this->settings->id, $this->settings->settings), 'yes');
    }
    public function delete($id)
    {
        $form_fields = $this->settings->get_form_fields();
        if (isset($form_fields[$id])) {
            $this->settings->settings[$id] = $this->settings->get_field_default($form_fields[$id]);
        } else {
            unset($this->settings->settings[$id]);
        }
        $this->update_db_using_wordpress();
    }
}
