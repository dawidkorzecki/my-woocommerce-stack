<?php

/**
 * Class WPDesk_Flexible_Shipping_Logger_Factory
 *
 * @deprecated
 */
class WPDesk_Flexible_Shipping_Logger_Factory {
	const NULL_LOG_NAME = 'null-log';

	/**
	 * Logger.
	 *
	 * @var \Psr\Log\LoggerInterface
	 */
	private static $logger;

	/**
	 * Create logger.
	 *
	 * @return \Psr\Log\LoggerInterface
	 */
	public static function create_logger() {
		if ( null === self::$logger ) {
			$logger_settings = new WPDesk_Flexible_Shipping_Logger_Settings();

			if ( $logger_settings->is_enabled() ) {
				self::$logger = new WPDesk_Flexible_Shipping_WooCommerce_Context_Logger(
					@\FSVendor\WPDesk\Logger\LoggerFacade::get_logger( $logger_settings->get_logger_channel_name() ),
					$logger_settings->get_logging_context()
				);
			} else {
				self::$logger = new \Monolog\Logger( self::NULL_LOG_NAME );
			}
		}
		return self::$logger;
	}

}
