<?php
/**
 * Objects
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle queried objects
 *
 * @class WP_Grid_Builder\FrontEnd\Objects
 * @since 1.0.0
 */
class Objects {

	/**
	 * Holds filtered object ids
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $filtered_object_ids;

	/**
	 * Holds unfiltered object ids
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $unfiltered_object_ids;

	/**
	 * Holds imploded filtered object ids
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $imploded_filtered_object_ids;

	/**
	 * Holds imploded unfiltered object ids
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $imploded_unfiltered_object_ids;

	/**
	 * Query object ids from current query vars
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $query_vars Holds current query arguments.
	 * @return array Holds object ids.
	 */
	public function get_object_ids( $query_vars ) {

		$object_type = wpgb_get_queried_object_type();

		if ( empty( $object_type ) ) {
			return [];
		}

		switch ( $object_type ) {
			case 'post':
				return Helpers::get_post_ids( $query_vars, -1 );
			case 'term':
				return Helpers::get_term_ids( $query_vars, '' );
			case 'user':
				return Helpers::get_user_ids( $query_vars, -1 );
		}
	}

	/**
	 * Get filtered object ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Object ids.
	 */
	public function get_filtered_object_ids() {

		if ( ! empty( $this->filtered_object_ids ) ) {
			return $this->filtered_object_ids;
		}

		$query_vars = wpgb_get_filtered_query_vars();
		$this->filtered_object_ids = $this->get_object_ids( $query_vars );

		return $this->filtered_object_ids;

	}

	/**
	 * Get unfiltered object ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Object ids.
	 */
	public function get_unfiltered_object_ids() {

		if ( ! empty( $this->unfiltered_object_ids ) ) {
			return $this->unfiltered_object_ids;
		}

		$query_vars = wpgb_get_unfiltered_query_vars();
		$this->unfiltered_object_ids = $this->get_object_ids( $query_vars );

		return $this->unfiltered_object_ids;

	}

	/**
	 * Implode filtered object ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Object ids comma separated.
	 */
	public function implode_filtered_object_ids() {

		if ( ! empty( $this->imploded_filtered_object_ids ) ) {
			return $this->imploded_filtered_object_ids;
		}

		$this->imploded_filtered_object_ids = implode( ',', $this->get_filtered_object_ids() );

		// Return 0 if falsy to make sure where clause is valid.
		return $this->imploded_filtered_object_ids ?: 0;

	}

	/**
	 * Implode unfiltered object ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Object ids comma separated.
	 */
	public function implode_unfiltered_object_ids() {

		if ( ! empty( $this->imploded_unfiltered_object_ids ) ) {
			return $this->imploded_unfiltered_object_ids;
		}

		$this->imploded_unfiltered_object_ids = implode( ',', $this->get_unfiltered_object_ids() );

		// Return 0 if falsy to make sure where clause is valid.
		return $this->imploded_unfiltered_object_ids ?: 0;

	}
}
