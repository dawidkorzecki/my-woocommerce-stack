<?php
if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Options;

Options::add_field(
	array(
		'id'          => 'add_to_cart_action',
		'name'        => esc_html__( 'Action after add to cart', 'woodmart' ),
		'group'       => esc_html__( 'Shopping cart widget', 'woodmart' ),
		'description' => esc_html__( 'Choose between showing an informative popup and opening the shopping cart widget.', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'shop_section',
		'options'     => array(
			'popup'   => array(
				'name'  => esc_html__( 'Show popup', 'woodmart' ),
				'value' => 'popup',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/add-to-cart-action/popup.jpg',
			),
			'widget'  => array(
				'name'  => esc_html__( 'Display widget', 'woodmart' ),
				'value' => 'widget',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/add-to-cart-action/widget.jpg',
			),
			'nothing' => array(
				'name'  => esc_html__( 'No action', 'woodmart' ),
				'value' => 'nothing',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/add-to-cart-action/nothing.jpg',
			),
		),
		'default'     => 'widget',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'add_to_cart_action_timeout',
		'name'        => esc_html__( 'Hide widget automatically', 'woodmart' ),
		'group'       => esc_html__( 'Shopping cart widget', 'woodmart' ),
		'description' => esc_html__( 'After adding to cart the shopping cart widget will be hidden automatically', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_section',
		'default'     => false,
		'priority'    => 20,
		'requires'    => array(
			array(
				'key'     => 'add_to_cart_action',
				'compare' => 'not_equals',
				'value'   => 'nothing',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'add_to_cart_action_timeout_number',
		'name'        => esc_html__( 'Hide widget after', 'woodmart' ),
		'group'       => esc_html__( 'Shopping cart widget', 'woodmart' ),
		'description' => esc_html__( 'Set the number of seconds for the shopping cart widget to be displayed after adding to cart', 'woodmart' ),
		'type'        => 'range',
		'section'     => 'shop_section',
		'default'     => 3,
		'min'         => 2,
		'max'         => 20,
		'step'        => 1,
		'priority'    => 30,
		'requires'    => array(
			array(
				'key'     => 'add_to_cart_action',
				'compare' => 'not_equals',
				'value'   => 'nothing',
			),
			array(
				'key'     => 'add_to_cart_action_timeout',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'mini_cart_quantity',
		'name'        => esc_html__( 'Quantity input on shopping cart widget', 'woodmart' ),
		'description' => esc_html__( 'Give your customers an ability to change the number of products in the cart directly from the shopping cart widget.', 'woodmart' ),
		'group'       => esc_html__( 'Shopping cart widget', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_section',
		'default'     => '0',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'search_by_sku',
		'name'     => esc_html__( 'Search by product SKU', 'woodmart' ),
		'group'    => esc_html__( 'Search', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'shop_section',
		'default'  => '1',
		'priority' => 50,
	)
);

Options::add_field(
	array(
		'id'       => 'show_sku_on_ajax',
		'name'     => esc_html__( 'Show SKU on AJAX results', 'woodmart' ),
		'group'    => esc_html__( 'Search', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'shop_section',
		'requires' => array(
			array(
				'key'     => 'search_by_sku',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
		'default'  => false,
		'priority' => 60,
	)
);

Options::add_field(
	array(
		'id'          => 'relevanssi_search',
		'name'        => esc_html__( 'Use Relevanssi for AJAX search', 'woodmart' ),
		'group'       => esc_html__( 'Search', 'woodmart' ),
		'description' => 'You will need to install and activate this <a href="https://wordpress.org/plugins/relevanssi/" target="_blank">plugin</a>',
		'type'        => 'switcher',
		'section'     => 'shop_section',
		'default'     => '0',
		'priority'    => 70,
	)
);

Options::add_field(
	array(
		'id'          => 'hide_larger_price',
		'name'        => esc_html__( 'Hide "to" price', 'woodmart' ),
		'description' => esc_html__( 'This option will hide a higher price for variable products and leave only a small one.', 'woodmart' ),
		'group'       => esc_html__( 'Variable products', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_section',
		'default'     => false,
		'priority'    => 80,
	)
);

Options::add_field(
	array(
		'id'          => 'quick_shop_variable',
		'name'        => esc_html__( '"Quick Shop" for variable products', 'woodmart' ),
		'description' => esc_html__( 'Allow your users to purchase variable products directly from the shop page.', 'woodmart' ),
		'group'       => esc_html__( 'Variable products', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_section',
		'default'     => '1',
		'priority'    => 90,
	)
);

Options::add_field(
	array(
		'id'          => 'empty_cart_text',
		'name'        => esc_html__( 'Empty cart text', 'woodmart' ),
		'description' => esc_html__( 'Text will be displayed if user don\'t add any products to cart', 'woodmart' ),
		'group'       => esc_html__( 'Shopping cart', 'woodmart' ),
		'type'        => 'textarea',
		'wysiwyg'     => false,
		'section'     => 'shop_section',
		'default'     => 'Before proceed to checkout you must add some products to your shopping cart.<br> You will find a lot of interesting products on our "Shop" page.',
		'priority'    => 100,
	)
);

Options::add_field(
	array(
		'id'          => 'catalog_mode',
		'name'        => esc_html__( 'Enable catalog mode', 'woodmart' ),
		'description' => esc_html__( 'You can hide all "Add to cart" buttons, cart widget, cart and checkout pages. This will allow you to showcase your products as an online catalog without ability to make a purchase.', 'woodmart' ),
		'group'       => esc_html__( 'Catalog mode', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_section',
		'default'     => false,
		'priority'    => 110,
	)
);

Options::add_field(
	array(
		'id'          => 'login_prices',
		'name'        => esc_html__( 'Login to see add to cart and prices', 'woodmart' ),
		'description' => esc_html__( 'You can restrict shopping functions only for logged in customers.', 'woodmart' ),
		'group'       => esc_html__( 'Login to see prices', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'shop_section',
		'default'     => false,
		'priority'    => 120,
	)
);

Options::add_field(
	array(
		'id'          => 'size_guides',
		'name'        => esc_html__( 'Size guides', 'woodmart' ),
		'group'       => esc_html__( 'Size guides', 'woodmart' ),
		'description' => wp_kses(
			__( 'Turn on the size guide feature on the website. Read more information about this function in <a href="https://xtemos.com/docs/woodmart/faq-guides/create-size-guide-table/" target="_blank">our documentation</a>.', 'woodmart' ),
			array(
				'a'      => array(
					'href'   => true,
					'target' => true,
				),
				'br'     => array(),
				'strong' => array(),
			)
		),
		'type'        => 'switcher',
		'section'     => 'shop_section',
		'default'     => '1',
		'priority'    => 130,
	)
);

/**
 * Swatches.
 */
Options::add_field(
	array(
		'id'           => 'grid_swatches_attribute',
		'name'         => esc_html__( 'Grid swatch attribute to display', 'woodmart' ),
		'description'  => esc_html__( 'Choose the attribute that will be shown on the product grid.', 'woodmart' ),
		'type'         => 'select',
		'section'      => 'attribute_swatches_section',
		'options'      => woodmart_product_attributes_array(),
		'priority'     => 10,
		'empty_option' => true,
	)
);

Options::add_field(
	array(
		'id'          => 'swatches_use_variation_images',
		'name'        => esc_html__( 'Use images from product variations', 'woodmart' ),
		'description' => esc_html__( 'Swatches buttons will be filled with images chosen for product variations and not with images uploaded to attribute terms.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'attribute_swatches_section',
		'default'     => false,
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'       => 'swatches_labels_name',
		'name'     => esc_html__( 'Stick selected option name on desktop and tablet', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'attribute_swatches_section',
		'default'  => false,
		'priority' => 30,
	)
);

Options::add_field(
	array(
		'id'       => 'swatches_limit',
		'name'     => esc_html__( 'Limit swatches on grid', 'woodmart' ),
		'type'     => 'switcher',
		'section'  => 'attribute_swatches_section',
		'default'  => false,
		'priority' => 40,
	)
);

Options::add_field(
	array(
		'id'       => 'swatches_limit_count',
		'name'     => esc_html__( 'Number of visible swatches', 'woodmart' ),
		'type'     => 'range',
		'section'  => 'attribute_swatches_section',
		'default'  => 5,
		'min'      => 1,
		'step'     => 1,
		'max'      => 20,
		'priority' => 50,
		'requires' => array(
			array(
				'key'     => 'swatches_limit',
				'compare' => 'equals',
				'value'   => true,
			),
		),
	)
);

/**
 * Labels.
 */
Options::add_field(
	array(
		'id'       => 'label_shape',
		'name'     => esc_html__( 'Label shape', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'product_labels_section',
		'default'  => 'rounded',
		'options'  => array(
			'rounded'     => array(
				'name'  => esc_html__( 'Rounded', 'woodmart' ),
				'value' => 'rounded',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/product-label/rounded.jpg',
			),
			'rectangular' => array(
				'name'  => esc_html__( 'Rectangular', 'woodmart' ),
				'value' => 'rectangular',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/product-label/rectangular.jpg',
			),
		),
		'priority' => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'percentage_label',
		'name'        => esc_html__( 'Shop sale label in percentage', 'woodmart' ),
		'description' => esc_html__( 'Works with Simple, Variable and External products only.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'product_labels_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'new_label',
		'name'        => esc_html__( '"New" label on products', 'woodmart' ),
		'description' => esc_html__( 'This label is displayed for products if you enabled this option for particular items.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'product_labels_section',
		'default'     => '1',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'new_label_days_after_create',
		'name'        => esc_html__( 'Automatic "New" label period', 'woodmart' ),
		'description' => esc_html__( 'Set a number of days to keep your products marked as "New" after creation.', 'woodmart' ),
		'type'        => 'range',
		'section'     => 'product_labels_section',
		'default'     => 0,
		'min'         => 0,
		'max'         => 365,
		'step'        => 1,
		'priority'    => 31,
	)
);

Options::add_field(
	array(
		'id'          => 'hot_label',
		'name'        => esc_html__( '"Hot" label on products', 'woodmart' ),
		'description' => esc_html__( 'Your products marked as "Featured" will have a badge with "Hot" label.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'product_labels_section',
		'default'     => '1',
		'priority'    => 40,
	)
);

/**
 * Brands.
 */
Options::add_field(
	array(
		'id'           => 'brands_attribute',
		'name'         => esc_html__( 'Brand attribute', 'woodmart' ),
		'description'  => esc_html__( 'If you want to show brand image on your product page select desired attribute here', 'woodmart' ),
		'type'         => 'select',
		'section'      => 'brands_section',
		'options'      => woodmart_product_attributes_array(),
		'priority'     => 10,
		'default'      => 'pa_brand',
		'empty_option' => true,
	)
);

Options::add_field(
	array(
		'id'          => 'product_page_brand',
		'name'        => esc_html__( 'Show brand on the single product page', 'woodmart' ),
		'description' => esc_html__( 'You can disable/enable product\'s brand image on the single page.', 'woodmart' ),
		'group'       => esc_html__( 'Brand on the single product page', 'woodmart' ),
		'type'        => 'switcher',
		'on-text'     => esc_html__( 'Yes', 'woodmart' ),
		'off-text'    => esc_html__( 'No', 'woodmart' ),
		'section'     => 'brands_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'product_brand_location',
		'name'        => esc_html__( 'Brand position on the product page', 'woodmart' ),
		'description' => esc_html__( 'Select a position of the brand image on the single product page.', 'woodmart' ),
		'group'       => esc_html__( 'Brand on the single product page', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'brands_section',
		'options'     => array(
			'about_title' => array(
				'name'  => esc_html__( 'Above product title', 'woodmart' ),
				'value' => 'about_title',
			),
			'sidebar'     => array(
				'name'  => esc_html__( 'Sidebar', 'woodmart' ),
				'value' => 'sidebar',
			),
		),
		'priority'    => 30,
		'default'     => 'about_title',
	)
);

Options::add_field(
	array(
		'id'          => 'brand_tab',
		'name'        => esc_html__( 'Show tab with brand information', 'woodmart' ),
		'description' => esc_html__( ' If enabled you will see an additional tab with brand description on the single product page. Text will be taken from the "Description" field for each brand (attribute term).', 'woodmart' ),
		'group'       => esc_html__( 'Brand on the single product page', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'brands_section',
		'on-text'     => esc_html__( 'Yes', 'woodmart' ),
		'off-text'    => esc_html__( 'No', 'woodmart' ),
		'default'     => '1',
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'brand_tab_name',
		'name'        => esc_html__( 'Use brand name for tab title', 'woodmart' ),
		'description' => esc_html__( 'If you enable this option, the tab with the brand information will be called "About [Brand name]".', 'woodmart' ),
		'group'       => esc_html__( 'Brand on the single product page', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'brands_section',
		'on-text'     => esc_html__( 'Yes', 'woodmart' ),
		'off-text'    => esc_html__( 'No', 'woodmart' ),
		'default'     => false,
		'priority'    => 50,
	)
);

/**
 * Quick view.
 */
Options::add_field(
	array(
		'id'          => 'quick_view',
		'name'        => esc_html__( 'Quick view', 'woodmart' ),
		'description' => esc_html__( 'Enable Quick view option. Ability to see the product information with AJAX.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'quick_view_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'quick_view_variable',
		'name'        => esc_html__( 'Show variations on quick view', 'woodmart' ),
		'description' => esc_html__( 'Enable Quick view option for variable products. Will allow your users to purchase variable products directly from the quick view.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'quick_view_section',
		'default'     => '1',
		'priority'    => 20,
		'requires'    => array(
			array(
				'key'     => 'quick_view',
				'compare' => 'equals',
				'value'   => '1',
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'quick_view_layout',
		'name'        => esc_html__( 'Quick view layout', 'woodmart' ),
		'description' => esc_html__( 'Choose between horizontal and vertical layouts for the quick view window.', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'quick_view_section',
		'default'     => 'horizontal',
		'options'     => array(
			'horizontal' => array(
				'name'  => esc_html__( 'Horizontal', 'woodmart' ),
				'value' => 'horizontal',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/quick-view-layout/horizontal.jpg',
			),
			'vertical'   => array(
				'name'  => esc_html__( 'Vertical', 'woodmart' ),
				'value' => 'vertical',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/quick-view-layout/vertical.jpg',
			),
		),
		'priority'    => 30,
		'requires'    => array(
			array(
				'key'     => 'quick_view',
				'compare' => 'equals',
				'value'   => true,
			),
		),
	)
);

Options::add_field(
	array(
		'id'          => 'quickview_width',
		'name'        => esc_html__( 'Quick view width', 'woodmart' ),
		'description' => esc_html__( 'Set width of the quick view in pixels.', 'woodmart' ),
		'type'        => 'range',
		'section'     => 'quick_view_section',
		'default'     => 920,
		'min'         => 400,
		'step'        => 10,
		'max'         => 1200,
		'priority'    => 40,
		'requires'    => array(
			array(
				'key'     => 'quick_view',
				'compare' => 'equals',
				'value'   => true,
			),
		),
	)
);

/**
 * Compare.
 */
Options::add_field(
	array(
		'id'          => 'compare',
		'name'        => esc_html__( 'Enable compare', 'woodmart' ),
		'description' => wp_kses( __( 'Enable compare functionality built in with the theme. Read more information in our <a href="https://xtemos.com/docs/woodmart/woodmart-compare/">documentation</a>.', 'woodmart' ), 'default' ),
		'type'        => 'switcher',
		'section'     => 'compare_section',
		'default'     => '1',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'           => 'compare_page',
		'name'         => esc_html__( 'Compare page', 'woodmart' ),
		'description'  => esc_html__( 'Select a page for the compare table. It should contain the shortcode: [woodmart_compare]', 'woodmart' ),
		'type'         => 'select',
		'section'      => 'compare_section',
		'options'      => woodmart_get_pages_array(),
		'empty_option' => true,
		'select2'      => true,
		'default'      => 265,
		'priority'     => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'compare_on_grid',
		'name'        => esc_html__( 'Show button on product grid', 'woodmart' ),
		'description' => esc_html__( 'Display compare product button on all products grids and lists.', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'compare_section',
		'default'     => '1',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'fields_compare',
		'name'        => esc_html__( 'Select fields for compare table', 'woodmart' ),
		'description' => esc_html__( 'Choose which fields should be presented on the product compare page with table.', 'woodmart' ),
		'type'        => 'select',
		'multiple'    => true,
		'select2'     => true,
		'section'     => 'compare_section',
		'options'     => woodmart_compare_available_fields( true ),
		'default'     => array(
			'description',
			'sku',
			'availability',
		),
		'priority'    => 40,
	)
);

Options::add_field(
	array(
		'id'          => 'empty_compare_text',
		'name'        => esc_html__( 'Empty compare text', 'woodmart' ),
		'description' => esc_html__( 'Text will be displayed if user don\'t add any products to compare', 'woodmart' ),
		'default'     => 'No products added in the compare list. You must add some products to compare them.<br> You will find a lot of interesting products on our "Shop" page.',
		'type'        => 'textarea',
		'wysiwyg'     => false,
		'section'     => 'compare_section',
		'priority'    => 50,
	)
);

/**
 * Wishlist (60).
 */

/**
 * Thank you page.
 */
Options::add_field(
	array(
		'id'       => 'thank_you_page_content_type',
		'name'     => esc_html__( 'Extra content for "Thank you page"', 'woodmart' ),
		'type'     => 'buttons',
		'section'  => 'thank_you_page_section',
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
		'priority' => 10,
		'class'    => 'xts-html-block-switch'
	)
);

Options::add_field(
	array(
		'id'          => 'thank_you_page_extra_content',
		'type'        => 'textarea',
		'wysiwyg'     => true,
		'section'     => 'thank_you_page_section',
		'requires'    => array(
			array(
				'key'     => 'thank_you_page_content_type',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'           => 'thank_you_page_html_block',
		'type'         => 'select',
		'section'      => 'thank_you_page_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => '',
		'callback'     => 'woodmart_get_theme_settings_html_blocks_array',
		'requires'     => array(
			array(
				'key'     => 'thank_you_page_content_type',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'priority'     => 30,
	)
);


Options::add_field(
	array(
		'id'          => 'thank_you_page_default_content',
		'name'        => esc_html__( 'Default "Thank you page" content', 'woodmart' ),
		'description' => esc_html__( 'If you use custom extra content you can disable default WooCommerce order details on the thank you page', 'woodmart' ),
		'type'        => 'switcher',
		'section'     => 'thank_you_page_section',
		'default'     => '1',
		'priority'    => 40,
	)
);
