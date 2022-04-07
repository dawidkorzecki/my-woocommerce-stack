<?php
/**
 * Interface MethodSettings
 *
 * @package WPDesk\FS\TableRate\ShippingMethod
 */

namespace WPDesk\FS\TableRate\ShippingMethod;

/**
 * Interface for Method Settings.
 */
interface MethodSettings {

	/**
	 * @param array $method_settings Current method settings.
	 * @param bool  $with_integration_settings Append integration settings.
	 *
	 * @return array
	 */
	public function get_settings_fields( array $method_settings, $with_integration_settings );
}
