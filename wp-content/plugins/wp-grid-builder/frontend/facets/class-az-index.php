<?php
/**
 * A-Z Index facet
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Facets;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AZ_Index
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\AZ_Index
 * @since 1.5.0
 */
class AZ_Index {

	/**
	 * Constructor
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function __construct() {}

	/**
	 * Query facet choices
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array Holds facet items.
	 */
	public function query_facet( $facet ) {

		global $wpdb;

		$where_clause = wpgb_get_where_clause( $facet );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT UPPER(LEFT(facet_name, 1)) AS string, COUNT(DISTINCT object_id) AS count
				FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				AND $where_clause
				GROUP BY string",
				$facet['slug']
			),
			'ARRAY_A'
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$results = $this->format_results( $results );

		return $this->set_indexes( $facet, $results );

	}

	/**
	 * Format facet choices
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $results Holds available facet items.
	 * @return array
	 */
	public function format_results( $results ) {

		$strings = array_map( 'remove_accents', array_column( $results, 'string' ) );
		$results = array_map( 'array_filter', array_combine( $strings, $results ) );

		$results['#'] = [
			'count' => array_reduce(
				$results,
				function( $total, $item ) {

					if ( isset( $item['string'] ) && is_numeric( $item['string'] ) ) {
						// phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInTernaryCondition
						$total += (int) $item['count'];
					}

					return $total;

				}
			) ?: 0,
		];

		return $results;

	}

	/**
	 * Normalize facet choices
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet   Holds facet settings.
	 * @param array $results Holds available facet items.
	 * @return array
	 */
	public function set_indexes( $facet, $results ) {

		$letters = array_diff( array_map( 'trim', explode( ',', $facet['alphabetical_index'] ) ), [ '' ] );
		$numbers = array_diff( array_map( 'trim', explode( ',', $facet['numeric_index'] ) ), [ '' ] );

		return array_merge(
			$this->normalize( $results, $letters, 'letter' ),
			$this->normalize( $results, $numbers, 'number' )
		);

	}

	/**
	 * Normalize facet choices
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array  $items    Holds available facet items.
	 * @param array  $defaults Holds default items.
	 * @param string $type     Type of items.
	 * @return array
	 */
	public function normalize( $items, $defaults, $type ) {

		return array_map(
			function( $default ) use ( $items, $type ) {

				$value = strtoupper( remove_accents( $default ) );

				return (object) wp_parse_args(
					! empty( $items[ $value ] ) ? $items[ $value ] : [],
					[
						'facet_value' => $value,
						'facet_name'  => $default,
						'type'        => $type,
						'count'       => 0,
					]
				);

			},
			$defaults
		);

	}

	/**
	 * Render facet
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $items Holds facet items.
	 * @return string Facet markup.
	 */
	public function render_facet( $facet, $items ) {

		$letter_list = $this->render_buttons( $facet, $items, 'letter' );
		$number_list = $this->render_buttons( $facet, $items, 'number' );

		if ( empty( $letter_list ) && empty( $number_list ) ) {
			return '';
		}

		$output = '<div class="wpgb-az-index-facet">';

		if ( ! empty( $letter_list ) ) {

			$output .= '<ul class="wpgb-inline-list">';
			$output .= $this->render_reset( $facet );
			$output .= $letter_list;
			$output .= '</ul>';

		}

		if ( ! empty( $number_list ) ) {

			$output .= '<ul class="wpgb-inline-list">';

			if ( empty( $letter_list ) ) {
				$output .= $this->render_reset( $facet );
			}

			$output .= $number_list;
			$output .= '</ul>';

		}

		$output .= '</div>';

		return $output;

	}

	/**
	 * Render "all" button (reset)
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return string Reset button markup.
	 */
	public function render_reset( $facet ) {

		if ( ! $facet['all_label'] ) {
			return '';
		}

		$button = (object) [
			'facet_value' => '',
			'facet_name'  => $facet['all_label'],
		];

		$output  = '<li>';
		$output .= $this->render_button( $facet, $button );
		$output .= '</li>';

		return $output;

	}

	/**
	 * Render buttons
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array  $facet Holds facet settings.
	 * @param array  $items Holds facet items.
	 * @param string $type  Type of items.
	 * @return string Buttons markup.
	 */
	public function render_buttons( $facet, $items, $type ) {

		$output = '';

		foreach ( $items as $index => $item ) {

			// Skip if it does not match type.
			if ( $type !== $item->type ) {
				continue;
			}

			// Hide empty item if allowed.
			if ( ! $facet['show_empty'] && ! $item->count ) {
				continue;
			}

			$output .= '<li>';
			$output .= $this->render_button( $facet, $item );
			$output .= '</li>';

		}

		return $output;

	}

	/**
	 * Render button
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $item  Holds current list item.
	 * @return string Button markup.
	 */
	public function render_button( $facet, $item ) {

		$all_button = $facet['all_label'] && '' === $item->facet_value;

		// Select all button if no selection.
		if ( $all_button && empty( $facet['selected'] ) ) {
			$checked = true;
		} else {
			$checked = in_array( $item->facet_value, $facet['selected'], true );
		}

		$disabled = ! $all_button && ! $checked && empty( $item->count );
		$tabindex = $disabled ? -1 : 0;
		$pressed  = $checked ? 'true' : 'false';

		$output  = '<div class="wpgb-az-index" role="button" aria-pressed="' . esc_attr( $pressed ) . '" tabindex="' . esc_attr( $tabindex ) . '">';
			$output .= $this->render_input( $facet, $item, $disabled );
			$output .= '<span class="wpgb-az-index-label">';
				$output .= esc_html( $item->facet_name );
				$output .= isset( $item->count ) && $facet['show_count'] ? ' <span>(' . (int) $item->count . ')</span>' : '';
			$output .= '</span>';
		$output .= '</div>';

		return apply_filters( 'wp_grid_builder/facet/az_index', $output, $facet, $item );

	}

	/**
	 * Render checkbox/radio input
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array   $facet    Holds facet settings.
	 * @param array   $item     Holds current list item.
	 * @param boolean $disabled Input disabled state.
	 * @return string Checkbox/Radio input markup.
	 */
	public function render_input( $facet, $item, $disabled ) {

		return sprintf(
			'<input type="hidden" name="%1$s" value="%2$s"%3$s>',
			esc_attr( $facet['slug'] ),
			esc_attr( $item->facet_value ),
			disabled( $disabled, true, false )
		);

	}

	/**
	 * Query object ids (post, user, term) for selected facet values
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array Holds queried facet object ids.
	 */
	public function query_objects( $facet ) {

		global $wpdb;

		if ( in_array( '#', $facet['selected'], true ) ) {

			$facet['selected'] = array_merge( $facet['selected'], [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ] );
			unset( $facet['selected']['#'] );

		}

		$placeholders = rtrim( str_repeat( '%s,', count( $facet['selected'] ) ), ',' );

		$object_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT object_id
				FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				AND UPPER(LEFT(facet_name, 1)) IN ($placeholders)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				array_merge( (array) $facet['slug'], $facet['selected'] )
			)
		);

		return $object_ids;

	}
}
