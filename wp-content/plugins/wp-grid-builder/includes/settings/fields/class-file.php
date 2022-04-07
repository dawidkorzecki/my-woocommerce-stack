<?php
/**
 * File field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\File
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class File extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		echo '<div class="wpgb-upload-file" style="' . esc_attr( $args['width'] ? 'max-width:' . (int) $args['width'] . 'px' : '' ) . '">';

		printf(
			'<input type="text" class="wpgb-input" id="%s" name="%s" value="%s" placeholder="%s" autocomplete="off" %s>',
			esc_attr( $args['uid'] ),
			esc_attr( $args['name'] ),
			esc_url( $args['value'] ),
			esc_attr( $args['placeholder'] ),
			$args['width'] ? 'style="width:' . (int) $args['width'] . 'px"' : ''
		);

		echo '<button class="wpgb-button wpgb-button-small wpgb-upload-media" type="button" data-mime-type="' . esc_attr( $args['mime_type'] ) . '">';
			esc_html_e( 'upload', 'wp-grid-builder' );
		echo '</button>';

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
				'mime_type'   => 'audio',
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

		$mimes = [
			// Image formats.
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'bmp'          => 'image/bmp',
			'tif|tiff'     => 'image/tiff',
			'ico'          => 'image/x-icon',
			'svg'          => 'image/svg+xml',
			// Video formats.
			'avi'          => 'video/avi',
			'divx'         => 'video/divx',
			'mpeg|mpg|mpe' => 'video/mpeg',
			'mp4|m4v'      => 'video/mp4',
			'ogv'          => 'video/ogg',
			'webm'         => 'video/webm',
			// Audio formats.
			'mp3|m4a|m4b'  => 'audio/mpeg',
			'wav'          => 'audio/wav',
			'ogg|oga'      => 'audio/ogg',
		];

		// Check if url have allowed mine type.
		$file = wp_check_filetype( $val, $mimes );
		return $file['ext'] ? esc_url_raw( $val ) : null;

	}
}
