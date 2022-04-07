<?php
/**
 * Radio field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Radio
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Radio extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		$list = isset( $args['icons'] ) || isset( $args['html'] );

		echo $list ? '<ul class="wpgb-radio-list wpgb-list">' : '<div class="wpgb-radio-list">';

		foreach ( $args['options'] as $key => $val ) {

			$uid = 'wpgb-' . uniqid();

			echo $list ? '<li class="wpgb-list-item">' : '<span class="wpgb-radio-item">';

			printf(
				'<input type="radio" class="wpgb-input wpgb-radio wpgb-sr-only" id="%1$s" name="%2$s" value="%3$s" %4$s>',
				esc_attr( $uid ),
				esc_attr( $args['name'] ),
				esc_attr( $key ),
				checked( $args['value'], $key, 0 )
			);

			echo '<label for="' . esc_attr( $uid ) . '">';

			if ( isset( $args['icons'][ $key ] ) ) {
				echo '<svg><use xlink:href="' . esc_url( $args['icons'][ $key ] ) . '"></use></svg>';
			}

			echo '<span>' . esc_html( $val ) . '</span>';
			echo '</label>';

			echo isset( $args['html'][ $key ] ) ? wp_kses_post( $args['html'][ $key ] ) : '';

			echo $list ? '</li>' : '</span>';

		}

		echo $list ? '</ul>' : '</div>';

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
				'default' => '',
				'options' => [],
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

		return array_key_exists( $val, $args['options'] ) ? sanitize_text_field( $val ) : $args['default'];

	}
}
