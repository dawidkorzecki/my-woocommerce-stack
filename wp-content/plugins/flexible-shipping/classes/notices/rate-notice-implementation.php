<?php

namespace WPDesk\FS\Rate;

class RateNoticeImplementation extends RateNotice
{

	/**
	 * Action links
	 *
	 * @return array
	 */
	protected function action_links() {
		$actions[] = sprintf(
			__( '%1$sOk, you deserved it%2$s', 'flexible-shipping' ),
			'<a target="_blank" href="' . esc_url( 'https://wpde.sk/fs-rate-2' ) . '">',
			'</a>'
		);
		$actions[] = sprintf(
			__( '%1$sNope, maybe later%2$s', 'flexible-shipping' ),
			'<a data-type="date" class="fs-close-temporary-notice notice-dismiss-link" data-source="' . self::CLOSE_TEMPORARY_NOTICE . '" href="#">',
			'</a>'
		);
		$actions[] = sprintf(
			__( '%1$sI already did%2$s', 'flexible-shipping' ),
			'<a class="close-rate-notice notice-dismiss-link" data-source="already-did" href="#">',
			'</a>'
		);

		return $actions;
	}

	/**
	 * Should show message
	 *
	 * @return bool
	 */
	public function should_show_message() {
		$notice_date_dissmis = get_option( WPDesk_Flexible_Shipping_Rate_Notice::SETTINGS_OPTION_RATE_NOTICE_DATE_DISMISS, date( "Y-m-d H:i:s", strtotime( 'NOW + 2 weeks' ) ) );
		$notice_date         = strtotime( $notice_date_dissmis );
		$current_date        = strtotime( 'NOW' );
		$difference          = $current_date - $notice_date;
		$days                = (int) floor( $difference / ( 60 * 60 * 24 ) );
		if ( $days > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * Get rate message
	 *
	 * @return string
	 */
	protected function get_message() {
		$message   = __( 'Awesome, you\'ve been using Flexible Shipping for more than 2 weeks. Could you please do me a BIG favor and give it a 5-star rating on WordPress? ~FS Team', 'flexible-shipping' );
		$message .= '<br/>';
		$message .= implode( ' | ', $this->action_links() );
		return $message;
	}

}
