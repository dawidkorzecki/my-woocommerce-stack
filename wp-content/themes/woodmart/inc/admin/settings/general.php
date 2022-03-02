<?php

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}
use XTS\Options;

Options::add_field(
	array(
		'id'           => 'default_header',
		'name'         => esc_html__( 'Header', 'woodmart' ),
		'description'  => esc_html__( 'Set your default header for all pages from the list of all headers created with our Header Builder.', 'woodmart' ),
		'type'         => 'select',
		'section'      => 'general_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => woodmart_get_headers_array(),
		'priority'     => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'page_comments',
		'name'     => esc_html__( 'Show comments on pages', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'general_section',
		'default'  => '1',
		'on-text'  => esc_html__( 'Yes', 'woodmart' ),
		'off-text' => esc_html__( 'No', 'woodmart' ),
		'priority' => 20,
	)
);

Options::add_field(
	array(
		'id'           => 'custom_404_page',
		'name'         => esc_html__( 'Custom 404 page', 'woodmart' ),
		'type'         => 'select',
		'description'  => esc_html__( 'Select a page that will be shown as your default 404 error page.', 'woodmart' ),
		'section'      => 'general_section',
		'options'      => woodmart_get_pages( true ),
		'empty_option' => true,
		'select2'      => true,
		'priority'     => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'widget_title_tag',
		'name'        => esc_html__( 'Widget title tag', 'woodmart' ),
		'description' => esc_html__( 'Choose which HTML tag to use in widget title.', 'woodmart' ),
		'type'        => 'select',
		'section'     => 'general_section',
		'default'     => 'h5',
		'options'     => array(
			'h1'   => array(
				'name'  => 'h1',
				'value' => 'h1',
			),
			'h2'   => array(
				'name'  => 'h2',
				'value' => 'h2',
			),
			'h3'   => array(
				'name'  => 'h3',
				'value' => 'h3',
			),
			'h4'   => array(
				'name'  => 'h4',
				'value' => 'h4',
			),
			'h5'   => array(
				'name'  => 'h5',
				'value' => 'h5',
			),
			'h6'   => array(
				'name'  => 'h6',
				'value' => 'h6',
			),
			'p'    => array(
				'name'  => 'p',
				'value' => 'p',
			),
			'div'  => array(
				'name'  => 'div',
				'value' => 'div',
			),
			'span' => array(
				'name'  => 'span',
				'value' => 'span',
			),
		),
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'enqueue_posts_results',
		'name'        => esc_html__( 'Display results from blog', 'woodmart' ),
		'description' => esc_html__( 'Enable this option to show search results from the blog below the product results.', 'woodmart' ),
		'group'       => esc_html__( 'Search results', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'general_section',
		'on-text'     => esc_html__( 'Yes', 'woodmart' ),
		'off-text'    => esc_html__( 'No', 'woodmart' ),
		'default'     => false,
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'       => 'search_posts_results_column',
		'name'     => esc_html__( 'Number of columns for blog results', 'woodmart' ),
		'group'    => esc_html__( 'Search results', 'woodmart' ),
		'type'     => 'range',
		'section'  => 'general_section',
		'default'  => 2,
		'min'      => 2,
		'step'     => 1,
		'max'      => 6,
		'requires' => array(
			array(
				'key'     => 'enqueue_posts_results',
				'compare' => 'equals',
				'value'   => true,
			),
		),
		'priority' => 60,
	)
);

/**
 * Mobile bottom navbar.
 */
Options::add_field(
	array(
		'id'          => 'sticky_toolbar',
		'name'        => esc_html__( 'Enable Sticky navbar', 'woodmart' ),
		'description' => esc_html__( 'Sticky navigation toolbar will be shown at the bottom on mobile devices.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'general_navbar_section',
		'on-text'     => esc_html__( 'Yes', 'woodmart' ),
		'off-text'    => esc_html__( 'No', 'woodmart' ),
		'default'     => false,
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'sticky_toolbar_label',
		'name'        => esc_html__( 'Navbar labels', 'woodmart' ),
		'description' => esc_html__( 'Show/hide labels under icons in the mobile navbar.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'general_navbar_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'sticky_toolbar_fields',
		'name'        => esc_html__( 'Select buttons', 'woodmart' ),
		'description' => esc_html__( 'Choose which buttons will be used for sticky navbar.', 'woodmart' ),
		'type'        => 'select',
		'multiple'    => true,
		'select2'     => true,
		'section'     => 'general_navbar_section',
		'options'     => woodmart_get_sticky_toolbar_fields( true ),
		'default'     => array(
			'shop',
			'sidebar',
			'wishlist',
			'cart',
			'account',
		),
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'       => 'sticky_toolbar_custom_link_tabs',
		'name'     => esc_html__( 'Custom buttons', 'woodmart' ),
		'options'  => array(
			'link_1_tab' => array(
				'name'  => esc_html__( 'Button [1]', 'woodmart' ),
				'value' => 'link_1_tab',
			),
			'link_2_tab' => array(
				'name'  => esc_html__( 'Button [2]', 'woodmart' ),
				'value' => 'link_2_tab',
			),
			'link_3_tab' => array(
				'name'  => esc_html__( 'Button [3]', 'woodmart' ),
				'value' => 'link_3_tab',
			),
			'link_4_tab' => array(
				'name'  => esc_html__( 'Button [4]', 'woodmart' ),
				'value' => 'link_4_tab',
			),
			'link_5_tab' => array(
				'name'  => esc_html__( 'Button [5]', 'woodmart' ),
				'value' => 'link_5_tab',
			),
		),
		'default'  => 'link_1_tab',
		'tabs'     => 'default',
		'type'     => 'buttons',
		'section'  => 'general_navbar_section',
		'priority' => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'link_1_url',
		'name'     => esc_html__( 'Custom button URL', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_1_tab',
			),
		),
		'priority' => 50,
		'class' => 'xts-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_1_text',
		'name'     => esc_html__( 'Custom button text', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_1_tab',
			),
		),
		'priority' => 60,
		'class' => 'xts-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_1_icon',
		'name'     => esc_html__( 'Custom button icon', 'woodmart' ),
		'type'     => 'upload',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_1_tab',
			),
		),
		'priority' => 70,
		'class' => 'xts-last-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_2_url',
		'name'     => esc_html__( 'Custom button URL', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_2_tab',
			),
		),
		'priority' => 80,
		'class' => 'xts-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_2_text',
		'name'     => esc_html__( 'Custom button text', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_2_tab',
			),
		),
		'priority' => 90,
		'class' => 'xts-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_2_icon',
		'name'     => esc_html__( 'Custom button icon', 'woodmart' ),
		'type'     => 'upload',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_2_tab',
			),
		),
		'priority' => 100,
		'class' => 'xts-last-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_3_url',
		'name'     => esc_html__( 'Custom button URL', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_3_tab',
			),
		),
		'priority' => 110,
		'class' => 'xts-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_3_text',
		'name'     => esc_html__( 'Custom button text', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_3_tab',
			),
		),
		'priority' => 120,
		'class' => 'xts-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_3_icon',
		'name'     => esc_html__( 'Custom button icon', 'woodmart' ),
		'type'     => 'upload',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_3_tab',
			),
		),
		'priority' => 130,
		'class' => 'xts-last-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_4_url',
		'name'     => esc_html__( 'Custom button URL', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_4_tab',
			),
		),
		'priority' => 140,
		'class' => 'xts-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_4_text',
		'name'     => esc_html__( 'Custom button text', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_4_tab',
			),
		),
		'priority' => 150,
		'class' => 'xts-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_4_icon',
		'name'     => esc_html__( 'Custom button icon', 'woodmart' ),
		'type'     => 'upload',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_4_tab',
			),
		),
		'priority' => 160,
		'class' => 'xts-last-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_5_url',
		'name'     => esc_html__( 'Custom button URL', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_5_tab',
			),
		),
		'priority' => 170,
		'class' => 'xts-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_5_text',
		'name'     => esc_html__( 'Custom button text', 'woodmart' ),
		'type'     => 'text_input',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_5_tab',
			),
		),
		'priority' => 180,
		'class' => 'xts-tab-field'
	)
);

Options::add_field(
	array(
		'id'       => 'link_5_icon',
		'name'     => esc_html__( 'Custom button icon', 'woodmart' ),
		'type'     => 'upload',
		'section'  => 'general_navbar_section',
		'requires' => array(
			array(
				'key'     => 'sticky_toolbar_custom_link_tabs',
				'compare' => 'equals',
				'value'   => 'link_5_tab',
			),
		),
		'priority' => 190,
		'class' => 'xts-last-tab-field'
	)
);

/**
 * Age verify.
 */
Options::add_field(
	array(
		'id'       => 'age_verify',
		'name'     => esc_html__( 'Enable age verification popup', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'age_verify_section',
		'default'  => false,
		'on-text'  => esc_html__( 'Yes', 'woodmart' ),
		'off-text' => esc_html__( 'No', 'woodmart' ),
		'priority' => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'age_verify_text',
		'name'        => esc_html__( 'Popup message', 'woodmart' ),
		'description' => esc_html__( 'Write a message warning your visitors about age restriction on your website', 'woodmart' ),
		'default'     => '<h4 class="text-center">Are you over 18?</h4>
<p class="text-center">You must be 18 years of age or older to view page. Please verify your age to enter.</p>',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'section'     => 'age_verify_section',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'age_verify_text_error',
		'name'        => esc_html__( 'Error message', 'woodmart' ),
		'description' => esc_html__( 'This message will be displayed when the visitor don\'t verify his age.', 'woodmart' ),
		'default'     => '<h4 class="text-center">Access forbidden</h4>
<p class="text-center">Your access is restricted because of your age.</p>',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'section'     => 'age_verify_section',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'       => 'age_verify_color_scheme',
		'name'     => esc_html__( 'Text color scheme', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'age_verify_section',
		'options'  => array(
			'dark'  => array(
				'name'  => esc_html__( 'Dark', 'woodmart' ),
				'value' => 'dark',
			),
			'light' => array(
				'name'  => esc_html__( 'Light', 'woodmart' ),
				'value' => 'light',
			),
		),
		'default'  => 'dark',
		'priority' => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'age_verify_background',
		'name'     => esc_html__( 'Background', 'woodmart' ),
		'type'     => 'background',
		'section'  => 'age_verify_section',
		'selector' => '.wd-age-verify',
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'       => 'age_verify_width',
		'name'     => esc_html__( 'Width', 'woodmart' ),
		'type'     => 'range',
		'section'  => 'age_verify_section',
		'default'  => 500,
		'min'      => 400,
		'step'     => 10,
		'max'      => 1000,
		'priority' => 60,
	)
);

/**
 * Promo popup.
 */
Options::add_field(
	array(
		'id'          => 'promo_popup',
		'name'        => esc_html__( 'Enable promo popup', 'woodmart' ),
		'description' => esc_html__( 'Show promo popup to users when they enter the site.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'promo_popup_section',
		'on-text'     => esc_html__( 'Yes', 'woodmart' ),
		'off-text'    => esc_html__( 'No', 'woodmart' ),
		'default'     => false,
		'priority'    => 10,
	)
);



Options::add_field(
	array(
		'id'       => 'promo_popup_content_type',
		'name'     => esc_html__( 'Promo popup content', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'promo_popup_section',
		'options'  => array(
			'text'       => array(
				'name'  => esc_html__( 'Text', 'woodmart' ),
				'value' => 'text',
			),
			'html_block' => array(
				'name'  => esc_html__( 'HTML Block', 'woodmart' ),
				'value' => 'html_block',
			),
		),
		'default'  => 'text',
		'priority' => 20,
		'class'    => "xts-html-block-switch"
	)
);

Options::add_field(
	array(
		'id'       => 'popup_text',
		'type'     => 'textarea',
		'wysiwyg'  => true,
		'section'  => 'promo_popup_section',
		'requires' => array(
			array(
				'key'     => 'promo_popup_content_type',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'priority' => 30,
	)
);

Options::add_field(
	array(
		'id'           => 'popup_html_block',
		'type'         => 'select',
		'section'      => 'promo_popup_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => '',
		'callback'     => 'woodmart_get_theme_settings_html_blocks_array',
		'requires'     => array(
			array(
				'key'     => 'promo_popup_content_type',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'priority'     => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'popup_event',
		'name'     => esc_html__( 'Show popup after', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'promo_popup_section',
		'default'  => 'time',
		'options'  => array(
			'time'   => array(
				'name'  => esc_html__( 'some time', 'woodmart' ),
				'value' => 'time',
			),
			'scroll' => array(
				'name'  => esc_html__( 'user scroll', 'woodmart' ),
				'value' => 'scroll',
			),
		),
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'           => 'promo_timeout',
		'name'         => esc_html__( 'Popup delay', 'woodmart' ),
		'description'  => esc_html__( 'Show popup after some time (in milliseconds)', 'woodmart' ),
		'type'         => 'text_input',
		'section'      => 'promo_popup_section',
		'empty_option' => true,
		'default'      => '2000',
		'priority'     => 60,
		'requires'     => array(
			array(
				'key'     => 'popup_event',
				'compare' => 'equals',
				'value'   => 'time',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'popup_scroll',
		'name'        => esc_html__( 'Show after user scroll down the page', 'woodmart' ),
		'description' => esc_html__( 'Set the number of pixels users have to scroll down before popup opens', 'woodmart' ),
		'type'        => 'range',
		'section'     => 'promo_popup_section',
		'default'     => 1000,
		'min'         => 100,
		'step'        => 50,
		'max'         => 5000,
		'priority'    => 70,
		'requires'    => array(
			array(
				'key'     => 'popup_event',
				'compare' => 'equals',
				'value'   => 'scroll',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'promo_version',
		'name'        => esc_html__( 'Popup version', 'woodmart' ),
		'description' => esc_html__( 'If you apply any changes to your popup settings or content you might want to force the popup to all visitors who already closed it again. In this case, you just need to increase the banner version.', 'woodmart' ),
		'type'        => 'text_input',
		'section'     => 'promo_popup_section',
		'default'     => 1,
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'popup_pages',
		'name'        => esc_html__( 'Show after number of pages visited', 'woodmart' ),
		'description' => esc_html__( 'You can choose how many pages the user should visit before the popup will be shown.', 'woodmart' ),
		'type'        => 'range',
		'section'     => 'promo_popup_section',
		'default'     => 0,
		'min'         => 0,
		'step'        => 1,
		'max'         => 10,
		'priority'    => 90,
	)
);


Options::add_field(
	array(
		'id'          => 'popup-background',
		'name'        => esc_html__( 'Popup background', 'woodmart' ),
		'description' => esc_html__( 'Set background image or color for promo popup', 'woodmart' ),
		'type'        => 'background',
		'default'     => array(
			'color'    => '#111111',
			'repeat'   => 'no-repeat',
			'size'     => 'contain',
			'position' => 'left center',
		),
		'section'     => 'promo_popup_section',
		'selector'    => '.wd-popup.wd-promo-popup',
		'priority'    => 100,
	)
);

Options::add_field(
	array(
		'id'          => 'popup_width',
		'name'        => esc_html__( 'Popup width', 'woodmart' ),
		'description' => esc_html__( 'Set width of the promo popup in pixels.', 'woodmart' ),
		'type'        => 'range',
		'section'     => 'promo_popup_section',
		'default'     => 800,
		'min'         => 400,
		'step'        => 10,
		'max'         => 1000,
		'priority'    => 110,
	)
);

Options::add_field(
	array(
		'id'          => 'promo_popup_hide_mobile',
		'name'        => esc_html__( 'Hide for mobile devices', 'woodmart' ),
		'description' => esc_html__( 'You can disable this option for mobile devices completely.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'promo_popup_section',
		'on-text'     => esc_html__( 'Yes', 'woodmart' ),
		'off-text'    => esc_html__( 'No', 'woodmart' ),
		'default'     => '1',
		'priority'    => 120,
	)
);

/**
 * Cookies.
 */
Options::add_field(
	array(
		'id'          => 'cookies_info',
		'name'        => esc_html__( 'Show cookies info', 'woodmart' ),
		'description' => esc_html__( 'Under EU privacy regulations, websites must make it clear to visitors what information about them is being stored. This specifically includes cookies. Turn on this option and user will see info box at the bottom of the page that your web-site is using cookies.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'cookie_section',
		'on-text'     => esc_html__( 'Yes', 'woodmart' ),
		'off-text'    => esc_html__( 'No', 'woodmart' ),
		'default'     => false,
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'cookies_text',
		'name'        => esc_html__( 'Popup text', 'woodmart' ),
		'description' => esc_html__( 'Place here some information about cookies usage that will be shown in the popup.', 'woodmart' ),
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'section'     => 'cookie_section',
		'default'     => esc_html__( 'We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies.', 'woodmart' ),
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'           => 'cookies_policy_page',
		'name'         => esc_html__( 'Page with details', 'woodmart' ),
		'description'  => esc_html__( 'Choose page that will contain detailed information about your Privacy Policy', 'woodmart' ),
		'type'         => 'select',
		'section'      => 'cookie_section',
		'options'      => woodmart_get_pages_array(),
		'empty_option' => true,
		'select2'      => true,
		'priority'     => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'cookies_version',
		'name'        => esc_html__( 'Cookies version', 'woodmart' ),
		'description' => esc_html__( 'If you change your cookie policy information you can increase their version to show the popup to all visitors again.', 'woodmart' ),
		'type'        => 'text_input',
		'section'     => 'cookie_section',
		'default'     => 1,
		'priority'    => 40,
	)
);

/**
 * Header banner.
 */
Options::add_field(
	array(
		'id'          => 'header_banner',
		'name'        => esc_html__( 'Header banner', 'woodmart' ),
		'description' => esc_html__( 'Display a thin line above the header with your custom content. Useful for promotions and global messages.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'header_banner_section',
		'default'     => false,
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'header_banner_link',
		'name'        => esc_html__( 'Banner link', 'woodmart' ),
		'description' => esc_html__( 'The link will be added to the whole banner area.', 'woodmart' ),
		'type'        => 'text_input',
		'section'     => 'header_banner_section',
		'tags'        => 'header banner text link',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'header_banner_shortcode',
		'name'        => esc_html__( 'Banner content', 'woodmart' ),
		'description' => esc_html__( 'Place here shortcodes you want to see in the banner above the header. You can use shortcodes. Ex.: [social_buttons] or place an HTML Block built with page builder there like [html_block id="258"]', 'woodmart' ),
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'section'     => 'header_banner_section',
		'tags'        => 'header banner text content',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'       => 'header_banner_height_tabs',
		'name'     => esc_html__( 'Banner height', 'woodmart' ),
		'options'  => array(
			'desktop' => array(
				'name'  => esc_html__( 'Desktop', 'woodmart' ),
				'value' => 'desktop',
			),
			'mobile'  => array(
				'name'  => esc_html__( 'Mobile', 'woodmart' ),
				'value' => 'mobile',
			),
		),
		'default'  => 'desktop',
		'tabs'     => 'devices',
		'type'     => 'buttons',
		'section'  => 'header_banner_section',
		'priority' => 31,
	)
);

Options::add_field(
	array(
		'id'          => 'header_banner_height',
		'name'        => esc_html__( 'Banner height for desktop', 'woodmart' ),
		'description' => esc_html__( 'The height for the banner area in pixels on desktop devices.', 'woodmart' ),
		'type'        => 'range',
		'section'     => 'header_banner_section',
		'default'     => 40,
		'min'         => 0,
		'step'        => 1,
		'max'         => 200,
		'requires'    => array(
			array(
				'key'     => 'header_banner_height_tabs',
				'compare' => 'equals',
				'value'   => 'desktop',
			),
		),
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'header_banner_mobile_height',
		'name'        => esc_html__( 'Banner height for mobile', 'woodmart' ),
		'description' => esc_html__( 'The height for the banner area in pixels on mobile devices.', 'woodmart' ),
		'type'        => 'range',
		'section'     => 'header_banner_section',
		'default'     => 40,
		'min'         => 0,
		'step'        => 1,
		'max'         => 200,
		'requires'    => array(
			array(
				'key'     => 'header_banner_height_tabs',
				'compare' => 'equals',
				'value'   => 'mobile',
			),
		),
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'header_banner_color',
		'name'        => esc_html__( 'Banner text color', 'woodmart' ),
		'description' => esc_html__( 'Set light or dark text color scheme depending on the banner\'s background color.', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'header_banner_section',
		'default'     => 'light',
		'options'     => array(
			'dark'  => array(
				'name'  => esc_html__( 'Dark', 'woodmart' ),
				'value' => 'dark',
			),
			'light' => array(
				'name'  => esc_html__( 'Light', 'woodmart' ),
				'value' => 'light',
			),
		),
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'       => 'header_banner_bg',
		'name'     => esc_html__( 'Banner background', 'woodmart' ),
		'type'     => 'background',
		'section'  => 'header_banner_section',
		'selector' => '.header-banner',
		'priority' => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'header_close_btn',
		'name'        => esc_html__( 'Close button', 'woodmart' ),
		'description' => esc_html__( 'Show close banner button', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'header_banner_section',
		'default'     => '1',
		'tags'        => 'header banner color background',
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'header_banner_version',
		'name'        => esc_html__( 'Banner version', 'woodmart' ),
		'description' => esc_html__( 'If you apply any changes to your banner settings or content you might want to force the banner to all visitors who already closed it again. In this case, you just need to increase the banner version.', 'woodmart' ),
		'type'        => 'text_input',
		'section'     => 'header_banner_section',
		'default'     => '1',
		'priority'    => 90,
		'requires'    => array(
			array(
				'key'     => 'header_close_btn',
				'compare' => 'equals',
				'value'   => true,
			),
		),
	)
);
