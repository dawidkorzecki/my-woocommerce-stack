<?php
/**
 * Button facet
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
 * Button
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Button
 * @since 1.0.0
 */
class Button {

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

		if ( $facet['multiple'] ) {
			return ( new CheckBox() )->query_facet( $facet );
		}

		return ( new Radio() )->query_facet( $facet );

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

		$buttons = $this->render_buttons( $facet, $items );

		if ( empty( $buttons ) ) {
			return;
		}

		$output  = '<div class="wpgb-button-facet">';
		$output .= '<ul class="wpgb-inline-list">';
		$output .= $this->render_reset( $facet );
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

		$all_button = (object) [
			'facet_value' => '',
			'facet_name'  => $facet['all_label'],
		];

		// Prevent from unchecking (do a radio button).
		$facet['multiple'] = false;

		$output  = '<li>';
		$output .= $this->render_button( $facet, $all_button );
		$output .= '</li>';

		return $output;

	}

	/**
	 * Render buttons
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $item  Holds current list item.
	 * @return string Button markup.
	 */
	public function render_button( $facet, $item ) {

		$all_button = $facet['all_label'] && '' === $item->facet_value;

		// Select all button if no selection.
		if ( $all_button && empty( $facet['selected'] ) ) {
			$checked = true;
		} else {
			$checked = in_array( $item->facet_value, $facet['selected'], true );
		}

		$disabled = ! $all_button && ! $checked && empty( $item->count );
		$tabindex = $disabled ? -1 : 0;
		$pressed  = $checked ? 'true' : 'false';

		$output  = '<div class="wpgb-button" role="button" aria-pressed="' . esc_attr( $pressed ) . '" tabindex="' . esc_attr( $tabindex ) . '">';
			$output .= $this->render_input( $facet, $item, $disabled );
			$output .= '<span class="wpgb-button-label">';
				$output .= esc_html( $item->facet_name );
				$output .= isset( $item->count ) && $facet['show_count'] ? ' <span>(' . (int) $item->count . ')</span>' : '';
			$output .= '</span>';
		$output .= '</div>';

		return apply_filters( 'wp_grid_builder/facet/button', $output, $facet, $item );

	}

	/**
	 * Render checkbox/radio input
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $facet    Holds facet settings.
	 * @param array   $item     Holds current list item.
	 * @param boolean $disabled Input disabled state.
	 * @return string Checkbox/Radio input markup.
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

		if ( $facet['multiple'] ) {
			$instance = new CheckBox();
		} else {
			$instance = new Radio();
		}

		return $instance->query_objects( $facet );

	}
}
