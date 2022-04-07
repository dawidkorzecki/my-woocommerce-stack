<?php
/**
 * Interface AdditionalCost
 *
 * @package WPDesk\FS\TableRate\Rule\Cost
 */

namespace WPDesk\FS\TableRate\Rule\Cost;

use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\Forms\FieldProvider;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;
use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettings;

/**
 * Additional Costs Interface.
 */
interface AdditionalCost {

	/**
	 * @return string
	 */
	public function get_based_on();

	/**
	 * @return string
	 */
	public function get_name();

	/**
	 * @param ShippingContents $shipping_contents        .
	 * @param array            $additional_cost_settings .
	 * @param LoggerInterface  $logger                   .
	 *
	 * @return float
	 */
	public function calculate_cost( ShippingContents $shipping_contents, array $additional_cost_settings, LoggerInterface $logger );

	/**
	 * @param ShippingContents $shipping_contents        .
	 * @param array            $additional_cost_settings .
	 * @param LoggerInterface  $logger                   .
	 * @param MethodSettings   $method_settings          .
	 *
	 * @return float
	 */
	public function calculate_cost_with_method_settings( ShippingContents $shipping_contents, array $additional_cost_settings, LoggerInterface $logger, MethodSettings $method_settings );
}
