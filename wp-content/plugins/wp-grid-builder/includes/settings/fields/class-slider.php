<?php
/**
 * Slider field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Slider
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Slider extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		$value = ! $args['unit'] && isset( $args['units'][0] ) ? (float) $args['value'] . $args['units'][0] : $args['value'];
		$size  = ( (float) $args['value'] - $args['min'] ) / ( $args['max'] - $args['min'] ) * 100 + 0.5;
		$size  = round( is_rtl() ? 100 - $size : $size, 3 );

		echo '<div class="wpgb-range">';

		printf(
			'<input type="range" class="wpgb-input wpgb-range-slider" id="%s" value="%g" min="%g" max="%g" step="%g" data-steps="%s" data-units="%s" style="background-size: %.4f%% 100%%">',
			esc_attr( $args['uid'] ),
			(float) $args['value'],
			(float) $args['min'],
			(float) $args['max'],
			(float) min( $args['steps'] ),
			esc_attr( wp_json_encode( $args['steps'] ) ),
			esc_attr( wp_json_encode( $args['units'] ) ),
			esc_attr( $size )
		);

		printf(
			'<input type="text" class="wpgb-input wpgb-range-slider-value" name="%s" value="%s" aria-label="%s" autocomplete="off">',
			esc_attr( $args['name'] ),
			esc_attr( $value ),
			esc_attr( $args['label'] )
		);

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
				'default' => 0,
				'min'     => 0,
				'max'     => 100,
				'unit'    => false,
				'steps'   => [ 1 ],
				'units'   => [ '' ],
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
		if ( '' === $val && $args['min'] <= 0 && $args['max'] >= 0 ) {
			return '';
		}

		// Get step value according to current unit.
		$unit  = preg_replace( '/[0-9.,-]+/', '', $val );
		$index = (int) array_search( strtolower( $unit ), $args['units'], true );
		$args['step'] = isset( $args['steps'][ $index ] ) ? $args['steps'][ $index ] : 1;

		// Sanitize value.
		$val = ( new Number() )->sanitize( $val, $args );

		if ( $args['unit'] && isset( $args['units'][ $index ] ) && ! empty( $args['units'][ $index ] ) ) {
			return $val . $args['units'][ $index ];
		}

		return $val;

	}
}
