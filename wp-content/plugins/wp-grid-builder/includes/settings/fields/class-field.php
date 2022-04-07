<?php
/**
 * Field
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
 * Field class
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields\Field
 * @since 1.0.0
 */
class Field {

	/**
	 * Holds defaults field args.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $defaults = [
		'id'                => '',
		'label'             => '',
		'conditional_logic' => '',
	];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {}

	/**
	 * Process field parameters.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field Holds field parameters.
	 */
	public function process( $field ) {

		$field = $this->normalize( $field );
		$field = wp_parse_args( $field, $this->defaults );

		$this->set_uid( $field );
		$this->set_name( $field );
		$this->set_value( $field );
		$this->set_logic( $field );

		return $field;

	}

	/**
	 * Render field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field Holds field parameters.
	 */
	public function do_field( $field ) {

		$field = $this->process( $field );

		if ( 'section' === $field['type'] || 'repeater' === $field['type'] ) {

			$this->render( $field );
			return;

		}

		$tag   = $this->is_table() ? 'tr' : 'div';
		$class = $this->is_table() ? 'form-field' : 'wpgb-settings-field';
		$logic = $field['conditional_logic'];

		echo '<' . tag_escape( $tag ) . ' class="' . esc_attr( $class ) . '"' . ( ! empty( $logic ) ? ' data-field-condition="' . esc_attr( $logic ) . '"' : '' ) . '>';
		$this->label( $field );
		$this->field( $field );
		echo '</' . tag_escape( $tag ) . '>';

	}

	/**
	 * Set unique field id.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Holds field parameters.
	 */
	public function set_uid( &$field ) {

		$field['uid'] = 'wpgb-' . uniqid();

	}

	/**
	 * Set field name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Holds field parameters.
	 */
	public function set_name( &$field ) {

		if ( ! empty( $field['name'] ) ) {
			return;
		}

		if ( empty( $field['id'] ) ) {

			$field['name'] = '';
			return;

		}

		$field['name']  = WPGB_SLUG . '[' . $field['id'] . ']';
		$field['name'] .= ! empty( $field['multiple'] ) ? '[]' : '';

	}

	/**
	 * Set field value.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Holds field parameters.
	 */
	public function set_value( &$field ) {

		if ( isset( $field['value'] ) ) {
			return;
		}

		$field['value'] = '';

		if ( isset( $field['_value'] ) ) {
			$field['value'] = $field['_value'];
		} elseif ( ! empty( $field['default'] ) ) {
			$field['value'] = $field['default'];
		} elseif ( isset( $field['_default'] ) ) {
			$field['value'] = $field['_default'];
		}

	}

	/**
	 * Set field conditional logic.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Holds field parameters.
	 */
	public function set_logic( &$field ) {

		if ( empty( $field['conditional_logic'] ) ) {
			return;
		}

		$field['conditional_logic'] = wp_json_encode( $field['conditional_logic'] );

	}

	/**
	 * Render label
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Holds field parameters.
	 */
	public function label( $field ) {

		if ( empty( $field['label'] ) ) {
			return;
		}

		$format = '<label class="wpgb-field-label" for="%1$s">%2$s</label>';

		if ( in_array( $field['type'], [ 'group', 'radio', 'image', 'gallery', 'custom' ], true ) ) {
			$format = '<span class="wpgb-field-label">%2$s</span>';
		}

		if ( $this->is_table() ) {
			$format = '<th scope="row">' . $format . '</th>';
		}

		echo wp_kses_post(
			sprintf(
				$format,
				esc_attr( $field['uid'] ),
				esc_html( $field['label'] )
			)
		);

	}

	/**
	 * Render field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Holds field parameters.
	 */
	public function field( $field ) {

		$tag = $this->is_table() ? 'td' : 'div';

		echo '<' . tag_escape( $tag ) . ' class="wpgb-field-input">';
		$this->input( $field );
		echo '</' . tag_escape( $tag ) . '>';

	}

	/**
	 * Render input
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Holds field parameters.
	 */
	public function input( $field ) {

		$this->render( $field );
		$this->description( $field );
		$this->tooltip( $field );

	}

	/**
	 * Render field description
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Holds field parameters.
	 */
	public function description( $field ) {

		$style = '';
		$class = $this->is_table() ? 'description' : 'wpgb-field-desc';

		if ( empty( $field['description'] ) ) {
			return;
		}

		echo '<p class="' . sanitize_html_class( $class ) . '"' . ( ! empty( $field['width'] ) ? ' style="width:' . (int) $field['width'] . 'px"' : '' ) . '>';
			echo wp_kses_post( $field['description'] );
		echo '</p>';

	}

	/**
	 * Render field tooltip
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Holds field parameters.
	 */
	public function tooltip( $field ) {

		if ( empty( $field['tooltip'] ) ) {
			return;
		}

		$tooltip = wp_kses_post( $field['tooltip'] );

		echo '<span role="tooltip" aria-label="' . esc_attr( $tooltip ) . '" data-tooltip data-method="click" data-layout="large">';
			Helpers::get_icon( 'info' );
		echo '</span>';

	}

	/**
	 * Sanitize field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $field Holds field parameters.
	 * @param mixed  $value Field value.
	 * @return mixed Sanitized field value.
	 */
	public function sanitize_field( $field, $value ) {

		$field = $this->normalize( $field );

		return $this->sanitize( $value, $field );

	}

	/**
	 * Check if custom field are in table
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_table() {

		global $pagenow;

		return 'term.php' === $pagenow || 'user-edit.php' === $pagenow;

	}
}
