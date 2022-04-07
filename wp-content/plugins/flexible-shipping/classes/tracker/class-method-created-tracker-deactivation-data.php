<?php
/**
 * Class WPDesk_Flexible_Shipping_Method_Created_Tracker_Deactivation_Data
 *
 * @package Flexible Shipping.
 */

use WPDesk\FS\TableRate\ShippingMethodSingle;

/**
 * Class by which we can push created methods data to the deactivation filter
 */
class WPDesk_Flexible_Shipping_Method_Created_Tracker_Deactivation_Data implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable {

	const OPTION_FS_METHOD_CREATED_TRACKER = 'fs_method_created_tracker';

	const TRACK_USERS_AFTER_THIS_DATE = '2019-04-11 01:00:00';

	const NO_ACTION_WITH_FS                      = 0;
	const FLEXIBLE_SHIPPING_METHOD_ADDED_TO_ZONE = 1;
	const FLEXIBLE_SHIPPING_METHOD_ADDED_TO_FS   = 2;
	const FLEXIBLE_SHIPPING_SINGLE_ADDED_TO_ZONE = 3;
	const FLEXIBLE_SHIPPING_FS_INFO_VIEWED       = 4;

	/**
	 * Fires hooks
	 */
	public function hooks() {
		add_filter( 'wpdesk_tracker_deactivation_data', array( $this, 'append_variant_id_to_data' ) );
		add_action( 'woocommerce_shipping_zone_method_added', array( $this, 'maybe_update_option_on_zone_method_added' ), 10, 3 );
		add_filter( 'flexible_shipping_process_admin_options', array( $this, 'maybe_update_option_on_fs_method_saved' ) );
		add_action( 'admin_footer', array( $this, 'update_option_when_in_fs_info' ) );
	}

	/**
	 * .
	 */
	public function update_option_when_in_fs_info() {
		if ( ! $this->is_old_installation() && isset( $_GET['page'] ) && isset( $_GET['tab'] ) && isset( $_GET['section'] )
			&& 'wc-settings' === $_GET['page'] && 'shipping' === $_GET['tab'] && 'flexible_shipping_info' === $_GET['section']
		) {
			$option_value = intval( get_option( self::OPTION_FS_METHOD_CREATED_TRACKER, '0' ) );
			if ( self::NO_ACTION_WITH_FS === $option_value ) {
				update_option( self::OPTION_FS_METHOD_CREATED_TRACKER, self::FLEXIBLE_SHIPPING_FS_INFO_VIEWED );
			}
		}
	}

	/**
	 * Maybe update option on FS method saved.
	 *
	 * @param array $shipping_method Shipping method.
	 *
	 * @return array
	 */
	public function maybe_update_option_on_fs_method_saved( array $shipping_method ) {
		if ( ! $this->is_old_installation() ) {
			$option_value = intval( get_option( self::OPTION_FS_METHOD_CREATED_TRACKER, '0' ) );
			if ( self::FLEXIBLE_SHIPPING_METHOD_ADDED_TO_FS !== $option_value ) {
				update_option( self::OPTION_FS_METHOD_CREATED_TRACKER, self::FLEXIBLE_SHIPPING_METHOD_ADDED_TO_FS );
			}
		}
		return $shipping_method;
	}

	/**
	 * Maybe update option on zone method added action.
	 *
	 * @param int    $instance_id Instance ID.
	 * @param string $type Type.
	 * @param int    $zone_id Zone ID.
	 */
	public function maybe_update_option_on_zone_method_added( $instance_id, $type, $zone_id ) {
		if ( WPDesk_Flexible_Shipping::METHOD_ID === $type ) {
			if ( ! $this->is_old_installation() ) {
				$option_value = intval( get_option( self::OPTION_FS_METHOD_CREATED_TRACKER, '0' ) );
				if ( self::NO_ACTION_WITH_FS === $option_value || self::FLEXIBLE_SHIPPING_FS_INFO_VIEWED === $option_value ) {
					update_option( self::OPTION_FS_METHOD_CREATED_TRACKER, self::FLEXIBLE_SHIPPING_METHOD_ADDED_TO_ZONE );
				}
			}
		}
		if ( ShippingMethodSingle::SHIPPING_METHOD_ID === $type ) {
			if ( ! $this->is_old_installation() ) {
				update_option( self::OPTION_FS_METHOD_CREATED_TRACKER, self::FLEXIBLE_SHIPPING_SINGLE_ADDED_TO_ZONE );
			}
		}
	}

	/**
	 * If this a old user? If so then FS should work like always.
	 *
	 * @return bool
	 */
	private function is_old_installation() {
		return strtotime( self::TRACK_USERS_AFTER_THIS_DATE ) > $this->activation_date_according_to_wpdesk_helper();
	}

	/**
	 * Activation date according to wpdesk helper.
	 *
	 * @return int timestamp
	 */
	private function activation_date_according_to_wpdesk_helper() {
		$option_name     = 'plugin_activation_flexible-shipping/flexible-shipping.php';
		$activation_date = get_option( $option_name, current_time( 'mysql' ) );

		if ( ! $activation_date ) {
			return time();
		}

		return strtotime( $activation_date );
	}

	/**
	 * Set fs_method_created option value to data array
	 *
	 * @param array $data Data.
	 *
	 * @return array
	 */
	public function append_variant_id_to_data( array $data ) {
		if ( ! $this->is_old_installation() ) {
			if ( WPDesk_Flexible_Shipping_Tracker::is_plugin_flexible_shipping_in_data( $data ) ) {
				$data['fs_method_created'] = intval( get_option( self::OPTION_FS_METHOD_CREATED_TRACKER, '0' ) );
			}
		}
		return $data;
	}

}
