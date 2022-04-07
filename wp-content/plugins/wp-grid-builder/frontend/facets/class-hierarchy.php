<?php
/**
 * Hierarchy facet
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
 * Hierarchy
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Hierarchy
 * @since 1.0.0
 */
class Hierarchy {

	/**
	 * Rendered items counter
	 *
	 * @since 1.0.0
	 * @var integer
	 */
	public $count = 0;

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
	 * @return array Holds facet items.
	 */
	public function query_facet( $facet ) {

		global $wpdb;

		$order_clause = wpgb_get_orderby_clause( $facet );
		$where_clause = wpgb_get_filtered_where_clause( $facet, 'OR' );

		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		$parent_id = 'taxonomy' === $source ? $this->get_term_id( $facet ) : 0;
		$ancestors = 'taxonomy' === $source ? $this->get_ancestors( $facet, $parent_id ) : [];

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$children = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT facet_name, facet_value, facet_id, facet_parent, COUNT(DISTINCT object_id) AS count
				FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				AND facet_parent = %d
				AND $where_clause
				GROUP BY facet_value
				ORDER BY $order_clause
				LIMIT %d",
				$facet['slug'],
				$parent_id,
				$facet['limit']
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( ! empty( $ancestors ) ) {
			return array_merge( $ancestors, $children );
		}

		return $children;

	}

	/**
	 * Get term id from slug
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return integer Term id.
	 */
	public function get_term_id( $facet ) {

		if ( empty( $facet['selected'] ) ) {
			return 0;
		}

		$query = [
			'number' => 1,
			'fields' => 'ids',
			'slug'   => reset( $facet['selected'] ),
		];

		return reset( ( new \WP_Term_Query( $query ) )->terms );

	}

	/**
	 * Get ancestor terms of selected term.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $facet Holds facet settings.
	 * @param integer $parent_id Parent id.
	 * @return array Holds ancestor items.
	 */
	public function get_ancestors( $facet, $parent_id ) {

		if ( 0 === (int) $parent_id ) {
			return [];
		}

		$ids = get_ancestors( $parent_id, $facet['taxonomy'] );
		$ids = array_merge( $ids, (array) $parent_id );
		$all = $facet['all_label'] ?: __( 'Any', 'wp-grid-builder' );

		if ( ! empty( $facet['exclude'] ) ) {
			$ids = array_diff( $ids, (array) $facet['exclude'] );
		} elseif ( ! empty( $facet['include'] ) ) {
			$ids = array_intersect( $ids, (array) $facet['include'] );
		}

		$terms = get_terms(
			[
				'include' => $ids,
			]
		);

		$ancestors   = [];
		$ancestors[] = (object) [
			'facet_parent' => $facet['parent'] ?: 0,
			'facet_value'  => '',
			'facet_name'   => $all,
			'facet_id'     => 0,
			'count'        => 0,
		];

		foreach ( $terms as $term ) {

			if ( $term->term_id === (int) $facet['parent'] ) {
				continue;
			}

			// Take into account included/excluded terms from facet settings.
			if (
				in_array( (string) $term->term_id, $facet['include'], true ) &&
				! in_array( (string) $term->parent, $facet['include'], true )
			) {
				$term->parent = 0;
			}

			$ancestors[] = (object) [
				'facet_parent' => $term->parent,
				'facet_value'  => $term->slug,
				'facet_name'   => $term->name,
				'facet_id'     => $term->term_id,
				'count'        => 0,
			];

		}

		return $ancestors;

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

		$parent = 0;

		// To override parent if set.
		if ( $facet['selected'] && ! empty( $facet['parent'] ) ) {
			$parent = $facet['parent'];
		}

		$list = $this->render_list( $facet, $items, $parent );

		if ( empty( $list ) ) {
			return;
		}

		$output  = '<div class="wpgb-hierarchy-facet">';
		$output .= $list;
		$output .= '</div>';

		return $output;

	}

	/**
	 * Render facet
	 *
	 * @since 1.2.0 Handle shortcode [number] in button label.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $facet  Holds facet settings.
	 * @param array   $items  Holds facet items.
	 * @param integer $parent Parent id to process children.
	 * @return string Hierarchy list markup.
	 */
	public function render_list( $facet, $items, $parent = 0 ) {

		$list = '';

		foreach ( $items as $index => $item ) {

			// If is not a child item or root item.
			if ( (int) $item->facet_parent !== (int) $parent ) {
				continue;
			}

			$hidden = $this->count >= $facet['display_limit'] ? ' hidden' : '';

			$list .= '<li' . esc_attr( $hidden ) . '>';
			$list .= $this->render_item( $facet, $item );

			// Count rendered items.
			++$this->count;

			// To prevent infinite loop with back link id set to 0.
			if ( $item->facet_id ) {
				$list .= $this->render_list( $facet, $items, $item->facet_id );
			}

			$list .= '</li>';

			unset( $items[ $index ] );

		}

		if ( empty( $list ) ) {
			return;
		}

		$output  = '<ul class="wpgb-hierarchical-list">';
		$output .= $list;
		$output .= '</ul>';

		if ( 0 === $parent && $this->count > $facet['display_limit'] ) {

			$output .= '<button type="button" class="wpgb-toggle-hidden" aria-expanded="false">';
			$output .= esc_html( str_replace( '[number]', $this->count - $facet['display_limit'], $facet['show_more_label'] ) );
			$output .= '</button>';

		}

		return $output;

	}

	/**
	 * Render Checkbox
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $item  Holds current list item.
	 * @return string Hierarchy item markup.
	 */
	public function render_item( $facet, $item ) {

		$disabled = isset( $item->count ) ? ! $item->count : 0;
		$checked  = in_array( $item->facet_value, $facet['selected'], true );
		$pressed  = $checked ? 'true' : 'false';

		$output  = '<div class="wpgb-hierarchy" role="button" aria-pressed="' . esc_attr( $pressed ) . '" tabindex="0">';
		$output .= $this->render_input( $facet, $item );

		if ( ! $item->count && ! $checked ) {

			$output .= '<span class="wpgb-hierarchy-control">';
			$output .= wpgb_svg_icon( 'wpgb/arrows/arrow-left', false );
			$output .= '</span>';

		}

		$output .= '<span class="wpgb-hierarchy-label">';
		$output .= esc_html( $item->facet_name );
		$output .= $item->count && $facet['show_count'] ? '&nbsp;<span>(' . (int) $item->count . ')</span>' : '';
		$output .= '</span>';
		$output .= '</div>';

		return apply_filters( 'wp_grid_builder/facet/hierarchy', $output, $facet, $item );

	}

	/**
	 * Render radio input
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $item  Holds current list item.
	 * @return string Radio input markup.
	 */
	public function render_input( $facet, $item ) {

		return sprintf(
			'<input type="hidden" name="%1$s" value="%2$s">',
			esc_attr( $facet['slug'] ),
			esc_attr( $item->facet_value )
		);

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

		return ( new Radio() )->query_objects( $facet );

	}
}
