<?php
/**
 * Color field
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings\Fields;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings API Field
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields\Color
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Color extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		global $pagenow;

		if ( 'edit-tags.php' === $pagenow || 'term.php' === $pagenow ) {

			echo '<input class="wpgb-color-picker" id="' . esc_attr( $args['uid'] ) . '" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $args['value'] ) . '">';
			return;

		}

		echo '<div class="wpgb-color" data-gradient="' . esc_attr( $args['gradient'] ) . '" data-alpha="' . esc_attr( $args['alpha'] ) . '">';

			echo '<div class="wpgb-color-picker">';
				echo '<input class="wpgb-input wpgb-color-picker-input" id="' . esc_attr( $args['uid'] ) . '" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $args['value'] ) . '" readonly>';
				echo '<span class="wpgb-color-picker-preview" style="' . esc_attr( $args['value'] ? 'background:' . $args['value'] : '' ) . '"></span>';
				echo '<span class="wpgb-color-picker-text">' . esc_html__( 'Select Color', 'wp-grid-builder' ) . '</span>';
			echo '</div>';

			echo '<div class="wpgb-color-clear">';
				esc_html_e( 'Clear', 'wp-grid-builder' );
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
			[
				'default'  => '',
				'alpha'    => true,
				'gradient' => false,
			]
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
	 * @return string
	 */
	public function sanitize( $val, $args = [] ) {

		// Authorize empty value.
		if ( empty( $val ) ) {
			return '';
		}

		// Sanitize gradient.
		if ( $args['gradient'] ) {
			return sanitize_text_field( $val );
		}

		// Sanitize the hex color and return default if empty.
		if ( strpos( $val, 'rgba' ) === false || ! $args['alpha'] ) {

			$val = sanitize_hex_color( $val );
			return empty( $val ) ? $args['default'] : $val;

		}

		// Match additive colors and alpha canal.
		$val = str_replace( ' ', '', $val );
		sscanf( $val, 'rgba(%d,%d,%d,%f)', $r, $g, $b, $a );

		// If all values are numerics.
		if ( is_numeric( $r ) && is_numeric( $g ) && is_numeric( $b ) && is_numeric( $a ) ) {
			return 'rgba(' . abs( $r ) . ',' . abs( $g ) . ',' . abs( $b ) . ',' . max( min( abs( $a ), 1 ), 0 ) . ')';
		} else {
			return $args['default'];
		}

	}
}
