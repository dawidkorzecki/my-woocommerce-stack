<?php
/**
 * REST API
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API
 *
 * @class WP_Grid_Builder\FrontEnd\REST_API
 * @since 1.5.0
 */
final class REST_API {

	/**
	 * REST API namespace
	 *
	 * @since 1.5.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $namespace = 'wpgb/v1';

	/**
	 * Constructor
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'rest_api_init', [ $this, 'register_routes' ] );

	}

	/**
	 * Register custom routes
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/fetch',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'fetch_callback' ],
				'permission_callback' => [ $this, 'permission' ],
				'args'                => [
					'source_type' => [ 'default' => 'post_type' ],
					'query_args'  => [ 'default' => [] ],
					'facets'      => [ 'default' => [] ],
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/search',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'search_callback' ],
				'permission_callback' => [ $this, 'permission' ],
				'args'                => [
					'source_type' => [ 'default' => 'post_type' ],
					'query_args'  => [ 'default' => [] ],
					'facets'      => [ 'default' => [] ],
					'search'      => [
						'default' => [
							'facet'  => '',
							'string' => '',
						],
					],
				],
			]
		);

	}

	/**
	 * Handle REST API permission callback
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param WP_REST_Request $request This function accepts a REST request to process data.
	 * @return boolean
	 */
	public function permission( $request ) {

		return apply_filters( 'wp_grid_builder/rest_api/permission', false, $request );

	}

	/**
	 * Handle REST API request callback
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param WP_REST_Request $request This function accepts a REST request to process data.
	 */
	public function hooks( $request ) {

		do_action( 'wp_grid_builder/rest_api/callback', $request, $this->params );
		add_filter( 'wp_grid_builder/facet/query_string', [ $this, 'query_string' ], 1, 1 );
		add_filter( 'wp_grid_builder/facet/response', [ $this, 'format_response' ], PHP_INT_MAX - 9, 3 );

	}

	/**
	 * Get requested parameters
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param WP_REST_Request $request This function accepts a REST request to process data.
	 */
	public function get_params( $request ) {

		$this->params           = $request->get_params();
		$this->params['params'] = $request['facets'];
		$this->params['facets'] = wpgb_get_facet_ids( array_keys( $request['facets'] ) );

		if ( ! empty( $request['search']['facet'] ) ) {
			$this->params['search']['facet'] = wpgb_get_facet_id( $request['search']['facet'] );
		}
	}

	/**
	 * Handle fetch endpoint
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param WP_REST_Request $request This function accepts a REST request to process data.
	 * @return array
	 */
	public function fetch_callback( $request ) {

		$this->get_params( $request );
		$this->hooks( $request );

		return rest_ensure_response(
			apply_filters(
				'wp_grid_builder/rest_api/response',
				[
					'results' => $this->query(),
					'facets'  => wpgb_refresh_facets( $this->params, false ),
					'total'   => wpgb_get_found_objects(),
				],
				$request
			)
		);

	}

	/**
	 * Handle search endpoint
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param WP_REST_Request $request This function accepts a REST request to process data.
	 * @return array
	 */
	public function search_callback( $request ) {

		$this->get_params( $request );
		$this->hooks( $request );
		$this->query();

		return rest_ensure_response(
			apply_filters(
				'wp_grid_builder/rest_api/response',
				$this->format_choices(
					wpgb_search_facet_choices( $this->params )
				),
				$request
			)
		);

	}

	/**
	 * Query content
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array
	 */
	public function query() {

		$this->params['query_args']['wp_grid_builder'] = 'rest_api';

		switch ( $this->params['source_type'] ) {
			case 'user':
				$query   = new \WP_User_Query( $this->params['query_args'] );
				$results = $query->get_results();
				break;
			case 'term':
				$query   = new \WP_Term_Query( $this->params['query_args'] );
				$results = $query->terms;
				break;
			default:
				$query   = new \WP_Query( $this->params['query_args'] );
				$results = $query->posts;
		}

		return apply_filters( 'wp_grid_builder/rest_api/results', $results, $query, $this->params );

	}

	/**
	 * Inject query string parameters
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $query_string Holds facet query string parameters.
	 * @return array
	 */
	public function query_string( $query_string ) {

		return array_map( 'array_filter', $this->params['params'] );

	}

	/**
	 * Format facet response
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $response Holds facet response.
	 * @param array $facet    Holds facet settings.
	 * @param array $choices  Holds facet choices.
	 * @return array
	 */
	public function format_response( $response, $facet, $choices ) {

		$response['choices'] = $this->format_choices( $choices );
		unset( $response['html'] );

		return $response;

	}

	/**
	 * Format facet choices
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $choices Holds facet choices.
	 * @return array
	 */
	public function format_choices( $choices ) {

		return array_map(
			function( $choice ) {

				$row = [
					'value' => $choice->facet_value,
					'name'  => $choice->facet_name,
					'count' => (int) $choice->count,
				];

				// Selection facet.
				if ( ! empty( $choice->facet_slug ) ) {
					$row['slug'] = $choice->facet_slug;
				}

				// Color facet.
				if ( ! empty( $choice->color ) ) {
					$row['color'] = $choice->color;
				}

				// Taxonomy terms.
				if ( ! empty( $choice->facet_id ) ) {

					$row['term_id'] = (int) $choice->facet_id;

					if ( isset( $choice->facet_parent ) ) {
						$row['parent_id'] = (int) $choice->facet_parent;
					}
				}

				return $row;

			},
			array_values( $choices )
		);

	}
}
