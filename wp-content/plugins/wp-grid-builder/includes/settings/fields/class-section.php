<?php
/**
 * Section field
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings\Fields;

use WP_Grid_Builder\Includes\Settings\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings API Field
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields\Section
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Section extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		if ( empty( $args['fields'] ) ) {
			return;
		}

		$logic = $args['conditional_logic'];

		echo '<div class="wpgb-admin-section"' . ( ! empty( $logic ) ? ' data-field-condition="' . esc_attr( $logic ) . '"' : '' ) . '>';

		if ( ! empty( $args['title'] ) ) {
			echo '<h3>' . esc_html( $args['title'] ) . '</h3>';
		}

		if ( ! empty( $args['subtitle'] ) ) {
			echo '<p>' . wp_kses_post( $args['subtitle'] ) . '</p>';
		}

		Settings::get_instance()->do_fields( $args['fields'] );

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
				'fields'   => [],
				'title'    => '',
				'subtitle' => '',
			]
		);

	}

	/**
	 * Sanitize field value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $val  Fields values.
	 * @param array $args Holds field parameters.
	 */
	public function sanitize( $val, $args = [] ) {}
}
