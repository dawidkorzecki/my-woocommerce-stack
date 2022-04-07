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
use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings API Field
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields\Repeater
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Repeater extends Field {

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
		echo '<table class="wpgb-table wpgb-repeater" data-limit="' . esc_attr( $args['limit'] ) . '">';
		$this->filter_rows( $args );
		$this->do_header( $args );
		$this->do_body( $args );
		echo '</table>';
		echo '</div>';

		$this->repeat_button( $args );

	}

	/**
	 * Filter empty rows
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function filter_rows( &$args ) {

		// Filter values to remove empty rows in repeater.
		if ( ! empty( $args['value'] ) ) {
			$args['value'] = array_map( 'array_filter', (array) $args['value'] );
		}

		$args['value'] = array_filter( (array) $args['value'] );

		if ( empty( $args['value'] ) ) {

			$args['value'] = [];

			foreach ( $args['fields'] as $field ) {

				$value = ! empty( $field['default'] ) ? $field['default'] : '';
				$args['value'][0][ $field['id'] ] = $value;

			}
		}

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
		echo '<td data-colname="handle"></td>';

		foreach ( $args['fields'] as $field ) {

			echo '<td data-colname="' . esc_attr( $field['id'] ) . '">';
			echo isset( $field['label'] ) ? esc_html( $field['label'] ) : '';
			echo '</td>';

		}

		echo '<td data-colname="delete"></td>';
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

		echo '<tbody>';

		foreach ( (array) $args['value'] as $index => $value ) {

			echo '<tr>';
			$this->handle_icon( $args );
			$this->do_cell( $args, $index );
			$this->delete_button( $args );
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

		$fields = $this->get_repeater_fields( $args, $index );

		foreach ( $fields as $field ) {

			echo '<td data-colname="' . esc_attr( $field['id'] ) . '">';
			Settings::get_instance()->do_field( $field );
			echo '</td>';

		}

	}

	/**
	 * Handle drag icon
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function handle_icon( $args ) {

		echo '<td data-colname="handle">';
			echo '<div class="wpgb-repeater-sort">';
				echo '<span class="wpgb-sr-only">' . esc_attr__( 'Drag to sort', 'wp-grid-builder' ) . '</span>';
				Helpers::get_icon( 'handle' );
			echo '</div>';
		echo '</td>';

	}

	/**
	 * Render delete button
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function delete_button( $args ) {

		echo '<td data-colname="delete">';
			echo '<button type="button" class="wpgb-button wpgb-button-small wpgb-red wpgb-delete-row">';
				echo '<span class="wpgb-sr-only">' . esc_html__( 'Delete row', 'wp-grid-builder' ) . '</span>';
				Helpers::get_icon( 'cross' );
			echo '</button>';
		echo '</td>';

	}

	/**
	 * Render repeater button
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function repeat_button( $args ) {

		echo '<button type="button" class="wpgb-button wpgb-button-small wpgb-add-row">';
			Helpers::get_icon( 'plus' );
			echo esc_html( $args['add_text'] );
		echo '</button>';

	}

	/**
	 * Get repeater fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $args  Holds fields parameters.
	 * @param integer $index Table row index.
	 */
	public function get_repeater_fields( $args, $index ) {

		return array_map(
			function( $field ) use ( $args, $index ) {

				$field['label'] = '';
				$field['value'] = $this->get_field_value( $args, $field, $index );
				$field['name']  = $this->get_field_name( $args, $field, $index );

				if ( ! empty( $field['fields'] ) ) {

					$field_id = $field['id'];
					$field['id'] = rtrim( str_replace( 'wpgb[', '', $field['name'] ), ']' );
					$field['fields'] = $this->get_repeater_fields( $field, '' );
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
				'fields'   => [],
				'default'  => [],
				'limit'    => 100,
				'add_text' => __( 'Add row', 'wp-grid-builder' ),
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
