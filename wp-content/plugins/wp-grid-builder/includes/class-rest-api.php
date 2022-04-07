<?php
/**
 * Rest API
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add REST API custom routes
 *
 * @class WP_Grid_Builder\Includes\REST_API
 * @since 1.0.0
 */
class REST_API {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'rest_api_init', [ $this, 'add_route' ] );

	}

	/**
	 * Add custom route to WP Rest API
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_route() {

		register_rest_route(
			'wp_grid_builder/v1',
			'/get/',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get' ],
				'permission_callback' => [ $this, 'get_permission' ],
				'args'                => [
					'type' => [
						'type'     => 'string',
						'required' => true,
					],
				],
			]
		);

	}

	/**
	 * Get permission for custom route
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function get_permission() {

		return current_user_can( 'edit_posts' );

	}

	/**
	 * Get grid(s) or facet(s)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data REST route parameters (required).
	 * @return array.
	 */
	public function get( $data ) {

		if ( 'grids' !== $data['type'] && 'facets' !== $data['type'] ) {
			return [];
		}

		$objects = (array) Database::query_results(
			[
				'select'  => 'id, name',
				'from'    => $data['type'],
				'orderby' => 'modified_date',
			]
		);

		return array_map(
			function( $object ) {
				return [
					'value' => $object['id'],
					'label' => $object['name'],
				];
			},
			$objects
		);

	}
}
