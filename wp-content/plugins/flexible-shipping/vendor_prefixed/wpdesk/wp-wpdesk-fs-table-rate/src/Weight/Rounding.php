<?php

/**
 * Rounding.
 *
 * @package WPDesk\FS\TableRate
 */
namespace FSVendor\WPDesk\FS\TableRate\Weight;

use FSVendor\WPDesk\FS\TableRate\Settings\RuleSettings;
/**
 * Can compute rounding precision from Flexible Shipping rules.
 */
class Rounding
{
    /**
     * @var array
     */
    private $shipping_method_rules;
    /**
     * WeightRounding constructor.
     *
     * @param RuleSettings[] $shipping_method_rules .
     */
    public function __construct(array $shipping_method_rules)
    {
        $this->shipping_method_rules = $shipping_method_rules;
    }
    /**
     * @return int
     */
    public function get_rounding_from_rules()
    {
        $rounding = 0;
        foreach ($this->shipping_method_rules as $rule) {
            $rounding = \max($rounding, $this->get_rounding_from_rule($rule));
        }
        return $rounding;
    }
    /**
     * @param RuleSettings $rule .
     *
     * @return int
     */
    private function get_rounding_from_rule($rule)
    {
        if ($rule->is_based_on_weight()) {
            return \max($this->get_rounding_from_value($rule->get_min()), $this->get_rounding_from_value($rule->get_max()));
        }
        return 0;
    }
    /**
     * @param string $value String representation for float.
     *
     * @return int
     */
    private function get_rounding_from_value($value)
    {
        $parts = \explode('.', $value);
        if (isset($parts[1])) {
            return \strlen($parts[1]);
        }
        return 0;
    }
}
