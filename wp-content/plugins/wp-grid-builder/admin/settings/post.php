<?php
/**
 * Post settings
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_types = get_post_types( [ 'public' => true ] );
unset( $post_types['attachment'] );

$post_settings = [
	'id'         => 'post',
	'title'      => WPGB_NAME,
	'post_types' => $post_types,
	'tabs'       => [
		[
			'id'    => 'media',
			'label' => __( 'Media Format', 'wp-grid-builder' ),
			'icon'  => Helpers::get_icon( 'pencil', true ),
		],
		[
			'id'    => 'style',
			'label' => __( 'Card Style', 'wp-grid-builder' ),
			'icon'  => Helpers::get_icon( 'card', true ),
		],
	],
	'fields'     => [
		[
			'id'       => 'custom_link_section',
			'tab'      => 'media',
			'type'     => 'section',
			'title'    => __( 'Custom Link', 'wp-grid-builder' ),
			'subtitle' => __( 'Used as alternative link to the permalink.', 'wp-grid-builder' ),
			'fields'   => [
				// permalink.
				[
					'id'    => 'permalink',
					'type'  => 'url',
					'width' => 320,
				],
			],
		],
		[
			'id'       => 'custom_format_section',
			'tab'      => 'media',
			'type'     => 'section',
			'title'    => __( 'Custom Format', 'wp-grid-builder' ),
			'subtitle' => __( 'Set up a custom media format.', 'wp-grid-builder' ),
			'fields'   => [
				// post_format.
				[
					'id'      => 'post_format',
					'type'    => 'radio',
					'options' => [
						''        => __( 'Default', 'wp-grid-builder' ),
						'gallery' => __( 'Gallery', 'wp-grid-builder' ),
						'audio'   => __( 'Audio', 'wp-grid-builder' ),
						'video'   => __( 'Video', 'wp-grid-builder' ),
					],
				],
			],
		],
		[
			'id'                => 'custom_image_section',
			'tab'               => 'media',
			'type'              => 'section',
			'title'             => __( 'Custom Image', 'wp-grid-builder' ),
			'subtitle'          => __( 'Alternative image to the featured image.', 'wp-grid-builder' ),
			'fields'            => [
				// attachment_id.
				[
					'id'   => 'attachment_id',
					'type' => 'image',
				],
			],
			'conditional_logic' => [
				[
					'field'   => 'post_format',
					'compare' => '!==',
					'value'   => 'gallery',
				],
			],
		],
		[
			'id'                => 'gallery_section',
			'tab'               => 'media',
			'type'              => 'section',
			'title'             => __( 'Gallery', 'wp-grid-builder' ),
			'fields'            => [
				// gallery_ids.
				[
					'id'   => 'gallery_ids',
					'type' => 'gallery',
				],
			],
			'conditional_logic' => [
				[
					'field'   => 'post_format',
					'compare' => '===',
					'value'   => 'gallery',
				],
			],
		],
		[
			'id'                => 'audio_section',
			'tab'               => 'media',
			'type'              => 'section',
			'title'             => __( 'Audio', 'wp-grid-builder' ),
			'fields'            => [
				// mp3_url.
				[
					'id'        => 'mp3_url',
					'type'      => 'file',
					'label'     => __( 'MP3 file', 'wp-grid-builder' ),
					'mime_type' => 'audio/mpeg, audio/mp3',
					'width'     => 340,
				],
				// ogg_url.
				[
					'id'        => 'ogg_url',
					'type'      => 'file',
					'label'     => __( 'OGG file', 'wp-grid-builder' ),
					'mime_type' => 'audio/ogg',
					'width'     => 340,
				],
			],
			'conditional_logic' => [
				[
					'field'   => 'post_format',
					'compare' => '===',
					'value'   => 'audio',
				],
			],
		],
		[
			'id'                => 'video_section',
			'tab'               => 'media',
			'type'              => 'section',
			'title'             => __( 'Video', 'wp-grid-builder' ),
			'fields'            => [
				// mp4_url.
				[
					'id'        => 'mp4_url',
					'type'      => 'file',
					'label'     => __( 'MP4 file', 'wp-grid-builder' ),
					'mime_type' => 'video/mp4',
					'width'     => 340,
				],
				// ogv_url.
				[
					'id'        => 'ogv_url',
					'type'      => 'file',
					'label'     => __( 'OGV file', 'wp-grid-builder' ),
					'mime_type' => 'video/ogg',
					'width'     => 340,
				],
				// webm_url.
				[
					'id'        => 'webm_url',
					'type'      => 'file',
					'label'     => __( 'WEBM file', 'wp-grid-builder' ),
					'mime_type' => 'video/webm',
					'width'     => 340,
				],
				// embed_url.
				[
					'id'          => 'embed_video_url',
					'type'        => 'text',
					'label'       => __( 'Embedded URL', 'wp-grid-builder' ),
					'description' => __( 'Works with Youtube, Vimeo, and Wistia embedded URL.', 'wp-grid-builder' ),
					'width'       => 252,
				],
				// video_ratio.
				[
					'id'      => 'video_ratio',
					'type'    => 'radio',
					'label'   => __( 'Aspect Ratio', 'wp-grid-builder' ),
					'options' => [
						''      => __( 'None', 'wp-grid-builder' ),
						'4:3'   => __( '4:3', 'wp-grid-builder' ),
						'16:9'  => __( '16:9', 'wp-grid-builder' ),
						'16:10' => __( '16:10', 'wp-grid-builder' ),
					],
				],
			],
			'conditional_logic' => [
				[
					'field'   => 'post_format',
					'compare' => '===',
					'value'   => 'video',
				],
			],
		],
		[
			'id'     => 'card_size_section',
			'tab'    => 'style',
			'type'   => 'section',
			'title'  => __( 'Card Size', 'wp-grid-builder' ),
			'fields' => [
				// columns.
				[
					'id'    => 'columns',
					'type'  => 'number',
					'label' => __( 'Columns Number', 'wp-grid-builder' ),
					'min'   => 1,
					'max'   => 12,
					'step'  => 1,
					'width' => 60,
				],
				// rows.
				[
					'id'    => 'rows',
					'type'  => 'number',
					'label' => __( 'Rows Number', 'wp-grid-builder' ),
					'min'   => 1,
					'max'   => 12,
					'step'  => 1,
					'width' => 60,
				],
			],
		],
		[
			'id'     => 'card_colors_section',
			'tab'    => 'style',
			'type'   => 'section',
			'title'  => _x( 'Card Colors', 'Post options colors of the card', 'wp-grid-builder' ),
			'fields' => [
				// content_background.
				[
					'id'       => 'content_background',
					'type'     => 'color',
					'label'    => __( 'Content Background', 'wp-grid-builder' ),
					'alpha'    => true,
					'gradient' => true,
				],
				// overlay_background.
				[
					'id'       => 'overlay_background',
					'type'     => 'color',
					'label'    => __( 'Overlay Background', 'wp-grid-builder' ),
					'alpha'    => true,
					'gradient' => true,
				],
				// content_color_scheme.
				[
					'id'      => 'content_color_scheme',
					'type'    => 'radio',
					'label'   => __( 'Content Color Scheme', 'wp-grid-builder' ),
					'options' => [
						''      => __( 'None', 'wp-grid-builder' ),
						'light' => __( 'Light', 'wp-grid-builder' ),
						'dark'  => __( 'Dark', 'wp-grid-builder' ),
					],
				],
				// overlay_color_scheme.
				[
					'id'      => 'overlay_color_scheme',
					'type'    => 'radio',
					'label'   => __( 'Overlay Color Scheme', 'wp-grid-builder' ),
					'options' => [
						''      => __( 'None', 'wp-grid-builder' ),
						'light' => __( 'Light', 'wp-grid-builder' ),
						'dark'  => __( 'Dark', 'wp-grid-builder' ),
					],
				],
			],
		],
	],
];

$defaults = require WPGB_PATH . 'admin/settings/defaults/post.php';

wp_grid_builder()->settings->register( $post_settings, $defaults );
