<?php
/**
 * Custom field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Custom
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Custom extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		if ( empty( $args['content'] ) ) {
			return;
		}

		// Add SVG tags support to sanitize icon (SVG<use>).
		$allowed_html_post = wp_kses_allowed_html( 'post' );
		$allowed_svg_tags  = Helpers::allowed_svg_tags();
		$allowed_html_tags = wp_parse_args( $allowed_svg_tags, $allowed_html_post );
		$allowed_html_tags['input'] = [
			'type'         => true,
			'name'         => true,
			'class'        => true,
			'min'          => true,
			'max'          => true,
			'step'         => true,
			'value'        => true,
			'style'        => true,
			'role'         => true,
			'aria-label'   => true,
			'data-tooltip' => true,
		];

		echo wp_kses( $args['content'], $allowed_html_tags );

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
				'content' => '',
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
