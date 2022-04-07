<?php
/**
 * Intercept
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
 * Intercept request to filter custom content
 *
 * @class WP_Grid_Builder\FrontEnd\Intercept
 * @since 1.5.1
 */
final class Intercept {

	/**
	 * Requested data
	 *
	 * @since 1.5.1
	 * @access protected
	 *
	 * @var array
	 */
	protected $request = [];

	/**
	 * Action type
	 *
	 * @since 1.5.1
	 * @access protected
	 *
	 * @var string
	 */
	protected $action = '';

	/**
	 * Content identifier
	 *
	 * @since 1.5.1
	 * @access protected
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Content interceptor
	 *
	 * @since 1.5.1
	 * @access protected
	 *
	 * @var string
	 */
	protected $interceptor = 'wpgb-content';

	/**
	 * Next query identifier
	 *
	 * @since 1.5.1
	 * @access protected
	 *
	 * @var string
	 */
	protected $next_query_id = '';

	/**
	 * Constructor
	 *
	 * @since 1.5.1
	 * @access public
	 */
	public function __construct() {

		add_filter( 'wp_grid_builder/async/intercept', [ $this, 'intercept' ], 10, 3 );
		add_filter( 'wp_grid_builder/facet/render_args', [ $this, 'content_name' ] );
		add_filter( 'wp_grid_builder/facet/transient_name', [ $this, 'transient_name' ] );

		add_shortcode( 'wpgb_query', [ $this, 'next_query_id' ] );
		add_action( 'pre_get_posts', [ $this, 'check_query_id' ], PHP_INT_MAX - 9 );

		if ( wpgb_get_option( 'filter_custom_content' ) ) {

			add_action( 'loop_no_results', [ $this, 'inject' ] );
			add_action( 'loop_start', [ $this, 'inject' ] );

		}
	}

	/**
	 * Maybe intercept request
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @param boolean $intercept Whether to intercept requested.
	 * @param string  $action    Requested action.
	 * @param array   $request   Requested data.
	 * @return boolean
	 */
	public function intercept( $intercept, $action, $request ) {

		if ( empty( $request['id'] ) || 0 !== stripos( $request['id'], $this->interceptor ) ) {
			return $intercept;
		}

		$this->action  = $action;
		$this->request = $request;
		$this->content = $request['id'];

		do_action( 'wp_grid_builder/async/' . $action, $this->request );
		add_action( 'shutdown', [ $this, 'filter' ], -1 );
		ob_start();

		return true;

	}

	/**
	 * Format content/grid name for consistency
	 * Prevent duplication of jS instances if name is inconsistent.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @param array $args Facet paramters.
	 * @return array
	 */
	public function content_name( $args ) {

		if ( 0 === stripos( trim( $args['grid'] ), $this->interceptor ) ) {
			$args['grid'] = strtolower( trim( $args['grid'] ) );
		}

		return $args;

	}

	/**
	 * Change transient name to correctly cache content depending of the page
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @param string $transient Facet transient name.
	 * @return string
	 */
	public function transient_name( $transient ) {

		if ( false !== stripos( $transient, 'g' . $this->interceptor ) ) {
			$transient .= md5( preg_replace( '/\?.*/', '', get_pagenum_link() ) );
		}

		return $transient;

	}

	/**
	 * Setup query ID for the next WP_Query
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function next_query_id( $atts ) {

		$this->next_query_id = 'wpgb-content';

		if ( empty( $atts['id'] ) ) {
			return;
		}

		$query_id = trim( $atts['id'] );

		if ( 0 === stripos( $query_id, $this->interceptor ) ) {
			$this->next_query_id = strtolower( $atts['id'] );
		}
	}

	/**
	 * Check if it is a filterable query from page content
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @param object $query Holds WP query object.
	 */
	public function check_query_id( $query ) {

		$this->set_query_id( $query );

		$query_id   = $query->get( 'wp_grid_builder' );
		$is_archive = $this->is_archive( $query_id );

		if ( ! $is_archive && ! $this->is_custom( $query_id ) ) {

			$this->unset_query_id( $query );
			return;

		}

		$query->set( 'wpgb_inject', $is_archive ? $this->interceptor : $query_id );

	}

	/**
	 * Add query ID setup from shortcode [wpgb_query]
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @param object $query Holds WP query object.
	 */
	public function set_query_id( $query ) {

		if ( empty( $this->next_query_id ) ) {
			return;
		}

		$query->set( 'wp_grid_builder', $this->next_query_id );
		$this->next_query_id = '';

	}

	/**
	 * Unset query ID that does not need to be filtered
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @param object $query Holds WP query object.
	 */
	public function unset_query_id( $query ) {

		if ( ! wpgb_doing_ajax() || empty( $this->request ) ) {
			return;
		}

		// Optimize query that does not need to be filtered.
		if ( $query->get( 'wp_grid_builder' ) ) {

			$query->set( 'posts_per_page', 1 );
			$query->set( 'no_found_rows', 1 );
			$query->set( 'meta_query', [] );
			$query->set( 'tax_query', [] );
			$query->set( 'post__in', [] );

		}

		// Unset query to prevent conflicts with multiple content.
		$query->set( 'wp_grid_builder', false );

	}

	/**
	 * Whether it is an archive query
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @param boolean|string $query_id Query identifier.
	 */
	public function is_archive( $query_id ) {

		return true === $query_id || 'true' === $query_id;

	}

	/**
	 * Whether it is a custom query
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @param boolean|string $query_id Query identifier.
	 */
	public function is_custom( $query_id ) {

		$query_id = strtolower( trim( $query_id ) );

		return (
			( ! empty( $this->request ) && $query_id === $this->content ) ||
			( empty( $this->request ) && 0 === strpos( $query_id, $this->interceptor ) )
		);

	}

	/**
	 * Inject hidden placeholder in the page
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @param object $query Holds WP query object.
	 */
	public function inject( $query ) {

		if ( ! $query->get( 'wpgb_inject' ) ) {
			return;
		}

		// We only inject in 3rd party content from in the body.
		if ( ! did_action( 'wp_head' ) || doing_action( 'wp_grid_builder/layout/do_loop' ) ) {
			return;
		}

		echo '<div class="' . sanitize_html_class( strtolower( $query->get( 'wpgb_inject' ) ) ) . '" hidden></div>';

		$query->set( 'wpgb_inject', false );

	}

	/**
	 * Fetch page content before to shutdown
	 *
	 * @since 1.5.1
	 * @access public
	 */
	public function fetch() {

		$content = '';

		preg_match( '/<body.*\/body>/s', ob_get_clean(), $body );

		if ( ! empty( $body ) ) {
			$content = trim( current( $body ) );
		}

		if ( ob_get_contents() ) {
			ob_end_clean();
		}

		return $content;

	}

	/**
	 * Filter page content
	 *
	 * @since 1.5.1
	 * @access public
	 */
	public function filter() {

		switch ( $this->action ) {
			case 'render':
				$this->render();
				break;
			case 'refresh':
				$this->refresh();
				break;
			case 'search':
				$this->search();
				break;
		}

		wp_die();

	}

	/**
	 * Render facets on first load
	 *
	 * @since 1.5.1
	 * @access protected
	 */
	protected function render() {

		ob_get_clean();

		if ( ob_get_contents() ) {
			ob_end_clean();
		}

		wp_send_json(
			apply_filters(
				'wp_grid_builder/async/render_response',
				[
					'facets' => wpgb_refresh_facets( $this->request ),
				],
				$this->request
			)
		);

	}

	/**
	 * Refresh facets and content
	 *
	 * @since 1.5.1
	 * @access protected
	 */
	protected function refresh() {

		wp_send_json(
			apply_filters(
				'wp_grid_builder/async/refresh_response',
				[
					'facets' => wpgb_refresh_facets( $this->request ),
					'posts'  => $this->fetch(),
				],
				$this->request
			)
		);

	}

	/**
	 * Search for facet choices
	 *
	 * @since 1.5.1
	 * @access protected
	 */
	protected function search() {

		ob_get_clean();

		if ( ob_get_contents() ) {
			ob_end_clean();
		}

		wp_send_json(
			apply_filters(
				'wp_grid_builder/async/search_response',
				wpgb_search_facet_choices( $this->request ),
				$this->request
			)
		);

	}
}
