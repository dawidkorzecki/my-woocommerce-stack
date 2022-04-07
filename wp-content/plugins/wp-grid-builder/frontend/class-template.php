<?php
/**
 * Template
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
 * Handle custom template
 *
 * @class WP_Grid_Builder\FrontEnd\Template
 * @since 1.0.0
 */
final class Template {

	/**
	 * Holds template settings
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Holds defaults settings
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $defaults = [
		'id'                 => 'template',
		'lang'               => '',
		'class'              => '',
		'source_type'        => 'post_type',
		'is_template'        => true,
		'is_main_query'      => false,
		'query_args'         => [],
		'render_callback'    => '',
		'noresults_callback' => '',
	];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds template settings.
	 */
	public function __construct( $settings = [] ) {

		$this->parse_settings( $settings );

	}

	/**
	 * Render template
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {

		if ( ! $this->maybe_handle() ) {
			return;
		}

		$this->query();
		$this->output();

		add_filter( 'wp_grid_builder/frontend/add_inline_script', [ $this, 'inline_script' ] );

	}

	/**
	 * Refresh template posts/terms/users
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function refresh() {

		// Allow refresh if it's a shadow template without valid callback.
		if ( empty( $this->settings->is_shadow ) && ! $this->maybe_handle() ) {
			return;
		}

		$this->query();

		// We do not loop if it's a shadow template.
		if ( ! empty( $this->settings->is_shadow ) ) {
			return;
		}

		$this->loop();

	}

	/**
	 * Check if we can hanlde render callback
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function maybe_handle() {

		if ( is_callable( $this->settings->render_callback ) ) {
			return true;
		}

		echo '<pre class="wpgb-error">';
			esc_html_e( "Invalid \"render_callback\". The callback must be a function or a class method name.\nThe callback must be declared in your functions.php file of your theme or in a plugin.", 'wp-grid-builder' );
		echo '</pre>';

		return false;

	}

	/**
	 * Parse template settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds template settings.
	 */
	public function parse_settings( $settings ) {

		$settings = $this->get_settings( $settings );
		$settings = wp_parse_args( $settings, $this->defaults );
		$settings = apply_filters( 'wp_grid_builder/template/args', $settings );
		$settings['id'] = trim( $settings['id'] );

		$this->settings = (object) $settings;

	}

	/**
	 * Get template settings
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param array $settings Holds template settings.
	 * @return array
	 */
	public function get_settings( $settings ) {

		$templates = apply_filters( 'wp_grid_builder/templates', [] );

		// Template accepts registered template ID as settings.
		if ( is_scalar( $settings ) ) {
			$settings = [ 'id' => $settings ];
		}

		// Get settings from registered template if available.
		if ( ! empty( $settings['id'] ) && ! empty( $templates[ $settings['id'] ] ) ) {

			$settings = wp_parse_args( $templates[ $settings['id'] ], $settings );
			$settings['registered'] = true;

		}

		return $settings;

	}

	/**
	 * Run query
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function query() {

		$query_args = $this->get_query_args();

		switch ( $this->settings->source_type ) {
			case 'user':
				$this->query = new \WP_User_Query( $query_args );
				break;
			case 'term':
				$this->query = new \WP_Term_Query( $query_args );
				break;
			default:
				if ( $this->settings->is_main_query ) {

					$this->query = $this->main_query();
					// Prevent to inject hidden template in archive pages.
					$this->query->set( 'wpgb_inject', false );

				} else {
					$this->query = new \WP_Query( $query_args );
				}
		}
	}

	/**
	 * Get query args
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array Holds query arguments.
	 */
	public function get_query_args() {

		$query_args = $this->settings->query_args;

		// Query args can be returned from a function/method.
		// It prevents performance issues if some query args are dynamics.
		if ( is_callable( $query_args ) ) {
			$query_args = call_user_func( $query_args );
		}

		$query_args['wp_grid_builder'] = $this->settings->id;

		return $query_args;

	}

	/**
	 * Get/run main WP query
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return object
	 */
	public function main_query() {

		global $wp_query;

		if ( wp_doing_ajax() && ! empty( $this->settings->main_query ) ) {

			// Turns off SQL_CALC_FOUND_ROWS even when limits are present.
			$this->settings->main_query['no_found_rows'] = true;
			// Add language to prevent issue when querying asynchronously.
			$this->settings->main_query['lang'] = $this->settings->lang;
			// Add WP Grid Builder to query args.
			$this->settings->main_query['wp_grid_builder'] = $this->settings->id;

			return new \WP_Query( $this->settings->main_query );

		} elseif ( is_main_query() && ! is_admin() ) {
			return $wp_query;
		}
	}

	/**
	 * Loop through queried posts/users/temrs
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function loop() {

		switch ( $this->settings->source_type ) {
			case 'user':
				$results = $this->user_loop();
				break;
			case 'term':
				$results = $this->term_loop();
				break;
			default:
				$results = $this->post_loop();
		}

		if ( ! $results ) {
			$this->no_results();
		}
	}

	/**
	 * Display no results message
	 *
	 * @since 1.1.5 Disable no results if callback set to false.
	 * @since 1.0.0
	 * @access public
	 */
	public function no_results() {

		$callback = $this->settings->noresults_callback;

		if ( false === $callback ) {
			return;
		}

		if ( ! empty( $callback ) && is_callable( $callback ) ) {
			call_user_func( $callback );
		} else {
			echo '<p class="wpgb-noresults">' . esc_html__( 'Sorry, no results match your search criteria.', 'wp-grid-builder' ) . '</p>';
		}
	}

	/**
	 * Loop through posts
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function post_loop() {

		if ( ! $this->query->have_posts() ) {
			return false;
		}

		while ( $this->query->have_posts() ) {

			global $post;

			$this->query->the_post();
			call_user_func( $this->settings->render_callback, $post );

		}

		wp_reset_postdata();

		return true;

	}

	/**
	 * Loop through users
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function user_loop() {

		$users = $this->query->get_results();

		if ( empty( $users ) ) {
			return false;
		}

		foreach ( $users as $user ) {
			call_user_func( $this->settings->render_callback, $user );
		}

		return true;

	}

	/**
	 * Loop through terms
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function term_loop() {

		$terms = $this->query->get_terms();

		if ( empty( $terms ) ) {
			return false;
		}

		foreach ( $terms as $term ) {
			call_user_func( $this->settings->render_callback, $term );
		}

		return true;

	}

	/**
	 * Output template
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function output() {

		$tag_name = apply_filters( 'wp_grid_builder/layout/wrapper_tag', 'div', $this->settings );

		echo '<!-- Gridbuilder ᵂᴾ Plugin (https://wpgridbuilder.com) -->';
		echo '<' . tag_escape( $tag_name ) . ' class="' . esc_attr( $this->get_classes() ) . '" data-options="' . esc_attr( $this->get_options() ) . '">';
		$this->loop();
		echo '</' . tag_escape( $tag_name ) . '>';

	}

	/**
	 * Get class names
	 *
	 * @since 1.0.4 Allow multiple custom class names.
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_classes() {

		$classes  = 'wp-grid-builder wpgb-template';
		$classes .= ' ' . sanitize_html_class( 'wpgb-grid-' . $this->settings->id );
		$classes .= ' ' . Helpers::sanitize_html_classes( $this->settings->class );

		return $classes;

	}

	/**
	 * Get JS options.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_options() {

		$options = [];

		foreach ( $this->defaults as $key => $val ) {

			// To be compliant with JS syntax.
			$js_key = ucwords( str_replace( '_', ' ', $key ) );
			$js_key = lcfirst( str_replace( ' ', '', $js_key ) );

			$options[ $js_key ] = $this->settings->{$key};

		}

		return $this->format_options( $options );

	}

	/**
	 * Format JS options to localize in HTML attribute.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param array $options Hold template settings.
	 * @return string
	 */
	public function format_options( $options ) {

		$options = array_filter( $options );

		if ( ! empty( $this->settings->registered ) ) {

			$allowed = [ 'id', 'isTemplate', 'isMainQuery' ];
			$options = array_intersect_key( $options, array_flip( $allowed ) );

		}

		return wp_json_encode( $options );

	}

	/**
	 * Add inline javascript code to instantiate template
	 *
	 * @since 1.1.5 Added wpgb.loaded event to support defer and async scripts.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $script Inline script.
	 * @return string
	 */
	public function inline_script( $script ) {

		$class = sanitize_html_class( 'wpgb-grid-' . $this->settings->id );

		$script .= 'window.addEventListener(\'wpgb.loaded\',function(){';
		$script .= 'var template = document.querySelector(' . wp_json_encode( '.wpgb-template.' . $class . ':not([data-instance])' ) . ');';
		$script .= 'if(template){';
		$script .= 'var wpgb = WP_Grid_Builder.instantiate(template);';
		$script .= 'wpgb.init && wpgb.init()';
		$script .= '}});';

		return apply_filters( 'wp_grid_builder/template/inline_script', $script );

	}
}
