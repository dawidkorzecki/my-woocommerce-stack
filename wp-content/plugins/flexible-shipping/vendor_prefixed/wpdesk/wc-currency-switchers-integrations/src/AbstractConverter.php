<?php

/**
 * Abstract converter.
 *
 * @package WPDesk\WooCommerce\CurrencySwitchers
 */
namespace FSVendor\WPDesk\WooCommerce\CurrencySwitchers;

/**
 * Abstract class for converters.
 */
abstract class AbstractConverter implements \FSVendor\WPDesk\WooCommerce\CurrencySwitchers\SwitcherConverter
{
    /**
     * @inheritDoc
     */
    abstract function convert($value);
    /**
     * @inheritDoc
     */
    public function convert_array($values)
    {
        foreach ($values as $key => $value) {
            if ($value) {
                $values[$key] = $this->convert($value);
            }
        }
        return $values;
    }
}
