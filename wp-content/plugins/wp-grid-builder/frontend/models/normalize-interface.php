<?php
/**
 * Normalize Interface
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

interface Normalize_Interface {

	/**
	 * Parse grid settings
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function parse();

}
