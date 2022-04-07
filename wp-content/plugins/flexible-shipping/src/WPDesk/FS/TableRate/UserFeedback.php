<?php
/**
 * User feedback.
 *
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\Tracker\UserFeedback\AjaxUserFeedbackDataHandler;

/**
 * Can display and handle user feedback when disabling new rules settings table.
 */
class UserFeedback implements Hookable {

	const THICKBOX_ID = 'new-rules-table-feedback';
	const USER_FEEDBACK_OPTION = 'flexible_shipping_new_rules_feedback';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'wpdesk_tracker_user_feedback_data_handled', array( $this, 'save_user_feedback' ) );
	}

	/**
	 * Saves user feedback for lated sending by Tracker.
	 *
	 * @param array $payload Tracker request data.
	 */
	public function save_user_feedback( $payload ) {
		if ( is_array( $payload ) && isset( $payload[ AjaxUserFeedbackDataHandler::FEEDBACK_ID ] ) && self::THICKBOX_ID === $payload[ AjaxUserFeedbackDataHandler::FEEDBACK_ID ] ) {
			update_option( self::USER_FEEDBACK_OPTION, $payload );
		}
	}

}
