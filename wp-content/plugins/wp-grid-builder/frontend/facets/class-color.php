<?php
/**
 * Color facet
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
 * Color Facet class
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Color
 * @since 1.5.0
 */
class Color {

	/**
	 * Rendered items counter
	 *
	 * @since 1.5.0
	 * @var integer
	 */
	public $count = 0;

	/**
	 * Constructor
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function __construct() {}

	/**
	 * Query facet choices
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array Holds facet items.
	 */
	public function query_facet( $facet ) {

		add_filter( 'wp_grid_builder/facet/orderby', [ $this, 'orderby_clause' ], 10, 2 );

		if ( $facet['multiple'] ) {
			$results = ( new CheckBox() )->query_facet( $facet );
		} else {
			$results = ( new Radio() )->query_facet( $facet );
		}

		remove_filter( 'wp_grid_builder/facet/orderby', [ $this, 'orderby_clause' ] );

		return $this->format_results( $results, $facet );

	}

	/**
	 * Format facet choices
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $items Holds facet items.
	 * @param array $facet Holds facet settings.
	 * @return array
	 */
	public function format_results( $items, $facet ) {

		$values = array_column( $facet['color_options'], 'color_value' );
		$colors = array_combine( $values, $facet['color_options'] );

		foreach ( $items as $item ) {

			$value = $item->facet_value;

			if ( ! empty( $colors[ $value ]['background_image'] ) ) {
				$item->color = $colors[ $value ]['background_image'];
			} elseif ( ! empty( $colors[ $value ]['background_color'] ) ) {
				$item->color = $colors[ $value ]['background_color'];
			} else {
				$item->color = $value;
			}

			if ( ! empty( $colors[ $value ]['color_label'] ) ) {
				$item->facet_name = $colors[ $value ]['color_label'];
			}
		}

		return $items;

	}

	/**
	 * Order by color options
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param string $orderby Order by clause.
	 * @param array  $facet   Holds facet items.
	 * @return string
	 */
	public function orderby_clause( $orderby, $facet ) {

		global $wpdb;

		if ( ! empty( $facet['color_order'] ) && ! empty( $facet['color_options'] ) ) {

			$values  = array_column( $facet['color_options'], 'color_value' );
			$holders = rtrim( str_repeat( '%s,', count( $values ) ), ',' );
			$orderby = $wpdb->prepare(
				"field(facet_value, $holders) DESC, $orderby", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				array_reverse( $values )
			);

		}

		return $orderby;

	}

	/**
	 * Render facet
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $items Holds facet items.
	 * @return string Facet markup.
	 */
	public function render_facet( $facet, $items ) {

		$buttons = $this->render_buttons( $facet, $items );

		if ( empty( $buttons ) ) {
			return;
		}

		$output  = '<div class="wpgb-color-facet">';
		$output .= '<ul class="wpgb-inline-list">';
		$output .= $buttons;
		$output .= '</ul>';

		if ( $this->count > $facet['display_limit'] ) {

			$output .= '<button type="button" class="wpgb-toggle-hidden" aria-expanded="false">';
			$output .= esc_html( str_replace( '[number]', $this->count - $facet['display_limit'], $facet['show_more_label'] ) );
			$output .= '</button>';

		}

		$output .= '</div>';

		return $output;

	}

	/**
	 * Render buttons
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $items Holds facet items.
	 * @return string Buttons markup.
	 */
	public function render_buttons( $facet, $items ) {

		$output = '';

		foreach ( $items as $index => $item ) {

			// Hide Children if allowed.
			if ( ! $facet['children'] && (int) $item->facet_parent > 0 ) {
				continue;
			}

			// Hide empty item if allowed.
			if ( ! $facet['show_empty'] && ! $item->count ) {
				continue;
			}

			$hidden = $this->count >= $facet['display_limit'] ? ' hidden' : '';

			$output .= '<li' . esc_attr( $hidden ) . '>';
			$output .= $this->render_button( $facet, $item );
			$output .= '</li>';

			// Count rendered items.
			++$this->count;

		}

		return $output;

	}

	/**
	 * Render button
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $item  Holds current list item.
	 * @return string Button markup.
	 */
	public function render_button( $facet, $item ) {

		$checked  = in_array( $item->facet_value, $facet['selected'], true );
		$disabled = ! $checked && empty( $item->count );
		$tabindex = $disabled ? -1 : 0;
		$pressed  = $checked ? 'true' : 'false';

		$output  = '<div class="wpgb-color" role="button" aria-pressed="' . esc_attr( $pressed ) . '" tabindex="' . esc_attr( $tabindex ) . '">';
			$output .= $this->render_input( $facet, $item, $disabled );
			$output .= '<span class="wpgb-color-control" style="' . esc_attr( $this->get_style( $item ) ) . '"></span>';
			$output .= '<span class="wpgb-color-label">';
				$output .= esc_html( $item->facet_name );
				$output .= isset( $item->count ) && $facet['show_count'] ? ' <span>(' . (int) $item->count . ')</span>' : '';
			$output .= '</span>';
		$output .= '</div>';

		return apply_filters( 'wp_grid_builder/facet/color', $output, $facet, $item );

	}

	/**
	 * Get button style
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $item Holds current list item.
	 * @return string
	 */
	public function get_style( $item ) {

		$type = explode( '/', wp_check_filetype( $item->color )['type'] );

		if ( 'image' === reset( $type ) ) {
			$style = 'background-image:url(' . esc_url( $item->color ) . ')';
		} else {
			$style = 'background:' . $item->color;
		}

		return $style;

	}

	/**
	 * Render button input
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array   $facet    Holds facet settings.
	 * @param array   $item     Holds current list item.
	 * @param boolean $disabled Input disabled state.
	 * @return string Button input markup.
	 */
	public function render_input( $facet, $item, $disabled ) {

		return sprintf(
			'<input type="hidden" name="%1$s" value="%2$s"%3$s>',
			esc_attr( $facet['slug'] ),
			esc_attr( $item->facet_value ),
			disabled( $disabled, true, false )
		);

	}

	/**
	 * Query object ids (post, user, term) for selected facet values
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array Holds queried facet object ids.
	 */
	public function query_objects( $facet ) {

		if ( $facet['multiple'] ) {
			$instance = new CheckBox();
		} else {
			$instance = new Radio();
		}

		return $instance->query_objects( $facet );

	}
}
