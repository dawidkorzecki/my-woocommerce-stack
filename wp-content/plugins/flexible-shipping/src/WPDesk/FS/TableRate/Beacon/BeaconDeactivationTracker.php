<?php
/**
 * Deactivation tracker data.
 *
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate\Beacon;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can add beacon data to deactivation tracker data.
 */
class BeaconDeactivationTracker implements Hookable {

	const ADDITIONAL_DATA = 'additional_data';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'wpdesk_tracker_deactivation_data', array( $this, 'append_beacon_data_to_deactivation_tracker' ) );
	}

	/**
	 * Set new rules table data to data array.
	 *
	 * @param array $data Data.
	 *
	 * @internal
	 *
	 * @return array
	 */
	public function append_beacon_data_to_deactivation_tracker( array $data ) {
		if ( empty( $data[ self::ADDITIONAL_DATA ] ) || ! is_array( $data[ self::ADDITIONAL_DATA ] ) ) {
			$data[ self::ADDITIONAL_DATA ] = array();
		}
		$data[ self::ADDITIONAL_DATA ]['beacon'] = array( 'clicked' => 1 === (int) get_option( BeaconClickedAjax::OPTION_NAME, 0 ) ? 'yes' : 'no' );

		return $data;
	}

}
