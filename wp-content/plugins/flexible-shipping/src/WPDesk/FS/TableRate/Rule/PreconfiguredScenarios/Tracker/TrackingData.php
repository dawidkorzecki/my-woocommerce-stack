<?php
/**
 * Class TrackingData
 *
 * @package WPDesk\FS\TableRate\Rule\PreconfiguredScenarios\Tracker
 */

namespace WPDesk\FS\TableRate\Rule\PreconfiguredScenarios\Tracker;

/**
 * Can provide preconfigured scenarios tracking data.
 */
class TrackingData {

	const OPTION_NAME = 'fs-predefined-scenarios-tracking-data';
	const SCENARIO_USED = 'scenario_used';
	const SCENARIO_COUNT = 'scenario_count';
	const SAVED_SCENARIOS = 'saved_scenarios';
	const UNAVAILABLE_SCENARIOS = 'unavailable_scenarios';

	/**
	 * .
	 */
	public function increase_saved_scenarios() {
		$tracking_data = $this->get_tracking_data();
		$tracking_data[ self::SAVED_SCENARIOS ]++;
		$this->save_tracking_data( $tracking_data );
	}

	/**
	 * @param string $scenario .
	 */
	public function increase_scenario_use_count( $scenario ) {
		$tracking_data = $this->get_tracking_data();
		$tracking_data[ self::SCENARIO_COUNT ][ $scenario ] = isset( $tracking_data[ self::SCENARIO_COUNT ][ $scenario ] ) ? (int) $tracking_data[ self::SCENARIO_COUNT ][ $scenario ] + 1 : 1;
		$this->save_tracking_data( $tracking_data );
		$this->set_scenario_used_if_not_set();
	}

	/**
	 * @param string $scenario .
	 */
	public function increase_scenario_unavailable_count( $scenario ) {
		$tracking_data = $this->get_tracking_data();
		$tracking_data[ self::UNAVAILABLE_SCENARIOS ][ $scenario ] = isset( $tracking_data[ self::UNAVAILABLE_SCENARIOS ][ $scenario ] ) ? (int) $tracking_data[ self::UNAVAILABLE_SCENARIOS ][ $scenario ] + 1 : 1;
		$this->save_tracking_data( $tracking_data );
	}

	/**
	 * .
	 */
	public function set_scenario_used_if_not_set() {
		$tracking_data = $this->get_tracking_data();
		if ( 'yes' !== $tracking_data[ self::SCENARIO_USED ] ) {
			$tracking_data[ self::SCENARIO_USED ] = 'yes';
			$this->save_tracking_data( $tracking_data );
		}
	}

	/**
	 * @return array
	 */
	public function get_tracking_data() {
		$tracking_data = get_option( self::OPTION_NAME );

		$tracking_data = is_array( $tracking_data ) ? $tracking_data : [
			self::SCENARIO_USED  => 'no',
			self::SCENARIO_COUNT => [],
		];

		if ( ! isset( $tracking_data[ self::SCENARIO_USED ] ) ) {
			$tracking_data[ self::SCENARIO_USED ] = 'no';
		}

		if ( ! isset( $tracking_data[ self::SAVED_SCENARIOS ] ) ) {
			$tracking_data[ self::SAVED_SCENARIOS ] = 0;
		}

		if ( ! isset( $tracking_data[ self::SCENARIO_COUNT ] ) || ! is_array( $tracking_data[ self::SCENARIO_COUNT ] ) ) {
			$tracking_data[ self::SCENARIO_COUNT ] = [];
		}

		return $tracking_data;
	}

	/**
	 * @param array $tracking_data .
	 */
	private function save_tracking_data( array $tracking_data ) {
		update_option( self::OPTION_NAME, $tracking_data, false );
	}

}
