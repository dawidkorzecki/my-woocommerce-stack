<?php

/**
 * Class WPDesk_Flexible_Shipping_UK_States
 */
class WPDesk_Flexible_Shipping_Shorcode_Unit_Weight implements \FSVendor\WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \FSVendor\WPDesk\PluginBuilder\Plugin\PluginAccess;

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_shortcode( 'unit_weight', [ $this, 'shortcode_unit_weight' ] );
	}

	/**
	 * Shortcode Unit Weight.
	 *
	 * @return string
	 */
	public function shortcode_unit_weight() {
		return '[' . get_option( 'woocommerce_weight_unit', '' ) . ']';
	}

}
