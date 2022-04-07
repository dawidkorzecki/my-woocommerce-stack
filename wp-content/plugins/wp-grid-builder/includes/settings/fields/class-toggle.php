<?php
/**
 * Toggle field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Toggle
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Toggle extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		echo '<div class="wpgb-toggle">';

		printf(
			'<input type="hidden" name="%2$s" value="0">
			<input type="checkbox" class="wpgb-input wpgb-checkbox" id="%1$s" name="%2$s" value="1" %3$s>',
			esc_attr( $args['uid'] ),
			esc_attr( $args['name'] ),
			checked( $args['value'], 1, 0 )
		);

		echo '<span></span>';

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
	 * @param mixed $val  Field value.
	 * @param array $args Holds field parameters.
	 * @return boolean
	 */
	public function sanitize( $val, $args = [] ) {

		return ! empty( $val ) ? 1 : 0;

	}
}
