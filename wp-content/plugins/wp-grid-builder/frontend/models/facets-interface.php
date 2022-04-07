<?php
/**
 * Facets Interface
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

interface Facets_Interface {

	/**
	 * Refresh facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param boolean $render Whether to render facet HTML.
	 */
	public function refresh( $render );

	/**
	 * Search in facets
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function search();

}
