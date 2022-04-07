<?php

/**
 * Class WPDesk_Flexible_Shipping_SaaS_Settings
 */
class WPDesk_Flexible_Shipping_Logger_Settings {

	const LOGGER_CHANNEL_NAME = 'fs';

	const DEBUG_LOG_OPTION = 'debug_log';

	const OPTION_NAME = 'fs_logger_enabled';

	const OPTION_VALUE_DISABLED = '0';
	const OPTION_VALUE_ENABLED  = '1';
	/**
	 * Enabled.
	 *
	 * @var bool
	 */
	private $enabled = false;

	/**
	 * SaaS settings.
	 *
	 * @var WPDesk_Flexible_Shipping_Settings
	 */
	private $saas_settings;

	/**
	 * WPDesk_Flexible_Shipping_SaaS_Logger_Settings constructor.
	 *
	 * @param WPDesk_Flexible_Shipping_Settings $saas_settings SaaS settings.
	 */
	public function __construct( WPDesk_Flexible_Shipping_Settings $saas_settings = null ) {
		$option_value  = get_option( self::OPTION_NAME, self::OPTION_VALUE_DISABLED );
		$this->enabled = self::OPTION_VALUE_ENABLED === $option_value;

		$this->saas_settings = $saas_settings;
	}

	/**
	 * Get logger channel name.
	 *
	 * @return string
	 */
	public function get_logger_channel_name() {
		return self::LOGGER_CHANNEL_NAME;
	}

	/**
	 * Is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return $this->enabled;
	}

	/**
	 * Update option from saas settings.
	 */
	public function update_option_from_saas_settings() {
		$saas_settings_value = $this->saas_settings->get_option( self::DEBUG_LOG_OPTION );
		if ( ! empty( $saas_settings_value ) && 'yes' === $saas_settings_value ) {
			$option_value = self::OPTION_VALUE_ENABLED;
		} else {
			$option_value = self::OPTION_VALUE_DISABLED;
		}
		update_option( self::OPTION_NAME, $option_value );
		$this->enabled = self::OPTION_VALUE_ENABLED === $option_value;
	}

	/**
	 * Add fields to settings.
	 *
	 * @param array $settings Settings.
	 *
	 * @return array
	 */
	public function add_fields_to_settings( array $settings ) {
		$settings[ self::DEBUG_LOG_OPTION ] = array(
			'type'  => 'checkbox',
			'label' => __( 'Enable Debug Mode', 'flexible-shipping' ),
			'title' => __( 'Debug mode', 'flexible-shipping' ),
		);
		if ( 'yes' === $this->saas_settings->get_option( self::DEBUG_LOG_OPTION ) ) {
			$downloader = new WPDesk_Flexible_Shipping_Logger_Downloader();

			$settings[ self::DEBUG_LOG_OPTION ]['description'] = sprintf(
				// Translators: URL.
				__( '%1$sDownload debug.log file%2$s', 'flexible-shipping' ),
				sprintf( '<a href="%1$s" target="_blank">', $downloader->get_download_url() ),
				'</a>'
			);
		}
		return $settings;
	}

}
