<?php
/**
 * Group field
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings\Fields;

use WP_Grid_Builder\Includes\Settings\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings API Field
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields\Row
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Group extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		if ( empty( $args['fields'] ) ) {
			return;
		}

		$fields = $this->get_group_fields( $args );
		$total  = count( $fields );
		$count  = 0;

		echo '<div class="wpgb-field-group">';

		foreach ( $fields as $field ) {

			++$count;
			Settings::get_instance()->do_field( $field );

			if ( ! empty( $args['separator'] ) && $count < $total ) {
				echo '<span class="wpgb-field-group-sep">' . esc_html( $args['separator'] ) . '</span>';
			}
		}

		echo '</div>';

	}

	/**
	 * Get and map group field names
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function get_group_fields( $args ) {

		if ( empty( $args['group_names'] ) ) {
			return $args['fields'];
		}

		return array_map(
			function( $field ) use ( $args ) {

				if ( empty( $field['name'] ) ) {
					$field['name'] = $args['name'] . '[' . $field['id'] . ']';
				}

				if ( empty( $field['value'] ) && isset( $args['value'][ $field['id'] ] ) ) {
					$field['value'] = $args['value'][ $field['id'] ];
				}

				return $field;

			},
			$args['fields']
		);

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
				'fields'      => [],
				'separator'   => '',
				'group_names' => false,
			]
		);

	}

	/**
	 * Sanitize field value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $val  Fields values.
	 * @param array $args Holds field parameters.
	 * @return array
	 */
	public function sanitize( $val, $args = [] ) {

		// We do nothing because we recursively sanitize subfields.
		// Subfields were already sanitized at this stage.
		return $val;

	}
}
