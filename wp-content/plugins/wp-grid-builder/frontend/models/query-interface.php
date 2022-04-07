<?php
/**
 * Query Interface
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

interface Query_Interface {

	/**
	 * Parse current source query
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function parse_query();

	/**
	 * Retrieve results from parsed query
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function get_results();

}
