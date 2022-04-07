<?php
/**
 * Checkbox facet
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
 * Checkbox
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Checkbox
 * @since 1.0.0
 */
class Checkbox {

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

		// Only one query needed if no facets selected.
		if ( ! wpgb_has_selected_facets() ) {
			return $this->get_facet_items( $facet, false );
		}

		// If we do not show empty items and do not order by item count.
		// In these cases order does not rely on unfiltered facet order.
		if ( ! $facet['show_empty'] && 'count' !== $facet['orderby'] ) {
			return $this->get_facet_items( $facet );
		}

		// If show empty items or count order.
		return $this->merge_facets(
			$this->get_facet_items( $facet ),
			$this->get_facet_items( $facet, false )
		);

	}

	/**
	 * Query facet items from object ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $facet Holds facet settings.
	 * @param boolean $filtered Where clause state.
	 * @return array Holds facet items.
	 */
	public function get_facet_items( $facet, $filtered = true ) {

		global $wpdb;

		$order_clause = wpgb_get_orderby_clause( $facet );

		if ( $filtered ) {
			$where_clause = wpgb_get_filtered_where_clause( $facet, $facet['logic'] );
		} else {
			$where_clause = wpgb_get_unfiltered_where_clause();
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$facet_values = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT facet_name, facet_value, facet_id, facet_parent, COUNT(DISTINCT object_id) AS count
				FROM {$wpdb->prefix}wpgb_index
				WHERE slug = %s
				AND $where_clause
				GROUP BY facet_value
				ORDER BY $order_clause
				LIMIT %d",
				$facet['slug'],
				$facet['limit']
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return $facet_values;

	}

	/**
	 * Merge facet items
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $filtered_facet   Holds filtered facet items.
	 * @param array $unfiltered_facet Holds unfiltered facet items.
	 * @return array Holds facet items.
	 */
	public function merge_facets( $filtered_facet, $unfiltered_facet ) {

		$filtered   = [];
		$unfiltered = [];

		// Rebuild filtered facet items with value as key.
		foreach ( $filtered_facet as $item ) {
			// Key as string (in case it's an integer) to preserve order.
			$filtered[ '_' . $item->facet_value ] = $item;
		}

		// Rebuild unfiltered facet items with value as key.
		foreach ( $unfiltered_facet as $item ) {
			$unfiltered[ '_' . $item->facet_value ] = $item;
		}

		// Preserve filtered item count.
		$facet = array_map(
			function( $item ) use ( $filtered ) {

				$facet_value = '_' . $item->facet_value;
				$is_filtered = isset( $filtered[ $facet_value ] );
				$item->count = $is_filtered ? $filtered[ $facet_value ]->count : 0;

				return $item;

			},
			$unfiltered
		);

		return array_values( $facet );

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

		$list = $this->render_list( $facet, $items );

		if ( empty( $list ) ) {
			return;
		}

		return sprintf( '<div class="wpgb-checkbox-facet">%s</div>', $list );

	}

	/**
	 * Render list
	 *
	 * @since 1.2.0 Handle shortcode [number] in button label.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $facet  Holds facet settings.
	 * @param array   $items  Holds facet items.
	 * @param integer $parent Parent id to process children.
	 * @return string List markup.
	 */
	public function render_list( $facet, $items, $parent = 0 ) {

		$list = '';

		foreach ( $items as $index => $item ) {

			if ( ! $this->should_render( $facet, $item, $parent ) ) {
				continue;
			}

			$children = '';

			// Recursively get children.
			if ( $facet['hierarchical'] ) {
				$children = $this->render_list( $facet, $items, $item->facet_id );
			}

			$list .= sprintf(
				'<li%1$s>%2$s%3$s</li>',
				$this->item_attributes( $facet, $item, $children ),
				$this->render_checkbox( $facet, $item ),
				$children
			);

			// Count rendered items (exclude treeitems).
			if ( 0 === (int) $item->facet_parent || ! $facet['treeview'] ) {
				++$this->count;
			}

			unset( $items[ $index ] );

		}

		if ( empty( $list ) ) {
			return '';
		}

		$list = sprintf(
			'<ul class="wpgb-hierarchical-list"%1$s>%2$s</ul>',
			$facet['treeview'] ? ' role="group"' : '',
			$list
		);

		if ( 0 === $parent && $this->count > $facet['display_limit'] ) {

			$list .= '<button type="button" class="wpgb-toggle-hidden" aria-expanded="false">';
			$list .= esc_html( str_replace( '[number]', $this->count - $facet['display_limit'], $facet['show_more_label'] ) );
			$list .= '</button>';

		}

		return $list;

	}

	/**
	 * Check if we should render item
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @param array   $facet  Holds facet settings.
	 * @param array   $item   Current facet item.
	 * @param integer $parent Parent id to process children.
	 * @return boolean
	 */
	public function should_render( $facet, $item, $parent ) {

		// Do not render if hierarchical list and not a child item.
		if ( $facet['hierarchical'] && (int) $item->facet_parent !== (int) $parent ) {
			return false;
		}

		// Do not render children if not hierarchical list.
		if ( ! $facet['hierarchical'] && ! $facet['children'] && (int) $item->facet_parent > 0 ) {
			return false;
		}

		// Do not render empty item if not allowed.
		if ( ! $facet['show_empty'] && ! $item->count ) {
			return false;
		}

		return true;

	}

	/**
	 * Get item attribute
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @param array $facet    Holds facet settings.
	 * @param array $item     Current facet item.
	 * @param array $children Holds item children.
	 * @return string HTML attributes.
	 */
	public function item_attributes( $facet, $item, $children ) {

		$atts = '';

		if ( $facet['treeview'] ) {

			$role  = ! empty( $children ) ? 'treeitem' : 'none';
			$atts .= ' role="' . $role . '" tabindex="-1"';
			$atts .= 'treeitem' === $role ? ' aria-expanded="false"' : '';

		}

		if (
			$this->count >= $facet['display_limit'] &&
			( ! $facet['treeview'] || 0 === (int) $item->facet_parent )
		) {
			$atts .= ' hidden';
		}

		return $atts;

	}

	/**
	 * Render Checkbox
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @param array $item  Holds current list item.
	 * @return string Checkbox item markup.
	 */
	public function render_checkbox( $facet, $item ) {

		$selected = in_array( $item->facet_value, $facet['selected'], true );
		$disabled = ! $selected && empty( $item->count );
		$tabindex = $facet['treeview'] && ! $disabled ? '' : ( $disabled ? -1 : 0 );
		$tabindex = '' !== $tabindex ? ' tabindex="' . $tabindex . '"' : '';
		$pressed  = $selected ? 'true' : 'false';

		$output = '<div class="wpgb-checkbox" role="button" aria-pressed="' . $pressed . '"' . $tabindex . '>';
			$output .= $this->render_input( $facet, $item, $disabled );
			$output .= '<span class="wpgb-checkbox-control"></span>';
			$output .= '<span class="wpgb-checkbox-label">';
				$output .= esc_html( $item->facet_name );
				$output .= $facet['show_count'] ? '&nbsp;<span>(' . (int) $item->count . ')</span>' : '';
			$output .= '</span>';
		$output .= '</div>';

		return apply_filters( 'wp_grid_builder/facet/checkbox', $output, $facet, $item );

	}

	/**
	 * Render checkbox input
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $facet    Holds facet settings.
	 * @param array   $item     Holds current list item.
	 * @param boolean $disabled Input disabled state.
	 * @return string Checkbox input markup.
	 */
	public function render_input( $facet, $item, $disabled ) {

		return sprintf(
			'<input type="hidden" name="%1$s[]" value="%2$s"%3$s>',
			esc_attr( $facet['slug'] ),
			esc_attr( $item->facet_value ),
			disabled( $disabled, true, false )
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

		global $wpdb;

		$object_ids = [];
		$values = $facet['selected'];

		if ( 'OR' === $facet['logic'] ) {

			$placeholders = rtrim( str_repeat( '%s,', count( $values ) ), ',' );

			return $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT object_id
					FROM {$wpdb->prefix}wpgb_index
					WHERE slug = %s
					AND facet_value IN ($placeholders)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					array_merge( (array) $facet['slug'], $values )
				)
			);

		}

		// Making several queries is faster than using one query with HAVING clause.
		foreach ( $values as $index => $value ) {

			$results = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT object_id
					FROM {$wpdb->prefix}wpgb_index
					WHERE slug = %s
					AND facet_value IN (%s)",
					$facet['slug'],
					$value
				)
			);

			$object_ids = $index > 0 ? array_intersect( $object_ids, $results ) : $results;

			if ( empty( $object_ids ) ) {
				break;
			}
		}

		return $object_ids;

	}
}
