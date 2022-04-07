<?php
/**
 * Url field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Url
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Url extends Field {

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
			'<input type="text" class="wpgb-input wpgb-text" id="%s" name="%s" value="%s" placeholder="%s" %s autocomplete="off">',
			esc_attr( $args['uid'] ),
			esc_attr( $args['name'] ),
			esc_attr( $args['value'] ),
			esc_attr( $args['placeholder'] ),
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
				'width'       => '',
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

		return esc_url_raw( $val );

	}
}
