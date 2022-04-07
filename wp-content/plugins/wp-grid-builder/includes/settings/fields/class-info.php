<?php
/**
 * Info field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Info
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Info extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.1 Added warning class.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		if ( empty( $args['content'] ) ) {
			return;
		}

		$class  = $args['id'];
		$class .= $args['warning'] ? ' wpgb-warning' : '';
		$class  = Helpers::sanitize_html_classes( $class );

		echo '<p class="wpgb-nota-bene wpgb-nota-' . esc_attr( $class ) . '">';
			echo '<span>';
				Helpers::get_icon( 'info' );
				echo wp_kses_post( $args['content'] );
			echo '</span>';
		echo '</p>';

	}

	/**
	 * Normalize field parameters
	 *
	 * @since 1.0.1 Added warning attribute.
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
				'content' => '',
				'warning' => false,
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
	 * @return void
	 */
	public function sanitize( $val, $args = [] ) {}
}
