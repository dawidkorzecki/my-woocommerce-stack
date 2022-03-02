<?php
/**
 * Elementor config file.
 *
 * @package woodmart
 */

use Elementor\Plugin;
use XTS\Elementor\Controls\Autocomplete;
use XTS\Elementor\Controls\Google_Json;
use XTS\Elementor\Controls\Buttons;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}

if ( ! function_exists( 'woodmart_elementor_maybe_init_cart' ) ) {
	/**
	 * Ini woo cart in elementor.
	 */
	function woodmart_elementor_maybe_init_cart() {
		if ( ! woodmart_woocommerce_installed() ) {
			return;
		}

		WC()->initialize_session();
	}

	add_action( 'elementor/editor/before_enqueue_scripts', 'woodmart_elementor_maybe_init_cart' );
}

if ( ! function_exists( 'woodmart_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function woodmart_elementor_register_elementor_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_location(
			'header',
			[
				'is_core'         => false,
				'public'          => false,
				'label'           => esc_html__( 'Header', 'woodmart' ),
				'edit_in_content' => false,
			]
		);

		$elementor_theme_manager->register_location(
			'footer',
			[
				'is_core'         => false,
				'public'          => false,
				'label'           => esc_html__( 'Footer', 'woodmart' ),
				'edit_in_content' => false,
			]
		);
	}

	add_action( 'elementor/theme/register_locations', 'woodmart_elementor_register_elementor_locations' );
}

if ( ! function_exists( 'woodmart_elementor_custom_shapes' ) ) {
	/**
	 * Custom shapes.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function woodmart_elementor_custom_shapes() {
		return [
			'wd_clouds'       => [
				'title'    => '[XTemos] Clouds',
				'has_flip' => false,
				'path'     => WOODMART_THEMEROOT . '/images/svg/clouds-top.svg',
				'url'      => WOODMART_IMAGES . '/svg/clouds-top.svg',
			],
			'wd_curved_line'  => [
				'title'    => '[XTemos] Curved line',
				'has_flip' => true,
				'path'     => WOODMART_THEMEROOT . '/images/svg/curved-line-top.svg',
				'url'      => WOODMART_IMAGES . '/svg/curved-line-top.svg',
			],
			'wd_paint_stroke' => [
				'title'    => '[XTemos] Paint stroke',
				'has_flip' => true,
				'path'     => WOODMART_THEMEROOT . '/images/svg/paint-stroke-top.svg',
				'url'      => WOODMART_IMAGES . '/svg/paint-stroke-top.svg',
			],
			'wd_sweet_wave'   => [
				'title'    => '[XTemos] Sweet wave',
				'has_flip' => true,
				'path'     => WOODMART_THEMEROOT . '/images/svg/sweet-wave-top.svg',
				'url'      => WOODMART_IMAGES . '/svg/sweet-wave-top.svg',
			],
			'wd_triangle'     => [
				'title'    => '[XTemos] Triangle',
				'has_flip' => false,
				'path'     => WOODMART_THEMEROOT . '/images/svg/triangle-top.svg',
				'url'      => WOODMART_IMAGES . '/svg/triangle-top.svg',
			],
			'wd_waves_small'  => [
				'title'    => '[XTemos] Waves small',
				'has_flip' => false,
				'path'     => WOODMART_THEMEROOT . '/images/svg/waves-small-top.svg',
				'url'      => WOODMART_IMAGES . '/svg/waves-small-top.svg',
			],
			'wd_waves_wide'   => [
				'title'    => '[XTemos] Waves wide',
				'has_flip' => false,
				'path'     => WOODMART_THEMEROOT . '/images/svg/waves-wide-top.svg',
				'url'      => WOODMART_IMAGES . '/svg/waves-wide-top.svg',
			],
		];
	}

	add_filter( 'elementor/shapes/additional_shapes', 'woodmart_elementor_custom_shapes' );
}

if ( ! function_exists( 'woodmart_elementor_custom_animations' ) ) {
	/**
	 * Custom animations.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function woodmart_elementor_custom_animations() {
		return [
			'XTemos' => [
				'wd-anim-slide-from-bottom' => 'Slide From Bottom',
				'wd-anim-slide-from-top'    => 'Slide From Top',
				'wd-anim-slide-from-left'   => 'Slide From Left',
				'wd-anim-slide-from-right'  => 'Slide From Right',
				'wd-anim-left-flip-y'       => 'Left Flip Y',
				'wd-anim-right-flip-y'      => 'Right Flip Y',
				'wd-anim-top-flip-x'        => 'Top Flip X',
				'wd-anim-bottom-flip-x'     => 'Bottom Flip X',
				'wd-anim-zoom-in'           => 'Zoom In',
				'wd-anim-rotate-z'          => 'Rotate Z',
			],
		];
	}

	add_filter( 'elementor/controls/animations/additional_animations', 'woodmart_elementor_custom_animations' );
}

if ( ! function_exists( 'woodmart_get_posts_by_query' ) ) {
	/**
	 * Get post by search
	 *
	 * @since 1.0.0
	 */
	function woodmart_get_posts_by_query() {
		$search_string = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : ''; // phpcs:ignore
		$post_type     = isset( $_POST['post_type'] ) ? $_POST['post_type'] : 'post'; // phpcs:ignore
		$results       = array();

		$query = new WP_Query(
			array(
				's'              => $search_string,
				'post_type'      => $post_type,
				'posts_per_page' => - 1,
			)
		);

		if ( ! isset( $query->posts ) ) {
			return;
		}

		foreach ( $query->posts as $post ) {
			$results[] = array(
				'id'   => $post->ID,
				'text' => $post->post_title,
			);
		}

		wp_send_json( $results );
	}

	add_action( 'wp_ajax_woodmart_get_posts_by_query', 'woodmart_get_posts_by_query' );
	add_action( 'wp_ajax_nopriv_woodmart_get_posts_by_query', 'woodmart_get_posts_by_query' );
}

if ( ! function_exists( 'woodmart_get_posts_title_by_id' ) ) {
	/**
	 * Get post title by ID
	 *
	 * @since 1.0.0
	 */
	function woodmart_get_posts_title_by_id() {
		$ids       = isset( $_POST['id'] ) ? $_POST['id'] : array(); // phpcs:ignore
		$post_type = isset( $_POST['post_type'] ) ? $_POST['post_type'] : 'post'; // phpcs:ignore
		$results   = array();

		$query = new WP_Query(
			array(
				'post_type'      => $post_type,
				'post__in'       => $ids,
				'posts_per_page' => - 1,
				'orderby'        => 'post__in',
			)
		);

		if ( ! isset( $query->posts ) ) {
			return;
		}

		foreach ( $query->posts as $post ) {
			$results[ $post->ID ] = $post->post_title;
		}

		wp_send_json( $results );
	}

	add_action( 'wp_ajax_woodmart_get_posts_title_by_id', 'woodmart_get_posts_title_by_id' );
	add_action( 'wp_ajax_nopriv_woodmart_get_posts_title_by_id', 'woodmart_get_posts_title_by_id' );
}

if ( ! function_exists( 'woodmart_get_taxonomies_title_by_id' ) ) {
	/**
	 * Get taxonomies title by id
	 *
	 * @since 1.0.0
	 */
	function woodmart_get_taxonomies_title_by_id() {
		$ids     = isset( $_POST['id'] ) ? $_POST['id'] : array(); // phpcs:ignore
		$results = array();

		$args = array(
			'include' => $ids,
		);

		$terms = get_terms( $args );

		if ( is_array( $terms ) && $terms ) {
			foreach ( $terms as $term ) {
				if ( is_object( $term ) ) {
					$results[ $term->term_id ] = $term->name . ' (' . $term->taxonomy . ')';
				}
			}
		}

		wp_send_json( $results );
	}

	add_action( 'wp_ajax_woodmart_get_taxonomies_title_by_id', 'woodmart_get_taxonomies_title_by_id' );
	add_action( 'wp_ajax_nopriv_woodmart_get_taxonomies_title_by_id', 'woodmart_get_taxonomies_title_by_id' );
}

if ( ! function_exists( 'woodmart_get_taxonomies_by_query' ) ) {
	/**
	 * Get taxonomies by search
	 *
	 * @since 1.0.0
	 */
	function woodmart_get_taxonomies_by_query() {
		$search_string = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : ''; // phpcs:ignore
		$taxonomy      = isset( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : ''; // phpcs:ignore
		$results       = array();

		$args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'search'     => $search_string,
		);

		$terms = get_terms( $args );

		if ( is_array( $terms ) && $terms ) {
			foreach ( $terms as $term ) {
				if ( is_object( $term ) ) {
					$results[] = array(
						'id'   => $term->term_id,
						'text' => $term->name . ' (' . $term->taxonomy . ')',
					);
				}
			}
		}

		wp_send_json( $results );
	}

	add_action( 'wp_ajax_woodmart_get_taxonomies_by_query', 'woodmart_get_taxonomies_by_query' );
	add_action( 'wp_ajax_nopriv_woodmart_get_taxonomies_by_query', 'woodmart_get_taxonomies_by_query' );
}

if ( ! function_exists( 'woodmart_register_elementor_controls' ) ) {
	/**
	 * Registering New Controls
	 *
	 * @since 1.0.0
	 */
	function woodmart_register_elementor_controls() {
		$controls_manager = Plugin::$instance->controls_manager;
		$controls_manager->register_control( 'wd_autocomplete', new Autocomplete() );
		$controls_manager->register_control( 'wd_buttons', new Buttons() );
		$controls_manager->register_control( 'wd_google_json', new Google_Json() );
	}

	add_action( 'elementor/controls/controls_registered', 'woodmart_register_elementor_controls' );
}


if ( ! function_exists( 'woodmart_elementor_enqueue_editor_styles' ) ) {
	/**
	 * Enqueue elementor editor custom styles
	 *
	 * @since 1.0.0
	 */
	function woodmart_elementor_enqueue_editor_styles() {
		wp_enqueue_style( 'woodmart-elementor-editor-style', WOODMART_THEME_DIR . '/inc/integrations/elementor/assets/css/editor.css', array( 'elementor-editor' ), woodmart_get_theme_info( 'Version' ) );
	}

	add_action( 'elementor/editor/before_enqueue_styles', 'woodmart_elementor_enqueue_editor_styles' );
}

if ( ! function_exists( 'woodmart_add_elementor_widget_categories' ) ) {
	/**
	 * Add theme widget categories
	 *
	 * @since 1.0.0
	 */
	function woodmart_add_elementor_widget_categories() {
		Plugin::instance()->elements_manager->add_category(
			'wd-elements',
			array(
				'title' => esc_html__( '[XTemos] Elements', 'woodmart' ),
				'icon'  => 'fab fa-plug',
			)
		);
	}

	woodmart_add_elementor_widget_categories();
}

if ( ! function_exists( 'woodmart_add_custom_font_group' ) ) {
	/**
	 * Add custom font group to font control
	 *
	 * @since 1.0.0
	 *
	 * @param array $font_groups Default font groups.
	 *
	 * @return array
	 */
	function woodmart_add_custom_font_group( $font_groups ) {
		return array( 'wd_fonts' => esc_html__( 'Theme fonts', 'woodmart' ) ) + $font_groups;
	}

	add_filter( 'elementor/fonts/groups', 'woodmart_add_custom_font_group' );
}

if ( ! function_exists( 'woodmart_add_custom_fonts_to_theme_group' ) ) {
	/**
	 * Add custom fonts to theme group
	 *
	 * @since 1.0.0
	 *
	 * @param array $additional_fonts Additional fonts.
	 *
	 * @return array
	 */
	function woodmart_add_custom_fonts_to_theme_group( $additional_fonts ) {
		$theme_fonts  = array();
		$content_font = woodmart_get_opt( 'primary-font' );
		$title_font   = woodmart_get_opt( 'text-font' );
		$alt_font     = woodmart_get_opt( 'secondary-font' );

		if ( isset( $content_font[0] ) && isset( $content_font[0]['font-family'] ) && $content_font[0]['font-family'] ) {
			$theme_fonts[ $content_font[0]['font-family'] ] = 'wd_fonts';
		}

		if ( isset( $title_font[0] ) && isset( $title_font[0]['font-family'] ) && $title_font[0]['font-family'] ) {
			$theme_fonts[ $title_font[0]['font-family'] ] = 'wd_fonts';
		}

		if ( isset( $alt_font[0] ) && isset( $alt_font[0]['font-family'] ) && $alt_font[0]['font-family'] ) {
			$theme_fonts[ $alt_font[0]['font-family'] ] = 'wd_fonts';
		}

		return $theme_fonts + $additional_fonts;
	}

	add_filter( 'elementor/fonts/additional_fonts', 'woodmart_add_custom_fonts_to_theme_group' );
}
