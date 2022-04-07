<?php
/**
 * Meta query field
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
 * @class WP_Grid_Builder\Includes\Settings\Fields\Meta_Query
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Meta_Query extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		$this->fields  = $args['fields'];

		if ( empty( $args['value'] ) ) {
			$args['value'] = $args['default'];
		}

		$this->render_clauses( $args['value'] );

	}

	/**
	 * Render meta-data clauses
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $args Holds field parameters.
	 * @param integer $index Field indexes.
	 */
	public function render_clauses( $args, $index = [] ) {

		$count  = 0;
		$level  = 0;
		$nested = [];

		if ( ! is_array( $args ) || empty( $args ) ) {
			return;
		}

		// If there isn't any clause, do not render.
		if ( isset( $args['relation'] ) && count( $args ) === 1 ) {
			return;
		}

		echo '<div class="wpgb-meta-clauses">';

		if ( isset( $args['relation'] ) ) {

			$this->render_clause_field( 'relation', $args, $index );
			unset( $args['relation'] );

		}

		foreach ( $args as $key => $clause ) {

			// If nested clause.
			if ( ! isset( $clause['key'] ) ) {

				array_push( $nested, $clause );
				continue;

			}

			$level = array_merge( $index, (array) $count );
			$this->render_clause( $clause, $level );
			++$count;

		}

		$this->render_clause_button();
		$this->render_delete_button();

		// Render nested clauses.
		if ( ! empty( $nested ) ) {

			array_map(
				function( $clauses ) use ( &$index, &$count ) {

					$level = array_merge( $index, (array) $count );
					$this->render_clauses( $clauses, $level );
					$count++;

				},
				$nested
			);

		}

		// Limit nested clauses to one level (maybe unlock in next updates).
		if ( count( $level ) === 1 ) {
			$this->render_relation_button();
		}

		echo '</div>';

	}

	/**
	 * Add clause button
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_clause_button() {

		echo '<button type="button" class="wpgb-button wpgb-button-small wpgb-meta-clause wpgb-add-metakey">';
			echo '<span class="wpgb-sr-only">' . esc_html__( 'Add Meta Key', 'wp-grid-builder' ) . '</span>';
			Helpers::get_icon( 'plus' );
		echo '</button>';

	}

	/**
	 * Add delete button
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_delete_button() {

		echo '<button type="button" class="wpgb-button wpgb-button-small wpgb-red wpgb-delete-metakey">';
			echo '<span class="wpgb-sr-only">' . esc_html__( 'Delete', 'wp-grid-builder' ) . '</span>';
			Helpers::get_icon( 'cross' );
		echo '</button>';

	}

	/**
	 * Add button add metaky relation
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_relation_button() {

		echo '<div class="wpgb-add-relation">';
			echo '<button type="button" class="wpgb-button wpgb-button-small wpgb-button-icon wpgb-blue">';
				Helpers::get_icon( 'plus' );
				esc_html_e( 'Add Relation', 'wp-grid-builder' );
			echo '</button>';
		echo '</div>';

	}

	/**
	 * Render meta query fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $args Holds all field parameters.
	 * @param integer $index Field indexes.
	 */
	public function render_clause( $args, $index ) {

		echo '<div class="wpgb-meta-clause">';
			$this->render_clause_field( 'key', $args, $index );
			$this->render_clause_field( 'type', $args, $index );
			$this->render_clause_field( 'value', $args, $index );
			$this->render_clause_field( 'compare', $args, $index );
			$this->render_delete_button();
		echo '</div>';

	}

	/**
	 * Render relation field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string  $type Field type.
	 * @param array   $args Holds all field parameters.
	 * @param integer $index Field indexes.
	 */
	public function render_clause_field( $type, $args, $index ) {

		$index = $index ? '[' . implode( '][', $index ) . ']' : '';
		$field = $this->fields[ $type ];

		$field['name']  = 'wpgb[meta_query]' . $index . '[' . $type . ']';
		$field['value'] = isset( $args[ $type ] ) ? $args[ $type ] : '';

		Settings::get_instance()->do_field( $field );

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
				'default' => [
					'relation' => 'AND',
					[
						'key'     => '',
						'type'    => 'CHAR',
						'value'   => '',
						'compare' => '=',
					],
				],
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

		$val = $this->filter_clause( $val );

		if ( empty( $val ) || ( is_array( $val ) && count( $val ) === 1 ) ) {
			return [];
		}

		return $val;

	}

	/**
	 * Recursively filter meta clause args
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds meta query args.
	 * @return array
	 */
	public function filter_clause( $args ) {

		if ( empty( $args ) || ! is_array( $args ) ) {
			return $args;
		}

		$args = array_map(
			function( $clause ) {
				return is_array( $clause ) ? $this->filter_clause( $clause ) : $clause;
			},
			$args
		);

		if (
			( isset( $args['type'] ) && ! isset( $args['key'] ) ) ||
			( isset( $args['key'] ) && empty( $args['key'] ) )
		) {
			return;
		}

		return array_filter(
			$args,
			function( $clause ) {
				return '' !== $clause && null !== $clause &&
					( ! is_array( $clause ) || count( $clause ) > 1 );
			}
		);

	}
}
