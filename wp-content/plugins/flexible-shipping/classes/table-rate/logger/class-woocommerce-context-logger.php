<?php

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * Class WPDesk_Flexible_Shipping_WooCommerce_Context_Logger
 */
class WPDesk_Flexible_Shipping_WooCommerce_Context_Logger implements LoggerInterface {

	use LoggerTrait;

	/**
	 * Logger.
	 *
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * Default context.
	 *
	 * @var array
	 */
	private $default_context;

	/**
	 * WPDesk_Flexible_Shipping_Context_Logger constructor.
	 *
	 * @param LoggerInterface $logger Logger.
	 * @param string          $source WooCommerce source context.
	 * @param array           $context Default context for logger.
	 */
	public function __construct( LoggerInterface $logger, $source, array $context = [] ) {
		$this->logger = $logger;

		$context['source']     = $source;
		$this->default_context = $context;
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param string $level Level.
	 * @param string $message Message.
	 * @param array  $context Context.
	 *
	 * @return void
	 */
	public function log( $level, $message, array $context = array() ) {
		$context = array_merge( $this->default_context, $context );
		$this->logger->log( $level, $message, $context );
	}

}

