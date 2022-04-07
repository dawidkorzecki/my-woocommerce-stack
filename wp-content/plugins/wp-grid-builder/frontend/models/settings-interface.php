<?php
/**
 * Settings Interface
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

interface Settings_Interface {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $grid Holds grid parameters.
	 */
	public function __construct( $grid );

	/**
	 * Query grid settings
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function query();

}
