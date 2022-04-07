<?php
/**
 * Class ShippingMethodFirstSettingsTime
 *
 * @package WPDesk\FS\Nps
 */

namespace WPDesk\FS\Nps;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can save option on Flexible Shipping method settings saved.
 */
class ShippingMethodFirstSettingsTime implements Hookable {

	const OPTION_NAME = 'flexible_shipping_method_settings_saved';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'flexible_shipping_process_admin_options', array( $this, 'add_option_for_nps' ) );
	}

	/**
	 * @param array $setting .
	 *
	 * @return array
	 */
	public function add_option_for_nps( $setting ) {
		add_option( self::OPTION_NAME, current_time( 'timestamp' ) );

		return $setting;
	}

}
