<?php
/**
 * Fonts field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Fonts
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Fonts extends Field {

	/**
	 * Holds Google Fonts
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $google_fonts;

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {}

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
	 * @param mixed $arr Field value.
	 * @param array $args Holds field parameters.
	 * @param array $new_val Holds new values.
	 * @return array
	 */
	public function sanitize( $arr, $args = [], $new_val = [] ) {

		if ( empty( self::$google_fonts ) ) {
			self::$google_fonts = Helpers::get_google_fonts();
		}

		foreach ( $arr as $key => $val ) {

			if ( is_array( $val ) ) {

				// Check font family.
				if ( ! isset( self::$google_fonts[ $key ] ) ) {
					continue;
				}

				$new_val[ $key ] = [];
				$new_val[ $key ] = $this->sanitize( $val, '', $new_val[ $key ] );

			} else {
				// Set font weight.
				$new_val[ $key ] = sanitize_text_field( $val );
			}
		}

		return $new_val;

	}
}
