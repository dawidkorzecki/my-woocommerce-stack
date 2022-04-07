<?php
/**
 * Class BeaconDisplayStrategy
 *
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate\Beacon;

use Exception;
use FSVendor\WPDesk\Beacon\BeaconGetShouldShowStrategy;
use WC_Shipping_Zones;
use WPDesk\FS\TableRate\ShippingMethodSingle;
use WPDesk_Flexible_Shipping;

/**
 * Beacon display strategy.
 */
class BeaconDisplayStrategy extends BeaconGetShouldShowStrategy {

	/**
	 * BeaconDisplayStrategy constructor.
	 */
	public function __construct() {
		$conditions = array(
			array(
				'page' => 'wc-settings',
				'tab'  => 'shipping',
			),
		);
		parent::__construct( $conditions );
	}

	/**
	 * Should Beacon be visible?
	 *
	 * @return bool
	 */
	public function shouldDisplay() {
		if ( parent::shouldDisplay() ) {
			if ( isset( $_GET['instance_id'] ) && ! wpdesk_is_plugin_active( 'flexible-shipping-pro/flexible-shipping-pro.php' ) ) { // phpcs:ignore
				$instance_id = sanitize_text_field( $_GET['instance_id'] );  // phpcs:ignore
				try {
					$shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );
					if ( $shipping_method && ( ( $shipping_method instanceof WPDesk_Flexible_Shipping ) || ( $shipping_method instanceof ShippingMethodSingle ) ) ) {

						return true;
					}
				} catch ( Exception $e ) {

					return false;
				}
			}
			if ( isset( $_GET['section'] ) && sanitize_key( $_GET['section'] ) === 'flexible_shipping_info' ) { // phpcs:ignore

				return true;
			}
		}

		return false;
	}
}
