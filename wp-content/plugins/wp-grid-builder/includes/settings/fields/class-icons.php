<?php
/**
 * Icons field
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings\Fields;

use WP_Grid_Builder\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings API Field
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields\Icons
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Icons extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		echo '<div class="wpgb-icon">';
			echo '<div class="wpgb-icon-picker">';

				echo '<span class="wpgb-icon-picker-svg">';
					Includes\icons::display( $args['value'] );
				echo '</span>';

				echo '<span class="wpgb-icon-picker-label">' . esc_html__( 'Select icon', 'wp-grid-builder' ) . '</span>';

				printf(
					'<input type="hidden" class="wpgb-input" id="%s" name="%s" value="%s">',
					esc_attr( $args['uid'] ),
					esc_attr( $args['name'] ),
					esc_attr( $args['value'] )
				);

			echo '</div>';
		echo '</div>';

		$this->render_popup();

	}

	/**
	 * Render icon popup
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_popup() {

		echo '<div class="wpgb-icons-popup">';

		printf(
			'<input type="search" class="wpgb-input" placeholder="%1$s" aria-label="%1$s">',
			esc_attr__( 'Search Icons', 'wp-grid-builder' )
		);

		echo '<div class="wpgb-icons-popup-holder">';
		echo '<div class="wpgb-icons-popup-wrapper">';

		$icon_sets = Includes\icons::get();

		foreach ( $icon_sets as $set => $args ) {

			foreach ( $args['icons'] as $icon ) {

				echo '<span class="wpgb-icon-item" data-icon="' . esc_attr( $set . '/' . $icon ) . '">';
					echo '<svg><use xlink:href="' . esc_url( $args['sprite'] . '#' . $icon ) . '"/></svg>';
				echo '</span>';

			}
		}

		echo '</div>';
		echo '</div>';
		echo '</div>';

	}

	/**
	 * Normalize field parameters
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field Holds field parameters.
	 * @return array
	 */
	public function normalize( $field ) {

		return wp_parse_args(
			$field,
			[ 'default' => '' ]
		);

	}

	/**
	 * Sanitize field value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $val Field value.
	 * @param array $args Holds field parameters.
	 * @return array
	 */
	public function sanitize( $val, $args = [] ) {

		if ( empty( $val ) ) {
			return '';
		}

		$icon = Includes\Icons::get( $val );

		if ( empty( $icon ) ) {
			return '';
		}

		return sanitize_text_field( $val );

	}
}
