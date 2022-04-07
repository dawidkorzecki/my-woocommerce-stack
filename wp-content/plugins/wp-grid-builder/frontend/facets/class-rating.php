<?php
/**
 * Rating facet
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
 * Rating
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Rating
 * @since 1.0.0
 */
class Rating {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {}

	/**
	 * Query facet choices
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array facet values.
	 */
	public function query_facet( $facet ) {

		global $wpdb;

		$where_clause = wpgb_get_filtered_where_clause( $facet, 'OR' );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT COUNT(*) AS count, FLOOR(facet_value) AS rating
				FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				AND FLOOR(facet_value) >= 1
				AND $where_clause
				GROUP BY rating",
				$facet['slug']
			),
			'ARRAY_A'
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( ! $facet['show_empty'] && empty( $results ) ) {
			return [];
		}

		return $this->format_results( $results );

	}

	/**
	 * Format facet choices
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $results Holds queried facet results.
	 * @return array Ratings values.
	 */
	public function format_results( $results ) {

		$defaults = array_fill( 1, 5, 0 );
		$ratings  = array_column( $results, 'rating' );
		$ratings  = array_combine( $ratings, $results );
		$ratings  = array_replace( $defaults, $ratings );

		krsort( $ratings );

		// Sum rating count to lowest ratings.
		return array_map(
			function( $rating ) use ( &$total, &$index ) {

				$count = isset( $rating['count'] ) ? $rating['count'] : 0;
				$total = (int) $count + (int) $total;
				$index = ( $index ?: 6 ) - 1;
				/* translators: %d: number of stars */
				$name = _n( '%d star', '%d stars', $index, 'wp-grid-builder' );

				return (object) [
					'facet_name'  => sprintf( $name, $index ),
					'facet_value' => $index,
					'count'       => $total,
				];

			},
			array_values( $ratings )
		);

	}

	/**
	 * Render facet
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $items Holds facet items.
	 * @return string Facet markup.
	 */
	public function render_facet( $facet, $items ) {

		$list = '';

		foreach ( $items as $item ) {

			if ( ! $facet['show_empty'] && ! $item->count ) {
				continue;
			}

			$list .= '<li>';
			$list .= $this->render_rating( $facet, $item );
			$list .= '</li>';

		}

		if ( empty( $list ) ) {
			return '';
		}

		$output  = '<div class="wpgb-rating-facet">';
			$output .= '<ul class="wpgb-hierarchical-list">';
				$output .= $this->render_reset( $facet );
				$output .= $list;
			$output .= '</ul>';
		$output .= '</div>';

		return $output;

	}

	/**
	 * Render "all" button (reset)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return string Rest button markup.
	 */
	public function render_reset( $facet ) {

		if ( empty( $facet['selected'] ) ) {
			return '';
		}

		$all = $facet['all_label'] ?: __( 'Any', 'wp-grid-builder' );

		$output  = '<li>';
			$output .= '<div class="wpgb-rating wpgb-rating-reset" role="button" aria-pressed="false" tabindex="0">';
				$output .= '<input type="hidden" name="' . esc_attr( $facet['slug'] ) . '" value="">';
				$output .= '<span class="wpgb-rating-control">';
					$output .= wpgb_svg_icon( 'wpgb/arrows/arrow-left', false );
				$output .= '</span>';
				$output .= '<span class="wpgb-rating-label">' . esc_html( $all ) . '</span>';
			$output .= '</div>';
		$output .= '</li>';

		return $output;

	}

	/**
	 * Render rating stars
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $item  Holds current list item.
	 * @return string Rating button markup.
	 */
	public function render_rating( $facet, $item ) {

		$selected = (int) reset( $facet['selected'] );
		$checked  = $item->facet_value === $selected;
		$tabindex = ! $checked && empty( $item->count ) ? -1 : 0;
		$pressed  = $checked ? 'true' : 'false';

		$output  = '<div class="wpgb-rating" role="button" aria-pressed="' . esc_attr( $pressed ) . '" tabindex="' . esc_attr( $tabindex ) . '">';
			$output .= $this->render_input( $facet, $item );
			$output .= '<span class="wpgb-rating-control">';
				$output .= $this->render_icon( $item );
				$output .= '<span class="wpgb-sr-only">' . esc_html( $item->facet_name ) . '</span>';
			$output .= '</span>';
			$output .= '<span class="wpgb-rating-label">';
				$output .= $item->facet_value < 5 ? __( '&#38; up', 'wp-grid-builder' ) : '';
				$output .= $facet['show_count'] ? '&nbsp;<span>(' . (int) $item->count . ')</span>' : '';
			$output .= '</span>';
		$output .= '</div>';

		return apply_filters( 'wp_grid_builder/facet/rating', $output, $facet, $item );

	}

	/**
	 * Render rating radio input
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $item  Holds current list item.
	 * @return string Radio markup.
	 */
	public function render_input( $facet, $item ) {

		return sprintf(
			'<input type="hidden" name="%1$s" value="%2$s"%3$s>',
			esc_attr( $facet['slug'] ),
			esc_attr( $item->facet_value ),
			disabled( ! $item->count, true, false )
		);

	}

	/**
	 * Render rating stars
	 *
	 * @since 1.0.0
	 * @access public
	 * @param array $item Holds current list item.
	 * @return string SVG stars icon.
	 */
	public function render_icon( $item ) {

		ob_start();
		wpgb_rating_stars_icon( $item->facet_value );
		return ob_get_clean();

	}

	/**
	 * Query object ids (post, user, term) for selected facet values
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return array Holds queried facet object ids.
	 */
	public function query_objects( $facet ) {

		global $wpdb;

		$values = $facet['selected'];
		$rating = reset( $values ) ?: 0;

		return $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT object_id
				FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				AND facet_value >= %d",
				$facet['slug'],
				$rating
			)
		);

	}
}
