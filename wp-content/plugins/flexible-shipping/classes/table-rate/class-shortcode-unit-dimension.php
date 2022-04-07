<?php

/**
 * Class WPDesk_Flexible_Shipping_UK_States
 */
class WPDesk_Flexible_Shipping_Shorcode_Unit_Dimension implements \FSVendor\WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \FSVendor\WPDesk\PluginBuilder\Plugin\PluginAccess;

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_shortcode( 'unit_dimension', [ $this, 'shortcode_unit_dimension' ] );
	}

	/**
	 * Shortcode Unit Dimension
	 *
	 * @return string
	 */
	public function shortcode_unit_dimension() {
		return '[' . get_option( 'woocommerce_dimension_unit', '' ) . ']';
	}

}
