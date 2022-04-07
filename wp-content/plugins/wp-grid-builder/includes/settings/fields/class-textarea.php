<?php
/**
 * Textarea field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Textarea
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Textarea extends Field {

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
			'<textarea class="wpgb-textarea" id="%s" name="%s" cols="%d" rows="%d" placeholder="%s" autocomplete="off">%s</textarea>',
			esc_attr( $args['uid'] ),
			esc_attr( $args['name'] ),
			(int) $args['cols'],
			(int) $args['rows'],
			esc_attr( $args['placeholder'] ),
			esc_textarea( html_entity_decode( $args['value'] ) )
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
				'cols'        => 1,
				'rows'        => 5,
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

		return sanitize_textarea_field( $val );

	}
}
