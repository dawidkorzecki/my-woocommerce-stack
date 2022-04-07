<?php
/**
 * Password field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Password
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Password extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		echo '<div class="wpgb-password-holder">';

		printf(
			'<input type="password" class="wpgb-input wpgb-password" id="%s" name="%s" value="%s" placeholder="%s" %s %s autocomplete="new-password">',
			esc_attr( $args['uid'] ),
			esc_attr( $args['name'] ),
			esc_attr( $args['value'] ),
			esc_attr( $args['placeholder'] ),
			$args['width'] ? ' style="width:' . (int) $args['width'] . 'px"' : '',
			esc_attr( $args['disabled'] ? ' disabled' : '' )
		);

		Helpers::get_icon( 'preview' );

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
				'default'     => '',
				'placeholder' => '',
				'width'       => '',
				'disabled'    => '',
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

		return trim( wp_filter_nohtml_kses( $val ) );

	}
}
