<?php
/**
 * Config class.
 *
 * @package xts
 */

namespace XTS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Config class.
 *
 * @since 1.0.0
 */
class Config extends Singleton {
	/**
	 * Config.
	 *
	 * @var object
	 */
	private $config = array();

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {}

	/**
	 * Get config file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Config name.
	 *
	 * @return mixed
	 */
	public function get_config( $name ) {
		if ( isset( $this->config[ $name ] ) ) {
			return $this->config[ $name ];
		}

		$path = WOODMART_CONFIGS . '/' . $name . '.php';

		if ( $path ) {
			$this->config[ $name ] = include_once $path;
			return $this->config[ $name ];
		}

		return false;
	}
}
