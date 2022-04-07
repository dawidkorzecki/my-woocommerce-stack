<?php
/**
 * Default Grid settings
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	// Helpers.
	'is_main_query'                => false,
	'is_overview'                  => false,
	'is_preview'                   => false,
	'is_dynamic'                   => false,
	'is_gutenberg'                 => false,
	'inline_style'                 => false,
	// Naming.
	'id'                           => 0,
	'name'                         => '',
	'class'                        => '',
	// Error messages.
	'no_posts_msg'                 => '',
	'no_results_msg'               => '',
	// Gutenberg.
	'align'                        => '',
	'className'                    => '',
	// Source.
	'source'                       => 'post_type',
	// Post type query.
	'posts_per_page'               => 10,
	'offset'                       => 0,
	'post_type'                    => [ 'post' ],
	'post_status'                  => [ 'publish' ],
	'author__in'                   => [],
	'post__in'                     => [],
	'post__not_in'                 => [],
	'post_mime_type'               => '',
	'attachment_ids'               => [],
	'order'                        => 'DESC',
	'orderby'                      => [],
	'tax_query'                    => [],
	'tax_query_operator'           => 'IN',
	'tax_query_relation'           => 'OR',
	'tax_query_children'           => '',
	'meta_key'                     => '',
	'meta_query'                   => [],
	// User query.
	'role'                         => [],
	'role__in'                     => [],
	'role__not_in'                 => [],
	'user__in'                     => [],
	'user__not_in'                 => [],
	'has_published_posts'          => [],
	'user_orderby'                 => [],
	// Term query.
	'taxonomy'                     => [],
	'term__in'                     => [],
	'term__not_in'                 => [],
	'hide_empty'                   => false,
	'childless'                    => false,
	// Media Formats settings.
	'post_formats'                 => [ 'gallery', 'video', 'audio' ],
	'first_media'                  => 0,
	'gallery_slideshow'            => 0,
	'product_image_hover'          => 0,
	'embedded_video_poster'        => 0,
	'video_lightbox'               => 0,
	'default_thumbnail'            => 0,
	'thumbnail_aspect'             => '',
	'thumbnail_ratio'              => [
		'x' => 4,
		'y' => 3,
	],
	'thumbnail_size'               => 'medium_large',
	'thumbnail_size_mobile'        => 'medium_large',
	// Default grid settings.
	'type'                         => 'masonry',
	'card_sizes'                   => [
		[
			'browser' => 9999,
			'columns' => 6,
			'height'  => 240,
			'gutter'  => 0,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
		[
			'browser' => 1200,
			'columns' => 5,
			'height'  => 240,
			'gutter'  => -1,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
		[
			'browser' => 992,
			'columns' => 4,
			'height'  => 220,
			'gutter'  => -1,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
		[
			'browser' => 768,
			'columns' => 3,
			'height'  => 220,
			'gutter'  => -1,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
		[
			'browser' => 576,
			'columns' => 2,
			'height'  => 200,
			'gutter'  => -1,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
		[
			'browser' => 320,
			'columns' => 1,
			'height'  => 200,
			'gutter'  => -1,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
	],
	'horizontal_order'             => 0,
	'fit_rows'                     => 0,
	'equal_rows'                   => 0,
	'equal_columns'                => 0,
	'fill_last_row'                => 0,
	'center_last_row'              => 0,
	'override_card_sizes'          => 0,
	'columns'                      => 1,
	'rows'                         => 1,
	// Default layout settings.
	'layout'                       => 'vertical',
	'full_width'                   => 0,
	'grid_layout'                  => [],
	// Default slider settings.
	'draggable'                    => 1,
	'initial_index'                => 0,
	'contain'                      => 0,
	'slide_align'                  => 'left',
	'group_cells'                  => 1,
	'rows_number'                  => 1,
	'auto_play'                    => 0,
	'free_scroll'                  => 0,
	'free_friction'                => '0.075',
	'friction'                     => '0.285',
	'attraction'                   => '0.025',
	'prev_next_buttons_size'       => '48px',
	'prev_next_buttons_color'      => '#333333',
	'prev_next_buttons_background' => '',
	'page_dots_color'              => '#cccccc',
	'page_dots_selected_color'     => '#333333',
	/* translators: %s: Carousel page dot selected placeholder */
	'page_dot_aria_label'          => sprintf( esc_html__( 'Page dot %s', 'wp-grid-builder' ), '%d' ),
	// Default card settings.
	'cards'                        => [],
	'content_background'           => '',
	'content_color_scheme'         => 'dark',
	'overlay_background'           => '',
	'overlay_color_scheme'         => 'light',
	// Default animations settings.
	'reveal'                       => 0,
	'animation'                    => '',
	'timing_function'              => 'ease',
	'cubic_bezier_function'        => 'cubic-bezier(0.1,0.3,0.2,1)',
	'transition'                   => 700,
	'transition_delay'             => 100,
	// Lazy load.
	'lazy_load'                    => 0,
	'lazy_load_spinner'            => 0,
	'lazy_load_blurred_image'      => 0,
	'lazy_load_background'         => '#e0e4e9',
	'lazy_load_spinner_color'      => '#0069ff',
	// Default load settings.
	'loader'                       => 0,
	'loader_type'                  => 'wpgb-loader-1',
	'loader_color'                 => '#0069ff',
	'loader_size'                  => 1,
	// Default customization settings.
	'custom_css'                   => '',
	'custom_js'                    => '',
	// Dynamic settings.
	'js_options'                   => [
		'id'                  => '',
		'type'                => '',
		'source'              => '',
		'loader'              => '',
		'layout'              => '',
		'reveal'              => '',
		'is_main_query'       => '',
		'lazy_load'           => '',
		'card_sizes'          => '',
		'fit_rows'            => '',
		'equal_rows'          => '',
		'equal_columns'       => '',
		'fill_last_row'       => '',
		'center_last_row'     => '',
		'horizontal_order'    => '',
		'full_width'          => '',
		'transition_delay'    => '',
		'gallery_slideshow'   => '',
		'rows_number'         => '',
		'slide_align'         => '',
		'group_cells'         => '',
		'draggable'           => '',
		'initial_index'       => '',
		'contain'             => '',
		'free_scroll'         => '',
		'free_friction'       => '',
		'friction'            => '',
		'attraction'          => '',
		'auto_play'           => '',
		'page_dot_aria_label' => '',
		'is_preview'          => '',
		'is_gutenberg'        => '',
	],
	// Dynamic variables.
	'no_found_rows'                => false,
	'permalink'                    => '',
	'main_query'                   => [],
	'error'                        => '',
	'facets'                       => [],
	'lang'                         => '',
];
