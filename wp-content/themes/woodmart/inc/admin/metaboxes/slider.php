<?php
/**
 * Slider metaboxes
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Metaboxes;

if ( ! function_exists( 'woodmart_register_slider_metaboxes' ) ) {
	/**
	 * Register slider metaboxes
	 *
	 * @since 1.0.0
	 */
	function woodmart_register_slider_metaboxes() {
		$slide_metabox = Metaboxes::add_metabox(
			array(
				'id'         => 'xts_slide_metaboxes',
				'title'      => esc_html__( 'Slide Settings', 'woodmart' ),
				'post_types' => array( 'woodmart_slide' ),
			)
		);

		$slide_metabox->add_section(
			array(
				'id'       => 'slide_content',
				'name'     => esc_html__( 'Slide content', 'woodmart' ),
				'priority' => 10,
				'icon'     => WOODMART_ASSETS . '/assets/images/dashboard-icons/settings.svg',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'slide_image_settings_tab',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'options'  => array(
					'desktop' => array(
						'name'  => esc_html__( 'Desktop', 'woodmart' ),
						'value' => 'desktop',
					),
					'tablet'  => array(
						'name'  => esc_html__( 'Tablet', 'woodmart' ),
						'value' => 'tablet',
					),
					'mobile'  => array(
						'name'  => esc_html__( 'Mobile', 'woodmart' ),
						'value' => 'mobile',
					),
				),
				'default'  => 'desktop',
				'tabs'     => 'default',
				'type'     => 'buttons',
				'section'  => 'slide_content',
				'priority' => 9,
			)
		);

		// Desktop.
		$slide_metabox->add_field(
			array(
				'id'        => 'bg_image_desktop',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'      => esc_html__( 'Background image', 'woodmart' ),
				'type'      => 'upload',
				'section'   => 'slide_content',
				'data_type' => 'url',
				'requires'  => array(
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'desktop',
					),
				),
				'priority'  => 20,
				'class'     => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'           => 'bg_image_size_desktop',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'         => esc_html__( 'Background size', 'woodmart' ),
				'type'         => 'select',
				'empty_option' => true,
				'select2'      => true,
				'section'      => 'slide_content',
				'options'      => array(
					'cover'   => array(
						'name'  => esc_html__( 'Cover', 'woodmart' ),
						'value' => 'cover',
					),
					'contain' => array(
						'name'  => esc_html__( 'Contain', 'woodmart' ),
						'value' => 'contain',
					),
					'inherit' => array(
						'name'  => esc_html__( 'Inherit', 'woodmart' ),
						'value' => 'inherit',
					),
				),
				'default'      => 'cover',
				'requires'     => array(
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'desktop',
					),
				),
				'priority'     => 30,
				'class'        => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'           => 'bg_image_position_desktop',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'         => esc_html__( 'Background position', 'woodmart' ),
				'type'         => 'select',
				'empty_option' => true,
				'select2'      => true,
				'section'      => 'slide_content',
				'options'      => array(
					'left-top'      => array(
						'name'  => esc_html__( 'Left Top', 'woodmart' ),
						'value' => 'left top',
					),
					'left-center'   => array(
						'name'  => esc_html__( 'Left Center', 'woodmart' ),
						'value' => 'left center',
					),
					'left-bottom'   => array(
						'name'  => esc_html__( 'Left Bottom', 'woodmart' ),
						'value' => 'left bottom',
					),
					'center-top'    => array(
						'name'  => esc_html__( 'Center Top', 'woodmart' ),
						'value' => 'center top',
					),
					'center-center' => array(
						'name'  => esc_html__( 'Center Center', 'woodmart' ),
						'value' => 'center center',
					),
					'center-bottom' => array(
						'name'  => esc_html__( 'Center Bottom', 'woodmart' ),
						'value' => 'center bottom',
					),
					'right-top'     => array(
						'name'  => esc_html__( 'Right Top', 'woodmart' ),
						'value' => 'right top',
					),
					'right-center'  => array(
						'name'  => esc_html__( 'Right Center', 'woodmart' ),
						'value' => 'right center',
					),
					'right-bottom'  => array(
						'name'  => esc_html__( 'Right Bottom', 'woodmart' ),
						'value' => 'right bottom',
					),
					'custom'        => array(
						'name'  => esc_html__( 'Custom', 'woodmart' ),
						'value' => 'custom',
					),
				),
				'default'      => 'center center',
				'requires'     => array(
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'desktop',
					),
				),
				'priority'     => 40,
				'class'        => 'xts-last-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'bg_image_position_x_desktop',
				'type'     => 'text_input',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'     => esc_html__( 'Position by X (px)', 'woodmart' ),
				'section'  => 'slide_content',
				'requires' => array(
					array(
						'key'     => 'bg_image_position_desktop',
						'compare' => 'equals',
						'value'   => 'custom',
					),
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'desktop',
					),
				),
				'priority' => 50,
				'class'    => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'bg_image_position_y_desktop',
				'type'     => 'text_input',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'     => esc_html__( 'Position by Y (px)', 'woodmart' ),
				'section'  => 'slide_content',
				'requires' => array(
					array(
						'key'     => 'bg_image_position_desktop',
						'compare' => 'equals',
						'value'   => 'custom',
					),
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'desktop',
					),
				),
				'priority' => 60,
				'class'    => 'xts-last-tab-field',
			)
		);

		// Tablet.
		$slide_metabox->add_field(
			array(
				'id'        => 'bg_image_tablet',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'      => esc_html__( 'Background image', 'woodmart' ),
				'type'      => 'upload',
				'section'   => 'slide_content',
				'data_type' => 'url',
				'requires'  => array(
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'tablet',
					),
				),
				'priority'  => 80,
				'class'     => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'           => 'bg_image_size_tablet',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'         => esc_html__( 'Background size', 'woodmart' ),
				'type'         => 'select',
				'empty_option' => true,
				'select2'      => true,
				'section'      => 'slide_content',
				'options'      => array(
					'cover'   => array(
						'name'  => esc_html__( 'Cover', 'woodmart' ),
						'value' => 'cover',
					),
					'contain' => array(
						'name'  => esc_html__( 'Contain', 'woodmart' ),
						'value' => 'contain',
					),
					'inherit' => array(
						'name'  => esc_html__( 'Inherit', 'woodmart' ),
						'value' => 'inherit',
					),
				),
				'requires'     => array(
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'tablet',
					),
				),
				'priority'     => 90,
				'class'        => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'           => 'bg_image_position_tablet',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'         => esc_html__( 'Background position', 'woodmart' ),
				'type'         => 'select',
				'empty_option' => true,
				'select2'      => true,
				'section'      => 'slide_content',
				'options'      => array(
					'left-top'      => array(
						'name'  => esc_html__( 'Left Top', 'woodmart' ),
						'value' => 'left top',
					),
					'left-center'   => array(
						'name'  => esc_html__( 'Left Center', 'woodmart' ),
						'value' => 'left center',
					),
					'left-bottom'   => array(
						'name'  => esc_html__( 'Left Bottom', 'woodmart' ),
						'value' => 'left bottom',
					),
					'center-top'    => array(
						'name'  => esc_html__( 'Center Top', 'woodmart' ),
						'value' => 'center top',
					),
					'center-center' => array(
						'name'  => esc_html__( 'Center Center', 'woodmart' ),
						'value' => 'center center',
					),
					'center-bottom' => array(
						'name'  => esc_html__( 'Center Bottom', 'woodmart' ),
						'value' => 'center bottom',
					),
					'right-top'     => array(
						'name'  => esc_html__( 'Right Top', 'woodmart' ),
						'value' => 'right top',
					),
					'right-center'  => array(
						'name'  => esc_html__( 'Right Center', 'woodmart' ),
						'value' => 'right center',
					),
					'right-bottom'  => array(
						'name'  => esc_html__( 'Right Bottom', 'woodmart' ),
						'value' => 'right bottom',
					),
					'custom'        => array(
						'name'  => esc_html__( 'Custom', 'woodmart' ),
						'value' => 'custom',
					),
				),
				'requires'     => array(
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'tablet',
					),
				),
				'priority'     => 100,
				'class'        => 'xts-last-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'bg_image_position_x_tablet',
				'type'     => 'text_input',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'     => esc_html__( 'Position by X (px)', 'woodmart' ),
				'section'  => 'slide_content',
				'requires' => array(
					array(
						'key'     => 'bg_image_position_tablet',
						'compare' => 'equals',
						'value'   => 'custom',
					),
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'tablet',
					),
				),
				'priority' => 110,
				'class'    => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'bg_image_position_y_tablet',
				'type'     => 'text_input',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'     => esc_html__( 'Position by Y (px)', 'woodmart' ),
				'section'  => 'slide_content',
				'requires' => array(
					array(
						'key'     => 'bg_image_position_tablet',
						'compare' => 'equals',
						'value'   => 'custom',
					),
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'tablet',
					),
				),
				'priority' => 120,
				'class'    => 'xts-last-tab-field',
			)
		);

		// Mobile.
		$slide_metabox->add_field(
			array(
				'id'        => 'bg_image_mobile',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'      => esc_html__( 'Background image', 'woodmart' ),
				'type'      => 'upload',
				'section'   => 'slide_content',
				'data_type' => 'url',
				'requires'  => array(
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'mobile',
					),
				),
				'priority'  => 140,
				'class'     => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'           => 'bg_image_size_mobile',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'         => esc_html__( 'Background size', 'woodmart' ),
				'type'         => 'select',
				'empty_option' => true,
				'select2'      => true,
				'section'      => 'slide_content',
				'options'      => array(
					'cover'   => array(
						'name'  => esc_html__( 'Cover', 'woodmart' ),
						'value' => 'cover',
					),
					'contain' => array(
						'name'  => esc_html__( 'Contain', 'woodmart' ),
						'value' => 'contain',
					),
					'inherit' => array(
						'name'  => esc_html__( 'Inherit', 'woodmart' ),
						'value' => 'inherit',
					),
				),
				'requires'     => array(
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'mobile',
					),
				),
				'priority'     => 150,
				'class'        => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'           => 'bg_image_position_mobile',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'         => esc_html__( 'Background position', 'woodmart' ),
				'type'         => 'select',
				'empty_option' => true,
				'select2'      => true,
				'section'      => 'slide_content',
				'options'      => array(
					'left-top'      => array(
						'name'  => esc_html__( 'Left Top', 'woodmart' ),
						'value' => 'left top',
					),
					'left-center'   => array(
						'name'  => esc_html__( 'Left Center', 'woodmart' ),
						'value' => 'left center',
					),
					'left-bottom'   => array(
						'name'  => esc_html__( 'Left Bottom', 'woodmart' ),
						'value' => 'left bottom',
					),
					'center-top'    => array(
						'name'  => esc_html__( 'Center Top', 'woodmart' ),
						'value' => 'center top',
					),
					'center-center' => array(
						'name'  => esc_html__( 'Center Center', 'woodmart' ),
						'value' => 'center center',
					),
					'center-bottom' => array(
						'name'  => esc_html__( 'Center Bottom', 'woodmart' ),
						'value' => 'center bottom',
					),
					'right-top'     => array(
						'name'  => esc_html__( 'Right Top', 'woodmart' ),
						'value' => 'right top',
					),
					'right-center'  => array(
						'name'  => esc_html__( 'Right Center', 'woodmart' ),
						'value' => 'right center',
					),
					'right-bottom'  => array(
						'name'  => esc_html__( 'Right Bottom', 'woodmart' ),
						'value' => 'right bottom',
					),
					'custom'        => array(
						'name'  => esc_html__( 'Custom', 'woodmart' ),
						'value' => 'custom',
					),
				),
				'requires'     => array(
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'mobile',
					),
				),
				'priority'     => 160,
				'class'        => 'xts-last-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'bg_image_position_x_mobile',
				'type'     => 'text_input',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'     => esc_html__( 'Position by X (px)', 'woodmart' ),
				'section'  => 'slide_content',
				'requires' => array(
					array(
						'key'     => 'bg_image_position_mobile',
						'compare' => 'equals',
						'value'   => 'custom',
					),
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'mobile',
					),
				),
				'priority' => 170,
				'class'    => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'bg_image_position_y_mobile',
				'type'     => 'text_input',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'     => esc_html__( 'Position by Y (px)', 'woodmart' ),
				'section'  => 'slide_content',
				'requires' => array(
					array(
						'key'     => 'bg_image_position_mobile',
						'compare' => 'equals',
						'value'   => 'custom',
					),
					array(
						'key'     => 'slide_image_settings_tab',
						'compare' => 'equals',
						'value'   => 'mobile',
					),
				),
				'priority' => 180,
				'class'    => 'xts-last-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'        => 'bg_color',
				'group'    => esc_html__( 'Images settings', 'woodmart' ),
				'name'      => esc_html__( 'Background color', 'woodmart' ),
				'type'      => 'color',
				'section'   => 'slide_content',
				'default'   => '#fefefe',
				'data_type' => 'hex',
				'priority'  => 181,
			)
		);

		// General.
		$slide_metabox->add_field(
			array(
				'id'       => 'content_settings_tab',
				'group'    => esc_html__( 'Slide content', 'woodmart' ),
				'options'  => array(
					'desktop' => array(
						'name'  => esc_html__( 'Desktop', 'woodmart' ),
						'value' => 'desktop',
					),
					'tablet'  => array(
						'name'  => esc_html__( 'Tablet', 'woodmart' ),
						'value' => 'tablet',
					),
					'mobile'  => array(
						'name'  => esc_html__( 'Mobile', 'woodmart' ),
						'value' => 'mobile',
					),
				),
				'default'  => 'desktop',
				'tabs'     => 'default',
				'type'     => 'buttons',
				'section'  => 'slide_content',
				'priority' => 190,
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'vertical_align',
				'name'     => esc_html__( 'Vertical content align', 'woodmart' ),
				'group'    => esc_html__( 'Slide content', 'woodmart' ),
				'type'     => 'buttons',
				'default'  => 'middle',
				'section'  => 'slide_content',
				'options'  => array(
					'top'    => array(
						'name'  => esc_html__( 'Top', 'woodmart' ),
						'value' => 'top',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/top.jpg',
					),
					'middle' => array(
						'name'  => esc_html__( 'Middle', 'woodmart' ),
						'value' => 'middle',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/middle.jpg',
					),
					'bottom' => array(
						'name'  => esc_html__( 'Bottom', 'woodmart' ),
						'value' => 'bottom',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/bottom.jpg',
					),
				),
				'requires' => array(
					array(
						'key'     => 'content_settings_tab',
						'compare' => 'equals',
						'value'   => 'desktop',
					),
				),
				'priority' => 191,
				'class'     => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'horizontal_align',
				'name'     => esc_html__( 'Horizontal content align', 'woodmart' ),
				'group'    => esc_html__( 'Slide content', 'woodmart' ),
				'type'     => 'buttons',
				'section'  => 'slide_content',
				'options'  => array(
					'left'   => array(
						'name'  => esc_html__( 'Left', 'woodmart' ),
						'value' => 'left',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/left.jpg',
					),
					'center' => array(
						'name'  => esc_html__( 'Center', 'woodmart' ),
						'value' => 'center',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/center.jpg',
					),
					'right'  => array(
						'name'  => esc_html__( 'Right', 'woodmart' ),
						'value' => 'right',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/right.jpg',
					),
				),
				'requires' => array(
					array(
						'key'     => 'content_settings_tab',
						'compare' => 'equals',
						'value'   => 'desktop',
					),
				),
				'default'  => 'left',
				'priority' => 192,
				'class'     => 'xts-last-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'vertical_align_tablet',
				'name'     => esc_html__( 'Vertical content align', 'woodmart' ),
				'group'    => esc_html__( 'Slide content', 'woodmart' ),
				'type'     => 'buttons',
				'section'  => 'slide_content',
				'options'  => array(
					'top'    => array(
						'name'  => esc_html__( 'Top', 'woodmart' ),
						'value' => 'top',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/top.jpg',
					),
					'middle' => array(
						'name'  => esc_html__( 'Middle', 'woodmart' ),
						'value' => 'middle',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/middle.jpg',
					),
					'bottom' => array(
						'name'  => esc_html__( 'Bottom', 'woodmart' ),
						'value' => 'bottom',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/bottom.jpg',
					),
				),
				'requires' => array(
					array(
						'key'     => 'content_settings_tab',
						'compare' => 'equals',
						'value'   => 'tablet',
					),
				),
				'priority' => 193,
				'class'     => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'horizontal_align_tablet',
				'name'     => esc_html__( 'Horizontal content align', 'woodmart' ),
				'group'    => esc_html__( 'Slide content', 'woodmart' ),
				'type'     => 'buttons',
				'section'  => 'slide_content',
				'options'  => array(
					'left'   => array(
						'name'  => esc_html__( 'Left', 'woodmart' ),
						'value' => 'left',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/left.jpg',
					),
					'center' => array(
						'name'  => esc_html__( 'Center', 'woodmart' ),
						'value' => 'center',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/center.jpg',
					),
					'right'  => array(
						'name'  => esc_html__( 'Right', 'woodmart' ),
						'value' => 'right',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/right.jpg',
					),
				),
				'requires' => array(
					array(
						'key'     => 'content_settings_tab',
						'compare' => 'equals',
						'value'   => 'tablet',
					),
				),
				'priority' => 194,
				'class'     => 'xts-last-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'vertical_align_mobile',
				'name'     => esc_html__( 'Vertical content align', 'woodmart' ),
				'group'    => esc_html__( 'Slide content', 'woodmart' ),
				'type'     => 'buttons',
				'section'  => 'slide_content',
				'options'  => array(
					'top'    => array(
						'name'  => esc_html__( 'Top', 'woodmart' ),
						'value' => 'top',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/top.jpg',
					),
					'middle' => array(
						'name'  => esc_html__( 'Middle', 'woodmart' ),
						'value' => 'middle',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/middle.jpg',
					),
					'bottom' => array(
						'name'  => esc_html__( 'Bottom', 'woodmart' ),
						'value' => 'bottom',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/bottom.jpg',
					),
				),
				'requires' => array(
					array(
						'key'     => 'content_settings_tab',
						'compare' => 'equals',
						'value'   => 'mobile',
					),
				),
				'priority' => 195,
				'class'     => 'xts-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'horizontal_align_mobile',
				'name'     => esc_html__( 'Horizontal content align', 'woodmart' ),
				'group'    => esc_html__( 'Slide content', 'woodmart' ),
				'type'     => 'buttons',
				'section'  => 'slide_content',
				'options'  => array(
					'left'   => array(
						'name'  => esc_html__( 'Left', 'woodmart' ),
						'value' => 'left',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/left.jpg',
					),
					'center' => array(
						'name'  => esc_html__( 'Center', 'woodmart' ),
						'value' => 'center',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/center.jpg',
					),
					'right'  => array(
						'name'  => esc_html__( 'Right', 'woodmart' ),
						'value' => 'right',
						'image' => WOODMART_ASSETS_IMAGES . '/settings/cmb2-align/right.jpg',
					),
				),
				'requires' => array(
					array(
						'key'     => 'content_settings_tab',
						'compare' => 'equals',
						'value'   => 'mobile',
					),
				),
				'priority' => 196,
				'class'     => 'xts-last-tab-field',
			)
		);

		$slide_metabox->add_field(
			array(
				'id'          => 'content_without_padding',
				'type'        => 'checkbox',
				'name'        => esc_html__( 'Content no space', 'woodmart' ),
				'group'       => esc_html__( 'Slide content', 'woodmart' ),
				'description' => esc_html__( 'The content block will not have any paddings', 'woodmart' ),
				'section'     => 'slide_content',
				'priority'    => 210,
			)
		);

		$slide_metabox->add_field(
			array(
				'id'          => 'content_full_width',
				'type'        => 'checkbox',
				'name'        => esc_html__( 'Full width content', 'woodmart' ),
				'group'       => esc_html__( 'Slide content', 'woodmart' ),
				'description' => esc_html__( 'Takes the slider\'s width', 'woodmart' ),
				'section'     => 'slide_content',
				'priority'    => 220,
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'slide_content_width_tabs',
				'name'     => esc_html__( 'Content width', 'woodmart' ),
				'group'    => esc_html__( 'Slide content', 'woodmart' ),
				'options'  => array(
					'desktop' => array(
						'name'  => esc_html__( 'Desktop', 'woodmart' ),
						'value' => 'desktop',
					),
					'tablet'  => array(
						'name'  => esc_html__( 'Tablet', 'woodmart' ),
						'value' => 'tablet',
					),
					'mobile'  => array(
						'name'  => esc_html__( 'Mobile', 'woodmart' ),
						'value' => 'mobile',
					),
				),
				'default'  => 'desktop',
				'tabs'     => 'devices',
				'type'     => 'buttons',
				'section'  => 'slide_content',
				'requires' => array(
					array(
						'key'     => 'content_full_width',
						'compare' => 'not_equals',
						'value'   => 'on',
					),
				),
				'priority' => 221,
			)
		);

		$slide_metabox->add_field(
			array(
				'id'          => 'content_width',
				'name'        => esc_html__( 'Content width', 'woodmart' ),
				'description' => esc_html__( 'Set your value in pixels.', 'woodmart' ),
				'group'       => esc_html__( 'Slide content', 'woodmart' ),
				'type'        => 'range',
				'min'         => '100',
				'max'         => '1200',
				'step'        => '5',
				'default'     => '1200',
				'section'     => 'slide_content',
				'requires'    => array(
					array(
						'key'     => 'content_full_width',
						'compare' => 'not_equals',
						'value'   => 'on',
					),
					array(
						'key'     => 'slide_content_width_tabs',
						'compare' => 'equals',
						'value'   => 'desktop',
					),
				),
				'priority'    => 222,
			)
		);

		$slide_metabox->add_field(
			array(
				'id'          => 'content_width_tablet',
				'name'        => esc_html__( 'Content width', 'woodmart' ),
				'description' => esc_html__( 'Set your value in pixels.', 'woodmart' ),
				'group'       => esc_html__( 'Slide content', 'woodmart' ),
				'type'        => 'range',
				'min'         => '100',
				'max'         => '1200',
				'step'        => '5',
				'default'     => '1200',
				'section'     => 'slide_content',
				'requires'    => array(
					array(
						'key'     => 'content_full_width',
						'compare' => 'not_equals',
						'value'   => 'on',
					),
					array(
						'key'     => 'slide_content_width_tabs',
						'compare' => 'equals',
						'value'   => 'tablet',
					),
				),
				'priority'    => 223,
			)
		);

		$slide_metabox->add_field(
			array(
				'id'          => 'content_width_mobile',
				'name'        => esc_html__( 'Content width', 'woodmart' ),
				'description' => esc_html__( 'Set your value in pixels.', 'woodmart' ),
				'group'       => esc_html__( 'Slide content', 'woodmart' ),
				'type'        => 'range',
				'min'         => '50',
				'max'         => '800',
				'step'        => '5',
				'default'     => '500',
				'section'     => 'slide_content',
				'requires'    => array(
					array(
						'key'     => 'content_full_width',
						'compare' => 'not_equals',
						'value'   => 'on',
					),
					array(
						'key'     => 'slide_content_width_tabs',
						'compare' => 'equals',
						'value'   => 'mobile',
					),
				),
				'priority'    => 224,
			)
		);

		$slide_metabox->add_field(
			array(
				'id'          => 'slide_animation',
				'name'        => esc_html__( 'Animation', 'woodmart' ),
				'description' => esc_html__( 'Select a content appearance animation', 'woodmart' ),
				'group'       => esc_html__( 'Slide content', 'woodmart' ),
				'type'        => 'select',
				'section'     => 'slide_content',
				'options'     => array(
					'none'              => array(
						'name'  => esc_html__( 'None', 'woodmart' ),
						'value' => 'none',
					),
					'slide-from-top'    => array(
						'name'  => esc_html__( 'Slide from top', 'woodmart' ),
						'value' => 'slide-from-top',
					),
					'slide-from-bottom' => array(
						'name'  => esc_html__( 'Slide from bottom', 'woodmart' ),
						'value' => 'slide-from-bottom',
					),
					'slide-from-right'  => array(
						'name'  => esc_html__( 'Slide from right', 'woodmart' ),
						'value' => 'slide-from-right',
					),
					'slide-from-left'   => array(
						'name'  => esc_html__( 'Slide from left', 'woodmart' ),
						'value' => 'slide-from-left',
					),
					'top-flip-x'        => array(
						'name'  => esc_html__( 'Top flip X', 'woodmart' ),
						'value' => 'top-flip-x',
					),
					'bottom-flip-x'     => array(
						'name'  => esc_html__( 'Bottom flip X', 'woodmart' ),
						'value' => 'bottom-flip-x',
					),
					'right-flip-y'      => array(
						'name'  => esc_html__( 'Right flip Y', 'woodmart' ),
						'value' => 'right-flip-y',
					),
					'left-flip-y'       => array(
						'name'  => esc_html__( 'Left flip Y', 'woodmart' ),
						'value' => 'left-flip-y',
					),
					'zoom-in'           => array(
						'name'  => esc_html__( 'Zoom in', 'woodmart' ),
						'value' => 'zoom-in',
					),
				),
				'priority'    => 230,
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'link',
				'type'     => 'text_input',
				'name'     => esc_html__( 'Link', 'woodmart' ),
				'group'    => esc_html__( 'Slide link', 'woodmart' ),
				'section'  => 'slide_content',
				'class'    => 'xts-col-6',
				'priority' => 240,
			)
		);

		$slide_metabox->add_field(
			array(
				'id'       => 'link_target_blank',
				'type'     => 'checkbox',
				'name'     => esc_html__( 'Open link in new tab', 'woodmart' ),
				'group'    => esc_html__( 'Slide link', 'woodmart' ),
				'section'  => 'slide_content',
				'class'    => 'xts-col-6',
				'priority' => 250,
			)
		);
	}

	add_action( 'init', 'woodmart_register_slider_metaboxes', 100 );
}

$slider_metabox = Metaboxes::add_metabox(
	array(
		'id'         => 'xts_slider_metaboxes',
		'title'      => esc_html__( 'Slide Settings', 'woodmart' ),
		'object'     => 'term',
		'taxonomies' => array( 'woodmart_slider' ),
	)
);

$slider_metabox->add_section(
	array(
		'id'       => 'slide_content',
		'name'     => esc_html__( 'Slide content', 'woodmart' ),
		'priority' => 10,
		'icon'     => WOODMART_ASSETS . '/assets/images/dashboard-icons/settings.svg',
	)
);

$slider_metabox->add_field(
	array(
		'id'       => 'animation',
		'name'     => esc_html__( 'Slide change animation', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'slide_content',
		'default'  => 'slide',
		'options'  => array(
			'slide'      => array(
				'name'  => 'Slide',
				'value' => 'slide',
			),
			'fade'       => array(
				'name'  => 'Fade',
				'value' => 'fade',
			),
			'parallax'   => array(
				'name'  => 'Parallax',
				'value' => 'parallax',
			),
			'distortion' => array(
				'name'  => 'Distortion',
				'value' => 'distortion',
			),
		),
		'priority' => 8,
	)
);

$slider_metabox->add_field(
	array(
		'id'          => 'stretch_slider',
		'name'        => esc_html__( 'Stretch slider', 'woodmart' ),
		'description' => esc_html__( 'Make slider full width', 'woodmart' ),
		'type'        => 'checkbox',
		'section'     => 'slide_content',
		'class'       => 'xts-col-6',
		'priority'    => 10,
	)
);

$slider_metabox->add_field(
	array(
		'id'          => 'stretch_content',
		'name'        => esc_html__( 'Full with content', 'woodmart' ),
		'description' => esc_html__( 'Make content full width', 'woodmart' ),
		'type'        => 'checkbox',
		'section'     => 'slide_content',
		'requires'    => array(
			array(
				'key'     => 'stretch_slider',
				'compare' => 'equals',
				'value'   => 'on',
			),
		),
		'class'       => 'xts-col-6',
		'priority'    => 11,
	)
);

$slider_metabox->add_field(
	array(
		'id'       => 'slider_height_settings_tab',
		'name'     => esc_html__( 'Height', 'woodmart' ),
		'options'  => array(
			'desktop' => array(
				'name'  => esc_html__( 'Desktop', 'woodmart' ),
				'value' => 'desktop',
			),
			'tablet'  => array(
				'name'  => esc_html__( 'Tablet', 'woodmart' ),
				'value' => 'tablet',
			),
			'mobile'  => array(
				'name'  => esc_html__( 'Mobile', 'woodmart' ),
				'value' => 'mobile',
			),
		),
		'default'  => 'desktop',
		'tabs'     => 'devices',
		'type'     => 'buttons',
		'section'  => 'slide_content',
		'priority' => 12,
	)
);

$slider_metabox->add_field(
	array(
		'id'          => 'height',
		'name'        => esc_html__( 'Height on desktop', 'woodmart' ),
		'description' => esc_html__( 'Set your value in pixels.', 'woodmart' ),
		'type'        => 'range',
		'min'         => '100',
		'max'         => '1200',
		'step'        => '5',
		'default'     => '500',
		'section'     => 'slide_content',
		'requires'    => array(
			array(
				'key'     => 'slider_height_settings_tab',
				'compare' => 'equals',
				'value'   => 'desktop',
			),
		),
		'priority'    => 20,
	)
);

$slider_metabox->add_field(
	array(
		'id'          => 'height_tablet',
		'name'        => esc_html__( 'Height on tablet', 'woodmart' ),
		'description' => esc_html__( 'Set your value in pixels.', 'woodmart' ),
		'type'        => 'range',
		'min'         => '100',
		'max'         => '1200',
		'step'        => '5',
		'default'     => '500',
		'section'     => 'slide_content',
		'requires'    => array(
			array(
				'key'     => 'slider_height_settings_tab',
				'compare' => 'equals',
				'value'   => 'tablet',
			),
		),
		'priority'    => 30,
	)
);

$slider_metabox->add_field(
	array(
		'id'          => 'height_mobile',
		'name'        => esc_html__( 'Height on mobile', 'woodmart' ),
		'description' => esc_html__( 'Set your value in pixels.', 'woodmart' ),
		'type'        => 'range',
		'min'         => '100',
		'max'         => '1200',
		'step'        => '5',
		'default'     => '500',
		'section'     => 'slide_content',
		'requires'    => array(
			array(
				'key'     => 'slider_height_settings_tab',
				'compare' => 'equals',
				'value'   => 'mobile',
			),
		),
		'priority'    => 40,
	)
);

$slider_metabox->add_field(
	array(
		'id'       => 'arrows_style',
		'name'     => esc_html__( 'Arrows style', 'woodmart' ),
		'type'     => 'buttons',
		'default'  => '1',
		'section'  => 'slide_content',
		'options'  => array(
			'1' => array(
				'name'  => 'Style 1',
				'value' => '1',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/slider-navigation/arrow-style-1.jpg',
			),
			'2' => array(
				'name'  => 'Style 2',
				'value' => '2',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/slider-navigation/arrow-style-2.jpg',
			),
			'3' => array(
				'name'  => 'Style 3',
				'value' => '3',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/slider-navigation/arrow-style-3.jpg',
			),
			'0' => array(
				'name'  => 'Disable',
				'value' => '0',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/slider-navigation/arrow-style-disable.jpg',
			),
		),
		'priority' => 45,
	)
);

$slider_metabox->add_field(
	array(
		'id'       => 'pagination_style',
		'name'     => esc_html__( 'Pagination style', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'slide_content',
		'default'  => '1',
		'options'  => array(
			'1' => array(
				'name'  => 'Style 1',
				'value' => '1',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/slider-navigation/pagination-style-1.jpg',
			),
			'2' => array(
				'name'  => 'Style 2',
				'value' => '2',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/slider-navigation/pagination-style-2.jpg',
			),
			'0' => array(
				'name'  => 'Disable',
				'value' => '0',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/slider-navigation/pagination-style-disable.jpg',
			),
		),
		'priority' => 50,
	)
);

$slider_metabox->add_field(
	array(
		'id'       => 'pagination_color',
		'name'     => esc_html__( 'Navigation color scheme', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'slide_content',
		'default'  => '1',
		'options'  => array(
			'light' => array(
				'name'  => 'Light',
				'value' => 'light',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/slider-navigation/pagination-color-light.jpg',
			),
			'dark'  => array(
				'name'  => 'Dark',
				'value' => 'dark',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/slider-navigation/pagination-color-dark.jpg',
			),
		),
		'priority' => 50,
	)
);

$slider_metabox->add_field(
	array(
		'id'          => 'autoplay',
		'name'        => esc_html__( 'Enable autoplay', 'woodmart' ),
		'description' => 'Rotate slider images automatically.',
		'type'        => 'checkbox',
		'section'     => 'slide_content',
		'priority'    => 60,
	)
);

$slider_metabox->add_field(
	array(
		'id'       => 'autoplay_speed',
		'name'     => esc_html__( 'Autoplay speed', 'woodmart' ),
		'type'     => 'range',
		'min'      => '1000',
		'max'      => '30000',
		'step'     => '100',
		'default'  => '9000',
		'section'  => 'slide_content',
		'priority' => 61,
		'requires' => array(
			array(
				'key'     => 'autoplay',
				'compare' => 'equals',
				'value'   => 'on',
			),
		),
	)
);

$slider_metabox->add_field(
	array(
		'id'          => 'scroll_carousel_init',
		'name'        => esc_html__( 'Init carousel on scroll', 'woodmart' ),
		'description' => esc_html__( 'This option allows you to init carousel script only when visitor scroll the page to the slider. Useful for performance optimization.', 'woodmart' ),
		'type'        => 'checkbox',
		'section'     => 'slide_content',
		'priority'    => 70,
	)
);


