<?php
/**
 * Builder field
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings\Fields;

use WP_Grid_Builder\Includes\Settings\Settings;
use WP_Grid_Builder\Includes\Database;
use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings API Field
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields\Builder
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Builder extends Field {

	/**
	 * Holds available facets
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $facets = [];

	/**
	 * Holds available carousel facets
	 *
	 * @since 1.1.7
	 * @var array
	 */
	private $carousel = [
		'page-dots'   => true,
		'prev-button' => true,
		'next-button' => true,
	];

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		$this->fields = $args['fields'];

		$value = $args['value'];
		$query = [
			'page'   => WPGB_SLUG . '-facet-settings',
			'create' => 'true',
		];

		echo '<div class="wpgb-layout-holder">';

			echo '<div class="wpgb-available-facets">';
				echo '<h3 class="wpgb-layout-label">' . esc_html__( 'Available facets', 'wp-grid-builder' ) . '</h3>';
				$this->render_available_facets( $value );
			echo '</div>';

			echo '<a class="wpgb-button wpgb-button-small wpgb-button-icon wpgb-green" href="' . esc_url( add_query_arg( $query, admin_url( 'admin.php' ) ) ) . '" target="_blank">';
				Helpers::get_icon( 'filter' );
				esc_html_e( 'Create a Facet', 'wp-grid-builder' );
			echo '</a>';

			echo '<div class="wpgb-layout-inner">';

				echo '<h3 class="wpgb-layout-label">' . esc_html__( 'Grid Layout', 'wp-grid-builder' ) . '</h3>';
				echo '<div class="wpgb-layout wpgb-layout-area">';

					$this->render_style_fields( 'wrapper', $value );
					$this->render_buttons( [ 'settings' ] );
					$this->render_drop_zone( [ 'sidebar-left' ], $value );

					echo '<div class="wpgb-layout-main">';
						$this->render_drop_zone( [ 'area-top-1', 'area-top-2' ], $value );

						echo '<div class="wpgb-layout-grid">';

							echo '<div class="wpgb-layout-wrapper">';
								echo '<div class="wpgb-layout-item"></div>';
								echo '<div class="wpgb-layout-item"></div>';
								echo '<div class="wpgb-layout-item"></div>';
								echo '<div class="wpgb-layout-item"></div>';
								echo '<div class="wpgb-layout-item"></div>';
								echo '<div class="wpgb-layout-item"></div>';
							echo '</div>';

							$this->render_drop_zone( [ 'area-left' ], $value );
							$this->render_drop_zone( [ 'area-right' ], $value );

						echo '</div>';

						$this->render_drop_zone( [ 'area-bottom-1', 'area-bottom-2' ], $value );

					echo '</div>';

					$this->render_drop_zone( [ 'sidebar-right' ], $value );

				echo '</div>';
			echo '</div>';
		echo '</div>';

	}

	/**
	 * Render drop zone area
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $areas Holds area slugs.
	 * @param array $args  Holds area settings.
	 */
	public function render_drop_zone( $areas, $args ) {

		foreach ( $areas as $area ) {

			$buttons   = 'area-left' === $area || 'area-right' === $area || 'sidebar-left' === $area || 'sidebar-right' === $area ? [ 'settings' ] : null;
			$alignment = isset( $args[ $area ]['style']['justify-content'] ) ? $args[ $area ]['style']['justify-content'] : null;

			echo '<div class="wpgb-layout-area" data-area="' . esc_attr( $area ) . '">';

				$this->render_style_fields( $area, $args );
				$this->render_buttons( $buttons, $alignment );

				echo '<ul class="wpgb-layout-facets" data-area="' . esc_attr( $area ) . '" style="' . esc_attr( $alignment ? ' justify-content:' . esc_attr( $alignment ) : '' ) . '">';
					echo '<li class="wpgb-layout-facet-placeholder">';
						esc_html_e( 'Drop Zone', 'wp-grid-builder' );
						echo '<input type="hidden" name="' . esc_attr( WPGB_SLUG . '[grid_layout][' . $area . '][facets]' ) . '" value="">';
					echo '</li>';
					$this->render_facets( $area, $args );
				echo '</ul>';

			echo '</div>';

		}

	}

	/**
	 * Render available facets list
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds layout settings.
	 */
	public function render_available_facets( $args ) {

		$added    = array_column( (array) $args, 'facets' );
		$added    = array_reduce( array_filter( $added ), 'array_merge', [] );
		$facets   = $this->get_available_facets();
		$defaults = apply_filters( 'wp_grid_builder/facets', [] );
		$defaults = array_merge( $defaults, $this->carousel );

		echo '<ul class="wpgb-layout-facets">';

		foreach ( $facets as $facet => $args ) {

			if ( in_array( (string) $facet, $added, true ) ) {
				continue;
			}

			if ( ! isset( $defaults[ $args['type'] ] ) ) {
				continue;
			}

			echo '<li class="wpgb-layout-facet" data-facet="' . esc_attr( $facet ) . '" title="' . esc_attr__( 'Drag &#38; Drop', 'wp-grid-builder' ) . '">';
				echo '<input type="hidden" value="' . esc_attr( $facet ) . '">';
				echo '<svg><use xlink:href="' . esc_url( $args['icon'] ) . '"></use></svg>';
				echo $args['name'] ? '<span>' . esc_html( $args['name'] ) . '</span>' : '';
			echo '</li>';

		}

		echo '</ul>';

	}

	/**
	 * Render facets in area
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $area Holds area slug.
	 * @param array $args Holds area settings.
	 */
	public function render_facets( $area, $args ) {

		if ( ! isset( $args[ $area ]['facets'] ) ) {
			return;
		}

		$facets    = (array) $args[ $area ]['facets'];
		$available = $this->get_available_facets();
		$defaults  = apply_filters( 'wp_grid_builder/facets', [] );
		$defaults  = array_merge( $defaults, $this->carousel );

		foreach ( $facets as $facet ) {

			if ( ! isset( $available[ $facet ] ) ) {
				continue;
			}

			$args = $available[ $facet ];

			if ( ! isset( $defaults[ $args['type'] ] ) ) {
				continue;
			}

			echo '<li class="wpgb-layout-facet" data-facet="' . esc_attr( $facet ) . '" title="' . esc_attr__( 'Drag &#38; Drop', 'wp-grid-builder' ) . '">';
				echo '<input type="hidden" name="' . esc_attr( WPGB_SLUG . '[grid_layout][' . $area . '][facets][]' ) . '" value="' . esc_attr( $facet ) . '">';
				echo '<svg><use xlink:href="' . esc_url( $args['icon'] ) . '"></use></svg>';
				echo $args['name'] ? '<span>' . esc_html( $args['name'] ) . '</span>' : '';
			echo '</li>';

		}

	}

	/**
	 * Render buttons
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $allowed Holds allowed buttons.
	 * @param mixed $alignment Alignment type.
	 */
	public function render_buttons( $allowed = [], $alignment = '' ) {

		$buttons = [
			'flex-start' => __( 'Align Left', 'wp-grid-builder' ),
			'center'     => __( 'Center', 'wp-grid-builder' ),
			'flex-end'   => __( 'Align Right', 'wp-grid-builder' ),
			'settings'   => __( 'Settings', 'wp-grid-builder' ),
		];

		$icons = [
			'flex-start' => 'align-left',
			'center'     => 'align-center',
			'flex-end'   => 'align-right',
			'settings'   => 'settings',
		];

		echo '<ul class="wpgb-layout-buttons">';

		foreach ( $buttons as $button => $title ) {

			if ( empty( $allowed ) || in_array( $button, $allowed, true ) ) {

				$class = $alignment && strpos( $button, $alignment ) !== false ? 'wpgb-layout-button-active' : '';

				echo '<li class="' . sanitize_html_class( $class ) . '" data-button="' . esc_attr( $button ) . '" title="' . esc_attr( $title ) . '">';
					Helpers::get_icon( $icons[ $button ] );
				echo '</li>';

			}
		}

		echo '</ul>';

	}

	/**
	 * Render style settings field in area
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $area Holds area slug.
	 * @param array $args Holds area settings.
	 */
	public function render_style_fields( $area, $args ) {

		$fields = array_map(
			function( $field ) use ( $args, $area ) {

				if ( ! isset( $field['fields'] ) ) {

					$field['name']  = 'wpgb[grid_layout][' . $area . '][style][' . $field['id'] . ']';
					$field['value'] = isset( $args[ $area ]['style'][ $field['id'] ] ) ? $args[ $area ]['style'][ $field['id'] ] : '';

					return $field;

				}

				$field['fields'] = array_map(
					function( $field ) use ( $args, $area ) {

						$field['name']  = 'wpgb[grid_layout][' . $area . '][style][' . $field['id'] . ']';
						$field['value'] = isset( $args[ $area ]['style'][ $field['id'] ] ) ? $args[ $area ]['style'][ $field['id'] ] : '';

						return $field;

					},
					$field['fields']
				);

				return $field;

			},
			$this->fields
		);

		echo '<div class="wpgb-layout-style">';
		unset( $fields[0] ); // Unset facet field.
		Settings::get_instance()->do_fields( $fields );
		echo '</div>';

	}

	/**
	 * Return available facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Available facets
	 */
	public function get_available_facets() {

		if ( ! empty( $this->facets ) ) {
			return $this->facets;
		}

		$this->facets = [
			'page-dots'   => [
				'type'  => 'page-dots',
				'name'  => __( 'Carousel Dots', 'wp-grid-builder' ),
				'icons' => [ 'small' => Helpers::get_icon( 'page-dots-facet-small', true ) ],
			],
			'prev-button' => [
				'type'  => 'prev-button',
				'name'  => '',
				'icons' => [ 'small' => Helpers::get_icon( 'prev-button-facet-small', true ) ],
			],
			'next-button' => [
				'type'  => 'next-button',
				'name'  => '',
				'icons' => [ 'small' => Helpers::get_icon( 'next-button-facet-small', true ) ],
			],
		];

		$defaults = apply_filters( 'wp_grid_builder/facets', [] );
		$defaults = array_merge( $this->facets, $defaults );
		$results  = Database::query_results(
			[
				'select'  => 'id, name, type',
				'from'    => 'facets',
				'orderby' => 'modified_date',
			]
		);

		foreach ( (array) $results as $facet ) {
			$this->facets[ $facet['id'] ] = $facet;
		}

		$this->facets = array_map(
			function( $facet ) use ( $defaults ) {

				$facet_type    = ! empty( $facet['type'] ) ? $facet['type'] : 'filter';
				$facet_icon    = isset( $defaults[ $facet_type ]['icons']['small'] );
				$facet['icon'] = $facet_icon ? $defaults[ $facet_type ]['icons']['small'] : Helpers::get_icon( 'filter-action-small', true );

				return $facet;

			},
			$this->facets
		);

		return $this->facets;

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
			[ 'default' => [] ]
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
