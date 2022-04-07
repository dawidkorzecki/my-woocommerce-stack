<?php

/**
 * Method Settings Implementation.
 *
 * @package WPDesk\FS\TableRate\Settings
 */
namespace FSVendor\WPDesk\FS\TableRate\Settings;

use FSVendor\WPDesk\FS\TableRate\CalculationMethodOptions;
use FSVendor\WPDesk\FS\TableRate\Logger\CanFormatForLog;
/**
 * Class MethodSettingsImplementation
 */
class MethodSettingsImplementation implements \FSVendor\WPDesk\FS\TableRate\Settings\MethodSettings, \FSVendor\WPDesk\FS\TableRate\Logger\CanFormatForLog
{
    use CheckboxValue;
    /**
     * @var array
     */
    private $raw_settings;
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $enabled;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;
    /**
     * @var float
     */
    private $free_shipping;
    /**
     * @var string
     */
    private $free_shipping_label;
    /**
     * @var bool
     */
    private $free_shipping_cart_notice;
    /**
     * @var string
     */
    private $calculation_method;
    /**
     * @var string
     */
    private $cart_calculation;
    /**
     * @var bool
     */
    private $visibility;
    /**
     * @var bool
     */
    private $default;
    /**
     * @var bool
     */
    private $debug_mode;
    /**
     * @var string
     */
    private $integration;
    /**
     * @var IntegrationSettingsImplementation
     */
    private $integration_settings;
    /**
     * @var RuleSettings[]
     */
    private $rules_settings;
    /**
     * @var string
     */
    private $tax_status;
    /**
     * @var string
     */
    private $prices_include_tax;
    /**
     * MethodSettingsImplementation constructor.
     *
     * @param array $raw_settings
     * @param string $id
     * @param string $enabled
     * @param string $title
     * @param string $description
     * @param string $tax_status
     * @param string $prices_include_tax
     * @param string $free_shipping
     * @param string $free_shipping_label
     * @param string $free_shipping_cart_notice
     * @param string $calculation_method
     * @param string $cart_calculation
     * @param string $visibility
     * @param string $default
     * @param string $debug_mode
     * @param string $integration
     * @param IntegrationSettingsImplementation $integration_settings
     * @param array $rules_settings
     */
    public function __construct(array $raw_settings, $id, $enabled, $title, $description, $tax_status, $prices_include_tax, $free_shipping, $free_shipping_label, $free_shipping_cart_notice, $calculation_method, $cart_calculation, $visibility, $default, $debug_mode, $integration, \FSVendor\WPDesk\FS\TableRate\Settings\IntegrationSettingsImplementation $integration_settings, array $rules_settings)
    {
        $this->raw_settings = $raw_settings;
        $this->id = $id;
        $this->enabled = $enabled;
        $this->title = $title;
        $this->description = $description;
        $this->tax_status = $tax_status;
        $this->prices_include_tax = $prices_include_tax;
        $this->free_shipping = $free_shipping;
        $this->free_shipping_label = $free_shipping_label;
        $this->free_shipping_cart_notice = $free_shipping_cart_notice;
        $this->calculation_method = $calculation_method;
        $this->cart_calculation = $cart_calculation;
        $this->visibility = $visibility;
        $this->default = $default;
        $this->debug_mode = $debug_mode;
        $this->integration = $integration;
        $this->integration_settings = $integration_settings;
        $this->rules_settings = $rules_settings;
    }
    /**
     * @return array
     */
    public function get_raw_settings()
    {
        return $this->raw_settings;
    }
    /**
     * @return string
     */
    public function get_id()
    {
        return $this->id;
    }
    /**
     * @return bool
     */
    public function get_enabled()
    {
        return $this->enabled;
    }
    /**
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }
    /**
     * @return string
     */
    public function get_description()
    {
        return $this->description;
    }
    /**
     * @return float
     */
    public function get_free_shipping()
    {
        return $this->free_shipping;
    }
    /**
     * @return string
     */
    public function get_free_shipping_label()
    {
        return $this->free_shipping_label;
    }
    /**
     * @return bool
     */
    public function get_free_shipping_cart_notice()
    {
        return $this->free_shipping_cart_notice;
    }
    /**
     * @return string
     */
    public function get_calculation_method()
    {
        return $this->calculation_method;
    }
    /**
     * @return string
     */
    public function get_cart_calculation()
    {
        return $this->cart_calculation;
    }
    /**
     * @return string
     * @deprecated
     */
    public function get_visible()
    {
        return $this->visibility;
    }
    /**
     * @return string
     */
    public function get_visibility()
    {
        return $this->visibility;
    }
    /**
     * @return bool
     */
    public function get_default()
    {
        return $this->default;
    }
    /**
     * @return bool
     */
    public function get_debug_mode()
    {
        return $this->debug_mode;
    }
    /**
     * @return string
     */
    public function get_integration()
    {
        return $this->integration;
    }
    /**
     * @return IntegrationSettingsImplementation
     */
    public function get_integration_settings()
    {
        return $this->integration_settings;
    }
    /**
     * @return RuleSettings[]
     */
    public function get_rules_settings()
    {
        return $this->rules_settings;
    }
    /**
     * @return string
     */
    public function get_tax_status()
    {
        return $this->tax_status;
    }
    /**
     * @return string
     */
    public function get_prices_include_tax()
    {
        return $this->prices_include_tax;
    }
    /**
     * @return string
     */
    public function format_for_log()
    {
        return \sprintf(\__('Method settings:%1$s Enabled: %2$s Method Title: %3$s Method Description: %4$s Tax status: %5$s Costs includes tax: %6$s Free Shipping: %7$s Free Shipping Label: %8$s \'Left to free shipping\' notice: %9$s Rules Calculation: %10$s Cart Calculation: %11$s Visibility (Show only for logged in users): %12$s Default: %13$s Debug mode: %14$s', 'flexible-shipping'), "\n", $this->get_as_translated_checkbox_value($this->get_enabled()) . "\n", $this->get_title() . "\n", $this->get_description() . "\n", $this->get_tax_status_translated() . "\n", $this->get_prices_include_tax() . "\n", $this->get_free_shipping() . "\n", $this->get_free_shipping_label() . "\n", $this->get_as_translated_checkbox_value($this->get_free_shipping_cart_notice()) . "\n", (new \FSVendor\WPDesk\FS\TableRate\CalculationMethodOptions())->get_option_label($this->get_calculation_method()) . "\n", (new \FSVendor\WPDesk\FS\TableRate\Settings\CartCalculationOptions())->get_option_label($this->get_cart_calculation()) . "\n", $this->get_as_translated_checkbox_value($this->get_visibility()) . "\n", $this->get_as_translated_checkbox_value($this->get_default()) . "\n", $this->get_as_translated_checkbox_value($this->get_debug_mode()) . "\n") . $this->integration_settings->format_for_log();
    }
    /**
     * @return string
     */
    public function get_tax_status_translated()
    {
        $tax_status_options = array('taxable' => \__('Taxable', 'flexible-shipping'), 'none' => \_x('None', 'Tax status', 'flexible-shipping'));
        return isset($tax_status_options[$this->tax_status]) ? $tax_status_options[$this->tax_status] : $this->tax_status;
    }
}
