<?php

/**
 * Method settings.
 * @package WPDesk\FS\TableRate\Settings
 */
namespace FSVendor\WPDesk\FS\TableRate\Settings;

/**
 * FS Shipping method settings.
 */
interface MethodSettings
{
    /**
     * @return array
     */
    public function get_raw_settings();
    /**
     * @return string
     */
    public function get_id();
    /**
     * @return string
     */
    public function get_enabled();
    /**
     * @return string
     */
    public function get_title();
    /**
     * @return string
     */
    public function get_description();
    /**
     * @return string
     */
    public function get_free_shipping();
    /**
     * @return string
     */
    public function get_free_shipping_label();
    /**
     * @return string
     */
    public function get_free_shipping_cart_notice();
    /**
     * @return string
     */
    public function get_calculation_method();
    /**
     * @return string
     * @deprecated
     */
    public function get_visible();
    /**
     * @return string
     */
    public function get_visibility();
    /**
     * @return string
     */
    public function get_default();
    /**
     * @return string
     */
    public function get_debug_mode();
    /**
     * @return string
     */
    public function get_integration();
    /**
     * @return IntegrationSettings|null
     */
    public function get_integration_settings();
    /**
     * @return array
     */
    public function get_rules_settings();
    /**
     * @return string
     */
    public function get_tax_status();
    /**
     * @return string
     */
    public function get_prices_include_tax();
}
