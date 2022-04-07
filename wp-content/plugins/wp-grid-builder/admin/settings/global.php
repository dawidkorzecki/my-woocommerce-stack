<?php
/**
 * Global settings
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

$manage_options = current_user_can( 'manage_options' );

$global_settings = [
	'id'     => 'global',
	'header' => [
		'toggle'  => true,
		'buttons' => [
			[
				'title'  => __( 'Reset', 'wp-grid-builder' ),
				'icon'   => 'reset',
				'color'  => 'red',
				'action' => 'reset',
			],
			[
				'title'  => __( 'Export', 'wp-grid-builder' ),
				'icon'   => 'export',
				'color'  => 'blue',
				'action' => 'export',
			],
			[
				'title'  => __( 'Save Changes', 'wp-grid-builder' ),
				'icon'   => 'save',
				'color'  => 'green',
				'action' => 'save',
			],
		],
	],
	'tabs'   => [
		[
			'id'       => 'general',
			'label'    => __( 'General', 'wp-grid-builder' ),
			'title'    => __( 'General Settings', 'wp-grid-builder' ),
			'subtitle' => __( 'Manage features and settings available in back-office.', 'wp-grid-builder' ) . '<br>' .
						__( 'Control generated plugin assets and facet\'s indexer behaviour.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'switch', true ),
		],
		[
			'id'       => 'colors',
			'label'    => __( 'Color Schemes', 'wp-grid-builder' ),
			'title'    => __( 'Color Schemes', 'wp-grid-builder' ),
			'subtitle' => __( 'Define palettes to globally manage your colors in one place.', 'wp-grid-builder' ) . '<br>' .
						__( 'You can use the following color schemes in your cards, grids and posts.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'color', true ),
		],
		[
			'id'       => 'sizes',
			'label'    => __( 'Image Sizes', 'wp-grid-builder' ),
			'title'    => __( 'Image Sizes', 'wp-grid-builder' ),
			'subtitle' => __( 'Define additional image sizes on your WordPress site.', 'wp-grid-builder' ) . '<br>' .
						__( 'By setting <code>0</code> to the width and height, no additional sizes will be generated on upload.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'image', true ),
		],
		[
			'id'       => 'lightbox',
			'label'    => __( 'Lightbox', 'wp-grid-builder' ),
			'title'    => __( 'Lightbox', 'wp-grid-builder' ),
			'subtitle' => sprintf(
				'%1$s</p>
				<ul class = "wpgb-unordered-list wpgb-plugin-list">
					<li><strong>Easy FancyBox</strong> %2$s (<a href="https://fr.wordpress.org/plugins/easy-fancybox/" rel="external noopener noreferrer" target="_blank">%4$s</a>)</li>
					<li><strong>ModuloBox Lite</strong> %2$s (<a href="https://wordpress.org/plugins/modulobox-lite/" rel="external noopener noreferrer" target="_blank">%4$s</a>)</li>
					<li><strong>ModuloBox</strong> %2$s (<a href="https://theme-one.com/modulobox/" rel="external noopener noreferrer" target="_blank">%3$s</a>)</li>
					<li><strong>FooBox V2</strong> %2$s (<a href="https://fooplugins.com/plugins/foobox/" rel="external noopener noreferrer" target="_blank">%3$s</a>)</li>
				</ul>
				<p>',
				__( 'Gridbuilder ᵂᴾ comes with a lightbox to open images and videos in a popup.', 'wp-grid-builder' ) . '<br>' .
				__( 'The plugin is also compatible with the following lightbox plugins:', 'wp-grid-builder' ),
				__( 'Plugin', 'wp-grid-builder' ),
				__( 'Purchase', 'wp-grid-builder' ),
				__( 'Download', 'wp-grid-builder' )
			),
			'icon'     => Helpers::get_icon( 'lightbox', true ),
		],
	],
	'fields' => [
		[
			'id'     => 'plugin_settings_section',
			'tab'    => 'general',
			'type'   => 'section',
			'title'  => __( 'Plugin Settings', 'wp-grid-builder' ),
			'fields' => [
				$manage_options ?
				// uninstall.
				[
					'id'      => 'uninstall',
					'type'    => 'toggle',
					'label'   => __( 'Delete Data on Uninstall', 'wp-grid-builder' ),
					'tooltip' => __( 'When enabled, all custom tables and options of the plugin will be deleted on uninstall. So, all your grids, cards and facets will be removed.', 'wp-grid-builder' ),
				] : '',
				// post_formats_support.
				[
					'id'      => 'post_formats_support',
					'type'    => 'toggle',
					'label'   => __( 'Post Formats Support', 'wp-grid-builder' ),
					'tooltip' => __( 'Add Post Formats feature to any post type. Useful, if your theme does not support Post Formats.', 'wp-grid-builder' ),
				],
				// post_meta.
				[
					'id'      => 'post_meta',
					'type'    => 'toggle',
					'label'   => __( 'Display Post Options', 'wp-grid-builder' ),
					'tooltip' => __( 'Display plugin settings (Post Formats) on edit post pages. These settings are always available in preview mode of a grid.', 'wp-grid-builder' ),
				],
				// term_meta.
				[
					'id'      => 'term_meta',
					'type'    => 'toggle',
					'label'   => __( 'Display Term Options', 'wp-grid-builder' ),
					'tooltip' => __( 'Display plugin settings (Term Colors) on taxonomy and term edit pages.', 'wp-grid-builder' ),
				],
			],
		],
		[
			'id'     => 'gutenberg_section',
			'tab'    => 'general',
			'type'   => 'section',
			'title'  => __( 'Gutenberg Editor', 'wp-grid-builder' ),
			'fields' => [
				// render_blocks.
				[
					'id'      => 'render_blocks',
					'type'    => 'toggle',
					'label'   => __( 'Render Blocks in Editor', 'wp-grid-builder' ),
					'tooltip' => __( 'This is an experimental feature. The plugin uses dynamic blocks to render grids and facets. It may slow down the loading time in Gutenberg.', 'wp-grid-builder' ),
				],
			],
		],
		[
			'id'     => 'facet_settings_section',
			'tab'    => 'general',
			'type'   => 'section',
			'title'  => __( 'Facet &#38; Indexer', 'wp-grid-builder' ),
			'fields' => [
				// filter_custom_content.
				[
					'id'      => 'filter_custom_content',
					'type'    => 'toggle',
					'label'   => __( 'Filter Custom Content', 'wp-grid-builder' ),
					'tooltip' => __( 'When enabled, facets with grid attribute "wpgb-content" will be able to filter archive results from your theme and custom WP_Query.', 'wp-grid-builder' ),
				],
				// history.
				[
					'id'      => 'history',
					'type'    => 'toggle',
					'label'   => __( 'Browser\'s History', 'wp-grid-builder' ),
					'tooltip' => __( 'Allow history navigation by pushing facet parameters in url when filtering.', 'wp-grid-builder' ),
				],
				// auto_index.
				[
					'id'      => 'auto_index',
					'type'    => 'toggle',
					'label'   => __( 'Auto Indexing', 'wp-grid-builder' ),
					'tooltip' => __( 'Automatically index facets when saving, duplicating or importing.', 'wp-grid-builder' ),
				],
				// stop_indexer.
				[
					'id'      => 'stop_indexer',
					'type'    => 'custom',
					'label'   => __( 'Stop Indexer', 'wp-grid-builder' ),
					'content' => sprintf(
						'<button type="button" class="wpgb-button wpgb-button-small wpgb-purple" data-action="stop_indexer" data-nonce="%1$s">%2$s%3$s</button>',
						wp_create_nonce( WPGB_SLUG . '_global_settings_stop_indexer' ),
						Helpers::get_icon( 'stop', false, false ),
						__( 'Stop Indexer', 'wp-grid-builder' )
					),
				],
				// clear_index.
				[
					'id'      => 'clear_index',
					'type'    => 'custom',
					'label'   => __( 'Clear Index Table', 'wp-grid-builder' ),
					'content' => sprintf(
						'<button type="button" class="wpgb-button wpgb-button-small wpgb-red" data-action="clear_index" data-nonce="%1$s">%2$s%3$s</button>
						<div class="wpgb-index-stats%4$s">%5$s</div>',
						wp_create_nonce( WPGB_SLUG . '_global_settings_clear_index' ),
						Helpers::get_icon( 'delete', false, false ),
						__( 'Clear Index', 'wp-grid-builder' ),
						wp_doing_ajax() ? '' : ' wpgb-loading',
						wp_doing_ajax() ? esc_html__( 'Please refresh the page to get index stats.', 'wp-grid-builder' ) : esc_html__( 'Loading index stats...', 'wp-grid-builder' )
					),
				],
			],
		],
		[
			'id'     => 'optimization_section',
			'tab'    => 'general',
			'type'   => 'section',
			'title'  => __( 'Assets & Cache', 'wp-grid-builder' ),
			'fields' => [
				// load_polyfills.
				[
					'id'      => 'load_polyfills',
					'type'    => 'toggle',
					'label'   => __( 'Load Polyfills', 'wp-grid-builder' ),
					'tooltip' => __( 'When enabled, it will load an additional JS script to add support for older browsers not supporting ECMAScript 2015 like Internet Explorer 11.', 'wp-grid-builder' ),
				],
				// clear_cache.
				[
					'id'      => 'clear_cache',
					'type'    => 'custom',
					'label'   => __( 'Plugin Cache', 'wp-grid-builder' ),
					'tooltip' => __( 'The plugin caches some content like facets to prevent additional queries. You can clear the cache at any time to prevent any conflict.', 'wp-grid-builder' ),
					'content' => sprintf(
						'<button type="button" class="wpgb-button wpgb-button-small wpgb-red" data-action="clear_cache" data-nonce="%1$s">%2$s%3$s</button>',
						wp_create_nonce( WPGB_SLUG . '_global_settings_clear_cache' ),
						Helpers::get_icon( 'delete', false, false ),
						__( 'Clear Cache', 'wp-grid-builder' )
					),
				],
				// delete_stylesheets.
				[
					'id'      => 'delete_stylesheets',
					'type'    => 'custom',
					'label'   => __( 'Plugin Style Sheets', 'wp-grid-builder' ),
					'tooltip' => __( 'The plugin generates style sheets (CSS) for grids and facets display in pages. Style sheets are dynamically generated on first load if missing.', 'wp-grid-builder' ),
					'content' => sprintf(
						'<button type="button" class="wpgb-button wpgb-button-small wpgb-red" data-action="delete_stylesheets" data-nonce="%1$s">%2$s%3$s</button>',
						wp_create_nonce( WPGB_SLUG . '_global_settings_delete_stylesheets' ),
						Helpers::get_icon( 'delete', false, false ),
						__( 'Delete Style Sheets', 'wp-grid-builder' )
					),
				],
				// cache_info.
				[
					'id'      => 'cache_info',
					'type'    => 'info',
					'content' =>
					__( '<strong>If you use a cache plugin</strong>, and made change to your grids or facets, you may need to clear Gridbuilder ᵂᴾ cache before caching.', 'wp-grid-builder' ) . '<br>' .
					__( '<strong>If you use an optimization plugin</strong> which concatenates style sheets (CSS) you may need to delete Gridbuilder ᵂᴾ style sheets before concatenation.', 'wp-grid-builder' ),
				],
			],
		],
		[
			'id'     => 'dark_color_schemes_section',
			'tab'    => 'colors',
			'type'   => 'section',
			'title'  => __( 'Dark color schemes', 'wp-grid-builder' ),
			'fields' => [
				// dark_scheme_1.
				[
					'id'      => 'dark_scheme_1',
					'type'    => 'color',
					'label'   => __( 'Primary Color', 'wp-grid-builder' ),
					'tooltip' => __( 'Default color used for title tags.', 'wp-grid-builder' ),
					'alpha'   => true,
				],
				// dark_scheme_2.
				[
					'id'      => 'dark_scheme_2',
					'type'    => 'color',
					'label'   => __( 'Secondary Color', 'wp-grid-builder' ),
					'tooltip' => __( 'Default color used for paragraph tags.', 'wp-grid-builder' ),
					'alpha'   => true,
				],
				// dark_scheme_3.
				[
					'id'      => 'dark_scheme_3',
					'type'    => 'color',
					'label'   => __( 'Tertiary Color', 'wp-grid-builder' ),
					'tooltip' => __( 'Default color used for all other tags.', 'wp-grid-builder' ),
					'alpha'   => true,
				],
			],
		],
		[
			'id'     => 'light_color_schemes_section',
			'tab'    => 'colors',
			'type'   => 'section',
			'title'  => __( 'Light color schemes', 'wp-grid-builder' ),
			'fields' => [
				// light_scheme_1.
				[
					'id'      => 'light_scheme_1',
					'type'    => 'color',
					'label'   => __( 'Primary Color', 'wp-grid-builder' ),
					'tooltip' => __( 'Default color used for title tags.', 'wp-grid-builder' ),
					'alpha'   => true,
				],
				// light_scheme_2.
				[
					'id'      => 'light_scheme_2',
					'type'    => 'color',
					'label'   => __( 'Secondary Color', 'wp-grid-builder' ),
					'tooltip' => __( 'Default color used for paragraph tags.', 'wp-grid-builder' ),
					'alpha'   => true,
				],
				// light_scheme_3.
				[
					'id'      => 'light_scheme_3',
					'type'    => 'color',
					'label'   => __( 'Tertiary Color', 'wp-grid-builder' ),
					'tooltip' => __( 'Default color used for all other tags.', 'wp-grid-builder' ),
					'alpha'   => true,
				],
			],
		],
		[
			'id'     => 'accent_color_section',
			'tab'    => 'colors',
			'type'   => 'section',
			'title'  => __( 'Accent Color', 'wp-grid-builder' ),
			'fields' => [
				// accent_scheme_1.
				[
					'id'    => 'accent_scheme_1',
					'type'  => 'color',
					'label' => __( 'Accent Color', 'wp-grid-builder' ),
					'alpha' => true,
				],
			],
		],
		[
			'id'     => 'image_sizes_section',
			'tab'    => 'sizes',
			'type'   => 'section',
			'fields' => [
				// image_sizes.
				[
					'id'     => 'image_sizes',
					'type'   => 'table',
					'rows'   => [
						[
							'name'  => '',
							'icon'  => '',
							'label' => __( 'Size 1', 'wp-grid-builder' ),
						],
						[
							'name'  => '',
							'icon'  => '',
							'label' => __( 'Size 2', 'wp-grid-builder' ),
						],
						[
							'name'  => '',
							'icon'  => '',
							'label' => __( 'Size 3', 'wp-grid-builder' ),
						],
						[
							'name'  => '',
							'icon'  => '',
							'label' => __( 'Size 4', 'wp-grid-builder' ),
						],
						[
							'name'  => '',
							'icon'  => '',
							'label' => __( 'Size 5', 'wp-grid-builder' ),
						],
					],
					'fields' => [
						[
							'id'    => 'width',
							'type'  => 'number',
							'label' => __( 'Width (px)', 'wp-grid-builder' ),
							'width' => 80,
						],
						[
							'id'    => 'height',
							'type'  => 'number',
							'label' => __( 'Height (px)', 'wp-grid-builder' ),
							'width' => 80,
						],
						[
							'id'    => 'crop',
							'type'  => 'toggle',
							'label' => __( 'Crop Image', 'wp-grid-builder' ),
						],
					],
				],
			],
		],
		[
			'id'     => 'images_info_section',
			'tab'    => 'sizes',
			'type'   => 'section',
			'fields' => [
				// image_sizes_info.
				[
					'id'      => 'image_sizes_info',
					'type'    => 'info',
					'content' =>
					'<strong>' . __( 'Image sizes are only generated when uploading image(s) in your WordPress media library.', 'wp-grid-builder' ) . '</strong><br>' .
					__( 'If you change or add sizes after uploading images, you must regenerate them in order to correctly generate all sizes.', 'wp-grid-builder' ) . '<br>' .
					sprintf(
						/* translators: 1: external url, 2: rel external */
						__( 'You can easily regenerate your image sizes thanks to <a href="%1$s" rel="%2$s" target="_blank">Regenerate Thumbnails Plugin</a>.', 'wp-grid-builder' ),
						'https://wordpress.org/plugins/regenerate-thumbnails/',
						'external noopener noreferrer'
					),
				],
			],
		],
		[
			'id'     => 'lightbox_plugin_section',
			'tab'    => 'lightbox',
			'type'   => 'section',
			'title'  => __( 'Lightbox Plugin', 'wp-grid-builder' ),
			'fields' => [
				// lightbox_plugin.
				[
					'id'          => 'lightbox_plugin',
					'type'        => 'select',
					'label'       => __( 'Open Media with', 'wp-grid-builder' ),
					'width'       => 380,
					'placeholder' => _x( 'None', 'Open Media With default value', 'wp-grid-builder' ),
					'options'     => [
						'wp_grid_builder' => WPGB_NAME,
						'easy_fancybox'   => 'Easy FancyBox',
						'modulobox_lite'  => 'ModuloBox Lite',
						'modulobox'       => 'ModuloBox',
						'foobox'          => 'FooBox V2',
					],
					'disabled'    => [
						'easy_fancybox'  => ! is_plugin_active( 'easy-fancybox/easy-fancybox.php' ),
						'modulobox_lite' => ! is_plugin_active( 'modulobox-lite/modulobox.php' ),
						'modulobox'      => ! is_plugin_active( 'modulobox/modulobox.php' ),
						'foobox'         => ! ( class_exists( 'FooBox' ) || class_exists( 'fooboxV2' ) ),
					],
				],
			],
		],
		[
			'id'                => 'lightbox_content_section',
			'tab'               => 'lightbox',
			'type'              => 'section',
			'title'             => __( 'Image &#38; Caption', 'wp-grid-builder' ),
			'fields'            => [
				// lightbox_image_size.
				[
					'id'      => 'lightbox_image_size',
					'type'    => 'select',
					'label'   => __( 'Image Size', 'wp-grid-builder' ),
					'tooltip' => __( 'Image size displayed inside the lightbox.', 'wp-grid-builder' ),
					'width'   => 380,
					'options' => Helpers::get_image_sizes(),
				],
				// lightbox_title.
				[
					'id'          => 'lightbox_title',
					'type'        => 'select',
					'label'       => __( 'Caption Title', 'wp-grid-builder' ),
					'width'       => 380,
					'placeholder' => _x( 'None', 'Caption Title default value', 'wp-grid-builder' ),
					'options'     => [
						'title'       => __( 'Image Title', 'wp-grid-builder' ),
						'caption'     => __( 'Image Caption', 'wp-grid-builder' ),
						'alt'         => __( 'Image Alt Text', 'wp-grid-builder' ),
						'description' => __( 'Image Description', 'wp-grid-builder' ),
					],
				],
				// lightbox_description.
				[
					'id'          => 'lightbox_description',
					'type'        => 'select',
					'label'       => __( 'Caption Description', 'wp-grid-builder' ),
					'width'       => 380,
					'placeholder' => _x( 'None', 'Caption Description default value', 'wp-grid-builder' ),
					'options'     => [
						'title'       => __( 'Image Title', 'wp-grid-builder' ),
						'caption'     => __( 'Image Caption', 'wp-grid-builder' ),
						'alt'         => __( 'Image Alt Text', 'wp-grid-builder' ),
						'description' => __( 'Image Description', 'wp-grid-builder' ),
					],
				],
			],
			'conditional_logic' => [
				[
					'field'   => 'lightbox_plugin',
					'compare' => '!==',
					'value'   => '',
				],
			],
		],
		[
			'id'                => 'lightbox_messages_section',
			'tab'               => 'lightbox',
			'type'              => 'section',
			'title'             => __( 'Messages &#38; Labels', 'wp-grid-builder' ),
			'fields'            => [
				// lightbox_counter_message.
				[
					'id'      => 'lightbox_counter_message',
					'type'    => 'text',
					'label'   => __( 'Counter Message', 'wp-grid-builder' ),
					'tooltip' => __( 'Message used in the slideshow counter. ([index] : Number of the current slide; [total] : Number of slides opened in the gallery).', 'wp-grid-builder' ),
					'width'   => 380,
				],
				// lightbox_error_message.
				[
					'id'      => 'lightbox_error_message',
					'type'    => 'text',
					'label'   => __( 'Error Message', 'wp-grid-builder' ),
					'tooltip' => __( 'Message displayed when a media fails to load.', 'wp-grid-builder' ),
					'width'   => 380,
				],
				// lightbox_previous_label.
				[
					'id'      => 'lightbox_previous_label',
					'type'    => 'text',
					'label'   => __( 'Previous Aria Label', 'wp-grid-builder' ),
					'tooltip' => __( 'Message used to provide the label to any assistive technologies.', 'wp-grid-builder' ),
					'width'   => 380,
				],
				// lightbox_next_label.
				[
					'id'      => 'lightbox_next_label',
					'type'    => 'text',
					'label'   => __( 'Next Aria Label', 'wp-grid-builder' ),
					'tooltip' => __( 'Message used to provide the label to any assistive technologies.', 'wp-grid-builder' ),
					'width'   => 380,
				],
				// lightbox_close_label.
				[
					'id'      => 'lightbox_close_label',
					'type'    => 'text',
					'label'   => __( 'Close Aria Label', 'wp-grid-builder' ),
					'tooltip' => __( 'Message used to provide the label to any assistive technologies.', 'wp-grid-builder' ),
					'width'   => 380,
				],
			],
			'conditional_logic' => [
				[
					'field'   => 'lightbox_plugin',
					'compare' => '===',
					'value'   => 'wp_grid_builder',
				],
			],
		],
		[
			'id'                => 'lightbox_colors_section',
			'tab'               => 'lightbox',
			'type'              => 'section',
			'title'             => __( 'Colors', 'wp-grid-builder' ),
			'conditional_logic' => [
				[
					'field'   => 'lightbox_plugin',
					'compare' => '===',
					'value'   => 'wp_grid_builder',
				],
			],
			'fields'            => [
				// lightbox_background.
				[
					'id'       => 'lightbox_background',
					'type'     => 'color',
					'label'    => __( 'Background Color', 'wp-grid-builder' ),
					'tooltip'  => __( 'Background color of the lightbox overlay.', 'wp-grid-builder' ),
					'gradient' => true,
				],
				// lightbox_controls_color.
				[
					'id'      => 'lightbox_controls_color',
					'type'    => 'color',
					'label'   => __( 'Button Controls Color', 'wp-grid-builder' ),
					'tooltip' => __( 'Previous, next and close boutons, and slideshow counter color.', 'wp-grid-builder' ),
				],
				// lightbox_spinner_color.
				[
					'id'    => 'lightbox_spinner_color',
					'type'  => 'color',
					'label' => __( 'Loading Spinner Color', 'wp-grid-builder' ),
				],
				// lightbox_title_color.
				[
					'id'    => 'lightbox_title_color',
					'type'  => 'color',
					'label' => __( 'Caption Title Color', 'wp-grid-builder' ),
				],
				// lightbox_desc_color.
				[
					'id'    => 'lightbox_desc_color',
					'type'  => 'color',
					'label' => __( 'Caption Description Color', 'wp-grid-builder' ),
				],
			],
		],
	],
];

$defaults = require WPGB_PATH . 'admin/settings/defaults/global.php';

wp_grid_builder()->settings->register( $global_settings, $defaults );
