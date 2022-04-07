<?php
/**
 * StyleSheet Interface
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface StyleSheet_Interface {

	/**
	 * Generate css
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function generate();

	/**
	 * Get css
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get();

}
