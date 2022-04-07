<?php
/**
 * Class NpsDisplayDecision
 *
 * @package WPDesk\FS\Nps
 */

namespace WPDesk\FS\Nps;

use FSVendor\WPDesk\Nps\DisplayDecisions\DisplayNpsDecision;

/**
 * Can decide if display NPS.
 */
class NpsDisplayDecision implements DisplayNpsDecision {

	const NEW_USERS_AFTER_THIS_DATE = '2021-06-07 01:00:00';

	/**
	 * @var DisplayNpsDecision
	 */
	private $user_display_decision;

	/**
	 * NpsDisplayDecision constructor.
	 *
	 * @param DisplayNpsDecision $user_display_decision .
	 */
	public function __construct( DisplayNpsDecision $user_display_decision ) {
		$this->user_display_decision = $user_display_decision;
	}

	/**
	 * @inheritDoc
	 */
	public function should_display_nps() {
		$should_display_nps = $this->is_new_installation() && $this->are_shipping_method_settings_saved() && $this->user_display_decision->should_display_nps();
		$should_display_nps =
			/**
			 * Should display NPS?
			 *
			 * @param bool $should_display_nps
			 */
			apply_filters( 'flexible-shipping/nps/should-display-nps', $should_display_nps );

		return $should_display_nps;
	}

	/**
	 * @return bool
	 */
	private function is_new_installation() {
		return strtotime( self::NEW_USERS_AFTER_THIS_DATE ) < $this->activation_date_according_to_wpdesk_helper();
	}

	/**
	 * Activation date according to wpdesk helper.
	 *
	 * @return int Activation date timestamp.
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
	 * @return bool
	 */
	private function are_shipping_method_settings_saved() {
		return get_option( ShippingMethodFirstSettingsTime::OPTION_NAME, false ) !== false;
	}

}
