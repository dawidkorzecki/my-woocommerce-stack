<?php
/**
 * Tracker.
 *
 * @package WPDesk\FS\Onboarding
 */

namespace WPDesk\FS\Onboarding\TableRate;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Class Tracker
 */
class Tracker implements Hookable {

	/**
	 * @var FinishOption
	 */
	private $option;

	/**
	 * Tracker constructor.
	 *
	 * @param FinishOption $option .
	 */
	public function __construct( FinishOption $option ) {
		$this->option = $option;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'wpdesk_tracker_data', [ $this, 'add_tracking_data' ], 12 );
	}

	/**
	 * Add onboarding data to tracker.
	 *
	 * @param array $data .
	 *
	 * @return array
	 */
	public function add_tracking_data( $data ): array {
		$data['flexible_shipping']['onboarding']['table_rate'] = $this->option->get_option_value();

		return $data;
	}
}
