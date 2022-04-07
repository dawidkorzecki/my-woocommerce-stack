<?php
/**
 * Class Tracker
 *
 * @package WPDesk\FS\TableRate\Debug
 */

namespace WPDesk\FS\TableRate\Debug;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can handle tracker data related to debug mode.
 */
class DebugTracker implements Hookable {

	const NOTICE_OPTION_NAME  = 'flexible_shipping_debug_notice_added';
	const NOTICE_OPTION_VALUE = 1;

	const SETTINGS_OPTION_NAME  = 'flexible_shipping_debug_was_enabled';
	const SETTINGS_OPTION_VALUE = 1;

	const PRIORITY_AFTER_FS_TRACKER = 12;

	const ADDITIONAL_DATA = 'additional_data';

	const METHOD_DEBUG_MODE = 'method_debug_mode';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'flexible_shipping_process_admin_options', array( $this, 'update_settings_option_if_not_set_and_debug_enabled' ) );

		add_action( 'flexible_shipping_debug_notice_added', array( $this, 'update_notice_option_if_not_set' ) );

		add_filter( 'wpdesk_tracker_data', array( $this, 'append_debug_notice_data_to_tracker' ), self::PRIORITY_AFTER_FS_TRACKER );
		add_filter( 'wpdesk_tracker_deactivation_data', array( $this, 'append_debug_notice_data_to_deactivation_tracker' ) );
	}

	/**
	 * Update option when debug is enabled and option not set.
	 *
	 * @param array $shipping_method_settings .
	 *
	 * @return array
	 */
	public function update_settings_option_if_not_set_and_debug_enabled( $shipping_method_settings ) {
		if ( isset( $shipping_method_settings[ self::METHOD_DEBUG_MODE ] ) && 'yes' === $shipping_method_settings[ self::METHOD_DEBUG_MODE ]
			&& self::SETTINGS_OPTION_VALUE !== (int) get_option( self::SETTINGS_OPTION_NAME, 0 )
		) {
			update_option( self::SETTINGS_OPTION_NAME, self::SETTINGS_OPTION_VALUE );
		}
		return $shipping_method_settings;
	}

	/**
	 * Update option when notice is added.
	 *
	 * @internal
	 */
	public function update_notice_option_if_not_set() {
		if ( self::NOTICE_OPTION_VALUE !== (int) get_option( self::NOTICE_OPTION_NAME, 0 ) ) {
			update_option( self::NOTICE_OPTION_NAME, self::NOTICE_OPTION_VALUE );
		}
	}

	/**
	 * Append debug notice dara to tracker.
	 *
	 * @param array $data .
	 *
	 * @return array
	 *
	 * @internal
	 */
	public function append_debug_notice_data_to_tracker( $data ) {
		$data['flexible_shipping']['debug_notice_was_enabled'] = self::SETTINGS_OPTION_VALUE === (int) get_option( self::SETTINGS_OPTION_NAME, 0 ) ? 'yes' : 'no';
		$data['flexible_shipping']['debug_notice_displayed'] = self::NOTICE_OPTION_VALUE === (int) get_option( self::NOTICE_OPTION_NAME, 0 ) ? 'yes' : 'no';

		return $data;
	}

	/**
	 * Set new rules table data to data array.
	 *
	 * @param array $data Data.
	 *
	 * @return array
	 *
	 * @internal
	 */
	public function append_debug_notice_data_to_deactivation_tracker( array $data ) {
		if ( empty( $data[ self::ADDITIONAL_DATA ] ) || ! is_array( $data[ self::ADDITIONAL_DATA ] ) ) {
			$data[ self::ADDITIONAL_DATA ] = array();
		}
		$data[ self::ADDITIONAL_DATA ]['fs_debug_notice_was_enabled'] = self::SETTINGS_OPTION_VALUE === (int) get_option( self::SETTINGS_OPTION_NAME, 0 ) ? 'yes' : 'no';
		$data[ self::ADDITIONAL_DATA ]['fs_debug_notice_displayed'] = self::NOTICE_OPTION_VALUE === (int) get_option( self::NOTICE_OPTION_NAME, 0 ) ? 'yes' : 'no';

		return $data;
	}

}
