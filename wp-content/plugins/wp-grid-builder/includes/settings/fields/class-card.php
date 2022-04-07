<?php
/**
 * Card field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Card
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Card extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		$class = new Select();
		$field = $class->normalize( $args );

		$class->render( $field );
		$this->render_button();

	}

	/**
	 * Select card button
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_button() {

		echo '<button type="button" class="wpgb-button wpgb-button-small wpgb-blue wpgb-select-card">';
			esc_html_e( 'Browse', 'wp-grid-builder' );
		echo '</button>';

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
			[ 'default' => [] ]
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
	 * @return array
	 */
	public function sanitize( $val, $args = [] ) {

		return array_map( 'sanitize_text_field', (array) $val );

	}
}
