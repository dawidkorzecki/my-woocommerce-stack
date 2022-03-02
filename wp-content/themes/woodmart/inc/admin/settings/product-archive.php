<?php
if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Options;

Options::add_field(
	array(
		'id'          => 'ajax_shop',
		'name'        => esc_html__( 'AJAX shop', 'woodmart' ),
		'description' => esc_html__( 'Enable AJAX functionality for filter widgets, categories navigation, and pagination on the shop page.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'product_archive_section',
		'on-text'     => esc_html__( 'Yes', 'woodmart' ),
		'off-text'    => esc_html__( 'No', 'woodmart' ),
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'ajax_scroll',
		'name'        => esc_html__( 'Scroll to top after AJAX', 'woodmart' ),
		'description' => esc_html__( 'Disable - Enable scroll to top after AJAX.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'product_archive_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'cat_desc_position',
		'name'        => esc_html__( 'Category description position', 'woodmart' ),
		'description' => esc_html__( 'You can change default products category description position and move it below the products.', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'product_archive_section',
		'options'     => array(
			'before' => array(
				'name'  => esc_html__( 'Before product grid', 'woodmart' ),
				'value' => 'before',
			),
			'after'  => array(
				'name'  => esc_html__( 'After product grid', 'woodmart' ),
				'value' => 'after',
			),
		),
		'default'     => 'before',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'       => 'shop_page_breadcrumbs',
		'name'     => esc_html__( 'Breadcrumbs on shop page', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'product_archive_section',
		'default'  => '1',
		'priority' => 40,
	)
);

/**
 * Product styles.
 */
Options::add_field(
	array(
		'id'          => 'products_hover',
		'name'        => esc_html__( 'Hover on product', 'woodmart' ),
		'description' => esc_html__( 'Choose one of those hover effects for products', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'products_styles_section',
		'default'     => 'base',
		'options'     => array(
			'info-alt' => array(
				'name'  => esc_html__( 'Full info on hover', 'woodmart' ),
				'value' => 'info-alt',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/hover/info-alt.jpg',
			),
			'info'     => array(
				'name'  => esc_html__( 'Full info on image', 'woodmart' ),
				'value' => 'info',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/hover/info.jpg',
			),
			'alt'      => array(
				'name'  => esc_html__( 'Icons and "add to cart" on hover', 'woodmart' ),
				'value' => 'alt',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/hover/alt.jpg',
			),
			'icons'    => array(
				'name'  => esc_html__( 'Icons on hover', 'woodmart' ),
				'value' => 'icons',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/hover/icons.jpg',
			),
			'quick'    => array(
				'name'  => esc_html__( 'Quick', 'woodmart' ),
				'value' => 'quick',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/hover/quick.jpg',
			),
			'button'   => array(
				'name'  => esc_html__( 'Show button on hover on image', 'woodmart' ),
				'value' => 'button',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/hover/button.jpg',
			),
			'base'     => array(
				'name'  => esc_html__( 'Show summary on hover', 'woodmart' ),
				'value' => 'base',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/hover/base.jpg',
			),
			'standard' => array(
				'name'  => esc_html__( 'Standard button', 'woodmart' ),
				'value' => 'standard',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/hover/standard.jpg',
			),
			'tiled'    => array(
				'name'  => esc_html__( 'Tiled', 'woodmart' ),
				'value' => 'tiled',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/hover/tiled.jpg',
			),
		),
		'priority'    => 30,
		'requires'    => array(
			array(
				'key'     => 'shop_view',
				'compare' => 'not_equals',
				'value'   => 'list',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'base_hover_mobile_click',
		'name'        => esc_html__( 'Open product on click on mobile', 'woodmart' ),
		'description' => esc_html__( 'If you disable this option, when user click on the product on mobile devices, it will see its description text and add to cart button. The product page will be opened on second click.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'products_styles_section',
		'default'     => false,
		'priority'    => 40,
		'requires'    => array(
			array(
				'key'     => 'products_hover',
				'compare' => 'equals',
				'value'   => 'base',
			),
			array(
				'key'     => 'shop_view',
				'compare' => 'not_equals',
				'value'   => 'list',
			),
		),
	)
);

Options::add_field(
	array(
		'id'       => 'product_quantity',
		'name'     => esc_html__( 'Quantity input on product', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'products_styles_section',
		'default'  => false,
		'priority' => 41,
		'requires' => array(
			array(
				'key'     => 'products_hover',
				'compare' => 'equals',
				'value'   => array( 'standard', 'quick' ),
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'products_bordered_grid',
		'name'        => esc_html__( 'Bordered grid', 'woodmart' ),
		'description' => esc_html__( 'Add borders between the products in your grid', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'products_styles_section',
		'default'     => false,
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'hover_image',
		'name'        => esc_html__( 'Hover image', 'woodmart' ),
		'description' => esc_html__( 'Disable - Enable hover image for products on the shop page.', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'products_styles_section',
		'default'     => '1',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'       => 'base_hover_content',
		'name'     => esc_html__( 'Hover content', 'woodmart' ),
		'group'    => esc_html__( 'Elements', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'products_styles_section',
		'options'  => array(
			'excerpt'         => array(
				'name'  => esc_html__( 'Excerpt', 'woodmart' ),
				'value' => 'excerpt',
			),
			'additional_info' => array(
				'name'  => esc_html__( 'Additional information', 'woodmart' ),
				'value' => 'additional_info',
			),
			'none'            => array(
				'name'  => esc_html__( 'None', 'woodmart' ),
				'value' => 'none',
			),
		),
		'default'  => 'excerpt',
		'requires' => array(
			array(
				'key'     => 'products_hover',
				'compare' => 'equals',
				'value'   => 'base',
			),
		),
		'priority' => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'grid_stock_progress_bar',
		'name'        => esc_html__( 'Stock progress bar', 'woodmart' ),
		'description' => esc_html__( 'Display a number of sold and in stock products as a progress bar.', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'products_styles_section',
		'default'     => false,
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_countdown',
		'name'        => esc_html__( 'Countdown timer', 'woodmart' ),
		'description' => esc_html__( 'Show timer for products that have scheduled date for the sale price', 'woodmart' ),
		'group'       => esc_html__( 'Elements', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'products_styles_section',
		'default'     => false,
		'priority'    => 90,
	)
);

Options::add_field(
	array(
		'id'       => 'categories_under_title',
		'name'     => esc_html__( 'Show product category next to title', 'woodmart' ),
		'group'    => esc_html__( 'Elements', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'products_styles_section',
		'default'  => false,
		'priority' => 100,
	)
);

Options::add_field(
	array(
		'id'       => 'brands_under_title',
		'name'     => esc_html__( 'Show product brands next to title', 'woodmart' ),
		'group'    => esc_html__( 'Elements', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'products_styles_section',
		'default'  => false,
		'priority' => 110,
	)
);


Options::add_field(
	array(
		'id'       => 'product_title_lines_limit',
		'name'     => esc_html__( 'Product title lines limit', 'woodmart' ),
		'group'    => esc_html__( 'Elements', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'products_styles_section',
		'options'  => array(
			'one'  => array(
				'name'  => esc_html__( 'One line', 'woodmart' ),
				'value' => 'one',
			),
			'two'  => array(
				'name'  => esc_html__( 'Two line', 'woodmart' ),
				'value' => 'one',
			),
			'none' => array(
				'name'  => esc_html__( 'None', 'woodmart' ),
				'value' => 'none',
			),
		),
		'default'  => 'none',
		'priority' => 120,
	)
);

Options::add_field(
	array(
		'id'          => 'products_masonry',
		'name'        => esc_html__( 'Masonry grid', 'woodmart' ),
		'description' => esc_html__( 'Useful if your products have different height.', 'woodmart' ),
		'group'       => esc_html__( 'Masonry', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'products_styles_section',
		'default'     => false,
		'priority'    => 130,
		'requires'    => array(
			array(
				'key'     => 'shop_view',
				'compare' => 'not_equals',
				'value'   => 'list',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'products_different_sizes',
		'name'        => esc_html__( 'Products grid with different sizes', 'woodmart' ),
		'description' => esc_html__( 'In this situation, some of the products will be twice bigger in width than others. Recommended to use with 6 columns grid only.', 'woodmart' ),
		'group'       => esc_html__( 'Masonry', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'products_styles_section',
		'default'     => false,
		'priority'    => 140,
		'requires'    => array(
			array(
				'key'     => 'shop_view',
				'compare' => 'not_equals',
				'value'   => 'list',
			),
			array(
				'key'     => 'products_masonry',
				'compare' => 'equals',
				'value'   => true,
			),
		),
	)
);

/**
 * Categories styles.
 */
Options::add_field(
	array(
		'id'          => 'categories_design',
		'name'        => esc_html__( 'Categories design', 'woodmart' ),
		'description' => esc_html__( 'Choose one of those designs for categories', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'categories_styles_section',
		'default'     => 'default',
		'options'     => array(
			'default'       => array(
				'name'  => esc_html__( 'Default', 'woodmart' ),
				'value' => 'default',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/categories/default.jpg',
			),
			'alt'           => array(
				'name'  => esc_html__( 'Alternative', 'woodmart' ),
				'value' => 'alt',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/categories/alt.jpg',
			),
			'center'        => array(
				'name'  => esc_html__( 'Center title', 'woodmart' ),
				'value' => 'center',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/categories/center.jpg',
			),
			'replace-title' => array(
				'name'  => esc_html__( 'Replace title', 'woodmart' ),
				'value' => 'replace-title',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/categories/replace-title.jpg',
			),
		),
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'categories_with_shadow',
		'name'     => esc_html__( 'Categories with shadow', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'categories_styles_section',
		'options'  => array(
			'enable'  => array(
				'name'  => esc_html__( 'Enable', 'woodmart' ),
				'value' => 'enable',
			),
			'disable' => array(
				'name'  => esc_html__( 'Disable', 'woodmart' ),
				'value' => 'disable',
			),
		),
		'default'  => 'enable',
		'priority' => 20,
		'requires' => array(
			array(
				'key'     => 'categories_design',
				'compare' => 'equals',
				'value'   => array( 'alt', 'default' ),
			),
		),
	)
);

Options::add_field(
	array(
		'id'       => 'hide_categories_product_count',
		'name'     => esc_html__( 'Hide product count on category', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'categories_styles_section',
		'on-text'  => esc_html__( 'Yes', 'woodmart' ),
		'off-text' => esc_html__( 'No', 'woodmart' ),
		'default'  => false,
		'priority' => 30,
	)
);

/**
 * Sidebar.
 */
Options::add_field(
	array(
		'id'          => 'shop_layout',
		'name'        => esc_html__( 'Shop Layout', 'woodmart' ),
		'description' => esc_html__( 'Select main content and sidebar alignment for shop pages.', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'shop_sidebar_section',
		'options'     => array(
			'full-width'    => array(
				'name'  => esc_html__( '1 Column', 'woodmart' ),
				'value' => 'full-width',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/none.png',
			),
			'sidebar-left'  => array(
				'name'  => esc_html__( '2 Column Left', 'woodmart' ),
				'value' => 'sidebar-left',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/left.png',
			),
			'sidebar-right' => array(
				'name'  => esc_html__( '2 Column Right', 'woodmart' ),
				'value' => 'sidebar-right',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/sidebar-layout/right.png',
			),
		),
		'priority'    => 10,
		'default'     => 'sidebar-left',
	)
);

Options::add_field(
	array(
		'id'          => 'shop_sidebar_width',
		'name'        => esc_html__( 'Sidebar size', 'woodmart' ),
		'description' => esc_html__( 'You can set different sizes for your shop pages sidebar', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'shop_sidebar_section',
		'options'     => array(
			2 => array(
				'name'  => esc_html__( 'Small', 'woodmart' ),
				'value' => 2,
			),
			3 => array(
				'name'  => esc_html__( 'Medium', 'woodmart' ),
				'value' => 3,
			),
			4 => array(
				'name'  => esc_html__( 'Large', 'woodmart' ),
				'value' => 4,
			),
		),
		'priority'    => 20,
		'default'     => 3,
		'requires'    => array(
			array(
				'key'     => 'shop_layout',
				'compare' => 'not_equals',
				'value'   => 'full-width',
			),
		),
	)
);

Options::add_field(
	array(
		'id'       => 'off_canvas_sidebar_tabs',
		'name'     => esc_html__( 'Off canvas sidebar', 'woodmart' ),
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
		'requires' => array(
			array(
				'key'     => 'shop_layout',
				'compare' => 'not_equals',
				'value'   => 'full-width',
			),
		),
		'default'  => 'desktop',
		'tabs'     => 'devices',
		'type'     => 'buttons',
		'section'  => 'shop_sidebar_section',
		'priority' => 21,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_hide_sidebar',
		'name'        => esc_html__( 'Off canvas sidebar for mobile', 'woodmart' ),
		'description' => esc_html__( 'You can hide the sidebar on mobile devices and show it nicely with a button click.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_sidebar_section',
		'default'     => '1',
		'priority'    => 30,
		'requires'    => array(
			array(
				'key'     => 'shop_layout',
				'compare' => 'not_equals',
				'value'   => 'full-width',
			),
			array(
				'key'     => 'off_canvas_sidebar_tabs',
				'compare' => 'equals',
				'value'   => 'mobile',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'shop_hide_sidebar_tablet',
		'name'        => esc_html__( 'Off canvas sidebar for tablet', 'woodmart' ),
		'description' => esc_html__( 'You can hide the sidebar on tablet devices and show it nicely with a button click.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_sidebar_section',
		'default'     => '1',
		'priority'    => 40,
		'requires'    => array(
			array(
				'key'     => 'shop_layout',
				'compare' => 'not_equals',
				'value'   => 'full-width',
			),
			array(
				'key'     => 'off_canvas_sidebar_tabs',
				'compare' => 'equals',
				'value'   => 'tablet',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'shop_hide_sidebar_desktop',
		'name'        => esc_html__( 'Off canvas sidebar for desktop', 'woodmart' ),
		'description' => esc_html__( 'You can hide the sidebar from the page and show it nicely with a button click.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_sidebar_section',
		'default'     => false,
		'priority'    => 50,
		'requires'    => array(
			array(
				'key'     => 'shop_layout',
				'compare' => 'not_equals',
				'value'   => 'full-width',
			),
			array(
				'key'     => 'off_canvas_sidebar_tabs',
				'compare' => 'equals',
				'value'   => 'desktop',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'sticky_filter_button',
		'name'        => esc_html__( 'Sticky off canvas sidebar button', 'woodmart' ),
		'description' => esc_html__( 'Display the filters button fixed on the screen for mobile and tablet devices.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_sidebar_section',
		'default'     => false,
		'priority'    => 51,
	)
);

/**
 * Page title.
 */
Options::add_field(
	array(
		'id'          => 'shop_title',
		'name'        => esc_html__( 'Shop title', 'woodmart' ),
		'description' => esc_html__( 'Show title for shop page, product categories or tags.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_page_title_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_categories',
		'name'        => esc_html__( 'Categories in page title', 'woodmart' ),
		'description' => esc_html__( 'This categories menu is generated automatically based on all categories in the shop. You are not able to manage this menu as other WordPress menus.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_page_title_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_categories_ancestors',
		'name'        => esc_html__( 'Show current category ancestors', 'woodmart' ),
		'description' => esc_html__( 'If you visit category Man, for example, only man\'s subcategories will be shown in the page title like T-shirts, Coats, Shoes etc.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_page_title_section',
		'default'     => false,
		'priority'    => 30,
		'requires'    => array(
			array(
				'key'     => 'shop_categories',
				'compare' => 'equals',
				'value'   => true,
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'show_categories_neighbors',
		'name'        => esc_html__( 'Show category neighbors if there is no children', 'woodmart' ),
		'description' => esc_html__( 'If the category you visit doesn\'t contain any subcategories, the page title menu will display this category\'s neighbors categories.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_page_title_section',
		'default'     => false,
		'priority'    => 40,
		'requires'    => array(
			array(
				'key'     => 'shop_categories_ancestors',
				'compare' => 'equals',
				'value'   => true,
			),
		),
	)
);

Options::add_field(
	array(
		'id'       => 'shop_products_count',
		'name'     => esc_html__( 'Show products count for each category', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'shop_page_title_section',
		'default'  => '1',
		'priority' => 50,
		'requires' => array(
			array(
				'key'     => 'shop_categories',
				'compare' => 'equals',
				'value'   => true,
			),
		),
	)
);

Options::add_field(
	array(
		'id'       => 'shop_page_title_hide_empty_categories',
		'name'     => esc_html__( 'Hide empty categories', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'shop_page_title_section',
		'default'  => false,
		'requires' => array(
			array(
				'key'     => 'shop_categories',
				'compare' => 'equals',
				'value'   => true,
			),
		),
		'priority' => 60,
	)
);

Options::add_field(
	array(
		'id'           => 'shop_page_title_categories_exclude',
		'type'         => 'select',
		'section'      => 'shop_page_title_section',
		'name'         => esc_html__( 'Exclude categories', 'woodmart' ),
		'select2'      => true,
		'empty_option' => true,
		'multiple'     => true,
		'requires'     => array(
			array(
				'key'     => 'shop_categories',
				'compare' => 'equals',
				'value'   => true,
			),
			array(
				'key'     => 'shop_categories_ancestors',
				'compare' => 'not_equals',
				'value'   => true,
			),
		),
		'autocomplete' => array(
			'type'   => 'term',
			'value'  => 'product_cat',
			'search' => 'woodmart_get_taxonomies_by_query_autocomplete',
			'render' => 'woodmart_get_taxonomies_by_ids_autocomplete',
		),
		'priority'     => 70,
	)
);

/**
 * Products grid.
 */
Options::add_field(
	array(
		'id'          => 'shop_view',
		'name'        => __( 'Shop products view', 'woodmart' ),
		'description' => __( 'You can set different view mode for the shop page', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'products_grid_section',
		'options'     => array(
			'grid'      => array(
				'name'  => esc_html__( 'Grid', 'woodmart' ),
				'value' => 'grid',
			),
			'list'      => array(
				'name'  => esc_html__( 'List', 'woodmart' ),
				'value' => 'list',
			),
			'grid_list' => array(
				'name'  => esc_html__( 'Grid / List', 'woodmart' ),
				'value' => 'grid_list',
			),
			'list_grid' => array(
				'name'  => esc_html__( 'List / Grid', 'woodmart' ),
				'value' => 'list_grid',
			),
		),
		'default'     => 'grid',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'products_columns_tabs',
		'name'     => esc_html__( 'Products columns', 'woodmart' ),
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
		'requires' => array(
			array(
				'key'     => 'shop_view',
				'compare' => 'not_equals',
				'value'   => 'list',
			),
		),
		'default'  => 'desktop',
		'tabs'     => 'devices',
		'type'     => 'buttons',
		'section'  => 'products_grid_section',
		'priority' => 11,
	)
);


Options::add_field(
	array(
		'id'          => 'products_columns',
		'name'        => esc_html__( 'Products columns on desktop', 'woodmart' ),
		'description' => esc_html__( 'How many products you want to show per row', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'products_grid_section',
		'options'     => array(
			2 => array(
				'name'  => 2,
				'value' => 2,
			),
			3 => array(
				'name'  => 3,
				'value' => 3,
			),
			4 => array(
				'name'  => 4,
				'value' => 4,
			),
			5 => array(
				'name'  => 5,
				'value' => 5,
			),
			6 => array(
				'name'  => 6,
				'value' => 6,
			),
		),
		'default'     => 3,
		'priority'    => 20,
		'requires'    => array(
			array(
				'key'     => 'shop_view',
				'compare' => 'not_equals',
				'value'   => 'list',
			),
			array(
				'key'     => 'products_columns_tabs',
				'compare' => 'equals',
				'value'   => 'desktop',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'products_columns_mobile',
		'name'        => esc_html__( 'Products columns on mobile', 'woodmart' ),
		'description' => esc_html__( 'How many products you want to show per row on mobile devices', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'products_grid_section',
		'options'     => array(
			1 => array(
				'name'  => 1,
				'value' => 1,
			),
			2 => array(
				'name'  => 2,
				'value' => 2,
			),
		),
		'default'     => 2,
		'priority'    => 30,
		'requires'    => array(
			array(
				'key'     => 'shop_view',
				'compare' => 'not_equals',
				'value'   => 'list',
			),
			array(
				'key'     => 'products_columns_tabs',
				'compare' => 'equals',
				'value'   => 'mobile',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'products_spacing',
		'name'        => esc_html__( 'Space between products', 'woodmart' ),
		'description' => esc_html__( 'You can set different spacing between blocks on shop page', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'products_grid_section',
		'options'     => array(
			0  => array(
				'name'  => 0,
				'value' => 0,
			),
			2  => array(
				'name'  => 2,
				'value' => 2,
			),
			6  => array(
				'name'  => 5,
				'value' => 6,
			),
			10 => array(
				'name'  => 10,
				'value' => 10,
			),
			20 => array(
				'name'  => 20,
				'value' => 20,
			),
			30 => array(
				'name'  => 30,
				'value' => 30,
			),
		),
		'default'     => 30,
		'priority'    => 40,
		'requires'    => array(
			array(
				'key'     => 'shop_view',
				'compare' => 'not_equals',
				'value'   => 'list',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'per_row_columns_selector',
		'name'        => esc_html__( 'Number of columns selector', 'woodmart' ),
		'description' => esc_html__( 'Allow customers to change number of columns per row', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'products_grid_section',
		'default'     => '1',
		'priority'    => 50,
		'requires'    => array(
			array(
				'key'     => 'shop_view',
				'compare' => 'not_equals',
				'value'   => 'list',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'products_columns_variations',
		'name'        => esc_html__( 'Available products columns variations', 'woodmart' ),
		'description' => esc_html__( 'What columns users may select to be displayed on the product page', 'woodmart' ),
		'type'        => 'select',
		'multiple'    => true,
		'select2'     => true,
		'section'     => 'products_grid_section',
		'options'     => array(
			2 => array(
				'name'  => 2,
				'value' => 2,
			),
			3 => array(
				'name'  => 3,
				'value' => 3,
			),
			4 => array(
				'name'  => 4,
				'value' => 4,
			),
			5 => array(
				'name'  => 5,
				'value' => 5,
			),
			6 => array(
				'name'  => 6,
				'value' => 6,
			),
		),
		'default'     => array( 2, 3, 4 ),
		'priority'    => 60,
		'requires'    => array(
			array(
				'key'     => 'per_row_columns_selector',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'shop_per_page',
		'name'        => esc_html__( 'Products per page', 'woodmart' ),
		'description' => esc_html__( 'Number of products per page', 'woodmart' ),
		'group'       => esc_html__( 'Pages', 'woodmart' ),
		'type'        => 'text_input',
		'section'     => 'products_grid_section',
		'default'     => 12,
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'per_page_links',
		'name'        => esc_html__( 'Products per page links', 'woodmart' ),
		'description' => esc_html__( 'Allow customers to change number of products per page', 'woodmart' ),
		'group'       => esc_html__( 'Pages', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'products_grid_section',
		'default'     => '1',
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'per_page_options',
		'name'        => esc_html__( 'Products per page variations', 'woodmart' ),
		'description' => esc_html__( 'For ex.: 12,24,36,-1. Use -1 to show all products on the page', 'woodmart' ),
		'group'       => esc_html__( 'Pages', 'woodmart' ),
		'type'        => 'text_input',
		'section'     => 'products_grid_section',
		'default'     => '9,24,36',
		'priority'    => 90,
		'requires'    => array(
			array(
				'key'     => 'per_page_links',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'shop_pagination',
		'name'        => esc_html__( 'Products pagination', 'woodmart' ),
		'description' => esc_html__( 'Choose a type for the pagination on your shop page.', 'woodmart' ),
		'group'       => esc_html__( 'Pages', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'products_grid_section',
		'options'     => array(
			'pagination' => array(
				'name'  => esc_html__( 'Pagination', 'woodmart' ),
				'value' => 'pagination',
			),
			'more-btn'   => array(
				'name'  => esc_html__( '"Load more" button', 'woodmart' ),
				'value' => 'more-btn',
			),
			'infinit'    => array(
				'name'  => esc_html__( 'Infinit scrolling', 'woodmart' ),
				'value' => 'infinit',
			),
		),
		'default'     => 'pagination',
		'priority'    => 100,
	)
);

/**
 * Widgets.
 */
Options::add_field(
	array(
		'id'          => 'categories_toggle',
		'name'        => esc_html__( 'Toggle function for categories widget', 'woodmart' ),
		'description' => esc_html__( 'Turn it on to enable accordion JS for the WooCommerce Product Categories widget. Useful if you have a lot of categories and subcategories.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'widgets_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'widgets_scroll',
		'name'        => esc_html__( 'Scroll for filters widgets', 'woodmart' ),
		'description' => esc_html__( 'You can limit your Layered Navigation widgets by height and enable nice scroll for them. Useful if you have a lot of product colors/sizes or other attributes for filters.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'widgets_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'widget_heights',
		'name'        => esc_html__( 'Height for filters widgets', 'woodmart' ),
		'description' => esc_html__( 'Set widgets height in pixels.', 'woodmart' ),
		'type'        => 'range',
		'section'     => 'widgets_section',
		'default'     => 280,
		'min'         => 100,
		'step'        => 1,
		'max'         => 800,
		'priority'    => 30,
		'requires'    => array(
			array(
				'key'     => 'widgets_scroll',
				'compare' => 'equals',
				'value'   => true,
			),
		),
	)
);

/**
 * Shop filers.
 */
Options::add_field(
	array(
		'id'          => 'shop_filters',
		'name'        => esc_html__( 'Shop filters', 'woodmart' ),
		'description' => esc_html__( 'Enable shop filters widget\'s area above the products.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_filters_section',
		'default'     => false,
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'shop_filters_always_open',
		'name'        => esc_html__( 'Shop filters area always opened', 'woodmart' ),
		'description' => esc_html__( 'If you enable this option the shop filters will be always opened on the shop page.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_filters_section',
		'default'     => false,
		'priority'    => 20,
		'requires'    => array(
			array(
				'key'     => 'shop_filters',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'shop_filters_close',
		'name'        => esc_html__( 'Stop close filters after click', 'woodmart' ),
		'description' => esc_html__( 'This option will prevent filters area from closing when you click on certain filter links.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_filters_section',
		'default'     => false,
		'priority'    => 30,
		'requires'    => array(
			array(
				'key'     => 'shop_filters_always_open',
				'compare' => 'equals',
				'value'   => '0',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'shop_filters_type',
		'name'        => esc_html__( 'Shop filters content type', 'woodmart' ),
		'description' => esc_html__( 'You can use widgets or custom HTML block with our Product filters page builder element.', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'shop_filters_section',
		'default'     => 'widgets',
		'options'     => array(
			'widgets' => array(
				'name'  => esc_html__( 'Widgets', 'woodmart' ),
				'value' => 'widgets',
			),
			'content' => array(
				'name'  => esc_html__( 'Custom content', 'woodmart' ),
				'value' => 'content',
			),
		),
		'priority'    => 40,
		'requires'    => array(
			array(
				'key'     => 'shop_filters',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'shop_filters_content',
		'name'        => esc_html__( 'Shop filters custom content', 'woodmart' ),
		'description' => esc_html__( 'You can create an HTML Block in Dashboard -> HTML Blocks and add Product filters page builder element there.', 'woodmart' ),
		'type'        => 'select',
		'section'     => 'shop_filters_section',
		'options'     => '',
		'callback'    => 'woodmart_get_theme_settings_html_blocks_array',
		'priority'    => 50,
		'requires'    => array(
			array(
				'key'     => 'shop_filters_type',
				'compare' => 'equals',
				'value'   => 'content',
			),
		),
	)
);
