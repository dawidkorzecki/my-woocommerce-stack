<?php
/**
 * Image field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Image
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Image extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		$url = ! empty( $args['value'] ) ? wp_get_attachment_image_src( $args['value'], 'medium' ) : null;

		echo '<div class="wpgb-image" title="' . esc_attr( 'Select image' ) . '">';
			echo '<input type="hidden" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $args['value'] ) . '">';
			Helpers::get_icon( 'plus' );
			echo '<div class="wpgb-image-background" style="' . esc_attr( $url ? 'background-image: url(' . esc_url( $url[0] ) . ')' : '' ) . '"></div>';
			echo '<span class="wpgb-image-delete" title="' . esc_attr__( 'Delete image', 'wp-grid-builder' ) . '">';
				Helpers::get_icon( 'cross' );
			echo '</span>';
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
	 * @return integer
	 */
	public function sanitize( $val, $args = [] ) {

		return ! empty( $val ) ? (int) $val : '';

	}
}
