<?php
/**
 * Class ExternalPluginAccess
 *
 * @package WPDesk\FS\Integration
 */

namespace WPDesk\FS\Integration;

use Psr\Log\LoggerInterface;

/**
 * Provides plugin data for integrations.
 */
class ExternalPluginAccess {

	/**
	 * @var string
	 */
	private $plugin_version;
	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * ExternalPluginAccess constructor.
	 *
	 * @param string          $plugin_version .
	 * @param LoggerInterface $logger .
	 */
	public function __construct( $plugin_version, LoggerInterface $logger ) {
		$this->plugin_version = $plugin_version;
		$this->logger         = $logger;
	}

	/**
	 * @return string
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

	/**
	 * @return LoggerInterface
	 */
	public function get_logger() {
		return $this->logger;
	}

}
