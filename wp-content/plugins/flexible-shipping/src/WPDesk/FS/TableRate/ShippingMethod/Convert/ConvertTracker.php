<?php
/**
 * Class ConvertTracker
 *
 * @package WPDesk\FS\TableRate\ShippingMethod\Convert
 */

namespace WPDesk\FS\TableRate\ShippingMethod\Convert;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WC_Shipping_Zones;
use WPDesk_Flexible_Shipping;

/**
 * Tracking of convert usages.
 */
class ConvertTracker implements Hookable {
	const OPTION_FIRST = 'flexible_shipping_convert_first';
	const OPTION_AGAIN = 'flexible_shipping_convert_again';
	const OPTION_DELETED = 'flexible_shipping_convert_deleted';

	const PRIORYTY_AFTER_FS_TRACKER = 12;

	/**
	 * @var array .
	 */
	private $to_delete_methods = array();

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'wpdesk_tracker_data', array( $this, 'add_tracking_data' ), self::PRIORYTY_AFTER_FS_TRACKER );

		// Trackers.
		add_action( 'woocommerce_shipping_zone_method_deleted', array( $this, 'track_deleted_method' ) );
		add_action( 'flexible-shipping/group-method/converted-method', array( $this, 'track_convert_method' ) );

		// Prepare before tracking.
		add_action(
			'wp_ajax_woocommerce_shipping_zone_methods_save_changes',
			array(
				$this,
				'check_deleted_methods',
			),
			5
		);
	}

	/**
	 * Track running of converting.
	 */
	public function track_convert_method() {
		$converting_again = filter_input( INPUT_GET, 'converting_again' );

		$this->update_count( $converting_again ? self::OPTION_AGAIN : self::OPTION_FIRST );
	}

	/**
	 * Check if FS Group has been deleted.
	 */
	public function check_deleted_methods() {
		if ( ! isset( $_POST['wc_shipping_zones_nonce'], $_POST['zone_id'], $_POST['changes'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( wp_unslash( $_POST['wc_shipping_zones_nonce'] ), 'wc_shipping_zones_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$changes = wp_unslash( $_POST['changes'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! isset( $changes['methods'] ) ) {
			return;
		}

		foreach ( $changes['methods'] as $instance_id => $data ) {
			if ( ! isset( $data['deleted'] ) ) {
				continue;
			}

			$shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );

			if ( ! $shipping_method instanceof WPDesk_Flexible_Shipping ) {
				continue;
			}

			$this->to_delete_methods[] = $instance_id;
		}
	}

	/**
	 * @param int $instance_id .
	 */
	public function track_deleted_method( $instance_id ) {
		if ( ! in_array( $instance_id, $this->to_delete_methods ) ) {
			return;
		}

		$this->update_count( self::OPTION_DELETED );
	}

	/**
	 * @param array $data .
	 *
	 * @return array
	 */
	public function add_tracking_data( $data ) {
		$data['flexible_shipping']['convert'] = $this->prepare_data();

		return $data;
	}

	/**
	 * @return array
	 */
	private function prepare_data() {
		return array(
			'first'   => (int) get_option( self::OPTION_FIRST, 0 ),
			'again'   => (int) get_option( self::OPTION_AGAIN, 0 ),
			'deleted' => (int) get_option( self::OPTION_DELETED, 0 ),
		);
	}

	/**
	 * Update option count.
	 *
	 * @param string $option .
	 */
	private function update_count( $option ) {
		update_option( $option, (int) get_option( $option, 0 ) + 1 );
	}
}
