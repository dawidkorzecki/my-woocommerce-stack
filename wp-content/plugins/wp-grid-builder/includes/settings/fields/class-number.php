<?php
/**
 * Number field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Number
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Number extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		printf(
			'<input type="number" class="wpgb-input wpgb-number" id="%s" name="%s" value="%g" min="%g" max="%g" step="%g" %s %s>',
			esc_attr( $args['uid'] ),
			esc_attr( $args['name'] ),
			(float) $args['value'],
			(float) $args['min'],
			(float) $args['max'],
			(float) $args['step'],
			$args['width'] ? ' style="width:' . (int) $args['width'] . 'px"' : '',
			esc_attr( $args['disabled'] ? ' disabled' : '' )
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
				'default'  => '',
				'disabled' => '',
				'min'      => 0,
				'max'      => 9999,
				'step'     => 1,
				'width'    => '',
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
	 * @return integer|float|string
	 */
	public function sanitize( $val, $args = [] ) {

		// Get default arguments to validate number.
		$val   = (float) str_replace( ',', '.', $val );
		$min   = is_numeric( $args['min'] ) ? $args['min'] : $val;
		$max   = is_numeric( $args['max'] ) ? $args['max'] : $val;
		$step  = is_numeric( $args['step'] ) ? $args['step'] : 1;
		$digit = strlen( substr( strrchr( $step, '.' ), 1 ) );

		// Make sure number is comprised between min and max values with right number of decimal.
		$val = ( $val - $min ) / $step * $step + $min;
		$val = max( min( $val, $max ), $min );
		$val = $digit ? number_format( $val, $digit, '.', '' ) : round( $val );

		// If high level of precision.
		if ( abs( (int) $val - $val ) < 0.00001 ) {
			return (int) $val;
		}

		// Keep as string to prevent precision errors.
		return rtrim( $val, 0 );

	}
}
