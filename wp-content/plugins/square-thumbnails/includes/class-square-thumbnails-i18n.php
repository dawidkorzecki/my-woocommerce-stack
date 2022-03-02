<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://ilmdesigns.com/
 * @since      1.0.0
 *
 * @package    Square_Thumbnails
 * @subpackage Square_Thumbnails/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Square_Thumbnails
 * @subpackage Square_Thumbnails/includes
 * @author     ILMDESIGNS <narcisbodea@gmail.com>
 */
class Square_Thumbnails_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'square-thumbnails',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
