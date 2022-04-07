<?php
/**
 * Select field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Select
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Select extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		if ( ! empty( $args['placeholder'] ) ) {
			echo '<input type="hidden" name="' . esc_attr( $args['name'] ) . '" value="">';
		}

		printf(
			'<select class="wpgb-select %s" id="%s" name="%s" data-search="%s" data-async="%s"%s%s>',
			! $args['multiple'] && ! empty( $args['placeholder'] ) ? 'wpgb-select-has-clear' : '',
			esc_attr( $args['uid'] ),
			esc_attr( $args['name'] ),
			esc_attr( $args['search'] ? true : '' ),
			esc_attr( $args['async'] ? $args['async'] : '' ),
			( $args['width'] ? ' style="width:' . (int) $args['width'] . 'px"' : '' ),
			esc_attr( $args['multiple'] ? ' multiple' : '' )
		);

		if ( ! empty( $args['placeholder'] ) ) {
			echo '<option value="">' . esc_html( $args['placeholder'] ) . '</option>';
		}

		if ( ! $args['validate'] && $args['value'] && ! $args['multiple'] ) {
			echo '<option value="' . esc_attr( $args['value'] ) . '" selected>' . esc_html( $args['value'] ) . '</option>';
		}

		$this->render_options( $args['options'], $args );

		echo '</select>';

	}

	/**
	 * Render optgroups and options in select field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $options Holds options.
	 * @param array $args Holds field parameters.
	 */
	public function render_options( $options, $args ) {

		foreach ( $options as $key => $option ) {

			if ( is_array( $option ) ) {

				echo '<optgroup label="' . esc_attr( $key ) . '">';
					$this->render_options( $option, $args );
				echo '</optgroup>';

				continue;

			}

			printf(
				'<option value="%s" %s %s>%s</option>',
				esc_attr( $key ),
				selected( in_array( (string) $key, array_map( 'strval', (array) $args['value'] ), true ), true, false ),
				isset( $args['disabled'][ $key ] ) && $args['disabled'][ $key ] ? 'disabled' : '',
				esc_html( $option )
			);

		}

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
				'multiple'    => false,
				'search'      => '',
				'async'       => '',
				'validate'    => true,
				'width'       => '',
				'options'     => [],
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

		if ( is_array( $val ) ) {

			$new_val = array_map(
				function( $val ) use ( $args ) {
					return $this->validate( $val, $args );
				},
				$val
			);

			$new_val = array_filter( $new_val );
			$new_val = array_unique( $new_val );

			return $new_val;

		}

		return $this->validate( $val, $args );

	}

	/**
	 * Validate selected value(s)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $val Field value.
	 * @param array $args Holds field parameters.
	 * @return string|array
	 */
	public function validate( $val, $args ) {

		// If no allowed options validation or async method.
		if ( ! $args['validate'] || $args['async'] ) {
			return sanitize_text_field( $val );
		}

		// If empty value allowed.
		if ( empty( $val ) && ! empty( $args['placeholder'] ) ) {
			return '';
		}

		$exist = $this->option_exists( $val, $args['options'] );

		if ( ! $exist ) {
			return '';
		}

		return $val;

	}

	/**
	 * Recursively check if key exists in available options
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $key Value to search in array.
	 * @param array $array Array to search in.
	 * @return boolean
	 */
	public function option_exists( $key, $array ) {

		if ( array_key_exists( $key, $array ) ) {
			return true;
		} else {

			foreach ( $array as $value ) {

				if ( is_array( $value ) && $this->option_exists( $key, $value ) ) {
					return true;
				}
			}
		}

		return false;

	}
}
