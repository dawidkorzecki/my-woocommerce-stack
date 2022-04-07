<?php
/**
 * Fields
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle fields
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields
 * @since 1.0.0
 */
class Fields {

	/**
	 * Holds registered field types.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $fields = [
		'builder'     => __NAMESPACE__ . '\Fields\Builder',
		'card'        => __NAMESPACE__ . '\Fields\Card',
		'checkbox'    => __NAMESPACE__ . '\Fields\Checkbox',
		'code'        => __NAMESPACE__ . '\Fields\Code',
		'color'       => __NAMESPACE__ . '\Fields\Color',
		'custom'      => __NAMESPACE__ . '\Fields\Custom',
		'file'        => __NAMESPACE__ . '\Fields\File',
		'fonts'       => __NAMESPACE__ . '\Fields\Fonts',
		'gallery'     => __NAMESPACE__ . '\Fields\Gallery',
		'group'       => __NAMESPACE__ . '\Fields\Group',
		'icons'       => __NAMESPACE__ . '\Fields\Icons',
		'image'       => __NAMESPACE__ . '\Fields\Image',
		'info'        => __NAMESPACE__ . '\Fields\Info',
		'meta_query'  => __NAMESPACE__ . '\Fields\Meta_Query',
		'number'      => __NAMESPACE__ . '\Fields\Number',
		'password'    => __NAMESPACE__ . '\Fields\Password',
		'radio'       => __NAMESPACE__ . '\Fields\Radio',
		'repeater'    => __NAMESPACE__ . '\Fields\Repeater',
		'section'     => __NAMESPACE__ . '\Fields\Section',
		'select'      => __NAMESPACE__ . '\Fields\Select',
		'slider'      => __NAMESPACE__ . '\Fields\Slider',
		'table'       => __NAMESPACE__ . '\Fields\Table',
		'text_number' => __NAMESPACE__ . '\Fields\Text_Number',
		'text'        => __NAMESPACE__ . '\Fields\Text',
		'textarea'    => __NAMESPACE__ . '\Fields\Textarea',
		'toggle'      => __NAMESPACE__ . '\Fields\Toggle',
		'url'         => __NAMESPACE__ . '\Fields\Url',
	];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		$this->fields = apply_filters( 'wp_grid_builder/field_types', $this->fields );

	}

	/**
	 * Get setting fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_fields() {

		$fields = [];

		foreach ( $this->get() as $args ) {
			$fields = array_merge( $fields, $this->get_subfields( $args['fields'] ) );
		}

		return $fields;

	}

	/**
	 * Get sub fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $fields Holds fields.
	 * @return array
	 */
	public function get_subfields( $fields = [] ) {

		$subfields = [];

		foreach ( $fields as $field ) {

			// Make sure it's a valid field.
			if ( empty( $field['type'] ) || empty( $field['id'] ) ) {
				continue;
			}

			// We get subfields from sections, accordions and groups.
			if (
				empty( $field['group_names'] ) &&
				(
					'group' === $field['type'] ||
					'section' === $field['type'] ||
					'accordion' === $field['type']
				)
			) {

				// We add accordion/section/group subfields.
				$subfields += $this->get_subfields( $field['fields'] );
				continue;

			}

			$subfields[ $field['id'] ] = $field;

			// We recursively get subfields.
			if ( ! empty( $field['fields'] ) ) {
				$subfields[ $field['id'] ]['fields'] = $this->get_subfields( $field['fields'] );
			}
		}

		return $subfields;

	}

	/**
	 * Get tab fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $tab Tab slug.
	 * @return array
	 */
	public function get_tab_fields( $tab = '' ) {

		return array_filter(
			$this->settings['fields'],
			function( $field ) use ( $tab ) {
				return isset( $field['tab'] ) && $field['tab'] === $tab;
			}
		);

	}

	/**
	 * Get field class
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field Holds field arguments.
	 * @return string Class name.
	 */
	public function get_field_class( $field ) {

		$type = $this->get_field_type( $field );

		if ( empty( $this->fields[ $type ] ) ) {
			return false;
		}

		$class = $this->fields[ $type ];

		if ( ! class_exists( $class ) ) {
			return false;
		}

		return $class;

	}

	/**
	 * Get field type
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field Holds field arguments.
	 * @return string Field type.
	 */
	public function get_field_type( $field ) {

		if ( empty( $field['type'] ) ) {
			return false;
		}

		return $field['type'];

	}

	/**
	 * Get field value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field Holds field arguments.
	 */
	public function get_field_value( $field ) {

		if ( empty( $field['id'] ) ) {
			return $field;
		}

		// Check and set value from global values.
		if ( isset( $this->settings['values'][ $field['id'] ] ) ) {
			$field['_value'] = $this->settings['values'][ $field['id'] ];
		}

		// Check and set default from global defaults.
		if ( isset( $this->settings['defaults'][ $field['id'] ] ) ) {
			$field['_default'] = $this->settings['defaults'][ $field['id'] ];
		}

		return $field;

	}

	/**
	 * Sanitize field value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field Holds field arguments.
	 * @param mixed $value Holds value to sanitize arguments.
	 * @return mixed Field value.
	 */
	public function sanitize_field_value( $field, $value = '' ) {

		$class = $this->get_field_class( $field );

		if ( ! $class ) {
			return '';
		}

		return ( new $class() )->sanitize_field( $field, $value );

	}

	/**
	 * Output fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $fields Holds fields.
	 */
	public function do_fields( $fields ) {

		array_map( [ $this, 'do_field' ], $fields );

	}

	/**
	 * Output field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field Holds field attributes.
	 */
	public function do_field( $field ) {

		$field = $this->get_field_value( $field );
		$field = apply_filters( 'wp_grid_builder/settings/' . $this->settings['id'] . '_field', $field );
		$class = $this->get_field_class( $field );

		if ( ! $class ) {
			return;
		}

		( new $class() )->do_field( $field );

	}
}
