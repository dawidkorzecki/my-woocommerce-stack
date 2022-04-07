<?php

namespace FSVendor\WPDesk\Nps\DisplayDecisions;

/**
 * Can determine if display NPS on shipping method settings page.
 */
class ShippingMethodDisplayNpsDecision implements \FSVendor\WPDesk\Nps\DisplayDecisions\DisplayNpsDecision
{
    /**
     * @var string
     */
    private $shipping_method_id;
    /**
     * ShippingMethodDisplayNpsDecision constructor.
     *
     * @param string $shipping_method_id
     * @param \WC_Shipping_Zones $wc_shipping_zones
     */
    public function __construct($shipping_method_id)
    {
        $this->shipping_method_id = $shipping_method_id;
    }
    /**
     * @return bool
     */
    public function should_display_nps()
    {
        if ($this->is_on_shipping_method_settings()) {
            $shippping_method = $this->get_shipping_method($_GET['instance_id']);
            if ($shippping_method instanceof \WC_Shipping_Method && $shippping_method->id === $this->shipping_method_id) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @return bool
     */
    private function is_on_shipping_method_settings()
    {
        return $this->is_parameter_present('page', 'wc-settings') && $this->is_parameter_present('tab', 'shipping') && $this->is_parameter_present('instance_id');
    }
    /**
     * @param string $name
     *
     * @return bool
     */
    private function is_parameter_present($name, $value = null)
    {
        return isset($_GET[$name]) && (empty($value) || $value === $_GET[$name]);
    }
    /**
     * @param string $instance_id
     *
     * @return bool|\WC_Shipping_Method
     * @codeCoverageIgnore
     */
    protected function get_shipping_method($instance_id)
    {
        return \WC_Shipping_Zones::get_shipping_method($instance_id);
    }
}
