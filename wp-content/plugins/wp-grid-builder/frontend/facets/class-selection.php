<?php
/**
 * Selection facet
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
 * Selection
 *
 * @class WP_Grid_Builder\FrontEnd\Facets\Selection
 * @since 1.0.0
 */
class Selection {

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
	 * @return array Holds facet choices.
	 */
	public function query_facet( $facet ) {

		$choices = [];
		$facets  = $this->get_facets();

		foreach ( $facets as $facet ) {

			switch ( $facet['type'] ) {
				case 'map':
				case 'geolocation':
					break;
				case 'range':
					$choices[] = $this->get_range_selection( $facet );
					break;
				case 'rating':
					$choices[] = $this->get_rating_selection( $facet );
					break;
				case 'date':
					$choices[] = $this->get_date_selection( $facet );
					break;
				case 'search':
				case 'autocomplete':
					$choices[] = $this->get_search_selection( $facet );
					break;
				default:
					$choices = array_merge( $choices, $this->get_default_selection( $facet ) );
					break;
			}
		}

		return array_values( array_filter( $choices ) );

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

		if ( empty( $items ) ) {
			return;
		}

		$output  = '<div class="wpgb-selection-facet">';
		$output .= '<ul class="wpgb-inline-list">';

		foreach ( $items as $item ) {

			$output .= '<li>';
			$output .= $this->render_button( $item );
			$output .= '</li>';

		}

		$output .= '</ul>';
		$output .= '</div>';

		return $output;

	}

	/**
	 * Render button
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $item Holds selection item.
	 * @return string
	 */
	public function render_button( $item ) {

		$input = sprintf(
			'<input type="hidden" name="%1$s" value="%2$s">',
			esc_attr( $item->facet_slug ),
			esc_attr( $item->facet_value )
		);

		$output  = '<div class="wpgb-button" role="button" aria-pressed="true" tabindex="0">';
			$output .= $input;
			$output .= '<span class="wpgb-button-control"></span>';
			$output .= '<span class="wpgb-button-label">' . esc_html( $item->facet_name ) . '</span>';
		$output .= '</div>';

		return apply_filters( 'wp_grid_builder/facet/selection', $output, $item->facet_slug, $item );

	}

	/**
	 * Get all facets with selections
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_facets() {

		// Keep selected order from query strings.
		$strings = wpgb_get_query_string();
		$strings = array_keys( $strings );
		$strings = array_flip( $strings );

		// Get facets from query string (selected facets).
		$facets = wpgb_get_facets_instance()->selections;
		$facets = array_merge( $strings, $facets );
		$facets = array_filter( $facets, 'is_array' );

		return $facets;

	}

	/**
	 * Get range values
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $facet Holds facet settings.
	 * @return array
	 */
	public function get_range_selection( $facet ) {

		$range = [ min( $facet['selected'] ), max( $facet['selected'] ) ];
		$range = array_unique( $range );
		$name  = array_map(
			function( $value ) use ( $facet ) {
				return $facet['prefix'] . $value . $facet['suffix'];
			},
			$range
		);

		return (object) [
			'facet_value' => wp_json_encode( $range ),
			'facet_name'  => implode( ' - ', $name ),
			'facet_slug'  => $facet['slug'],
		];

	}

	/**
	 * Get rating value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $facet Holds facet settings.
	 * @return array
	 */
	public function get_rating_selection( $facet ) {

		$value = (int) reset( $facet['selected'] );
		/* translators: %d: number of stars */
		$name = _n( '%d star', '%d stars', $value, 'wp-grid-builder' );
		$name = sprintf( $name, $value );

		return (object) [
			'facet_value' => $value,
			'facet_name'  => $name,
			'facet_slug'  => $facet['slug'],
		];

	}

	/**
	 * Get date values
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $facet Holds facet settings.
	 * @return array
	 */
	public function get_date_selection( $facet ) {

		$value  = (array) $facet['selected'];
		$format = $facet['date_format'] ?: 'Y-m-d';
		$name   = array_map(
			function( $date ) use ( $format ) {
				return date_i18n( $format, strtotime( $date ) );
			},
			$value
		);

		return (object) [
			'facet_value' => wp_json_encode( $value ),
			'facet_name'  => implode( ' - ', $name ),
			'facet_slug'  => $facet['slug'],
		];

	}

	/**
	 * Get search value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $facet Holds facet settings.
	 * @return array
	 */
	public function get_search_selection( $facet ) {

		$value = (array) $facet['selected'];

		return (object) [
			'facet_value' => wp_json_encode( $value ),
			'facet_name'  => implode( ',', $value ),
			'facet_slug'  => $facet['slug'],
		];

	}

	/**
	 * Get facet values
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $facet Holds facet settings.
	 * @return array
	 */
	public function get_default_selection( $facet ) {

		// If hide empty allowed then we need to fetch selected values from unfiltered query if missing (because of a search for example).
		if ( empty( $facet['choices'] ) ) {
			$facet['choices'] = ( new Async() )->get_selected_items( $facet, false );
		}

		// To preserve selection order.
		$items  = array_column( (array) $facet['choices'], null, 'facet_value' );
		$values = array_flip( $facet['selected'] );
		$values = array_intersect_key( array_replace( $values, $items ), $values );

		// If some items are missing (Show Empty Choices set to false).
		if ( count( $values ) < count( $facet['selected'] ) ) {
			$values = ( new Async() )->get_selected_items( $facet, false );
		}

		return array_map(
			function( $item ) use ( $facet ) {

				if ( ! is_object( $item ) ) {
					return null;
				}

				$item->facet_slug = $facet['slug'];

				return $item;

			},
			$values
		);

	}
}
