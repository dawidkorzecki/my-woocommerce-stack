<?php
/**
 * Repeater field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Table
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Table extends Field {

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

		echo '<div class="wpgb-table-wrapper">';
		echo '<table class="wpgb-table ' . sanitize_html_class( $args['class'] ) . '">';
		$this->do_header( $args );
		$this->do_body( $args );
		echo '</table>';
		echo '</div>';

	}

	/**
	 * Render table header
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function do_header( $args ) {

		echo '<thead><tr>';
		echo '<td data-colname="label"></td>';

		foreach ( $args['fields'] as $field ) {

			echo '<td data-colname="' . esc_attr( $field['id'] ) . '">';
			echo isset( $field['label'] ) ? esc_html( $field['label'] ) : '';
			echo '</td>';

		}

		echo '</tr></thead>';

	}

	/**
	 * Render table body
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function do_body( $args ) {

		$cell_nb = count( $args['rows'] );

		echo '<tbody>';

		for ( $i = 0; $i < $cell_nb; $i++ ) {

			echo '<tr>';

			echo '<td data-colname="label">';

			if ( ! empty( $args['rows'][ $i ]['label'] ) ) {
				echo esc_html( $args['rows'][ $i ]['label'] );
			}

			if ( ! empty( $args['rows'][ $i ]['icon'] ) ) {
				echo '<svg><use xlink:href="' . esc_url( $args['rows'][ $i ]['icon'] ) . '"></use></svg>';
			}

			echo '</td>';

			$this->do_cell( $args, $i );
			echo '</tr>';

		}

		echo '</tbody>';

	}

	/**
	 * Render table cell
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $args  Holds field parameters.
	 * @param integer $index Table row index.
	 */
	public function do_cell( $args, $index ) {

		$fields = $this->get_table_fields( $args, $index );

		foreach ( $fields as $field ) {

			echo '<td data-colname="' . esc_attr( $field['id'] ) . '">';
			Settings::get_instance()->do_field( $field );
			echo '</td>';

		}

	}

	/**
	 * Get table fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $args  Holds fields parameters.
	 * @param integer $index Table row index.
	 */
	public function get_table_fields( $args, $index ) {

		return array_map(
			function( $field ) use ( $args, $index ) {

				$field['label'] = '';
				$field['value'] = $this->get_field_value( $args, $field, $index );
				$field['name']  = $this->get_field_name( $args, $field, $index );

				if ( ! empty( $field['fields'] ) ) {

					$field_id = $field['id'];
					$field['id'] = rtrim( str_replace( 'wpgb[', '', $field['name'] ), ']' );
					$field['fields'] = $this->get_table_fields( $field, '' );
					$field['id'] = $field_id;

				}

				return $field;

			},
			$args['fields']
		);

	}

	/**
	 * Render table cell
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $args  Holds fields parameters.
	 * @param array   $field Holds field parameters.
	 * @param integer $index Table row index.
	 */
	public function get_field_name( $args, $field, $index ) {

		$name = array_filter(
			[ $args['id'], $index, $field['id'] ],
			function( $val ) {
				return '' !== $val;
			}
		);

		$name = implode( '][', $name );
		$name = 'wpgb[' . $name . ']';

		return $name;

	}

	/**
	 * Render table cell
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $args  Holds fields parameters.
	 * @param array   $field Holds field parameters.
	 * @param integer $index Table row index.
	 */
	public function get_field_value( $args, $field, $index ) {

		if ( ! isset( $args['value'][ $index ][ $field['id'] ] ) ) {
			return ! empty( $field['default'] ) ? $field['default'] : '';
		}

		return $args['value'][ $index ][ $field['id'] ];

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
				'rows'    => [
					[
						'label' => '',
						'icon'  => '',
					],
				],
				'class'   => '',
				'fields'  => [],
				'default' => [],
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
