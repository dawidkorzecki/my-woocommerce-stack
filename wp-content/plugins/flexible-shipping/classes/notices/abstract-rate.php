<?php

namespace WPDesk\FS\Rate;

abstract class RateNotice implements RateNoticeInterface  {

	const NOTICE_NAME = 'flexible_shipping_rate_plugin';

	const CLOSE_TEMPORARY_NOTICE  = 'close-temporary-notice-date';

	/**
	 * Get message
	 *
	 * @return mixed
	 */
	abstract protected function get_message();

	/**
	 * Action links
	 *
	 * @return array
	 */
	protected function action_links() {
		$actions[] = sprintf(
			__( '%1$sOk, you deserved it%2$s', 'flexible-shipping' ),
			'<a target="_blank" href="' . esc_url( 'https://wpde.sk/fs-rate' ) . '">',
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
	    return true;
    }

	/**
	 * Show admin notice
	 *
	 * @return string|void
	 */
	public function show_message() {
		new \FSVendor\WPDesk\Notice\PermanentDismissibleNotice(
			$this->get_message(),
			self::NOTICE_NAME,
			\FSVendor\WPDesk\Notice\Notice::NOTICE_TYPE_INFO,
			10,
			array(
				'class' => self::NOTICE_NAME,
				'id'    => self::NOTICE_NAME,
			)
		);
	}

}
