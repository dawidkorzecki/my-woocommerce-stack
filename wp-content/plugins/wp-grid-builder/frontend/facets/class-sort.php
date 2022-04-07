<?php
/**
 * Sort facet
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Facets;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sort
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Sort
 * @since 1.0.0
 */
class Sort {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {}

	/**
	 * Render facet
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return string Facet markup.
	 */
	public function render_facet( $facet ) {

		$options  = $this->render_options( $facet );
		$combobox = $facet['combobox'] ? ' wpgb-combobox' : '';
		$label    = $facet['title'] ?: __( 'Sort content', 'wp-grid-builder' );

		if ( empty( $options ) ) {
			return;
		}

		$output  = '<div class="wpgb-sort-facet">';
			$output .= '<label>';
				$output .= '<span class="wpgb-sr-only">' . esc_html( $label ) . '</span>';
				$output .= '<select class="wpgb-sort wpgb-select' . esc_attr( $combobox ) . '" name="' . esc_attr( $facet['slug'] ) . '">';
					$output .= $options;
				$output .= '</select>';
				$output .= ( new Select() )->select_icon( $facet );
			$output .= '</label>';
		$output .= '</div>';

		return $output;

	}

	/**
	 * Render sort options
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return string Options markup.
	 */
	public function render_options( $facet ) {

		$output  = '';
		$options = (array) $facet['sort_options'];

		if ( empty( $options ) ) {
			return;
		}

		foreach ( $options as $option ) {

			if ( empty( $option['label'] ) ) {
				continue;
			}

			$output .= $this->render_option( $facet, $option );
		}

		return $output;

	}

	/**
	 * Render sort option
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet  Holds facet settings.
	 * @param array $option Holds current select option.
	 * @return string Option markup.
	 */
	public function render_option( $facet, $option ) {

		$value   = $this->get_sort_value( $option );
		$current = reset( $facet['selected'] );

		$output = sprintf(
			'<option value="%1$s"%2$s>%3$s</option>',
			esc_attr( $value ),
			selected( $current, $value, false ),
			esc_html( $option['label'] )
		);

		return apply_filters( 'wp_grid_builder/facet/sort', $output, $facet, $option );

	}

	/**
	 * Get sort value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $options Holds sort options.
	 * @return string Sort option value.
	 */
	public function get_sort_value( $options ) {

		if ( $this->has_meta_key( $options ) ) {
			$value = isset( $options['meta_key'] ) ? $options['meta_key'] : '';
		} else {
			$value = isset( $options['orderby'] ) ? $options['orderby'] : '';
		}

		if ( ! empty( $value ) && isset( $options['order'] ) ) {
			$value .= '_' . $options['order'];
		}

		return $value;

	}

	/**
	 * Whether orderby value is a metadata key
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $options Holds sort options.
	 * @return boolean
	 */
	public function has_meta_key( $options ) {

		$orderby = isset( $options['orderby'] ) ? $options['orderby'] : '';

		return isset( $options['meta_key'] ) && ( 'meta_value' === $orderby || 'meta_value_num' === $orderby );

	}

	/**
	 * Query vars
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $query_vars Holds query vars.
	 * @return array Holds query vars to override.
	 */
	public function query_vars( $facet, $query_vars ) {

		$selected = reset( $facet['selected'] );

		if ( empty( $selected ) ) {
			return;
		}

		$sort_option = current(
			array_filter(
				(array) $facet['sort_options'],
				function( $option ) use ( $selected ) {
					return $this->get_sort_value( $option ) === $selected;
				}
			)
		);

		if ( empty( $sort_option['orderby'] ) || empty( $sort_option['order'] ) ) {
			return;
		}

		if ( in_array( $sort_option['orderby'], [ 'user__in', 'term__in' ], true ) ) {
			$sort_option['orderby'] = 'include';
		}

		$query_vars = [
			'orderby' => $sort_option['orderby'],
			'order'   => $sort_option['order'],
		];

		if ( $this->has_meta_key( $sort_option ) ) {
			$query_vars['meta_key'] = $sort_option['meta_key'];
		}

		return apply_filters( 'wp_grid_builder/facet/sort_query_vars', $query_vars, $facet );

	}
}
