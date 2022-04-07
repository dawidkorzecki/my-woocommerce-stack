<?php
/**
 * Radio facet
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
 * Radio
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Radio
 * @since 1.0.0
 */
class Radio {

	/**
	 * Rendered items counter
	 *
	 * @since 1.0.0
	 * @var integer
	 */
	public $count = 0;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {}

	/**
	 * Query facet choices
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array Holds facet items.
	 */
	public function query_facet( $facet ) {

		$facet['logic'] = 'OR';

		return ( new CheckBox() )->query_facet( $facet );

	}

	/**
	 * Render facet
	 *
	 * @since 1.2.0 Handle shortcode [number] in button label.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $items Holds facet items.
	 * @return string Facet markup.
	 */
	public function render_facet( $facet, $items ) {

		$list = $this->render_list( $facet, $items );

		if ( empty( $list ) ) {
			return;
		}

		$output  = '<div class="wpgb-radio-facet">';
		$output .= '<ul class="wpgb-hierarchical-list">';
		$output .= $this->render_reset( $facet );
		$output .= $list;
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
	 * Render "all" button (reset)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return string Reset button markup.
	 */
	public function render_reset( $facet ) {

		if ( ! $facet['all_label'] ) {
			return '';
		}

		$all = $facet['all_label'] ?: __( 'All', 'wp-grid-builder' );

		$all_button = (object) [
			'facet_value' => '',
			'facet_name'  => $all,
		];

		// Prevent from unchecking (do a radio button).
		$facet['multiple'] = false;

		$output  = '<li>';
		$output .= $this->render_radio( $facet, $all_button );
		$output .= '</li>';

		return $output;

	}

	/**
	 * Render radio items
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $items Holds facet items.
	 * @return string Radio items.
	 */
	public function render_list( $facet, $items ) {

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
			$output .= $this->render_radio( $facet, $item );
			$output .= '</li>';

			// Count rendered items.
			++$this->count;

		}

		return $output;

	}

	/**
	 * Render radio item
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $item  Holds current list item.
	 * @return string Radio markup.
	 */
	public function render_radio( $facet, $item ) {

		$all_button = $facet['all_label'] && '' === $item->facet_value;

		// Select all button if no selection.
		if ( $all_button && empty( $facet['selected'] ) ) {
			$checked = true;
		} else {
			$checked = in_array( $item->facet_value, $facet['selected'], true );
		}

		$disabled = ! $all_button && ! $checked && empty( $item->count );
		$pressed  = $checked ? 'true' : 'false';
		$tabindex = $disabled ? -1 : 0;

		$output = '<div class="wpgb-radio" role="button" aria-pressed="' . esc_attr( $pressed ) . '" tabindex="' . esc_attr( $tabindex ) . '">';
			$output .= $this->render_input( $facet, $item, $disabled );
			$output .= '<span class="wpgb-radio-control"></span>';
			$output .= '<span class="wpgb-radio-label">';
				$output .= esc_html( $item->facet_name );
				$output .= isset( $item->count ) && $facet['show_count'] ? '&nbsp;<span>(' . (int) $item->count . ')</span>' : '';
			$output .= '</span>';
		$output .= '</div>';

		return apply_filters( 'wp_grid_builder/facet/radio', $output, $facet, $item );

	}

	/**
	 * Render radio input
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $facet    Holds facet settings.
	 * @param array   $item     Holds current list item.
	 * @param boolean $disabled Input disabled state.
	 * @return string Radio input markup.
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
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array Holds queried facet object ids.
	 */
	public function query_objects( $facet ) {

		global $wpdb;

		$value = reset( $facet['selected'] );

		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT object_id
				FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				AND facet_value IN (%s)",
				$facet['slug'],
				$value
			)
		);

	}
}
