<?php
/**
 * Query Vars Interface
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

interface Filter_Interface {

	/**
	 * Update query variables
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $query Holds WP query object.
	 */
	public function update( $query );

}
