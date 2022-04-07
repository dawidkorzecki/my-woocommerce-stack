<?php
/**
 * Class SettingsDisplayPreparer
 *
 * @package WPDesk\FS\TableRate\ShippingMethod
 */

namespace WPDesk\FS\TableRate\ShippingMethod;

use FSVendor\WPDesk\Beacon\Beacon\WooCommerceSettingsFieldsModifier;

/**
 * Can prepare settings fields for display.
 */
class SettingsDisplayPreparer {

	/**
	 * Prepare settings for display.
	 * Ie. set select options when options are from API - prevents unwanted API calls on non settings requests.
	 *
	 * @param array $settings .
	 *
	 * @return array
	 */
	public function prepare_settings_for_display( array $settings ) {
		$modifier = new WooCommerceSettingsFieldsModifier();
		$settings = $modifier->append_beacon_search_data_to_fields( $settings );

		return apply_filters( 'flexible-shipping/settings/prepare-for-display', $settings );
	}
}
