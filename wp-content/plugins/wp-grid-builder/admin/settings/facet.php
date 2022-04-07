<?php
/**
 * Facet settings
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$facet_id     = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
$facet_slug   = '';
$facet_name   = __( 'New Facet', 'wp-grid-builder' );
$facet_types  = apply_filters( 'wp_grid_builder/facets', [] );
$facet_icons  = [];
$facet_values = [];

foreach ( $facet_types as $slug => $args ) {

	if ( empty( $args['icons']['large'] ) ) {
		$icon = ! empty( $args['type'] ) && 'load' === $args['type'] ? 'load-action' : 'filter-action';
		$icon = Helpers::get_icon( $icon, true );
	} else {
		$icon = $args['icons']['large'];
	}

	$facet_icons[ $slug ] = $icon;

}

// Query settings.
if ( $facet_id > 0 ) {

	$facet = Database::query_row(
		[
			'select' => 'slug, name, settings',
			'from'   => 'facets',
			'id'     => $facet_id,
		]
	);

	$facet_name   = $facet['name'];
	$facet_slug   = $facet['slug'];
	$facet_values = $facet['settings'];
	$facet_values = json_decode( $facet_values, true );

}

// Prepare select options.
$facet_options = [];
$term_options  = [];
$meta_options  = [];
$taxonomies    = Helpers::get_taxonomies_list();

if ( ! wp_doing_ajax() ) {

	// Prepare facet select options.
	$facet_ids     = ! empty( $facet_values['reset_facet'] ) ? $facet_values['reset_facet'] : [];
	$facet_options = Helpers::get_facets( $facet_ids );

	// Prepare term select options.
	$parent       = ! empty( $facet_values['parent'] ) ? $facet_values['parent'] : [];
	$include      = ! empty( $facet_values['include'] ) ? $facet_values['include'] : [];
	$exclude      = ! empty( $facet_values['exclude'] ) ? $facet_values['exclude'] : [];
	$term_ids     = array_merge( (array) $parent, $include, $exclude );
	$term_options = Helpers::get_terms( $term_ids, [] );

	// Get 3rd party custom meta keys.
	$meta_lower   = ! empty( $facet_values['meta_key'] ) ? $facet_values['meta_key'] : false;
	$meta_upper   = ! empty( $facet_values['meta_key_upper'] ) ? $facet_values['meta_key_upper'] : false;
	$meta_options = array_merge( [ $meta_lower => $meta_lower ], [ $meta_upper => $meta_upper ] );
	$meta_options = array_replace_recursive( $meta_options, apply_filters( 'wp_grid_builder/custom_fields', [], 'key' ) );
	$meta_options = array_filter( array_replace_recursive( $meta_options, apply_filters( 'wp_grid_builder/custom_fields', [], 'name' ) ) );

}

$naming = [
	// name.
	[
		'id'          => 'name',
		'type'        => 'text',
		'label'       => __( 'Facet Name', 'wp-grid-builder' ),
		'placeholder' => __( 'Enter a facet name', 'wp-grid-builder' ),
		'value'       => $facet_name,
		'width'       => 380,
	],
	// slug.
	[
		'id'          => 'slug',
		'type'        => 'text',
		'label'       => __( 'Facet Slug', 'wp-grid-builder' ),
		'placeholder' => __( 'Enter a facet slug', 'wp-grid-builder' ),
		'tooltip'     => __( 'Slug is used as query string in the url to dynamically filter content. The slug must be unique.', 'wp-grid-builder' ),
		'value'       => $facet_slug,
		'width'       => 380,
	],
	// title.
	[
		'id'          => 'title',
		'type'        => 'text',
		'label'       => __( 'Facet Title', 'wp-grid-builder' ),
		'placeholder' => __( 'Enter a facet title', 'wp-grid-builder' ),
		'tooltip'     => __( 'Title is optional and it is displayed above the facet.', 'wp-grid-builder' ),
		'width'       => 380,
	],
	// id.
	[
		'id'       => 'id',
		'type'     => 'text',
		'label'    => __( 'Generated CSS Class', 'wp-grid-builder' ),
		'tooltip'  => __( 'The facet ID is used as CSS class name for your facet.', 'wp-grid-builder' ),
		'width'    => 380,
		'disabled' => true,
		'value'    => empty( $facet_id ) ? __( 'Please save the facet to generate the CSS class.', 'wp-grid-builder' ) : 'wpgb-facet-' . $facet_id,
	],
	// shortcode.
	[
		'id'       => 'shortcode',
		'type'     => 'text',
		'label'    => __( 'Generated Shortcode', 'wp-grid-builder' ),
		'tooltip'  => __( 'Copy/paste this shortcode anywhere in a post/page to display a grid.', 'wp-grid-builder' ),
		'width'    => 380,
		'disabled' => true,
		'value'    => empty( $facet_id ) ? __( 'Please save the facet to generate the shortcode.', 'wp-grid-builder' ) : '[wpgb_facet id="' . $facet_id . '" grid="0"]',
	],
];

$facet_action = [
	// action.
	[
		'id'      => 'action',
		'type'    => 'radio',
		'options' => [
			'filter' => __( 'Filter', 'wp-grid-builder' ),
			'load'   => __( 'Load', 'wp-grid-builder' ),
			'sort'   => __( 'Sort', 'wp-grid-builder' ),
			'apply'  => __( 'Apply', 'wp-grid-builder' ),
			'reset'  => __( 'Reset', 'wp-grid-builder' ),
		],
		'icons'   => [
			'filter' => Helpers::get_icon( 'filter-action', true ),
			'load'   => Helpers::get_icon( 'load-action', true ),
			'sort'   => Helpers::get_icon( 'sort-facet-large', true ),
			'apply'  => Helpers::get_icon( 'apply-facet-large', true ),
			'reset'  => Helpers::get_icon( 'reset-facet-large', true ),
		],
	],
];

$filter_options = array_filter(
	$facet_types,
	function( $facet ) {
		return ! empty( $facet['type'] ) && ! empty( $facet['name'] ) && 'filter' === $facet['type'];
	}
);

$facet_type = [
	// filter_type.
	[
		'id'      => 'filter_type',
		'type'    => 'radio',
		'options' => array_map(
			function( $option ) {
				return $option['name'];
			},
			$filter_options
		),
		'icons'   => $facet_icons,
	],
];

$filter_source = [
	// source.
	[
		'id'      => 'source',
		'type'    => 'radio',
		'label'   => __( 'Data Source', 'wp-grid-builder' ),
		'options' => [
			'taxonomy' => __( 'Taxonomy', 'wp-grid-builder' ),
			'field'    => __( 'WordPress Field', 'wp-grid-builder' ),
			'metadata' => __( 'Custom Field', 'wp-grid-builder' ),
		],
	],
	// taxonomy.
	[
		'id'                => 'taxonomy',
		'type'              => 'select',
		'label'             => __( 'Taxonomy', 'wp-grid-builder' ),
		'options'           => $taxonomies,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'taxonomy',
			],
		],
	],
	// parent.
	[
		'id'                => 'parent',
		'type'              => 'select',
		'label'             => __( 'Parent Term', 'wp-grid-builder' ),
		'placeholder'       => _x( 'None', 'Parent Term default value', 'wp-grid-builder' ),
		'tooltip'           => __( 'Parent term to retrieve direct-child terms of.', 'wp-grid-builder' ),
		'async'             => 'search_terms',
		'options'           => $term_options,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'taxonomy',
			],
			[
				'field'   => 'taxonomy',
				'compare' => '!==',
				'value'   => '',
			],
		],
	],
	// include.
	[
		'id'                => 'include',
		'type'              => 'select',
		'label'             => __( 'Include Terms', 'wp-grid-builder' ),
		'placeholder'       => _x( 'None', 'Include Terms default value', 'wp-grid-builder' ),
		'async'             => 'search_terms',
		'multiple'          => true,
		'options'           => $term_options,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'taxonomy',
			],
			[
				'field'   => 'taxonomy',
				'compare' => '!==',
				'value'   => '',
			],
			[
				'field'   => 'exclude',
				'compare' => '==',
				'value'   => '',
			],
		],
	],
	// exclude.
	[
		'id'                => 'exclude',
		'type'              => 'select',
		'label'             => __( 'Exclude Terms', 'wp-grid-builder' ),
		'placeholder'       => _x( 'None', 'Exclude Terms default value', 'wp-grid-builder' ),
		'async'             => 'search_terms',
		'multiple'          => true,
		'options'           => $term_options,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'taxonomy',
			],
			[
				'field'   => 'taxonomy',
				'compare' => '!==',
				'value'   => '',
			],
			[
				'field'   => 'include',
				'compare' => '==',
				'value'   => '',
			],
		],
	],
	// field_type.
	[
		'id'                => 'field_type',
		'type'              => 'radio',
		'label'             => __( 'Field Type', 'wp-grid-builder' ),
		'options'           => [
			'post' => __( 'Post Field', 'wp-grid-builder' ),
			'user' => __( 'User Field', 'wp-grid-builder' ),
			'term' => __( 'Term Field', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			'relation' => 'OR',
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'field',
			],
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
		],
	],
	// post_field.
	[
		'id'                => 'post_field',
		'type'              => 'select',
		'label'             => __( 'Post Field', 'wp-grid-builder' ),
		'options'           => [
			'post_type'     => __( 'Post type', 'wp-grid-builder' ),
			'post_date'     => __( 'Post date', 'wp-grid-builder' ),
			'post_modified' => __( 'Post modified date', 'wp-grid-builder' ),
			'post_title'    => __( 'Post title', 'wp-grid-builder' ),
			'post_author'   => __( 'Post author', 'wp-grid-builder' ),
		],
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'field',
			],
			[
				'field'   => 'field_type',
				'compare' => '===',
				'value'   => 'post',
			],
		],
	],
	// user_field.
	[
		'id'                => 'user_field',
		'type'              => 'select',
		'label'             => __( 'User Field', 'wp-grid-builder' ),
		'options'           => [
			'display_name' => __( 'User display name', 'wp-grid-builder' ),
			'first_name'   => __( 'User first name', 'wp-grid-builder' ),
			'last_name'    => __( 'User last name', 'wp-grid-builder' ),
			'nickname'     => __( 'User nickname', 'wp-grid-builder' ),
			'roles'        => __( 'User roles', 'wp-grid-builder' ),
		],
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'field',
			],
			[
				'field'   => 'field_type',
				'compare' => '===',
				'value'   => 'user',
			],
		],
	],
	// term_field.
	[
		'id'                => 'term_field',
		'type'              => 'select',
		'label'             => __( 'Term Field', 'wp-grid-builder' ),
		'options'           => [
			'name'       => __( 'Term name', 'wp-grid-builder' ),
			'slug'       => __( 'Term slug', 'wp-grid-builder' ),
			'taxonomy'   => __( 'Term taxonomy', 'wp-grid-builder' ),
			'term_group' => __( 'Term group', 'wp-grid-builder' ),
		],
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'field',
			],
			[
				'field'   => 'field_type',
				'compare' => '===',
				'value'   => 'term',
			],
		],
	],
	// meta_key.
	[
		'id'                => 'meta_key',
		'type'              => 'select',
		'label'             => __( 'Custom Field', 'wp-grid-builder' ),
		'placeholder'       => __( 'Enter a field name', 'wp-grid-builder' ),
		'search'            => true,
		'async'             => 'search_custom_fields',
		'width'             => 380,
		'options'           => $meta_options,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
		],
	],
	// meta_key_upper.
	[
		'id'                => 'meta_key_upper',
		'type'              => 'select',
		'label'             => __( 'Custom Field (Upper Limit)', 'wp-grid-builder' ),
		'tooltip'           => __( 'Use another custom field as upper limit (optional).', 'wp-grid-builder' ),
		'placeholder'       => __( 'Enter a field name', 'wp-grid-builder' ),
		'search'            => true,
		'async'             => 'search_custom_fields',
		'width'             => 380,
		'options'           => $meta_options,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
			[
				'field'   => 'filter_type',
				'compare' => 'IN',
				'value'   => [ 'range', 'date' ],
			],
		],
	],
	// compare_type.
	[
		'id'                => 'compare_type',
		'type'              => 'select',
		'label'             => __( 'Compare Type', 'wp-grid-builder' ),
		'tooltip'           => __( 'Compares the values of the data source to the range of values selected by the user.', 'wp-grid-builder' ),
		'width'             => 380,
		'options'           => [
			'inside'    => __( 'Inside selected range', 'wp-grid-builder' ),
			'surround'  => __( 'Surround selected range', 'wp-grid-builder' ),
			'intersect' => __( 'Intersect selected range', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
			[
				'field'   => 'filter_type',
				'compare' => 'IN',
				'value'   => [ 'range', 'date' ],
			],
		],
	],
];

$filter_number = [
	// limit.
	[
		'id'                => 'limit',
		'type'              => 'number',
		'label'             => __( 'Choices Number', 'wp-grid-builder' ),
		'tooltip'           => __( 'Maximum number of choices displayed in the filter.', 'wp-grid-builder' ),
		'min'               => 1,
		'max'               => 9999,
		'width'             => 68,
		'conditional_logic' => [
			[
				'field'   => 'filter_type',
				'compare' => 'NOT IN',
				'value'   => [ 'range', 'rating' ],
			],
		],
	],
	// display_limit.
	[
		'id'                => 'display_limit',
		'type'              => 'number',
		'label'             => __( 'Limit Choices Number', 'wp-grid-builder' ),
		'tooltip'           => __( 'Show a toggle button if the limit is inferior to the number of choices set above.', 'wp-grid-builder' ),
		'min'               => 1,
		'max'               => 9999,
		'width'             => 68,
		'conditional_logic' => [
			[
				'field'   => 'filter_type',
				'compare' => 'NOT IN',
				'value'   => [ 'select', 'range', 'rating', 'search', 'autocomplete', 'selection' ],
			],
		],
	],
	// show_more_label.
	[
		'id'                => 'show_more_label',
		'type'              => 'text',
		'label'             => __( 'Expand Button label', 'wp-grid-builder' ),
		'tooltip'           => __( 'Button label used to reveal remaining choices in the list. [number] shortcode can be added to display the number of remaining choices.', 'wp-grid-builder' ),
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'filter_type',
				'compare' => 'NOT IN',
				'value'   => [ 'select', 'range', 'rating', 'search', 'autocomplete', 'selection' ],
			],
		],
	],
	// show_less_label.
	[
		'id'                => 'show_less_label',
		'type'              => 'text',
		'label'             => __( 'Collapse Button label', 'wp-grid-builder' ),
		'tooltip'           => __( 'Button label used to collapse the list of choices.', 'wp-grid-builder' ),
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'filter_type',
				'compare' => 'NOT IN',
				'value'   => [ 'select', 'range', 'rating', 'search', 'autocomplete', 'selection' ],
			],
		],
	],
];

$filter_order = [
	// color_order.
	[
		'id'                => 'color_order',
		'type'              => 'toggle',
		'label'             => __( 'Color Order', 'wp-grid-builder' ),
		'tooltip'           => __( 'Use the color options below as custom order.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'filter_type',
				'compare' => '===',
				'value'   => 'color',
			],
		],
	],
	// orderby.
	[
		'id'      => 'orderby',
		'type'    => 'select',
		'label'   => __( 'Order By', 'wp-grid-builder' ),
		'options' => [
			'count'       => __( 'Choice Count', 'wp-grid-builder' ),
			'facet_name'  => __( 'Choice Name', 'wp-grid-builder' ),
			'facet_value' => __( 'Choice Value', 'wp-grid-builder' ),
			'facet_order' => __( 'Term Order', 'wp-grid-builder' ),
		],
		'width'   => 380,
	],
	// order.
	[
		'id'      => 'order',
		'type'    => 'radio',
		'label'   => __( 'Order', 'wp-grid-builder' ),
		'options' => [
			'DESC' => __( 'Descending', 'wp-grid-builder' ),
			'ASC'  => __( 'Ascending', 'wp-grid-builder' ),
		],
		'width'   => 380,
	],
];

$filter_logic = [
	// logic.
	[
		'id'      => 'logic',
		'type'    => 'radio',
		'label'   => __( 'Logical Combination', 'wp-grid-builder' ),
		'options' => [
			'AND' => __( 'AND', 'wp-grid-builder' ),
			'OR'  => __( 'OR', 'wp-grid-builder' ),
		],
	],
	// multiple.
	[
		'id'                => 'multiple',
		'type'              => 'toggle',
		'label'             => __( 'Multiple Selection', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'filter_type',
				'compare' => 'NOT IN',
				'value'   => [ 'checkbox' ],
			],
		],
	],
];

$filter_display = [
	// hierarchical.
	[
		'id'                => 'hierarchical',
		'type'              => 'toggle',
		'label'             => __( 'Hierarchical', 'wp-grid-builder' ),
		'tooltip'           => __( 'Display taxonomy terms in hierarchical list.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'filter_type',
				'compare' => 'IN',
				'value'   => [ 'checkbox', 'select' ],
			],
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'taxonomy',
			],
		],
	],
	// treeview.
	[
		'id'                => 'treeview',
		'type'              => 'toggle',
		'label'             => __( 'Navigation Treeview', 'wp-grid-builder' ),
		'tooltip'           => __( 'Display toggle buttons on parent items that hold children. Tree items can be expanded/collapsed to reveal/hide child items.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'filter_type',
				'compare' => 'IN',
				'value'   => [ 'checkbox' ],
			],
			[
				'field'   => 'hierarchical',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'taxonomy',
			],
		],
	],
	// children.
	[
		'id'                => 'children',
		'type'              => 'toggle',
		'label'             => __( 'Show Children', 'wp-grid-builder' ),
		'tooltip'           => __( 'Show child terms of the selected taxonomy.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'taxonomy',
			],
			[
				'relation' => 'OR',
				[
					[
						'field'   => 'hierarchical',
						'compare' => '==',
						'value'   => 0,
					],
					[
						'field'   => 'filter_type',
						'compare' => '!==',
						'value'   => 'hierarchy',
					],
				],
				[
					'field'   => 'filter_type',
					'compare' => 'IN',
					'value'   => [ 'button', 'radio', 'color' ],
				],
			],
		],
	],
	// show_empty.
	[
		'id'                => 'show_empty',
		'type'              => 'toggle',
		'label'             => __( 'Show Empty Choices', 'wp-grid-builder' ),
		'tooltip'           => __( 'Show choices that match not results when filtered.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'filter_type',
				'compare' => '!==',
				'value'   => 'hierarchy',
			],
		],
	],
	// show_count.
	[
		'id'                => 'show_count',
		'type'              => 'toggle',
		'label'             => __( 'Show Choice Count', 'wp-grid-builder' ),
		'tooltip'           => __( 'Display the number of results associated to each choice.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'filter_type',
				'compare' => '!==',
				'value'   => 'range',
			],
		],
	],
	// all_label.
	[
		'id'                => 'all_label',
		'type'              => 'text',
		'label'             => __( 'Default Label', 'wp-grid-builder' ),
		'tooltip'           => __( 'Label of the default choice used to clear the filter selection. Leave empty to hide the default choice.', 'wp-grid-builder' ),
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'action',
				'compare' => '===',
				'value'   => 'filter',
			],
			[
				'field'   => 'filter_type',
				'compare' => 'IN',
				'value'   => [ 'radio', 'hierarchy', 'button', 'rating', 'az_index' ],
			],
		],
	],
];

$select = [
	// select_placeholder.
	[
		'id'                => 'select_placeholder',
		'type'              => 'text',
		'label'             => __( 'Placeholder', 'wp-grid-builder' ),
		'placeholder'       => __( 'Enter a placeholder', 'wp-grid-builder' ),
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'action',
				'compare' => '===',
				'value'   => 'filter',
			],
			[
				'field'   => 'filter_type',
				'compare' => '===',
				'value'   => 'select',
			],
		],
	],
	// combobox.
	[
		'id'      => 'combobox',
		'type'    => 'toggle',
		'label'   => __( 'Combobox', 'wp-grid-builder' ),
		'tooltip' => __( 'Replace the browser dropdown by a JavaScript based combobox including a search field.', 'wp-grid-builder' ),
	],
	// clearable.
	[
		'id'                => 'clearable',
		'type'              => 'toggle',
		'label'             => __( 'Clearable List', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'combobox',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
	// searchable.
	[
		'id'                => 'searchable',
		'type'              => 'toggle',
		'label'             => __( 'Searchable List', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'combobox',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
	// async.
	[
		'id'                => 'async',
		'type'              => 'toggle',
		'label'             => __( 'Asynchronous Search', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'action',
				'compare' => '===',
				'value'   => 'filter',
			],
			[
				'field'   => 'combobox',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'searchable',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
	// no_results.
	[
		'id'                => 'no_results',
		'type'              => 'text',
		'label'             => __( 'No Results Message', 'wp-grid-builder' ),
		'placeholder'       => __( 'No Results Found.', 'wp-grid-builder' ),
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'combobox',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
	// loading.
	[
		'id'                => 'loading',
		'type'              => 'text',
		'label'             => __( 'Loading Message', 'wp-grid-builder' ),
		'placeholder'       => __( 'Loading...', 'wp-grid-builder' ),
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'action',
				'compare' => '===',
				'value'   => 'filter',
			],
			[
				'field'   => 'combobox',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'searchable',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'async',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
	// search.
	[
		'id'                => 'search',
		'type'              => 'text',
		'label'             => __( 'Search Message', 'wp-grid-builder' ),
		'placeholder'       => __( 'Please enter 1 or more characters.', 'wp-grid-builder' ),
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'action',
				'compare' => '===',
				'value'   => 'filter',
			],
			[
				'field'   => 'combobox',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'searchable',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'async',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
	// select-info.
	[
		'id'                => 'select-info',
		'type'              => 'info',
		'warning'           => true,
		'content'           =>
		'<strong>' . __( 'Asynchronous search cannot maintain hierarchical listing.', 'wp-grid-builder' ) . '</strong><br>' .
		__( 'Because a search may not match parents and children in one query, this is not possible to maintain a hierarchical listing.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'hierarchical',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'action',
				'compare' => '===',
				'value'   => 'filter',
			],
			[
				'field'   => 'combobox',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'searchable',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'async',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
];

$range = [
	// prefix.
	[
		'id'           => 'prefix',
		'type'         => 'text',
		'label'        => __( 'Value Prefix', 'wp-grid-builder' ),
		'width'        => 380,
		'white_spaces' => true,
		'angle_quotes' => true,
	],
	// suffix.
	[
		'id'           => 'suffix',
		'type'         => 'text',
		'label'        => __( 'Value Suffix', 'wp-grid-builder' ),
		'width'        => 380,
		'white_spaces' => true,
		'angle_quotes' => true,
	],
	// step.
	[
		'id'    => 'step',
		'type'  => 'number',
		'label' => __( 'Step Interval', 'wp-grid-builder' ),
		'width' => 380,
		'min'   => 0.0001,
		'max'   => 999999,
		'step'  => 0.0001,
	],
	// thousands_separator.
	[
		'id'           => 'thousands_separator',
		'type'         => 'text',
		'label'        => __( 'Thousands Separator', 'wp-grid-builder' ),
		'width'        => 380,
		'white_spaces' => true,
	],
	// decimal_separator.
	[
		'id'           => 'decimal_separator',
		'type'         => 'text',
		'label'        => __( 'Decimal Separator', 'wp-grid-builder' ),
		'width'        => 380,
		'white_spaces' => true,
	],
	// decimal_places.
	[
		'id'    => 'decimal_places',
		'type'  => 'number',
		'label' => __( 'Decimal Places', 'wp-grid-builder' ),
		'width' => 380,
	],
	// reset_range.
	[
		'id'      => 'reset_range',
		'type'    => 'text',
		'label'   => __( 'Reset Label', 'wp-grid-builder' ),
		'tooltip' => __( 'Leave empty to hide the reset button.', 'wp-grid-builder' ),
		'width'   => 380,
	],
];

$date = [
	// date_type.
	[
		'id'      => 'date_type',
		'type'    => 'select',
		'label'   => __( 'Date Type', 'wp-grid-builder' ),
		'options' => [
			'single' => __( 'Single Date', 'wp-grid-builder' ),
			'range'  => __( 'Range of Dates', 'wp-grid-builder' ),
		],
		'width'   => 380,
	],
	// date_format.
	[
		'id'          => 'date_format',
		'type'        => 'text',
		'label'       => __( 'Date Format', 'wp-grid-builder' ),
		'placeholder' => 'Y-m-d',
		'width'       => 380,
	],
	// date_placeholder.
	[
		'id'          => 'date_placeholder',
		'type'        => 'text',
		'label'       => __( 'Date Placeholder', 'wp-grid-builder' ),
		'placeholder' => __( 'Enter a placeholder', 'wp-grid-builder' ),
		'description' => sprintf(
			/* Translators: %s: Flatpickr script. */
			__( 'See available <a href="%s" rel="external noopener noreferrer" target="_blank">formatting tokens</a> of Flatpickr library.', 'wp-grid-builder' ),
			'https://flatpickr.js.org/formatting/'
		),
		'width'       => 380,
	],
	// selection-info.
	[
		'id'      => 'date-info',
		'type'    => 'info',
		'content' => sprintf(
			/* Translators: %1$s: Dat format 1, %2$s: Date format 2 */
			__( 'Please, make sure your dates are stored as <strong>%1$s</strong> or <strong>%2$s</strong> formats.', 'wp-grid-builder' ) . '<br>' .
			__( 'This is required to correctly filter dates from custom fields. Post dates natively use the right format.', 'wp-grid-builder' ),
			'Y-m-d',
			'Y-m-d h:i:s'
		),
	],
];

$search_facet = [
	// search_placeholder.
	[
		'id'    => 'search_placeholder',
		'type'  => 'text',
		'label' => __( 'Search Placeholder', 'wp-grid-builder' ),
		'width' => 380,
	],
	// search_engine.
	[
		'id'       => 'search_engine',
		'type'     => 'select',
		'label'    => __( 'Search Engine', 'wp-grid-builder' ),
		'options'  => apply_filters(
			'wp_grid_builder/facet/search_engines',
			[
				'wordpress'  => 'WordPress',
				'relevanssi' => 'Relevanssi',
				'searchwp'   => 'SearchWP',
			]
		),
		'disabled' => [
			'relevanssi' => ! function_exists( 'relevanssi_search' ),
			'searchwp'   => ! class_exists( 'SWP_Query' ),
		],
		'width'    => 380,
	],
	// search_number.
	[
		'id'      => 'search_number',
		'type'    => 'number',
		'label'   => __( 'Search Number', 'wp-grid-builder' ),
		'tooltip' => __( 'Number of results to search. The number of results can significantly impact performance. Generally, a large amount of results is not necessary and may confuse users which will search again to narrow down results.', 'wp-grid-builder' ),
		'min'     => -1,
		'max'     => 9999,
		'width'   => 68,
	],
	// search_relevancy.
	[
		'id'      => 'search_relevancy',
		'type'    => 'toggle',
		'label'   => __( 'Search Relevancy', 'wp-grid-builder' ),
		'tooltip' => __( 'Keep search order relevance. Can be useful when using a plugin like SearchWP with weighting attributes.', 'wp-grid-builder' ),
		'width'   => 380,
	],
	// instant_search.
	[
		'id'      => 'instant_search',
		'type'    => 'toggle',
		'label'   => __( 'Instant Search', 'wp-grid-builder' ),
		'tooltip' => __( 'Update results and facets while typing.', 'wp-grid-builder' ),
		'width'   => 380,
	],
];

$autocomplete = [
	// acplt_placeholder.
	[
		'id'    => 'acplt_placeholder',
		'type'  => 'text',
		'label' => __( 'Search Placeholder', 'wp-grid-builder' ),
		'width' => 380,
	],
	// acplt_min_length.
	[
		'id'      => 'acplt_min_length',
		'type'    => 'number',
		'label'   => __( 'Min Characters', 'wp-grid-builder' ),
		'tooltip' => __( 'Minimum number of characters to trigger a search.', 'wp-grid-builder' ),
		'min'     => 1,
		'max'     => 999,
	],
	// acplt_relevance.
	[
		'id'      => 'acplt_relevance',
		'type'    => 'toggle',
		'label'   => __( 'Sort by Relevance', 'wp-grid-builder' ),
		'tooltip' => __( 'Sort queried choices (according to choices order) by relevance. Choices will be sorted by matched string position, alphabetically and by choice name length.', 'wp-grid-builder' ),
	],
	// acplt_auto_focus.
	[
		'id'      => 'acplt_auto_focus',
		'type'    => 'toggle',
		'label'   => __( 'Auto Focus', 'wp-grid-builder' ),
		'tooltip' => __( 'Automatically focus first suggestion in the list.', 'wp-grid-builder' ),
	],
	// acplt_highlight.
	[
		'id'      => 'acplt_highlight',
		'type'    => 'toggle',
		'label'   => __( 'Highlight', 'wp-grid-builder' ),
		'tooltip' => __( 'Highlight matched string in suggestion.', 'wp-grid-builder' ),
	],
	// acplt_match_all.
	[
		'id'                => 'acplt_match_all',
		'type'              => 'toggle',
		'label'             => __( 'Highlight All Matches', 'wp-grid-builder' ),
		'tooltip'           => __( 'Highlights all matching strings in suggestion.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'acplt_highlight',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
];

$color = [
	[
		'id'       => 'color_options',
		'type'     => 'repeater',
		'limit'    => 999,
		'add_text' => __( 'Add Color', 'wp-grid-builder' ),
		'fields'   => [
			// color_value.
			[
				'id'          => 'color_value',
				'type'        => 'text',
				'label'       => __( 'Choice Value', 'wp-grid-builder' ),
				'width'       => 120,
				'placeholder' => __( 'Enter a value', 'wp-grid-builder' ),
			],
			// color_label.
			[
				'id'          => 'color_label',
				'type'        => 'text',
				'label'       => __( 'Choice Label', 'wp-grid-builder' ),
				'width'       => 120,
				'placeholder' => __( 'Default', 'wp-grid-builder' ),
			],
			// background_color.
			[
				'id'       => 'background_color',
				'type'     => 'color',
				'label'    => __( 'Color', 'wp-grid-builder' ),
				'gradient' => true,
			],
			// or.
			[
				'id'      => 'or',
				'type'    => 'custom',
				'content' => __( 'or', 'wp-grid-builder' ),
			],
			// background_image.
			[
				'id'          => 'background_image',
				'type'        => 'file',
				'label'       => __( 'Image', 'wp-grid-builder' ),
				'width'       => 320,
				'mime_type'   => 'image',
				'placeholder' => __( 'Enter URL', 'wp-grid-builder' ),
			],
		],
	],
	// color_info.
	[
		'id'      => 'color_info',
		'type'    => 'info',
		'content' =>
		__( 'Above settings (optional) allow you to define colors and/or labels based on existing choice values.', 'wp-grid-builder' ) . '<br>' .
		__( 'If choice values are valid CSS colors, they will be automatically displayed.', 'wp-grid-builder' ) . '<br>' .
		__( 'The choice value corresponds to the term slug or the custom field value.', 'wp-grid-builder' ) . '<br>' .
		__( 'This value can also be found in the url when the grid is filtered by colors.', 'wp-grid-builder' ),
	],
];

$az_index = [
	[
		'id'      => 'alphabetical_index',
		'type'    => 'text',
		'label'   => __( 'Alphabetical Index', 'wp-grid-builder' ),
		'tooltip' => __( 'Comma separated list of letters used to filter.', 'wp-grid-builder' ),
		'width'   => 380,
	],
	[
		'id'          => 'numeric_index',
		'type'        => 'text',
		'label'       => __( 'Numeric Index', 'wp-grid-builder' ),
		'tooltip'     => __( 'Comma separated list of numbers used to filter.', 'wp-grid-builder' ),
		'description' => __( '# (hashtag) can be used as an alias to filter by all numbers.', 'wp-grid-builder' ),
		'width'       => 380,
	],
];

$selection = [
	// selection_info.
	[
		'id'      => 'selection-info',
		'type'    => 'info',
		'content' =>
		__( 'Selection facet outputs a list of all selected filter choices from each filter facet.', 'wp-grid-builder' ) . '<br>' .
		__( 'Users can unset choices of each filter directly from the selection facet.', 'wp-grid-builder' ),
	],
];

$load_options = array_filter(
	$facet_types,
	function( $facet ) {
		return ! empty( $facet['type'] ) && ! empty( $facet['name'] ) && 'load' === $facet['type'];
	}
);

$load_type = [
	// load_type.
	[
		'id'      => 'load_type',
		'type'    => 'radio',
		'options' => array_map(
			function( $option ) {
				return $option['name'];
			},
			$load_options
		),
		'icons'   => $facet_icons,
	],
];

$pagination = [
	// pagination.
	[
		'id'      => 'pagination',
		'type'    => 'radio',
		'label'   => __( 'Pagination Type', 'wp-grid-builder' ),
		'options' => [
			'numbered'  => _x( 'Numbered', 'Pagination type', 'wp-grid-builder' ),
			'prev_next' => __( 'Navigation Buttons', 'wp-grid-builder' ),
		],
	],
	// show_all.
	[
		'id'                => 'show_all',
		'type'              => 'toggle',
		'label'             => __( 'All Page Numbers', 'wp-grid-builder' ),
		'tooltip'           => __( 'Show all of the pages instead of a short list of the pages near the current page.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'pagination',
				'compare' => '!==',
				'value'   => 'prev_next',
			],
		],
	],
	// mid_size.
	[
		'id'                => 'mid_size',
		'type'              => 'number',
		'label'             => __( 'Middle Pages Size', 'wp-grid-builder' ),
		'tooltip'           => __( 'How many numbers to either side of current page, but not including current page.', 'wp-grid-builder' ),
		'min'               => 1,
		'max'               => 9999,
		'step'              => 1,
		'width'             => 68,
		'conditional_logic' => [
			[
				'field'   => 'show_all',
				'compare' => '!=',
				'value'   => 1,
			],
			[
				'field'   => 'pagination',
				'compare' => '!==',
				'value'   => 'prev_next',
			],
		],
	],
	// end_size.
	[
		'id'                => 'end_size',
		'type'              => 'number',
		'label'             => __( 'End Pages Size', 'wp-grid-builder' ),
		'tooltip'           => __( 'How many numbers on either the start and the end pagination edges.', 'wp-grid-builder' ),
		'min'               => 1,
		'max'               => 9999,
		'step'              => 1,
		'width'             => 68,
		'conditional_logic' => [
			[
				'field'   => 'show_all',
				'compare' => '!=',
				'value'   => 1,
			],
			[
				'field'   => 'pagination',
				'compare' => '!==',
				'value'   => 'prev_next',
			],
		],
	],
	// dots_page.
	[
		'id'                => 'dots_page',
		'type'              => 'text',
		'label'             => __( 'Dots Page Label', 'wp-grid-builder' ),
		'placeholder'       => '&hellip;',
		'width'             => 68,
		'conditional_logic' => [
			[
				'field'   => 'show_all',
				'compare' => '!=',
				'value'   => 1,
			],
			[
				'field'   => 'pagination',
				'compare' => '!==',
				'value'   => 'prev_next',
			],
		],
	],
	// prev_next.
	[
		'id'                => 'prev_next',
		'type'              => 'toggle',
		'label'             => __( 'Navigation Buttons', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'pagination',
				'compare' => '!==',
				'value'   => 'prev_next',
			],
		],
	],
	// prev_text.
	[
		'id'                => 'prev_text',
		'type'              => 'text',
		'label'             => __( 'Prev Button Label', 'wp-grid-builder' ),
		'placeholder'       => __( 'Enter a label', 'wp-grid-builder' ),
		'width'             => 280,
		'conditional_logic' => [
			'relation' => 'OR',
			[
				'field'   => 'prev_next',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'pagination',
				'compare' => '===',
				'value'   => 'prev_next',
			],
		],
	],
	// next_text.
	[
		'id'                => 'next_text',
		'type'              => 'text',
		'label'             => __( 'Next Button Label', 'wp-grid-builder' ),
		'placeholder'       => __( 'Enter a label', 'wp-grid-builder' ),
		'width'             => 280,
		'conditional_logic' => [
			'relation' => 'OR',
			[
				'field'   => 'prev_next',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'pagination',
				'compare' => '===',
				'value'   => 'prev_next',
			],
		],
	],
	// scroll_to_top.
	[
		'id'      => 'scroll_to_top',
		'type'    => 'toggle',
		'label'   => __( 'Scroll to Top', 'wp-grid-builder' ),
		'tooltip' => __( 'Smooth scrolling behavior is only supported by modern browsers, and depends on your site\'s scrolling behavior.', 'wp-grid-builder' ),
	],
	// scroll_to_top_offset.
	[
		'id'                => 'scroll_to_top_offset',
		'type'              => 'number',
		'label'             => __( 'Scroll Offset (px)', 'wp-grid-builder' ),
		'min'               => -9999,
		'max'               => 9999,
		'step'              => 1,
		'width'             => 68,
		'conditional_logic' => [
			[
				'field'   => 'scroll_to_top',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
];

$load_more = [
	// load_posts_number.
	[
		'id'    => 'load_posts_number',
		'type'  => 'number',
		'label' => __( 'Number to Load More', 'wp-grid-builder' ),
		'min'   => 0,
		'max'   => 100,
		'step'  => 1,
		'width' => 64,
	],
	// load_more_event.
	[
		'id'      => 'load_more_event',
		'type'    => 'radio',
		'label'   => __( 'Trigger Loading on', 'wp-grid-builder' ),
		'options' => [
			'onclick'  => __( 'Click', 'wp-grid-builder' ),
			'onscroll' => __( 'Scroll', 'wp-grid-builder' ),
		],
	],
	// load_more_remain.
	[
		'id'      => 'load_more_remain',
		'type'    => 'toggle',
		'label'   => __( 'Show Remaining Results', 'wp-grid-builder' ),
		'tooltip' => __( 'Displays the number of remaining results in the load more button.', 'wp-grid-builder' ),
	],
	// load_more_text.
	[
		'id'          => 'load_more_text',
		'type'        => 'text',
		'label'       => __( 'Button Label', 'wp-grid-builder' ),
		'placeholder' => __( 'Enter a label', 'wp-grid-builder' ),
		'tooltip'     => __( '[number] shortcode allows to display the number of remaining results.', 'wp-grid-builder' ),
		'width'       => 280,
	],
	// loading_text.
	[
		'id'          => 'loading_text',
		'type'        => 'text',
		'label'       => __( 'Loading Message', 'wp-grid-builder' ),
		'placeholder' => __( 'Enter a Message', 'wp-grid-builder' ),
		'width'       => 280,
	],
];

$per_page_options = [
	// per_page_options.
	[
		'id'          => 'per_page_options',
		'type'        => 'text',
		'label'       => __( 'Per Page Options', 'wp-grid-builder' ),
		'placeholder' => __( 'e.g.: 10, 25, 50, 100', 'wp-grid-builder' ),
		'tooltip'     => __( 'A comma separated list of choices.', 'wp-grid-builder' ),
		'width'       => 380,
	],
];

$result_count = [
	// result_count_singular.
	[
		'id'          => 'result_count_singular',
		'type'        => 'text',
		'label'       => __( 'Count text (Singular)', 'wp-grid-builder' ),
		'placeholder' => __( 'e.g.: [from] - [to] of [total] post', 'wp-grid-builder' ),
		'tooltip'     => __( 'The following shortcodes [from], [to], and [total] can be included in the text.', 'wp-grid-builder' ),
		'width'       => 380,
	],
	// result_count_plural.
	[
		'id'          => 'result_count_plural',
		'type'        => 'text',
		'label'       => __( 'Count text (Plural)', 'wp-grid-builder' ),
		'placeholder' => __( 'e.g.: [from] - [to] of [total] posts', 'wp-grid-builder' ),
		'tooltip'     => __( 'The following shortcodes [from], [to], and [total] can be included in the text.', 'wp-grid-builder' ),
		'width'       => 380,
	],
];

$reset = [
	// reset_label.
	[
		'id'    => 'reset_label',
		'type'  => 'text',
		'label' => __( 'Button Label', 'wp-grid-builder' ),
		'width' => 380,
	],
	// reset_facet.
	[
		'id'          => 'reset_facet',
		'type'        => 'select',
		'label'       => __( 'Reset Facets', 'wp-grid-builder' ),
		'width'       => 380,
		'search'      => true,
		'multiple'    => true,
		'options'     => $facet_options,
		'async'       => 'search_facets',
		'placeholder' => _x( 'All', 'Reset Facets default value', 'wp-grid-builder' ),
	],
];

$sort = [
	// sort_options.
	[
		'id'       => 'sort_options',
		'type'     => 'repeater',
		'limit'    => 20,
		'add_text' => __( 'Add Option', 'wp-grid-builder' ),
		'fields'   => [
			// label.
			[
				'id'          => 'label',
				'type'        => 'text',
				'label'       => __( 'Label', 'wp-grid-builder' ),
				'width'       => 160,
				'placeholder' => __( 'Enter a label', 'wp-grid-builder' ),
			],
			// orderby.
			'orderby'  => [
				'id'          => 'orderby',
				'type'        => 'select',
				'label'       => __( 'Order By', 'wp-grid-builder' ),
				'placeholder' => __( 'None', 'wp-grid-builder' ),
				'width'       => 210,
				'options'     => apply_filters(
					'wp_grid_builder/facet/sort_options',
					[
						__( 'Post Field', 'wp-grid-builder' ) ?: 'Post Field' => [
							'ID'            => __( 'Post id', 'wp-grid-builder' ),
							'title'         => __( 'Post title', 'wp-grid-builder' ),
							'post_name'     => __( 'Post name (slug)', 'wp-grid-builder' ),
							'author'        => __( 'Post author', 'wp-grid-builder' ),
							'date'          => __( 'Post date', 'wp-grid-builder' ),
							'modified'      => __( 'Post modified date', 'wp-grid-builder' ),
							'post__in'      => __( 'Included posts', 'wp-grid-builder' ),
							'menu_order'    => __( 'Menu order', 'wp-grid-builder' ),
							'comment_count' => __( 'Number of comments', 'wp-grid-builder' ),
						],
						__( 'User Field', 'wp-grid-builder' ) ?: 'User Field' => [
							'display_name'    => __( 'User display name', 'wp-grid-builder' ),
							'user_name'       => __( 'User name', 'wp-grid-builder' ),
							'user_login'      => __( 'User login', 'wp-grid-builder' ),
							'user_nicename'   => __( 'User nicename', 'wp-grid-builder' ),
							'user_email'      => __( 'User email', 'wp-grid-builder' ),
							'user_url'        => __( 'User url', 'wp-grid-builder' ),
							'user_registered' => __( 'User registered date', 'wp-grid-builder' ),
							'user__in'        => __( 'Included users', 'wp-grid-builder' ),
							'post_count'      => __( 'Post count', 'wp-grid-builder' ),
						],
						__( 'Term Field', 'wp-grid-builder' ) ?: 'Term Field' => [
							'term_id'     => __( 'Term id', 'wp-grid-builder' ),
							'name'        => __( 'Term name', 'wp-grid-builder' ),
							'slug'        => __( 'Term slug', 'wp-grid-builder' ),
							'description' => __( 'Term description', 'wp-grid-builder' ),
							'parent'      => __( 'Term parent', 'wp-grid-builder' ),
							'term_order'  => __( 'Term order', 'wp-grid-builder' ),
							'term_group'  => __( 'Term group', 'wp-grid-builder' ),
							'count'       => __( 'Term count', 'wp-grid-builder' ),
							'term__in'    => __( 'Included terms', 'wp-grid-builder' ),
						],
						__( 'Custom Field', 'wp-grid-builder' ) ?: 'Custom Field' => [
							'meta_value'     => __( 'Custom field', 'wp-grid-builder' ),
							'meta_value_num' => __( 'Numeric custom field', 'wp-grid-builder' ),
						],
					]
				),
			],
			// order.
			'order'    => [
				'id'      => 'order',
				'type'    => 'select',
				'label'   => __( 'Direction', 'wp-grid-builder' ),
				'width'   => 100,
				'options' => [
					'desc' => __( 'DESC', 'wp-grid-builder' ),
					'asc'  => __( 'ASC', 'wp-grid-builder' ),
				],
			],
			// meta_key.
			'meta_key' => [
				'id'          => 'meta_key',
				'type'        => 'select',
				'label'       => __( 'Custom Field', 'wp-grid-builder' ),
				'placeholder' => __( 'Enter a field name', 'wp-grid-builder' ),
				'search'      => true,
				'validate'    => false,
				'width'       => 210,
				'options'     => $meta_options,
				'async'       => 'search_custom_fields',
			],
		],
	],
];

$apply = [

	// apply_redirect.
	[
		'id'    => 'apply_redirect',
		'type'  => 'toggle',
		'label' => __( 'Apply Redirect', 'wp-grid-builder' ),
		'width' => 380,
	],
	// apply_history.
	wpgb_get_option( 'history' ) ? [
		'id'                => 'apply_history',
		'type'              => 'toggle',
		'label'             => __( 'Browser\'s History', 'wp-grid-builder' ),
		'tooltip'           => __( 'Allow history navigation by pushing facet parameters in url when filtering.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'apply_redirect',
				'compare' => '==',
				'value'   => 1,
			],
		],
	] : '',
	// apply_url.
	[
		'id'                => 'apply_url',
		'type'              => 'url',
		'label'             => __( 'Redirect URL', 'wp-grid-builder' ),
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'apply_redirect',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
	// apply_label.
	[
		'id'    => 'apply_label',
		'type'  => 'text',
		'label' => __( 'Button Label', 'wp-grid-builder' ),
		'width' => 380,
	],
	// apply_excluded.
	[
		'id'                => 'apply_excluded',
		'type'              => 'select',
		'label'             => __( 'Exclude Facets', 'wp-grid-builder' ),
		'width'             => 380,
		'search'            => true,
		'multiple'          => true,
		'options'           => $facet_options,
		'async'             => 'search_facets',
		'placeholder'       => _x( 'None', 'Excluded Facets default value', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'apply_redirect',
				'compare' => '!=',
				'value'   => 1,
			],
		],
	],
];

$facet_settings = [
	'id'     => 'facet',
	'tabs'   => [
		[
			'id'       => 'naming',
			'label'    => __( 'Naming', 'wp-grid-builder' ),
			'title'    => __( 'Naming', 'wp-grid-builder' ),
			'subtitle' => __( 'Define the facet name, slug and title.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'shortcode', true ),
		],
		[
			'id'       => 'behaviour',
			'label'    => __( 'Behaviour', 'wp-grid-builder' ),
			'title'    => __( 'Behaviour', 'wp-grid-builder' ),
			'subtitle' => __( 'Set up the facet\'s action and behavior.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'facet', true ),
		],
	],
	'header' => [
		'toggle'  => true,
		'buttons' => [
			[
				'title'  => __( 'Re-index', 'wp-grid-builder' ),
				'icon'   => 'reset',
				'color'  => 'purple',
				'action' => 'index',
			],
			[
				'title'  => __( 'Save Changes', 'wp-grid-builder' ),
				'icon'   => 'save',
				'color'  => 'green',
				'action' => 'save',
			],
		],
	],
	'fields' => [
		[
			'id'     => 'naming_section',
			'tab'    => 'naming',
			'type'   => 'section',
			'fields' => $naming,
		],
		[
			'id'     => 'facet_action_section',
			'tab'    => 'behaviour',
			'type'   => 'section',
			'title'  => __( 'Facet Action', 'wp-grid-builder' ),
			'fields' => $facet_action,
		],
		[
			'id'                => 'filter_type_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Filter Type', 'wp-grid-builder' ),
			'fields'            => $facet_type,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
			],
		],
		[
			'id'                => 'filter_by_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Filter By', 'wp-grid-builder' ),
			'fields'            => $filter_source,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
				[
					'field'   => 'filter_type',
					'compare' => 'NOT IN',
					'value'   => [ 'search', 'selection' ],
				],
			],
		],
		[
			'id'                => 'filter_logic_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Filter Logic', 'wp-grid-builder' ),
			'fields'            => $filter_logic,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
				[
					'field'   => 'filter_type',
					'compare' => 'IN',
					'value'   => [ 'checkbox', 'select', 'button', 'color', 'az_index' ],
				],
			],
		],
		[
			'id'                => 'filter_display_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Filter Display', 'wp-grid-builder' ),
			'fields'            => $filter_display,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
				[
					'field'   => 'filter_type',
					'compare' => 'IN',
					'value'   => [ 'checkbox', 'radio', 'select', 'button', 'hierarchy', 'rating', 'autocomplete', 'color', 'az_index' ],
				],
			],
		],
		[
			'id'                => 'filter_number_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Choices Number', 'wp-grid-builder' ),
			'fields'            => $filter_number,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
				[
					'field'   => 'filter_type',
					'compare' => 'IN',
					'value'   => [ 'checkbox', 'radio', 'select', 'button', 'hierarchy', 'autocomplete', 'color' ],
				],
			],
		],
		[
			'id'                => 'filter_order_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Choices Order', 'wp-grid-builder' ),
			'fields'            => $filter_order,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
				[
					'field'   => 'filter_type',
					'compare' => 'IN',
					'value'   => [ 'checkbox', 'radio', 'select', 'button', 'hierarchy', 'autocomplete', 'color' ],
				],
			],
		],
		[
			'id'                => 'filter_search_behaviour_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Search Field', 'wp-grid-builder' ),
			'fields'            => $search_facet,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
				[
					'field'   => 'filter_type',
					'compare' => '===',
					'value'   => 'search',
				],
			],
		],
		[
			'id'                => 'filter_autocomplete_behaviour_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Auto-Complete', 'wp-grid-builder' ),
			'fields'            => $autocomplete,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
				[
					'field'   => 'filter_type',
					'compare' => '===',
					'value'   => 'autocomplete',
				],
			],
		],
		[
			'id'                => 'filter_range_behaviour_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Range Slider', 'wp-grid-builder' ),
			'fields'            => $range,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
				[
					'field'   => 'filter_type',
					'compare' => '===',
					'value'   => 'range',
				],
			],
		],
		[
			'id'                => 'filter_date_behaviour_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Date Picker', 'wp-grid-builder' ),
			'fields'            => $date,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
				[
					'field'   => 'filter_type',
					'compare' => '===',
					'value'   => 'date',
				],
			],
		],
		[
			'id'                => 'facet_color_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Color Options', 'wp-grid-builder' ),
			'fields'            => $color,
			'conditional_logic' => [
				'relation' => 'AND',
				[
					[
						'field'   => 'action',
						'compare' => '===',
						'value'   => 'filter',
					],
					[
						'field'   => 'filter_type',
						'compare' => '===',
						'value'   => 'color',
					],
				],
			],
		],
		[
			'id'                => 'facet_az_index_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'A-Z Index', 'wp-grid-builder' ),
			'fields'            => $az_index,
			'conditional_logic' => [
				'relation' => 'AND',
				[
					[
						'field'   => 'action',
						'compare' => '===',
						'value'   => 'filter',
					],
					[
						'field'   => 'filter_type',
						'compare' => '===',
						'value'   => 'az_index',
					],
				],
			],
		],
		[
			'id'                => 'facet_selection_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Selections', 'wp-grid-builder' ),
			'fields'            => $selection,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'filter',
				],
				[
					'field'   => 'filter_type',
					'compare' => '===',
					'value'   => 'selection',
				],
			],
		],
		[
			'id'                => 'load_type_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Loading Type', 'wp-grid-builder' ),
			'fields'            => $load_type,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'load',
				],
			],
		],
		[
			'id'                => 'pagination_type_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Pagination', 'wp-grid-builder' ),
			'fields'            => $pagination,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'load',
				],
				[
					'field'   => 'load_type',
					'compare' => '===',
					'value'   => 'pagination',
				],
			],
		],
		[
			'id'                => 'load_more_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Load More', 'wp-grid-builder' ),
			'fields'            => $load_more,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'load',
				],
				[
					'field'   => 'load_type',
					'compare' => '===',
					'value'   => 'load_more',
				],
			],
		],
		[
			'id'                => 'per_page_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Per Page', 'wp-grid-builder' ),
			'fields'            => $per_page_options,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'load',
				],
				[
					'field'   => 'load_type',
					'compare' => '===',
					'value'   => 'per_page',
				],
			],
		],
		[
			'id'                => 'result_count_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Result Count', 'wp-grid-builder' ),
			'fields'            => $result_count,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'load',
				],
				[
					'field'   => 'load_type',
					'compare' => '===',
					'value'   => 'result_count',
				],
			],
		],
		[
			'id'                => 'sort_options_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Sort Options', 'wp-grid-builder' ),
			'fields'            => $sort,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'sort',
				],
			],
		],
		[
			'id'                => 'reset_options_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Reset Options', 'wp-grid-builder' ),
			'fields'            => $reset,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'reset',
				],
			],
		],
		[
			'id'                => 'apply_options_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Apply Options', 'wp-grid-builder' ),
			'fields'            => $apply,
			'conditional_logic' => [
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'apply',
				],
			],
		],
		[
			'id'                => 'select_behaviour_section',
			'tab'               => 'behaviour',
			'type'              => 'section',
			'title'             => __( 'Dropdown', 'wp-grid-builder' ),
			'fields'            => $select,
			'conditional_logic' => [
				'relation' => 'OR',
				[
					[
						'field'   => 'action',
						'compare' => '===',
						'value'   => 'filter',
					],
					[
						'field'   => 'filter_type',
						'compare' => '===',
						'value'   => 'select',
					],
				],
				[
					[
						'field'   => 'action',
						'compare' => '===',
						'value'   => 'load',
					],
					[
						'field'   => 'load_type',
						'compare' => '===',
						'value'   => 'per_page',
					],
				],
				[
					'field'   => 'action',
					'compare' => '===',
					'value'   => 'sort',
				],
			],
		],
	],
];

$defaults = require WPGB_PATH . 'admin/settings/defaults/facet.php';

wp_grid_builder()->settings->register( $facet_settings, $defaults );
