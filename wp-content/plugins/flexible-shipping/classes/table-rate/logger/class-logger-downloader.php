<?php

/**
 * Class WPDesk_Flexible_Shipping_SaaS_Settings
 */
class WPDesk_Flexible_Shipping_Logger_Downloader implements \FSVendor\WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \FSVendor\WPDesk\PluginBuilder\Plugin\PluginAccess;

	const GET_PARAMETER = 'fs-get-log';

	/**
	 * Logger factory.
	 *
	 * @var \FSVendor\WPDesk\Logger\WPDeskLoggerFactory
	 */
	private $logger_factory;

	/**
	 * WPDesk_Flexible_Shipping_SaaS_Logger_Downloader constructor.
	 *
	 * @param \FSVendor\WPDesk\Logger\WPDeskLoggerFactory $logger_factory Logger factory.
	 */
	public function __construct( \FSVendor\WPDesk\Logger\WPDeskLoggerFactory $logger_factory = null ) {
		$this->logger_factory = $logger_factory;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'admin_init', [ $this, 'handle_log_file_download' ] );
	}

	/**
	 * Get download URL.
	 *
	 * @return string
	 */
	public function get_download_url() {
		return admin_url( 'admin.php?' . self::GET_PARAMETER . '=1' );
	}

	/**
	 * Handle log file download.
	 */
	public function handle_log_file_download() {
		if ( isset( $_GET[ self::GET_PARAMETER ] ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				$logger_settings = new WPDesk_Flexible_Shipping_Logger_Settings();
				$file_name = $this->logger_factory->getFileName( $logger_settings->get_logger_channel_name() );
				if ( file_exists( $file_name ) ) {
					header( 'Content-Type: text/plain' );
					header( 'Content-Disposition: attachment; filename="fs-debug.log"' );
					readfile( $file_name );
				} else {
					wp_die( __( 'File not exists!', 'flexible-shipping' ) );
				}
			} else {
				wp_die( __( 'Insufficient privileges!', 'flexible-shipping' ) );
			}
		}
	}

}
