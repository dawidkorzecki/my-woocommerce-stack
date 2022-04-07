<?php
/**
 * Colors
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate global color schemes
 *
 * @class WP_Grid_Builder\FrontEnd\Colors
 * @since 1.1.5
 */
final class Colors {

	/**
	 * Generate global CSS
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get() {

		return (
			$this->schemes() .
			$this->lightbox() .
			$this->facets()
		);

	}

	/**
	 * Generate color schemes
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function schemes() {

		$schemes = '';
		$options = wpgb_get_options();

		// Accent color.
		if ( ! empty( $options['accent_scheme_1'] ) ) {

			$schemes .= '.wp-grid-builder [class*="wpgb-scheme-"] .wpgb-idle-accent-1,';
			$schemes .= '.wp-grid-builder [class*="wpgb-scheme-"] [class^="wpgb-block-"].wpgb-hover-accent-1:hover';
			$schemes .= '{color:' . esc_attr( $options['accent_scheme_1'] ) . '}';

		}

		// Color schemes.
		foreach ( [ 'dark', 'light' ] as $scheme ) {

			for ( $i = 1; $i < 4; $i++ ) {

				$color = $options[ $scheme . '_scheme_' . $i ];

				if ( empty( $color ) ) {
					continue;
				}

				// Idle and hover scheme.
				$schemes .= '.wp-grid-builder .wpgb-scheme-' . $scheme . ' .wpgb-idle-scheme-' . $i . ',';
				$schemes .= '.wp-grid-builder .wpgb-scheme-' . $scheme . ' [class^="wpgb-block-"].wpgb-hover-scheme-' . $i . ':hover';
				$schemes .= '{color:' . esc_attr( $color ) . '}';

			}
		}

		return $schemes;

	}

	/**
	 * Generate facet colors
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function facets() {

		$accent = wpgb_get_option( 'accent_scheme_1' );

		if ( empty( $accent ) ) {
			return '';
		}

		// Button active background color.
		$facets  = '.wpgb-facet .wpgb-button[aria-pressed="true"]';
		$facets .= '{background-color:' . esc_attr( $accent ) . ';border-color:' . esc_attr( $accent ) . '}';

		// Range slider progress color.
		$facets .= '.wpgb-facet .wpgb-range-facet .wpgb-range-slider .wpgb-range-progress';
		$facets .= '{background-color:' . esc_attr( $accent ) . '}';

		// Range slider thumb border color.
		$facets .= '.wpgb-facet .wpgb-range-facet .wpgb-range-slider .wpgb-range-thumb';
		$facets .= '{border-color:' . esc_attr( $accent ) . '}';

		// Checkbox button checked border color.
		$facets .= '.wpgb-facet .wpgb-checkbox-facet .wpgb-checkbox[aria-pressed="true"] .wpgb-checkbox-control';
		$facets .= '{border-color:' . esc_attr( $accent ) . ';background-color:' . esc_attr( $accent ) . '}';

		// Radio and color button checked border color.
		$facets .= '.wpgb-facet .wpgb-color-facet .wpgb-color[aria-pressed="true"] .wpgb-color-control,';
		$facets .= '.wpgb-facet .wpgb-radio-facet .wpgb-radio[aria-pressed="true"] .wpgb-radio-control';
		$facets .= '{border-color:' . esc_attr( $accent ) . '}';
		$facets .= '.wpgb-facet .wpgb-radio-facet .wpgb-radio-control:after';
		$facets .= '{background-color:' . esc_attr( $accent ) . '}';

		// Pagination Selected page color.
		$facets .= '.wpgb-facet .wpgb-pagination li a[aria-current]';
		$facets .= '{color:' . esc_attr( $accent ) . '}';

		// Load more and apply buttons background color.
		$facets .= '.wpgb-facet .wpgb-load-more,.wpgb-facet .wpgb-apply';
		$facets .= '{background-color:' . esc_attr( $accent ) . '}';

		// For 3rd party facets from add-ons for example.
		return apply_filters( 'wp_grid_builder/facet/style', $facets );

	}

	/**
	 * Generate lightbox colors
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function lightbox() {

		$lightbox = '';
		$options  = wpgb_get_options();

		if ( ! empty( $options['lightbox_background'] ) ) {

			$lightbox .= '.wpgb-lightbox-holder';
			$lightbox .= '{background:' . esc_attr( $options['lightbox_background'] ) . '}';

		}

		if ( ! empty( $options['lightbox_controls_color'] ) ) {

			$lightbox .= '.wpgb-lightbox-holder button,';
			$lightbox .= '.wpgb-lightbox-holder .wpgb-lightbox-counter';
			$lightbox .= '{color:' . esc_attr( $options['lightbox_controls_color'] ) . '}';

		}

		if ( ! empty( $options['lightbox_spinner_color'] ) ) {

			$lightbox .= '.wpgb-lightbox-holder:before';
			$lightbox .= '{color:' . esc_attr( $options['lightbox_spinner_color'] ) . '}';

		}

		if ( ! empty( $options['lightbox_title_color'] ) ) {

			$lightbox .= '.wpgb-lightbox-holder .wpgb-lightbox-title,';
			$lightbox .= '.wpgb-lightbox-holder .wpgb-lightbox-error';
			$lightbox .= '{color:' . esc_attr( $options['lightbox_title_color'] ) . '}';

		}

		if ( ! empty( $options['lightbox_desc_color'] ) ) {

			$lightbox .= '.wpgb-lightbox-holder .wpgb-lightbox-desc';
			$lightbox .= '{color:' . esc_attr( $options['lightbox_desc_color'] ) . '}';

		}

		return $lightbox;

	}
}
