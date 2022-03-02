<?php
if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Options;

/**
 * General.
 */
Options::add_section(
	array(
		'id'       => 'general_section',
		'name'     => esc_html__( 'General', 'woodmart' ),
		'priority' => 10,
		'icon'     => 'dashicons dashicons-admin-home',
	)
);

Options::add_section(
	array(
		'id'       => 'header_banner_section',
		'parent'   => 'general_section',
		'name'     => esc_html__( 'Header banner', 'woodmart' ),
		'priority' => 10,
	)
);

Options::add_section(
	array(
		'id'       => 'promo_popup_section',
		'parent'   => 'general_section',
		'name'     => esc_html__( 'Promo popup', 'woodmart' ),
		'priority' => 20,
	)
);

Options::add_section(
	array(
		'id'       => 'age_verify_section',
		'parent'   => 'general_section',
		'name'     => esc_html__( 'Age verify popup', 'woodmart' ),
		'priority' => 30,
	)
);

Options::add_section(
	array(
		'id'       => 'cookie_section',
		'parent'   => 'general_section',
		'name'     => esc_html__( 'Cookie Law Info', 'woodmart' ),
		'priority' => 40,
	)
);

Options::add_section(
	array(
		'id'       => 'general_navbar_section',
		'parent'   => 'general_section',
		'name'     => esc_html__( 'Mobile bottom navbar', 'woodmart' ),
		'priority' => 50,
	)
);

/**
 * General layout.
 */
Options::add_section(
	array(
		'id'       => 'general_layout_section',
		'name'     => esc_html__( 'General layout', 'woodmart' ),
		'priority' => 20,
		'icon'     => 'dashicons dashicons-layout',
	)
);

/**
 * Page title.
 */
Options::add_section(
	array(
		'id'       => 'page_title_section',
		'name'     => esc_html__( 'Page title', 'woodmart' ),
		'priority' => 30,
		'icon'     => 'dashicons dashicons-schedule',
	)
);

/**
 * Footer.
 */
Options::add_section(
	array(
		'id'       => 'footer_section',
		'name'     => esc_html__( 'Footer', 'woodmart' ),
		'priority' => 40,
		'icon'     => 'dashicons dashicons-editor-kitchensink',
	)
);

/**
 * Typography.
 */
Options::add_section(
	array(
		'id'       => 'typography_section',
		'name'     => esc_html__( 'Typography', 'woodmart' ),
		'priority' => 50,
		'icon'     => 'dashicons dashicons-editor-textcolor',
	)
);

Options::add_section(
	array(
		'id'       => 'typekit_section',
		'parent'   => 'typography_section',
		'name'     => esc_html__( 'Typekit Fonts', 'woodmart' ),
		'priority' => 10,
	)
);

Options::add_section(
	array(
		'id'       => 'custom_fonts_section',
		'parent'   => 'typography_section',
		'name'     => esc_html__( 'Custom Fonts', 'woodmart' ),
		'priority' => 20,
	)
);

Options::add_section(
	array(
		'id'       => 'advanced_typography_section',
		'parent'   => 'typography_section',
		'name'     => esc_html__( 'Advanced typography', 'woodmart' ),
		'priority' => 30,
	)
);


/**
 * Styles and colors.
 */
Options::add_section(
	array(
		'id'       => 'colors_section',
		'name'     => esc_html__( 'Styles and colors', 'woodmart' ),
		'priority' => 60,
		'icon'     => 'dashicons dashicons-admin-customizer',
	)
);

Options::add_section(
	array(
		'id'       => 'pages_bg_section',
		'parent'   => 'colors_section',
		'name'     => esc_html__( 'Pages background', 'woodmart' ),
		'priority' => 10,
	)
);

Options::add_section(
	array(
		'id'       => 'buttons_section',
		'parent'   => 'colors_section',
		'name'     => esc_html__( 'Buttons', 'woodmart' ),
		'priority' => 20,
	)
);

Options::add_section(
	array(
		'id'       => 'forms_section',
		'parent'   => 'colors_section',
		'name'     => esc_html__( 'Forms style', 'woodmart' ),
		'priority' => 30,
	)
);

Options::add_section(
	array(
		'id'       => 'notices_section',
		'parent'   => 'colors_section',
		'name'     => esc_html__( 'Notices', 'woodmart' ),
		'priority' => 40,
	)
);

/**
 * Blog.
 */
Options::add_section(
	array(
		'id'       => 'blog_section',
		'name'     => esc_html__( 'Blog', 'woodmart' ),
		'priority' => 70,
		'icon'     => 'dashicons dashicons-welcome-write-blog',
	)
);

Options::add_section(
	array(
		'id'       => 'blog_archive_section',
		'name'     => esc_html__( 'Blog archive', 'woodmart' ),
		'parent'   => 'blog_section',
		'priority' => 10,
		'icon'     => 'dashicons dashicons-admin-home',
	)
);

Options::add_section(
	array(
		'id'       => 'blog_singe_post_section',
		'name'     => esc_html__( 'Single post', 'woodmart' ),
		'parent'   => 'blog_section',
		'priority' => 20,
		'icon'     => 'dashicons dashicons-admin-home',
	)
);

/**
 * Portfolio.
 */
Options::add_section(
	array(
		'id'       => 'portfolio_section',
		'name'     => esc_html__( 'Portfolio', 'woodmart' ),
		'priority' => 80,
		'icon'     => 'dashicons dashicons-grid-view',
	)
);

Options::add_section(
	array(
		'id'       => 'portfolio_archive_section',
		'name'     => esc_html__( 'Portfolio archive', 'woodmart' ),
		'parent'   => 'portfolio_section',
		'priority' => 10,
		'icon'     => 'dashicons dashicons-admin-home',
	)
);

Options::add_section(
	array(
		'id'       => 'portfolio_singe_project_section',
		'name'     => esc_html__( 'Single project', 'woodmart' ),
		'parent'   => 'portfolio_section',
		'priority' => 20,
		'icon'     => 'dashicons dashicons-admin-home',
	)
);

/**
 * Shop.
 */
Options::add_section(
	array(
		'id'       => 'shop_section',
		'name'     => esc_html__( 'Shop', 'woodmart' ),
		'priority' => 90,
		'icon'     => 'dashicons dashicons-cart',
	)
);

Options::add_section(
	array(
		'id'       => 'attribute_swatches_section',
		'parent'   => 'shop_section',
		'name'     => esc_html__( 'Attribute swatches', 'woodmart' ),
		'priority' => 10,
	)
);

Options::add_section(
	array(
		'id'       => 'product_labels_section',
		'parent'   => 'shop_section',
		'name'     => esc_html__( 'Product labels', 'woodmart' ),
		'priority' => 20,
	)
);

Options::add_section(
	array(
		'id'       => 'brands_section',
		'parent'   => 'shop_section',
		'name'     => esc_html__( 'Brands', 'woodmart' ),
		'priority' => 30,
	)
);

Options::add_section(
	array(
		'id'       => 'quick_view_section',
		'parent'   => 'shop_section',
		'name'     => esc_html__( 'Quick view', 'woodmart' ),
		'priority' => 40,
	)
);

Options::add_section(
	array(
		'id'       => 'compare_section',
		'parent'   => 'shop_section',
		'name'     => esc_html__( 'Compare', 'woodmart' ),
		'priority' => 50,
	)
);

/**
 * Wishlist (60).
 */

Options::add_section(
	array(
		'id'       => 'thank_you_page_section',
		'parent'   => 'shop_section',
		'name'     => esc_html__( 'Thank you page', 'woodmart' ),
		'priority' => 70,
	)
);

/**
 * Product archive.
 */
Options::add_section(
	array(
		'id'       => 'product_archive_section',
		'name'     => esc_html__( 'Product archive', 'woodmart' ),
		'priority' => 100,
		'icon'     => 'dashicons dashicons-store',
	)
);

Options::add_section(
	array(
		'id'       => 'products_grid_section',
		'parent'   => 'product_archive_section',
		'name'     => esc_html__( 'Products grid', 'woodmart' ),
		'priority' => 10,
	)
);

Options::add_section(
	array(
		'id'       => 'products_styles_section',
		'parent'   => 'product_archive_section',
		'name'     => esc_html__( 'Products styles', 'woodmart' ),
		'priority' => 20,
	)
);

Options::add_section(
	array(
		'id'       => 'categories_styles_section',
		'parent'   => 'product_archive_section',
		'name'     => esc_html__( 'Categories styles', 'woodmart' ),
		'priority' => 30,
	)
);

Options::add_section(
	array(
		'id'       => 'shop_filters_section',
		'parent'   => 'product_archive_section',
		'name'     => esc_html__( 'Shop filters', 'woodmart' ),
		'priority' => 40,
	)
);

Options::add_section(
	array(
		'id'       => 'widgets_section',
		'parent'   => 'product_archive_section',
		'name'     => esc_html__( 'Widgets', 'woodmart' ),
		'priority' => 50,
	)
);

Options::add_section(
	array(
		'id'       => 'shop_page_title_section',
		'parent'   => 'product_archive_section',
		'name'     => esc_html__( 'Page title', 'woodmart' ),
		'priority' => 60,
	)
);

Options::add_section(
	array(
		'id'       => 'shop_sidebar_section',
		'parent'   => 'product_archive_section',
		'name'     => esc_html__( 'Sidebar', 'woodmart' ),
		'priority' => 70,
	)
);

/**
 * Single product.
 */
Options::add_section(
	array(
		'id'       => 'single_product_section',
		'name'     => esc_html__( 'Single product', 'woodmart' ),
		'priority' => 110,
		'icon'     => 'dashicons dashicons-products',
	)
);

Options::add_section(
	array(
		'id'       => 'product_images',
		'parent'   => 'single_product_section',
		'name'     => esc_html__( 'Images', 'woodmart' ),
		'priority' => 10,
	)
);

Options::add_section(
	array(
		'id'       => 'single_product_add_to_cart_section',
		'parent'   => 'single_product_section',
		'name'     => esc_html__( 'Add to cart options', 'woodmart' ),
		'priority' => 20,
	)
);

Options::add_section(
	array(
		'id'       => 'product_elements',
		'parent'   => 'single_product_section',
		'name'     => esc_html__( 'Show / hide elements', 'woodmart' ),
		'priority' => 30,
	)
);

Options::add_section(
	array(
		'id'       => 'product_share',
		'parent'   => 'single_product_section',
		'name'     => esc_html__( 'Share buttons', 'woodmart' ),
		'priority' => 40,
	)
);

Options::add_section(
	array(
		'id'       => 'product_tabs',
		'parent'   => 'single_product_section',
		'name'     => esc_html__( 'Tabs', 'woodmart' ),
		'priority' => 50,
	)
);

Options::add_section(
	array(
		'id'       => 'single_product_related_section',
		'parent'   => 'single_product_section',
		'name'     => esc_html__( 'Related products', 'woodmart' ),
		'priority' => 60,
	)
);

/**
 * Login/register section.
 */
Options::add_section(
	array(
		'id'       => 'login_section',
		'name'     => esc_html__( 'Login/Register', 'woodmart' ),
		'priority' => 120,
		'icon'     => 'dashicons dashicons-admin-users',
	)
);

/**
 * Share buttons configuration.
 */
Options::add_section(
	array(
		'id'       => 'social_profiles',
		'name'     => esc_html__( 'Social profiles', 'woodmart' ),
		'priority' => 130,
		'icon'     => 'dashicons dashicons-networking',
	)
);

Options::add_section(
	array(
		'id'       => 'social_links',
		'parent'   => 'social_profiles',
		'name'     => esc_html__( 'Links to social profiles', 'woodmart' ),
		'priority' => 10,
	)
);

Options::add_section(
	array(
		'id'       => 'social_share',
		'parent'   => 'social_profiles',
		'name'     => esc_html__( 'Share buttons', 'woodmart' ),
		'priority' => 10,
	)
);

/**
 * API integrations.
 */
Options::add_section(
	array(
		'id'       => 'api_integrations_section',
		'name'     => esc_html__( 'API integrations', 'woodmart' ),
		'priority' => 140,
		'icon'     => 'dashicons dashicons-rest-api',
	)
);

/**
 * Performance.
 */
Options::add_section(
	array(
		'id'       => 'performance',
		'name'     => esc_html__( 'Performance', 'woodmart' ),
		'priority' => 150,
		'icon'     => 'dashicons dashicons-performance',
	)
);

Options::add_section(
	array(
		'id'       => 'performance_css',
		'name'     => esc_html__( 'CSS', 'woodmart' ),
		'parent'   => 'performance',
		'priority' => 10,
	)
);

Options::add_section(
	array(
		'id'       => 'performance_js',
		'name'     => esc_html__( 'JS', 'woodmart' ),
		'parent'   => 'performance',
		'priority' => 20,
	)
);

Options::add_section(
	array(
		'id'       => 'fonts_section',
		'name'     => esc_html__( 'Fonts & Icons', 'woodmart' ),
		'parent'   => 'performance',
		'priority' => 30,
	)
);

Options::add_section(
	array(
		'id'       => 'performance_lazy_loading',
		'name'     => esc_html__( 'Lazy loading', 'woodmart' ),
		'parent'   => 'performance',
		'priority' => 40,
	)
);

Options::add_section(
	array(
		'id'       => 'plugins_section',
		'name'     => esc_html__( 'Plugins', 'woodmart' ),
		'parent'   => 'performance',
		'priority' => 50,
	)
);

Options::add_section(
	array(
		'id'       => 'preloader_section',
		'name'     => esc_html__( 'Preloader', 'woodmart' ),
		'parent'   => 'performance',
		'priority' => 60,
	)
);

/**
 * Maintenance.
 */
Options::add_section(
	array(
		'id'       => 'maintenance',
		'name'     => esc_html__( 'Maintenance', 'woodmart' ),
		'priority' => 160,
		'icon'     => 'dashicons dashicons-hammer',
	)
);

/**
 * White label.
 */
Options::add_section(
	array(
		'id'       => 'white_label_section',
		'name'     => esc_html__( 'White label', 'woodmart' ),
		'priority' => 170,
		'icon'     => 'dashicons dashicons-tag',
	)
);

/**
 * Custom CSS section.
 */
Options::add_section(
	array(
		'id'       => 'custom_css',
		'name'     => esc_html__( 'Custom CSS', 'woodmart' ),
		'priority' => 180,
		'icon'     => 'dashicons dashicons-buddicons-topics',
	)
);

/**
 * Custom JS section.
 */
Options::add_section(
	array(
		'id'       => 'custom_js',
		'name'     => esc_html__( 'Custom JS', 'woodmart' ),
		'priority' => 190,
		'icon'     => 'dashicons dashicons-media-text',
	)
);

/**
 * Other.
 */
Options::add_section(
	array(
		'id'       => 'other_section',
		'name'     => esc_html__( 'Other', 'woodmart' ),
		'priority' => 200,
		'icon'     => 'dashicons dashicons-admin-settings',
	)
);

/**
 * Import / Export / Reset.
 */
Options::add_section(
	array(
		'id'       => 'import_export',
		'name'     => esc_html__( 'Import / Export / Reset', 'woodmart' ),
		'priority' => 210,
		'icon'     => 'dashicons dashicons-image-rotate',
	)
);
