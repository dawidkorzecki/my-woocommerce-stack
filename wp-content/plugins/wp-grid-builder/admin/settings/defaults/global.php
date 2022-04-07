<?php
/**
 * Default Global settings
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
	// General.
	'uninstall'                => 0,
	'post_formats_support'     => 0,
	'post_meta'                => 0,
	'term_meta'                => 1,
	'filter_custom_content'    => 0,
	'history'                  => 1,
	'auto_index'               => 1,
	'render_blocks'            => 0,
	'load_polyfills'           => 1,
	// Color schemes.
	'dark_scheme_1'            => '#444444',
	'dark_scheme_2'            => '#777777',
	'dark_scheme_3'            => '#999999',
	'light_scheme_1'           => '#ffffff',
	'light_scheme_2'           => '#f6f6f6',
	'light_scheme_3'           => '#f5f5f5',
	'accent_scheme_1'          => '#0069ff',
	// Image sizes.
	'image_sizes'              => [
		[
			'width'  => 0,
			'height' => 0,
			'crop'   => 0,
		],
		[
			'width'  => 0,
			'height' => 0,
			'crop'   => 0,
		],
		[
			'width'  => 0,
			'height' => 0,
			'crop'   => 0,
		],
		[
			'width'  => 0,
			'height' => 0,
			'crop'   => 0,
		],
		[
			'width'  => 0,
			'height' => 0,
			'crop'   => 0,
		],
	],
	// Lightbox.
	'lightbox_plugin'          => 'wp_grid_builder',
	'lightbox_image_size'      => 'full',
	'lightbox_title'           => 'title',
	'lightbox_description'     => 'caption',
	// Lightbox Settings.
	'lightbox_counter_message' => '[index] / [total]',
	'lightbox_error_message'   => __( 'Sorry, an error occured while loading the content...', 'wp-grid-builder' ),
	'lightbox_previous_label'  => __( 'Previous slide', 'wp-grid-builder' ),
	'lightbox_next_label'      => __( 'Next slide', 'wp-grid-builder' ),
	'lightbox_close_label'     => __( 'Close lightbox', 'wp-grid-builder' ),
	'lightbox_background'      => 'linear-gradient(180deg, rgba(30,30,30,0.45) 0%, rgba(30,30,30,0.9) 100%)',
	'lightbox_controls_color'  => '#ffffff',
	'lightbox_spinner_color'   => '#ffffff',
	'lightbox_title_color'     => '#ffffff',
	'lightbox_desc_color'      => '#bbbbbb',
];
