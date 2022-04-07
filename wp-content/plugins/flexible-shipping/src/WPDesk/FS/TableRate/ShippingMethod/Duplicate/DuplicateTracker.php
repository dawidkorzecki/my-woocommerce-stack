<?php
/**
 * Class DuplicateTracker
 *
 * @package WPDesk\FS\TableRate\ShippingMethod\Duplicate
 */

namespace WPDesk\FS\TableRate\ShippingMethod\Duplicate;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 *  Tracking of duplicate usages.
 */
class DuplicateTracker implements Hookable {
	const PRIORITY_AFTER_FS_TRACKER = 12;

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'wpdesk_tracker_data', [ $this, 'add_tracking_data' ], self::PRIORITY_AFTER_FS_TRACKER );
	}

	/**
	 * @param array $data .
	 *
	 * @return array
	 */
	public function add_tracking_data( $data ): array {
		$data['flexible_shipping']['duplicate'] = (int) get_option( DuplicateAction::OPTION, 0 );

		return $data;
	}
}
