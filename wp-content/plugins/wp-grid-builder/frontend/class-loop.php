<?php
/**
 * Loop
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
 * Do grid loop
 *
 * @class WP_Grid_Builder\FrontEnd\Loop
 * @since 1.0.0
 */
final class Loop implements Models\Loop_Interface {

	/**
	 * Settings class dependency
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $settings = [];

	/**
	 * Query class dependency
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $query = [];

	/**
	 * Cards class dependency
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $cards = [];

	/**
	 * Holds object attributes
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $atts = [];

	/**
	 * Holds current object in loop
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var object
	 */
	public $object;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object Settings $settings Settings class instance.
	 * @param object Query    $query Query class instance.
	 * @param object Cards    $cards Cards class instance.
	 */
	public function __construct( Settings $settings, Query $query, Cards $cards ) {

		$this->settings = $settings;
		$this->query    = $query;
		$this->cards    = $cards;

	}

	/**
	 * Loop through objects
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function run() {

		$this->query->parse_query();
		$results = $this->query->get_results();
		$this->cards->query();

		if ( is_wp_error( $this->settings->error ) ) {

			Helpers::get_template( 'layout/message' );
			return;

		}

		foreach ( $results as $object ) {

			$this->object = $object;

			$this->get_card();
			$this->get_class();
			$this->get_size();
			$this->get_schemes();

			$this->cards->render( $this->atts, $this->settings->type );

		}
	}

	/**
	 * Get card
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_card() {

		$cards  = $this->settings->cards;
		$format = isset( $this->object->post_format ) ? $this->object->post_format : '';
		$type   = isset( $this->object->post_type ) ? $this->object->post_type : '';
		$meta   = isset( $this->object->metadata ) ? $this->object->metadata : '';

		// Get default card.
		$card = ! empty( $cards['default'] ) ? $cards['default'] : null;
		// Get card associated to a post type.
		$card = ! empty( $cards[ $type ] ) ? $cards[ $type ] : $card;
		// Get card associated to a post format.
		$card = ! empty( $cards[ $format ] ) ? $cards[ $format ] : $card;
		// Get card associated to a post/term/user meta (mainly for cards overview).
		$card = ! empty( $meta['card'] ) ? $meta['card'] : $card;

		$this->atts['card'] = $card;

	}

	/**
	 * Get item class names
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_class() {

		$this->atts['class']   = [];

		if ( ! empty( $this->object->ID ) ) {

			$prefix = 'wpgb-' . str_replace( '_type', '', $this->settings->source );
			$this->atts['class'][] = $prefix . '-' . absint( $this->object->ID );

		}

		if ( ! empty( $this->object->post_sticky ) ) {
			$this->atts['class'][] = 'wpgb-sticky';
		}

		if ( $this->settings->reveal ) {
			$this->atts['class'][] = 'wpgb-card-hidden';
		}
	}

	/**
	 * Get item size (col/row)
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_size() {

		$cols_nb = 1;
		$rows_nb = 1;

		if ( $this->settings->override_card_sizes ) {

			$cols_nb = $this->settings->columns;
			$rows_nb = $this->settings->rows;

		} elseif ( isset( $this->object->metadata[ WPGB_SLUG ] ) ) {

			$metadata = $this->object->metadata[ WPGB_SLUG ];
			$cols_nb  = ! empty( $metadata['columns'] ) ? $metadata['columns'] : $cols_nb;
			$rows_nb  = ! empty( $metadata['rows'] ) ? $metadata['rows'] : $rows_nb;

		}

		$this->atts['columns'] = $cols_nb;
		$this->atts['rows']    = $rows_nb;

	}

	/**
	 * Get item color scheme
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_schemes() {

		// Backgrounds from grid settings is present in stylesheet.
		$this->atts['content_background']   = '';
		$this->atts['overlay_background']   = '';
		$this->atts['content_color_scheme'] = $this->settings->content_color_scheme ?: 'dark';
		$this->atts['overlay_color_scheme'] = $this->settings->overlay_color_scheme ?: 'light';

		if ( isset( $this->object->metadata[ WPGB_SLUG ] ) ) {

			$metadata = $this->object->metadata[ WPGB_SLUG ];

			if ( ! empty( $metadata['content_background'] ) ) {
				$this->atts['content_background'] = $metadata['content_background'];
			}

			if ( ! empty( $metadata['overlay_background'] ) ) {
				$this->atts['overlay_background'] = $metadata['overlay_background'];
			}

			if ( ! empty( $metadata['content_color_scheme'] ) ) {
				$this->atts['content_color_scheme'] = $metadata['content_color_scheme'];
			}

			if ( ! empty( $metadata['overlay_color_scheme'] ) ) {
				$this->atts['overlay_color_scheme'] = $metadata['overlay_color_scheme'];
			}
		}
	}
}
