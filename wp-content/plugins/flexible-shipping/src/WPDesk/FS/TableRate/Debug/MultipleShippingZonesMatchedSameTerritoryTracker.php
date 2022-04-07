<?php
/**
 * Class MultipleShippingZonesMatchedSameTerritoryTracker
 *
 * @package WPDesk\FS\TableRate\Debug
 */

namespace WPDesk\FS\TableRate\Debug;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can append multiple shipping zones matched data to tracker.
 */
class MultipleShippingZonesMatchedSameTerritoryTracker implements Hookable {

	const OPTION_NAME                              = 'fs-multiple-zones-matched-notice-count';
	const TRACKER_DATA_NAME                        = 'multiple_zones_matched_notice_count';
	const PRIORITY_AFTER_FLEXIBLE_SHIPPING_TRACKER = \WPDesk_Flexible_Shipping_Tracker::TRACKER_DATA_FILTER_PRIORITY + 1;

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'flexible-shipping/notice/multiple-zone-matches-same-territory', array( $this, 'update_counter_option' ) );
		add_filter( 'wpdesk_tracker_data', array( $this, 'append_tracker_data' ), self::PRIORITY_AFTER_FLEXIBLE_SHIPPING_TRACKER );
	}

	/**
	 * @return bool
	 */
	public function update_counter_option() {
		return update_option( self::OPTION_NAME, (int) get_option( self::OPTION_NAME, 0 ) + 1 );
	}

	/**
	 * @param array $data .
	 *
	 * @return array
	 */
	public function append_tracker_data( $data ) {
		if ( is_array( $data ) && isset( $data['flexible_shipping'] ) && is_array( $data['flexible_shipping'] ) ) {
			$data['flexible_shipping'][ self::TRACKER_DATA_NAME ] = (int) get_option( self::OPTION_NAME, 0 );
		}

		return $data;
	}

}
