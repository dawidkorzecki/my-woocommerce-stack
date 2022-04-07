<?php
/**
 * Checkbox field
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings\Fields;

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings API Field
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields\Checkbox
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Checkbox extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		$list  = isset( $args['icons'] );
		$class = $list ? 'list' : 'checkbox';

		echo '<input type="hidden" id="' . esc_attr( $args['uid'] ) . '" name="' . esc_attr( $args['name'] ) . '" value="">';
		echo '<ul class="wpgb-checkbox-list ' . sanitize_html_class( $list ? 'wpgb-list' : '' ) . '">';

		foreach ( $args['options'] as $key => $val ) {

			$uid = 'wpgb-' . uniqid();

			echo '<li class="' . sanitize_html_class( 'wpgb-' . $class . '-item' ) . '">';

			printf(
				'<input type="checkbox" class="wpgb-input wpgb-checkbox wpgb-sr-only" id="%s" name="%s" value="%s" %s>',
				esc_attr( $uid ),
				esc_attr( $args['name'] ),
				esc_attr( $key ),
				checked( in_array( (string) $key, (array) $args['value'], true ), 1, 0 )
			);

			echo '<label for="' . esc_attr( $uid ) . '">';

			Helpers::get_icon( 'check' );

			if ( $list ) {
				echo '<svg><use xlink:href="' . esc_url( $args['icons'][ $key ] ) . '"></use></svg>';
			}

			echo '<span>' . esc_html( $val ) . '</span>';
			echo '</label>';
			echo '</li>';

		}

		echo '</ul>';

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
				'default'  => [],
				'options'  => [],
				'multiple' => true,
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
	 * @return array
	 */
	public function sanitize( $val, $args = [] ) {

		$new_val = [];

		foreach ( (array) $val as $v ) {
			$new_val[] = array_key_exists( $v, $args['options'] ) ? $v : null;
		}

		return array_map( 'sanitize_text_field', array_filter( $new_val ) );

	}
}
