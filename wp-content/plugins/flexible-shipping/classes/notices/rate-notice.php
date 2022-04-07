<?php

namespace WPDesk\FS\Rate;

/**
 * Display rate notice.
 */
class WPDesk_Flexible_Shipping_Rate_Notice implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable {

	const CLOSE_TEMPORARY_NOTICE_DATE    = 'close-temporary-notice-date';
	const CLOSE_ALREADY_DID              = 'already-did';

	const SETTINGS_OPTION_DISMISSED_COUNT = 'flexible_shipping_rate_dismissed_count';

	const SETTINGS_RATE_NOTICE_VARIANT_ID = 'flexible_shipping_rate_notice_variant_id';

	const SETTINGS_OPTION_RATE_NOTICE_DATE_DISMISS = 'flexible_shipping_rate_notice_date_dismiss';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'admin_notices', array( $this, 'add_admin_notice_action' ) );
		add_action( 'wpdesk_notice_dismissed_notice', array( $this, 'reset_rate_variant_action' ), 10, 2 );
		add_action( 'wp_ajax_flexible_shipping_rate_notice', array( $this, 'wp_ajax_flexible_shipping_rate_notice' ) );
		add_action( 'wp_ajax_flexible_shipping_close_rate_notice', array( $this, 'wp_ajax_flexible_shipping_close_rate_notice' ) );
	}

	/**
	 * Reset rate variant
	 *
	 * @param string $notice_name Notice name.
	 * @param string $source      Sorcue.
	 */
	public function reset_rate_variant_action( $notice_name, $source ) {
		if ( 'flexible_shipping_rate_plugin' !== $notice_name ) {
			return false;
		}

		$dismissed_count = (int) get_option( self::SETTINGS_OPTION_DISMISSED_COUNT, 0 );

		if ( ( empty( $source ) || self::CLOSE_TEMPORARY_NOTICE_DATE === $source ) ) {
			update_option( self::SETTINGS_OPTION_RATE_NOTICE_DATE_DISMISS, date( "Y-m-d H:i:s", strtotime( 'NOW + 2 weeks' ) ) );
			delete_option( \FSVendor\WPDesk\Notice\PermanentDismissibleNotice::OPTION_NAME_PREFIX . $notice_name );
			update_option( self::SETTINGS_OPTION_DISMISSED_COUNT, 1 );
		} elseif ( self::CLOSE_ALREADY_DID === $source ) {
			update_option( \FSVendor\WPDesk\Notice\PermanentDismissibleNotice::OPTION_NAME_PREFIX . $notice_name, 1 );
		}

		if ( $dismissed_count > 0 ) {
			update_option( \FSVendor\WPDesk\Notice\PermanentDismissibleNotice::OPTION_NAME_PREFIX . $notice_name, 1 );
		}

	}

	/**
	 * Should display notice.
	 *
	 * @return bool
	 */
	private function should_display_notice() {
		$current_screen     = get_current_screen();
		$display_on_screens = [ 'shop_order', 'edit-shop_order', 'woocommerce_page_wc-settings' ];
		if ( ! empty( $current_screen ) && in_array( $current_screen->id, $display_on_screens, true ) ) {
			return true;
		}
		return false;
	}

    /**
     * Generate rate notice variant ID.
     *
     * @return string
     */
    private function generate_rate_notice_variant_id()
    {
        return 'notice_2';
    }

	/**
	 * Set defaults for notice.
	 */
	private function set_notice_defaults() {
		add_option( self::SETTINGS_OPTION_RATE_NOTICE_DATE_DISMISS, date( "Y-m-d H:i:s", strtotime('NOW + 2 weeks') ) );
	}

	/**
	 * Add admin notice.
	 */
	public function add_admin_notice_action()
	{
		if ( $this->should_display_notice() ) {
			$instance = new RateNoticeImplementation();
			$this->set_notice_defaults();
			if( $instance->should_show_message() ) {
                $instance->show_message();
            }

		}
	}


}
