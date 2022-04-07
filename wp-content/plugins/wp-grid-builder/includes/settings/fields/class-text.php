<?php
/**
 * Text field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Text
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Text extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		// Revert back angle quote.
		if ( $args['angle_quotes'] ) {
			$args['value'] = str_replace( '&lt;', '<', $args['value'] );
		}

		printf(
			'<input type="text" class="wpgb-input wpgb-text" id="%s" name="%s" value="%s" placeholder="%s" autocomplete="off"%s%s%s>',
			esc_attr( $args['uid'] ),
			esc_attr( $args['name'] ),
			esc_attr( $args['value'] ),
			esc_attr( $args['placeholder'] ),
			$args['width'] ? ' style="width:' . (int) $args['width'] . 'px"' : '',
			esc_attr( $args['disabled'] ? ' disabled' : '' ),
			esc_attr( $args['readonly'] ? ' readonly' : '' )
		);

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
				'default'      => '',
				'placeholder'  => '',
				'width'        => '',
				'disabled'     => '',
				'readonly'     => '',
				'white_spaces' => false,
				'angle_quotes' => false,
			]
		);

	}

	/**
	 * Sanitize field value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $val  Field value.
	 * @param array $args Holds field parameters.
	 * @return string
	 */
	public function sanitize( $val, $args = [] ) {

		$s_spaces = '';
		$e_spaces = '';

		// Preserve white spaces at ends while using native sanitize_text_field.
		if ( $args['white_spaces'] ) {

			// If only white spaces.
			if ( ctype_space( $val ) ) {
				return $val;
			}

			preg_match( '/^\A\s*/', $val, $s_spaces );
			preg_match( '/\s*\z/', $val, $e_spaces );

			$s_spaces = isset( $s_spaces[0] ) ? $s_spaces[0] : '';
			$e_spaces = isset( $e_spaces[0] ) ? $e_spaces[0] : '';

		}

		// Preserve angle quotes.
		if ( $args['angle_quotes'] ) {
			$val = str_replace( '<', '&lt;', $val );
		}

		return $s_spaces . sanitize_text_field( $val ) . $e_spaces;

	}
}
