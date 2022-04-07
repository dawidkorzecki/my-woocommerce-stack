<?php
/**
 * Class WooSettingsPageChecker
 *
 * @package WPDesk\FS\Helpers
 */

namespace WPDesk\FS\Helpers;

use WC_Shipping_Method;
use WC_Shipping_Zones;
use WPDesk\FS\TableRate\ShippingMethodSingle;
use WPDesk_Flexible_Shipping;

/**
 * Helper for WooCommerce Settings Page.
 */
class WooSettingsPageChecker {
	/**
	 * @return bool
	 */
	public function is_fs_instance_method_edit(): bool {
		$tab  = $this->filter_input( INPUT_GET, 'tab' );
		$page = $this->filter_input( INPUT_GET, 'page' );

		if ( 'wc-settings' !== $page || 'shipping' !== $tab ) {
			return false;
		}

		$instance_id = absint( wp_unslash( $this->filter_input( INPUT_GET, 'instance_id' ) ) );

		if ( ! $instance_id ) {
			return false;
		}

		$shipping_method = $this->get_shipping_method( $instance_id );

		if ( ! $shipping_method ) {
			return false;
		}

		return is_a( $shipping_method, WPDesk_Flexible_Shipping::class ) || is_a( $shipping_method, ShippingMethodSingle::class );
	}

	/**
	 * @param int $instance_id .
	 *
	 * @return bool|WC_Shipping_Method
	 * @codeCoverageIgnore
	 */
	protected function get_shipping_method( int $instance_id ) {
		return WC_Shipping_Zones::get_shipping_method( $instance_id );
	}

	/**
	 * @param int    $type     .
	 * @param string $var_name .
	 *
	 * @return mixed
	 * @codeCoverageIgnore
	 */
	protected function filter_input( int $type, string $var_name ) {
		return filter_input( $type, $var_name );
	}
}
