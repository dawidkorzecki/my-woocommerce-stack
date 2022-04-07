<?php
/**
 * Class Tracker
 *
 * @package WPDesk\FS\TableRate\Rule\PreconfiguredScenarios\Tracker
 */

namespace WPDesk\FS\TableRate\Rule\PreconfiguredScenarios\Tracker;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can append data to tracker.
 */
class Tracker implements Hookable {

	const TRACKER_DATA_FILTER_PRIORITY = \WPDesk_Flexible_Shipping_Tracker::TRACKER_DATA_FILTER_PRIORITY + 1;

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'wpdesk_tracker_data', array( $this, 'append_data' ), self::TRACKER_DATA_FILTER_PRIORITY );
	}

	/**
	 * .
	 *
	 * @param array $data .
	 *
	 * @retrun array
	 */
	public function append_data( $data ) {
		$tracking_data = new TrackingData();

		$data['flexible_shipping']['preconfigured_scenarios'] = $tracking_data->get_tracking_data();

		return $data;
	}

}
