<?php
/**
 * Text number field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Text_Number
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Text_Number extends Field {

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
			'<input type="text" class="wpgb-input wpgb-text-number" id="%s" name="%s" value="%s" placeholder="%s" data-min="%g" data-max="%g" data-steps="%s" data-units="%s" %s>',
			esc_attr( $args['uid'] ),
			esc_attr( $args['name'] ),
			esc_attr( $args['value'] ),
			esc_attr( $args['placeholder'] ),
			(float) $args['min'],
			(float) $args['max'],
			esc_attr( wp_json_encode( $args['steps'] ) ),
			esc_attr( wp_json_encode( $args['units'] ) ),
			$args['width'] ? 'style="width:' . (int) $args['width'] . 'px"' : ''
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
				'default'     => '',
				'placeholder' => '',
				'steps'       => [ 1 ],
				'units'       => [ '' ],
				'width'       => '',
				'min'         => 0,
				'max'         => 100,
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

		// Authorize empty value.
		if ( '' === $val ) {
			return '';
		}

		// If equal to 0.
		if ( abs( (float) $val ) < 0.00001 ) {
			return 0;
		}

		// Get step value according to current unit.
		$unit  = preg_replace( '/[0-9.,-]+/', '', $val );
		$index = (int) array_search( strtolower( $unit ), $args['units'], true );
		$args['step'] = isset( $args['steps'][ $index ] ) ? $args['steps'][ $index ] : 1;

		// Sanitize and validate value.
		$val = ( new Number() )->sanitize( $val, $args );

		return $val . $args['units'][ $index ];

	}
}
