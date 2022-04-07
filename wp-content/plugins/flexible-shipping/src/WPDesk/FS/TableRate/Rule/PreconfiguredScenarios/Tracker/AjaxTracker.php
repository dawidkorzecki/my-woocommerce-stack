<?php
/**
 * Class AjaxTracker
 *
 * @package WPDesk\FS\TableRate\Rule\PreconfiguredScenarios\Tracker
 */

namespace WPDesk\FS\TableRate\Rule\PreconfiguredScenarios\Tracker;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can handle Ajax requests for predefined scenarios tracking.
 */
class AjaxTracker implements Hookable {

	const AJAX_ACTION = 'flexible_shipping_predefined_scenario_tracking';

	/**
	 * .
	 */
	public function hooks() {
		add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'handle_ajax' ] );
	}

	/**
	 * @internal
	 */
	public function handle_ajax() {
		check_ajax_referer( self::AJAX_ACTION, 'security' );
		$tracking_data = new TrackingData();
		$action        = isset( $_REQUEST['tracking_action'] ) ? sanitize_key( wp_unslash( $_REQUEST['tracking_action'] ) ) : '';
		$scenario      = isset( $_REQUEST['scenario'] ) ? sanitize_key( wp_unslash( $_REQUEST['scenario'] ) ) : '';

		if ( 'count_scenario' === $action ) {
			if ( $scenario ) {
				$tracking_data->increase_scenario_use_count( $scenario );
			}
		}
		if ( 'save_scenario' === $action ) {
			$tracking_data->increase_saved_scenarios();
		}
		if ( 'scenario_unavailable' === $action ) {
			$tracking_data->increase_scenario_unavailable_count( $scenario );
		}

		wp_send_json_success();
	}


}
