<?php
/**
 * Grid settings
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Loaders;
use WP_Grid_Builder\Includes\Database;
use WP_Grid_Builder\Includes\Animations;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$grid_id     = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
$grid_name   = __( 'New Grid', 'wp-grid-builder' );
$has_cards   = false;
$grid_values = [];

// Query settings.
if ( $grid_id > 0 ) {

	$grid = Database::query_row(
		[
			'select' => 'name, settings',
			'from'   => 'grids',
			'id'     => $grid_id,
		]
	);

	$grid_name   = $grid['name'];
	$grid_values = $grid['settings'];
	$grid_values = json_decode( $grid_values, true );

}

// Prepare select options.
$user_options = [];
$tax_options  = [];
$term_options = [];
$post_options = [];
$card_options = [];
$image_sizes  = Helpers::get_image_sizes();
$post_status  = Helpers::get_post_status();
$post_types   = Helpers::get_post_types();
$taxonomies   = Helpers::get_taxonomies_list();
$user_roles   = Helpers::get_roles();

if ( ! wp_doing_ajax() ) {

	// Count cards.
	$has_cards = ! empty( Database::count_items( 'cards' ) );

	// Prepare user select options.
	$author__in   = ! empty( $grid_values['author__in'] ) ? $grid_values['author__in'] : [];
	$user__in     = ! empty( $grid_values['user__in'] ) ? $grid_values['user__in'] : [];
	$user__not_in = ! empty( $grid_values['user__not_in'] ) ? $grid_values['user__not_in'] : [];
	$user_ids     = array_merge( (array) $author__in, (array) $user__in, (array) $user__not_in );
	$user_options = Helpers::get_users( $user_ids );

	// Prepare tax query select options.
	$tax_query   = ! empty( $grid_values['tax_query'] ) ? $grid_values['tax_query'] : [];
	$tax_options = Helpers::get_taxonomy_terms( (array) $tax_query );

	// Prepare term select options.
	$term__in     = ! empty( $grid_values['term__in'] ) ? $grid_values['term__in'] : [];
	$term__not_in = ! empty( $grid_values['term__not_in'] ) ? $grid_values['term__not_in'] : [];
	$term_ids     = array_merge( (array) $term__in, (array) $term__not_in );
	$term_options = Helpers::get_terms( $term_ids, [] );

	// Prepare post select options.
	$post__in     = ! empty( $grid_values['post__in'] ) ? $grid_values['post__in'] : [];
	$post__not_in = ! empty( $grid_values['post__not_in'] ) ? $grid_values['post__not_in'] : [];
	$post_ids     = array_merge( (array) $post__in, (array) $post__not_in );
	$post_options = Helpers::get_posts( $post_ids );

	$custom_cards  = apply_filters( 'wp_grid_builder/cards', [] );
	$builder_cards = ! empty( $grid_values['cards'] ) ? $grid_values['cards'] : [];
	$builder_cards = array_values( $builder_cards );
	$builder_cards = array_unique( $builder_cards );
	$builder_cards = ! empty( $builder_cards ) ? Database::query_results(
		[
			'select' => 'id, name',
			'from'   => 'cards',
			'id'     => $builder_cards,
		]
	) : [];

	foreach ( $builder_cards as $card ) {
		$card_options[ (int) $card['id'] ] = esc_html( $card['name'] );
	}

	foreach ( $custom_cards as $slug => $args ) {
		$card_options[ esc_html( $slug ) ] = esc_html( $args['name'] );
	}
}

$card_link = add_query_arg(
	[
		'page'   => WPGB_SLUG . '-card-builder',
		'create' => 'true',
	],
	admin_url( 'admin.php' )
);

$demo_link = add_query_arg(
	[ 'page' => WPGB_SLUG . '-cards' ],
	admin_url( 'admin.php' )
);

$naming = [
	// name.
	[
		'id'          => 'name',
		'type'        => 'text',
		'label'       => __( 'Grid Name', 'wp-grid-builder' ),
		'placeholder' => __( 'Enter a grid name', 'wp-grid-builder' ),
		'value'       => $grid_name,
		'width'       => 380,
	],
	// class.
	[
		'id'          => 'class',
		'type'        => 'text',
		'label'       => __( 'Custom CSS Class', 'wp-grid-builder' ),
		'placeholder' => __( 'Enter a class name', 'wp-grid-builder' ),
		'width'       => 380,
	],
	// id.
	[
		'id'       => 'id',
		'type'     => 'text',
		'label'    => __( 'Generated CSS Class', 'wp-grid-builder' ),
		'width'    => 380,
		'disabled' => true,
		'value'    => empty( $grid_id ) ? __( 'Please save the grid to generate the CSS class.', 'wp-grid-builder' ) : 'wpgb-grid-' . $grid_id,
		'tooltip'  => __( 'Useful to target a particular grid with CSS or JS.', 'wp-grid-builder' ),
	],
	// shortcode.
	[
		'id'       => 'shortcode',
		'type'     => 'text',
		'label'    => __( 'Generated Shortcode', 'wp-grid-builder' ),
		'width'    => 380,
		'disabled' => true,
		'value'    => empty( $grid_id ) ? __( 'Please save the grid to generate the shortcode.', 'wp-grid-builder' ) : '[wpgb_grid id="' . $grid_id . '"]',
		'tooltip'  => __( 'Copy/paste this shortcode anywhere in a post/page to display a grid.', 'wp-grid-builder' ),
	],
];

$error_messages = [
	// no_posts_msg.
	[
		'id'          => 'no_posts_msg',
		'type'        => 'text',
		'label'       => __( 'No Content Message', 'wp-grid-builder' ),
		'placeholder' => __( 'Sorry, no content found.', 'wp-grid-builder' ),
		'width'       => 380,
	],
	// no_results_msg.
	[
		'id'          => 'no_results_msg',
		'type'        => 'text',
		'label'       => __( 'No Results Message', 'wp-grid-builder' ),
		'placeholder' => __( 'Sorry, no results match your search criteria.', 'wp-grid-builder' ),
		'width'       => 380,
	],
];

$content_type = [
	// source.
	[
		'id'      => 'source',
		'type'    => 'radio',
		'options' => [
			'post_type' => __( 'Post Types', 'wp-grid-builder' ),
			'term'      => __( 'Terms', 'wp-grid-builder' ),
			'user'      => __( 'Users', 'wp-grid-builder' ),
		],
		'icons'   => [
			'post_type' => Helpers::get_icon( 'post-type', true ),
			'term'      => Helpers::get_icon( 'post-terms', true ),
			'user'      => Helpers::get_icon( 'user', true ),
		],
	],
];

$items_number = [
	// posts_per_page.
	[
		'id'      => 'posts_per_page',
		'type'    => 'number',
		'label'   => __( 'Items Per Page', 'wp-grid-builder' ),
		'tooltip' => __( '"-1" to show all items. "0" corresponds to the default number of posts per page set in WordPress Settings.', 'wp-grid-builder' ),
		'min'     => -1,
		'max'     => 100,
		'step'    => 1,
		'width'   => 80,
	],
	// offset.
	[
		'id'                => 'offset',
		'type'              => 'number',
		'label'             => __( 'Offset Items By', 'wp-grid-builder' ),
		'tooltip'           => __( 'The "offset" parameter is ignored when the number of items per page is set to "-1" (show all items).', 'wp-grid-builder' ),
		'min'               => 0,
		'max'               => 999,
		'step'              => 1,
		'width'             => 80,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => 'IN',
				'value'   => [ 'post_type', 'user', 'term' ],
			],
		],
	],
];

$queried_users = [
	// role.
	[
		'id'          => 'role',
		'type'        => 'select',
		'label'       => __( 'Users Roles', 'wp-grid-builder' ),
		'placeholder' => _x( 'Any', 'Users Roles default value', 'wp-grid-builder' ),
		'tooltip'     => __( 'Roles that users must match. Users must match each selected role.', 'wp-grid-builder' ),
		'options'     => $user_roles,
		'multiple'    => true,
		'search'      => true,
		'width'       => 380,
	],
	// role__in.
	[
		'id'          => 'role__in',
		'type'        => 'select',
		'label'       => __( 'Include Roles', 'wp-grid-builder' ),
		'placeholder' => _x( 'None', 'Include Roles default value', 'wp-grid-builder' ),
		'tooltip'     => __( 'Users must have at least one of the selected roles.', 'wp-grid-builder' ),
		'options'     => $user_roles,
		'multiple'    => true,
		'width'       => 380,
	],
	// role__not_in.
	[
		'id'          => 'role__not_in',
		'type'        => 'select',
		'label'       => __( 'Exclude Roles', 'wp-grid-builder' ),
		'placeholder' => _x( 'None', 'Exclude Roles default value', 'wp-grid-builder' ),
		'tooltip'     => __( 'Users matching one or more of the selected roles will not be included in results.', 'wp-grid-builder' ),
		'options'     => $user_roles,
		'multiple'    => true,
		'width'       => 380,
	],
	// user__in.
	[
		'id'                => 'user__in',
		'type'              => 'select',
		'label'             => __( 'Include Users', 'wp-grid-builder' ),
		'placeholder'       => _x( 'None', 'Include Users default value', 'wp-grid-builder' ),
		'options'           => $user_options,
		'async'             => 'search_users',
		'multiple'          => true,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'user__not_in',
				'compare' => '==',
				'value'   => '',
			],
		],
	],
	// user__not_in.
	[
		'id'                => 'user__not_in',
		'type'              => 'select',
		'label'             => __( 'Exclude Users', 'wp-grid-builder' ),
		'placeholder'       => _x( 'None', 'Exclude Users default value', 'wp-grid-builder' ),
		'options'           => $user_options,
		'async'             => 'search_users',
		'multiple'          => true,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'user__in',
				'compare' => '==',
				'value'   => '',
			],
		],
	],
	// has_published_posts.
	[
		'id'          => 'has_published_posts',
		'type'        => 'select',
		'label'       => __( 'Has Published Posts In', 'wp-grid-builder' ),
		'placeholder' => __( 'All post types', 'wp-grid-builder' ),
		'tooltip'     => __( 'Filter results to users who have published posts in the selected post types.', 'wp-grid-builder' ),
		'options'     => $post_types,
		'multiple'    => true,
		'search'      => true,
		'width'       => 380,
	],
];

$queried_taxonomy_terms = [
	// taxonomy.
	[
		'id'          => 'taxonomy',
		'type'        => 'select',
		'label'       => __( 'Taxonomies', 'wp-grid-builder' ),
		'placeholder' => _x( 'Any', 'Taxonomies default value', 'wp-grid-builder' ),
		'tooltip'     => __( 'Taxonomies, to which results should be limited.', 'wp-grid-builder' ),
		'options'     => $taxonomies,
		'multiple'    => true,
		'search'      => true,
		'width'       => 380,
	],
	// term__in.
	[
		'id'                => 'term__in',
		'type'              => 'select',
		'label'             => __( 'Include Terms', 'wp-grid-builder' ),
		'placeholder'       => _x( 'None', 'Include Terms default value', 'wp-grid-builder' ),
		'options'           => $term_options,
		'async'             => 'search_terms',
		'multiple'          => true,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'term__not_in',
				'compare' => '==',
				'value'   => '',
			],
		],
	],
	// term__not_in.
	[
		'id'                => 'term__not_in',
		'type'              => 'select',
		'label'             => __( 'Exclude Terms', 'wp-grid-builder' ),
		'placeholder'       => _x( 'None', 'Exclude Terms default value', 'wp-grid-builder' ),
		'options'           => $term_options,
		'async'             => 'search_terms',
		'multiple'          => true,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'term__in',
				'compare' => '==',
				'value'   => '',
			],
		],
	],
	// hide_empty.
	[
		'id'      => 'hide_empty',
		'type'    => 'toggle',
		'label'   => __( 'Hide Empty Terms', 'wp-grid-builder' ),
		'tooltip' => __( 'Whether to hide terms not assigned to any posts.', 'wp-grid-builder' ),
	],
	// childless.
	[
		'id'      => 'childless',
		'type'    => 'toggle',
		'label'   => __( 'Childless Terms', 'wp-grid-builder' ),
		'tooltip' => __( 'Limit results to terms that have no children.', 'wp-grid-builder' ),
	],
];

$queried_posts = [
	// post_type.
	[
		'id'          => 'post_type',
		'type'        => 'select',
		'label'       => __( 'Post Types', 'wp-grid-builder' ),
		'placeholder' => _x( 'Any', 'Post Types default value', 'wp-grid-builder' ),
		'options'     => $post_types,
		'multiple'    => true,
		'search'      => true,
		'width'       => 380,
	],
	// post_status.
	[
		'id'          => 'post_status',
		'type'        => 'select',
		'label'       => _x( 'Post Status', 'plural', 'wp-grid-builder' ),
		'placeholder' => __( 'Default', 'wp-grid-builder' ),
		'tooltip'     => __( 'Default value is "published", but if the user is logged in, "private" is added (according to WordPress).', 'wp-grid-builder' ),
		'options'     => $post_status,
		'multiple'    => true,
		'search'      => true,
		'width'       => 380,
	],
	// author__in.
	[
		'id'          => 'author__in',
		'type'        => 'select',
		'label'       => __( 'Post Authors', 'wp-grid-builder' ),
		'placeholder' => _x( 'Any', 'Post Authors default value', 'wp-grid-builder' ),
		'options'     => $user_options,
		'async'       => 'search_users',
		'multiple'    => true,
		'width'       => 380,
	],
	// post__in.
	[
		'id'                => 'post__in',
		'type'              => 'select',
		'label'             => __( 'Include Posts', 'wp-grid-builder' ),
		'placeholder'       => _x( 'None', 'Include Posts default value', 'wp-grid-builder' ),
		'options'           => $post_options,
		'async'             => 'search_posts',
		'multiple'          => true,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'post__not_in',
				'compare' => '==',
				'value'   => '',
			],
		],
	],
	// post__not_in.
	[
		'id'                => 'post__not_in',
		'type'              => 'select',
		'label'             => __( 'Exclude Posts', 'wp-grid-builder' ),
		'placeholder'       => _x( 'None', 'Exclude Posts default value', 'wp-grid-builder' ),
		'options'           => $post_options,
		'async'             => 'search_posts',
		'multiple'          => true,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'post__in',
				'compare' => '==',
				'value'   => '',
			],
		],
	],
	// post_mime_type.
	[
		'id'                => 'post_mime_type',
		'type'              => 'select',
		'label'             => __( 'Mime Types', 'wp-grid-builder' ),
		'placeholder'       => _x( 'Any', 'Mime Types default value', 'wp-grid-builder' ),
		'multiple'          => true,
		'width'             => 380,
		'options'           => [
			'image'       => __( 'Image', 'wp-grid-builder' ),
			'video'       => __( 'Video', 'wp-grid-builder' ),
			'audio'       => __( 'Audio', 'wp-grid-builder' ),
			'text'        => __( 'Text', 'wp-grid-builder' ),
			'application' => __( 'Applications', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			[
				'field'   => 'post_type',
				'compare' => '==',
				'value'   => 'attachment',
			],
			[
				'field'   => 'attachment_ids',
				'compare' => '==',
				'value'   => [],
			],
		],
	],
	// attachment_ids.
	[
		'id'                => 'attachment_ids',
		'type'              => 'gallery',
		'label'             => __( 'Include Media', 'wp-grid-builder' ),
		'tooltip'           => __( 'Drag &#38; drop media to create a custom order (only works if you have "Media" as unique post type). If no media are added, all media from your library will be queried.', 'wp-grid-builder' ),
		'mime_type'         => [],
		'conditional_logic' => [
			[
				'field'   => 'post_type',
				'compare' => 'CONTAINS',
				'value'   => 'attachment',
			],
		],
	],
];

$queried_order = [
	// orderby.
	[
		'id'                => 'orderby',
		'type'              => 'select',
		'label'             => __( 'Order By', 'wp-grid-builder' ),
		'placeholder'       => __( 'Post date (default)', 'wp-grid-builder' ),
		'options'           => [
			'none'           => __( 'None', 'wp-grid-builder' ),
			'ID'             => __( 'Post ID', 'wp-grid-builder' ),
			'title'          => __( 'Post title', 'wp-grid-builder' ),
			'name'           => __( 'Post name (slug)', 'wp-grid-builder' ),
			'author'         => __( 'Post author', 'wp-grid-builder' ),
			'date'           => __( 'Post date', 'wp-grid-builder' ),
			'modified'       => __( 'Post modified date', 'wp-grid-builder' ),
			'parent'         => __( 'Post parent ID', 'wp-grid-builder' ),
			'post__in'       => __( 'Included posts', 'wp-grid-builder' ),
			'rand'           => __( 'Random order', 'wp-grid-builder' ),
			'menu_order'     => __( 'Menu order', 'wp-grid-builder' ),
			'meta_value'     => __( 'Custom field', 'wp-grid-builder' ),
			'meta_value_num' => __( 'Numeric custom field', 'wp-grid-builder' ),
			'comment_count'  => __( 'Number of comments', 'wp-grid-builder' ),
		],
		'multiple'          => true,
		'search'            => true,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_type',
			],
		],
	],
	// user_orderby.
	[
		'id'                => 'user_orderby',
		'type'              => 'select',
		'label'             => __( 'Order By', 'wp-grid-builder' ),
		'placeholder'       => __( 'User login (default)', 'wp-grid-builder' ),
		'options'           => [
			'none'            => __( 'None', 'wp-grid-builder' ),
			'ID'              => __( 'User ID', 'wp-grid-builder' ),
			'display_name'    => __( 'User display name', 'wp-grid-builder' ),
			'user_name'       => __( 'User name', 'wp-grid-builder' ),
			'user_login'      => __( 'User login', 'wp-grid-builder' ),
			'user_nicename'   => __( 'User nicename', 'wp-grid-builder' ),
			'user_email'      => __( 'User email', 'wp-grid-builder' ),
			'user_url'        => __( 'User url', 'wp-grid-builder' ),
			'user_registered' => __( 'User registered date', 'wp-grid-builder' ),
			'include'         => __( 'Included users', 'wp-grid-builder' ),
			'post_count'      => __( 'Post count', 'wp-grid-builder' ),
			'meta_value'      => __( 'Custom field', 'wp-grid-builder' ),
			'meta_value_num'  => __( 'Numeric custom field', 'wp-grid-builder' ),
		],
		'multiple'          => true,
		'search'            => true,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'user',
			],
		],
	],
	// term_orderby.
	[
		'id'                => 'term_orderby',
		'type'              => 'select',
		'label'             => __( 'Order By', 'wp-grid-builder' ),
		'placeholder'       => __( 'Term name (default)', 'wp-grid-builder' ),
		'options'           => [
			'none'           => __( 'None', 'wp-grid-builder' ),
			'term_id'        => __( 'Term ID', 'wp-grid-builder' ),
			'name'           => __( 'Term name', 'wp-grid-builder' ),
			'slug'           => __( 'Term slug', 'wp-grid-builder' ),
			'description'    => __( 'Term description', 'wp-grid-builder' ),
			'parent'         => __( 'Term parent', 'wp-grid-builder' ),
			'term_order'     => __( 'Term order', 'wp-grid-builder' ),
			'term_group'     => __( 'Term group', 'wp-grid-builder' ),
			'count'          => __( 'Term count', 'wp-grid-builder' ),
			'include'        => __( 'Included terms', 'wp-grid-builder' ),
			'meta_value'     => __( 'Custom field', 'wp-grid-builder' ),
			'meta_value_num' => __( 'Numeric custom field', 'wp-grid-builder' ),
		],
		'multiple'          => true,
		'search'            => true,
		'width'             => 380,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'term',
			],
		],
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
	// meta_key.
	[
		'id'                => 'meta_key',
		'type'              => 'text',
		'label'             => __( 'Custom Field', 'wp-grid-builder' ),
		'placeholder'       => __( 'Enter a field name', 'wp-grid-builder' ),
		'width'             => 380,
		'conditional_logic' => [
			'relation' => 'OR',
			[
				'field'   => 'orderby',
				'compare' => 'CONTAINS',
				'value'   => 'meta_value',
			],
			[
				'field'   => 'user_orderby',
				'compare' => 'CONTAINS',
				'value'   => 'meta_value',
			],
			[
				'field'   => 'term_orderby',
				'compare' => 'CONTAINS',
				'value'   => 'meta_value',
			],
		],
	],
];

$queried_terms = [
	// tax_query.
	[
		'id'          => 'tax_query',
		'type'        => 'select',
		'label'       => __( 'Taxonomy Terms', 'wp-grid-builder' ),
		'placeholder' => _x( 'Any', 'Taxonomy terms default value', 'wp-grid-builder' ),
		'tooltip'     => __( 'Show posts associated with certain taxonomy.', 'wp-grid-builder' ),
		'options'     => $tax_options,
		'async'       => 'search_taxonomy_terms',
		'multiple'    => true,
		'width'       => 380,
	],
	// tax_query_operator.
	[
		'id'      => 'tax_query_operator',
		'type'    => 'radio',
		'label'   => __( 'Terms Operator', 'wp-grid-builder' ),
		'tooltip' => __( 'Operator to test selected terms. By default queried posts must include selected terms.', 'wp-grid-builder' ),
		'options' => [
			'IN'     => __( 'Include', 'wp-grid-builder' ),
			'NOT IN' => __( 'Exclude', 'wp-grid-builder' ),
		],
	],
	// tax_query_relation.
	[
		'id'      => 'tax_query_relation',
		'type'    => 'radio',
		'label'   => __( 'Terms Relation', 'wp-grid-builder' ),
		'tooltip' => __( 'Logical relationship between each taxonomy term.', 'wp-grid-builder' ),
		'options' => [
			'OR'  => __( 'OR', 'wp-grid-builder' ),
			'AND' => __( 'AND', 'wp-grid-builder' ),
		],
	],
	// tax_query_children.
	[
		'id'      => 'tax_query_children',
		'type'    => 'toggle',
		'label'   => __( 'Child Terms', 'wp-grid-builder' ),
		'tooltip' => __( 'Include children for hierarchical taxonomies.', 'wp-grid-builder' ),
	],
];

$queried_meta_data = [
	// meta_query.
	[
		'id'      => 'meta_query',
		'type'    => 'meta_query',
		'tooltip' => __( 'Show content associated with a certain custom field.', 'wp-grid-builder' ),
		'fields'  => [
			'relation' => [
				'id'      => 'relation',
				'type'    => 'select',
				'label'   => __( 'Relation', 'wp-grid-builder' ),
				'width'   => 80,
				'options' => [
					'AND' => __( 'AND', 'wp-grid-builder' ),
					'OR'  => __( 'OR', 'wp-grid-builder' ),
				],
			],
			'key'      => [
				'id'    => 'key',
				'type'  => 'text',
				'label' => __( 'Field Key', 'wp-grid-builder' ),
				'width' => 248,
			],
			'compare'  => [
				'id'      => 'compare',
				'type'    => 'select',
				'label'   => __( 'Compare With', 'wp-grid-builder' ),
				'width'   => 248,
				'options' => [
					'='           => __( 'Equals (=)', 'wp-grid-builder' ),
					'!='          => __( 'Does not equal (!=)', 'wp-grid-builder' ),
					'>'           => __( 'Greater than (>)', 'wp-grid-builder' ),
					'>='          => __( 'Greater than or equal to (>=)', 'wp-grid-builder' ),
					'<'           => __( 'Less than (&lt;)', 'wp-grid-builder' ),
					'<='          => __( 'Less than or equal to (&lt;=)', 'wp-grid-builder' ),
					'LIKE'        => __( 'Like', 'wp-grid-builder' ),
					'NOT LIKE'    => __( 'Not like', 'wp-grid-builder' ),
					'IN'          => __( 'In', 'wp-grid-builder' ),
					'NOT IN'      => __( 'Not in', 'wp-grid-builder' ),
					'BETWEEN'     => __( 'Between', 'wp-grid-builder' ),
					'NOT BETWEEN' => __( 'Not between', 'wp-grid-builder' ),
					'EXISTS'      => __( 'Exists', 'wp-grid-builder' ),
					'NOT EXISTS'  => __( 'Not exists', 'wp-grid-builder' ),
				],
			],
			'value'    => [
				'id'    => 'value',
				'type'  => 'text',
				'label' => __( 'Field Value', 'wp-grid-builder' ),
				'width' => 248,
			],
			'type'     => [
				'id'      => 'type',
				'type'    => 'select',
				'label'   => __( 'Field Type', 'wp-grid-builder' ),
				'width'   => 248,
				'options' => [
					'CHAR'     => __( 'Character', 'wp-grid-builder' ),
					'NUMERIC'  => __( 'Numeric', 'wp-grid-builder' ),
					'BINARY'   => __( 'Binary', 'wp-grid-builder' ),
					'DATE'     => __( 'Date', 'wp-grid-builder' ),
					'DATETIME' => __( 'Date time', 'wp-grid-builder' ),
					'TIME'     => __( 'Time', 'wp-grid-builder' ),
					'DECIMAL'  => __( 'Decimal', 'wp-grid-builder' ),
					'SIGNED'   => __( 'Signed', 'wp-grid-builder' ),
					'UNSIGNED' => __( 'Unsigned', 'wp-grid-builder' ),
				],
			],
		],
	],
];

$media_formats = [
	// post_formats.
	[
		'id'      => 'post_formats',
		'type'    => 'checkbox',
		'tooltip' => __( 'If no post formats are selected, only image will be displayed in cards (if available).', 'wp-grid-builder' ),
		'options' => [
			'gallery' => __( 'Gallery', 'wp-grid-builder' ),
			'audio'   => __( 'Audio', 'wp-grid-builder' ),
			'video'   => __( 'Video', 'wp-grid-builder' ),
		],
		'icons'   => [
			'gallery' => Helpers::get_icon( 'gallery-large', true ),
			'audio'   => Helpers::get_icon( 'audio-large', true ),
			'video'   => Helpers::get_icon( 'video-large', true ),
		],
	],
	// first_media.
	[
		'id'                => 'first_media',
		'type'              => 'toggle',
		'label'             => __( 'First Media Content', 'wp-grid-builder' ),
		'tooltip'           => __( 'Fetch first media in post content if missing (according to the post format).', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_type',
			],
		],
	],
	// gallery_slideshow.
	[
		'id'                => 'gallery_slideshow',
		'type'              => 'toggle',
		'label'             => __( 'Gallery Slideshow', 'wp-grid-builder' ),
		'tooltip'           => __( 'Enable slideshow for gallery post format.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'post_formats',
				'compare' => 'CONTAINS',
				'value'   => 'gallery',
			],
		],
	],
	// product_image_hover.
	class_exists( 'WooCommerce' ) ? [
		'id'                => 'product_image_hover',
		'type'              => 'toggle',
		'label'             => __( 'Product Image Hover', 'wp-grid-builder' ),
		'tooltip'           => __( 'Reveal first gallery image when hovering the main product image (WooCommerce).', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'post_type',
				'compare' => 'CONTAINS',
				'value'   => 'product',
			],
		],
	] : '',
	// embed_video_poster.
	[
		'id'                => 'embedded_video_poster',
		'type'              => 'toggle',
		'label'             => __( 'Embedded Video Posters', 'wp-grid-builder' ),
		'tooltip'           => __( 'Automatically fetches embedded video posters (Youtube, Vimeo and Wistia).', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'post_formats',
				'compare' => 'CONTAINS',
				'value'   => 'video',
			],
		],
	],
	// video_lightbox.
	[
		'id'                => 'video_lightbox',
		'type'              => 'toggle',
		'label'             => __( 'Open Videos in Lightbox', 'wp-grid-builder' ),
		'tooltip'           => __( 'When disabled, videos will be played in cards.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'post_formats',
				'compare' => 'CONTAINS',
				'value'   => 'video',
			],
		],
	],
];

$default_thumbnail = [
	// default_thumbnail.
	[
		'id'      => 'default_thumbnail',
		'type'    => 'image',
		'tooltip' => __( 'Add a default thumbnail in each grid card if missing.', 'wp-grid-builder' ),
	],
];

$thumbnail_ratio = [
	// thumbnail_aspect.
	[
		'id'      => 'thumbnail_aspect',
		'type'    => 'toggle',
		'label'   => __( 'Override Aspect Ratio', 'wp-grid-builder' ),
		'tooltip' => __( 'Allows to set the same image aspect ratio for all thumbnails in the grid.', 'wp-grid-builder' ),
	],
	// thumbnail_ratio.
	[
		'id'                => 'thumbnail_ratio',
		'type'              => 'group',
		'label'             => __( 'Thumbnail Aspect Ratio', 'wp-grid-builder' ),
		'group_names'       => true,
		'separator'         => '&nbsp;:&nbsp;',
		'fields'            => [
			[
				'id'    => 'x',
				'type'  => 'number',
				'min'   => 1,
				'max'   => 999,
				'width' => 64,
			],
			[
				'id'    => 'y',
				'type'  => 'number',
				'min'   => 1,
				'max'   => 999,
				'width' => 64,
			],
		],
		'conditional_logic' => [
			[
				'field'   => 'thumbnail_aspect',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
];

$thumbnail_sizes = [
	// thumbnail_size.
	[
		'id'      => 'thumbnail_size',
		'type'    => 'select',
		'label'   => __( 'Desktop Size', 'wp-grid-builder' ),
		'options' => $image_sizes,
		'width'   => 380,
	],
	// thumbnail_size_mobile.
	[
		'id'          => 'thumbnail_size_mobile',
		'type'        => 'select',
		'label'       => __( 'Mobile Size', 'wp-grid-builder' ),
		'options'     => $image_sizes,
		'description' => sprintf(
			/* translators: %s: Settings panel url */
			__( 'Additional image sizes can be set in the <a href="%s" target="_blank">plugin settings</a>.', 'wp-grid-builder' ),
			esc_url( add_query_arg( 'page', WPGB_SLUG . '-settings&tab=sizes', admin_url( 'admin.php' ) ) )
		),
		'width'       => 380,
	],
];

$layout_type = [
	// type.
	[
		'id'      => 'type',
		'type'    => 'radio',
		'options' => [
			'masonry'   => __( 'Masonry', 'wp-grid-builder' ),
			'metro'     => __( 'Metro', 'wp-grid-builder' ),
			'justified' => __( 'Justified', 'wp-grid-builder' ),
		],
		'icons'   => [
			'masonry'   => Helpers::get_icon( 'masonry-grid-large', true ),
			'metro'     => Helpers::get_icon( 'metro-grid-large', true ),
			'justified' => Helpers::get_icon( 'justified-grid-large', true ),
		],
	],
	// full_width.
	[
		'id'      => 'full_width',
		'type'    => 'toggle',
		'label'   => __( 'Full Width', 'wp-grid-builder' ),
		'tooltip' => __( 'Fills the entire browser width.', 'wp-grid-builder' ),
	],
];

$cards_position = [
	// horizontal_order.
	[
		'id'                => 'horizontal_order',
		'type'              => 'toggle',
		'label'             => __( 'Horizontal Order', 'wp-grid-builder' ),
		'tooltip'           => __( 'Lays out cards to (mostly) maintain horizontal left-to-right order. By default, a Masonry grid is ordered vertically from top-to-bottom.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'masonry',
			],
		],
	],
	// fit_rows.
	[
		'id'                => 'fit_rows',
		'type'              => 'toggle',
		'label'             => __( 'Fit into Rows', 'wp-grid-builder' ),
		'tooltip'           => __( 'Arrange cards into rows. Rows progress vertically. Similar to what you would expect from a classic column layout.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'masonry',
			],
		],
	],
	// equal_columns.
	[
		'id'                => 'equal_columns',
		'type'              => 'toggle',
		'label'             => __( 'Equal Height Columns', 'wp-grid-builder' ),
		'tooltip'           => __( 'Equalize column heights in each row in order to create a perfect Masonry layout.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'masonry',
			],
		],
	],
	// equal_rows.
	[
		'id'                => 'equal_rows',
		'type'              => 'toggle',
		'label'             => __( 'Equal Height Rows', 'wp-grid-builder' ),
		'tooltip'           => __( 'Equalize row heights in order to create a perfect Justified layout. This option will crop images.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'justified',
			],
		],
	],
	// fill_last_row.
	[
		'id'                => 'fill_last_row',
		'type'              => 'toggle',
		'label'             => __( 'Fill Last Row', 'wp-grid-builder' ),
		'tooltip'           => __( 'Force the last row to be fully filled by cards. This option will crop images.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'justified',
			],
			[
				'field'   => 'layout',
				'compare' => '===',
				'value'   => 'vertical',
			],
		],
	],
	// center_last_row.
	[
		'id'                => 'center_last_row',
		'type'              => 'toggle',
		'label'             => __( 'Center Last Row', 'wp-grid-builder' ),
		'tooltip'           => __( 'Center cards in the lasy row.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'justified',
			],
			[
				'field'   => 'layout',
				'compare' => '===',
				'value'   => 'vertical',
			],
		],
	],
];

$cards_layout = [
	// layout.
	[
		'id'      => 'layout',
		'type'    => 'radio',
		'options' => [
			'vertical'   => __( 'Standard', 'wp-grid-builder' ),
			'horizontal' => __( 'Carousel', 'wp-grid-builder' ),
		],
		'icons'   => [
			'vertical'   => Helpers::get_icon( 'vertical-layout', true ),
			'horizontal' => Helpers::get_icon( 'horizontal-layout', true ),
		],
	],
];

$slide_positions = [
	// initial_index.
	[
		'id'      => 'initial_index',
		'type'    => 'number',
		'label'   => __( 'Initial Slide Index', 'wp-grid-builder' ),
		'tooltip' => __( 'Zero-based index of the initial selected slide in the carousel.', 'wp-grid-builder' ),
		'width'   => 68,
		'min'     => 0,
		'max'     => 999,
	],
	// contain.
	[
		'id'      => 'contain',
		'type'    => 'toggle',
		'label'   => __( 'Contain Slides', 'wp-grid-builder' ),
		'tooltip' => __( 'Contains slides to carousel to prevent excess scroll at beginning or end.', 'wp-grid-builder' ),
		'min'     => 1,
		'max'     => 999,
	],
	// slide_align.
	[
		'id'      => 'slide_align',
		'type'    => 'radio',
		'label'   => __( 'Slides Alignment', 'wp-grid-builder' ),
		'options' => [
			'left'   => __( 'Left', 'wp-grid-builder' ),
			'center' => __( 'Center', 'wp-grid-builder' ),
			'right'  => __( 'Right', 'wp-grid-builder' ),
		],
	],
	// group_cells.
	[
		'id'      => 'group_cells',
		'type'    => 'slider',
		'label'   => __( 'Groups Cards By', 'wp-grid-builder' ),
		'tooltip' => __( 'Groups cards together in slides by a number or viewport percent. Flicking, page dots, and previous/next buttons are mapped to group slides.', 'wp-grid-builder' ),
		'steps'   => [ 1, 1 ],
		'units'   => [ '', '%' ],
		'unit'    => true,
		'min'     => 1,
		'max'     => 100,
	],
	// rows_number.
	[
		'id'                => 'rows_number',
		'type'              => 'slider',
		'label'             => __( 'Number of Rows', 'wp-grid-builder' ),
		'steps'             => [ 1 ],
		'units'             => [ '' ],
		'min'               => 1,
		'max'               => 12,
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '!==',
				'value'   => 'masonry',
			],
		],
	],
	// auto_play.
	[
		'id'      => 'auto_play',
		'type'    => 'slider',
		'label'   => __( 'Auto Play', 'wp-grid-builder' ),
		'tooltip' => __( 'Auto-playing will pause when mouse is hovered over, and resume when mouse is hovered off.', 'wp-grid-builder' ),
		'steps'   => [ 100 ],
		'units'   => [ 'ms' ],
		'unit'    => false,
		'min'     => 0,
		'max'     => 60000,
	],
];

$physics_behaviour = [
	// draggable.
	[
		'id'      => 'draggable',
		'type'    => 'toggle',
		'label'   => __( 'Draggable', 'wp-grid-builder' ),
		'tooltip' => __( 'Enables dragging and flicking thanks to a pointer (mouse, fingers, etc.).', 'wp-grid-builder' ),
	],
	// free_scroll.
	[
		'id'      => 'free_scroll',
		'type'    => 'toggle',
		'label'   => __( 'Free Scroll', 'wp-grid-builder' ),
		'tooltip' => __( 'Enables content to be freely scrolled and flicked without aligning slides to an end position.', 'wp-grid-builder' ),
		'min'     => 1,
		'max'     => 999,
	],
	// free_friction.
	[
		'id'                => 'free_friction',
		'type'              => 'slider',
		'label'             => __( 'Free Scroll Friction', 'wp-grid-builder' ),
		'tooltip'           => __( 'Friction used when free scrolling. When carousel ends are reached, standard friction is used.', 'wp-grid-builder' ),
		'steps'             => [ 0.001 ],
		'units'             => [ '' ],
		'min'               => 0.001,
		'max'               => 1.000,
		'conditional_logic' => [
			[
				'field'   => 'free_scroll',
				'compare' => '==',
				'value'   => '1',
			],
		],
	],
	// friction.
	[
		'id'      => 'friction',
		'type'    => 'slider',
		'label'   => __( 'Friction', 'wp-grid-builder' ),
		'tooltip' => __( 'Friction slows the movement of carousel. Higher friction makes the carousel feel stickier and less bouncy. Lower friction makes the carousel feel looser and more wobbly.', 'wp-grid-builder' ),
		'steps'   => [ 0.001 ],
		'units'   => [ '' ],
		'min'     => 0.001,
		'max'     => 1.000,
	],
	// attraction.
	[
		'id'      => 'attraction',
		'type'    => 'slider',
		'label'   => __( 'Attraction', 'wp-grid-builder' ),
		'tooltip' => __( 'Attraction attracts the position of the carousel to the selected slide. Higher attraction makes the carousel move faster. Lower makes it move slower.', 'wp-grid-builder' ),
		'steps'   => [ 0.001 ],
		'units'   => [ '' ],
		'min'     => 0.001,
		'max'     => 1,
	],
];

$carousel_appearance = [
	// prev_next_buttons_size.
	[
		'id'    => 'prev_next_buttons_size',
		'type'  => 'slider',
		'label' => __( 'Prev/next Buttons Size', 'wp-grid-builder' ),
		'steps' => [ 1, 0.001, 0.001, 0.01, 0.01 ],
		'units' => [ 'px', 'em', 'rem' ],
		'unit'  => true,
		'min'   => 1,
		'max'   => 100,
	],
	// prev_next_buttons_color.
	[
		'id'    => 'prev_next_buttons_color',
		'type'  => 'color',
		'label' => __( 'Prev/Next Color', 'wp-grid-builder' ),
		'alpha' => true,
	],
	// prev_next_buttons_background.
	[
		'id'       => 'prev_next_buttons_background',
		'type'     => 'color',
		'label'    => __( 'Prev/Next Background', 'wp-grid-builder' ),
		'alpha'    => true,
		'gradient' => true,
	],
	// page_dots_color.
	[
		'id'       => 'page_dots_color',
		'type'     => 'color',
		'label'    => __( 'Page Dots Color', 'wp-grid-builder' ),
		'alpha'    => true,
		'gradient' => true,
	],
	// page_dots_selected_color.
	[
		'id'       => 'page_dots_selected_color',
		'type'     => 'color',
		'label'    => __( 'Page Dots Selected Color', 'wp-grid-builder' ),
		'alpha'    => true,
		'gradient' => true,
	],
];

$grid_builder = [
	// builder-info.
	[
		'id'      => 'builder-info',
		'type'    => 'info',
		'content' =>
			'<strong>' . __( 'Facets can be added anywhere in pages thanks to Gutenberg Blocks, Shortcodes, and Widgets.', 'wp-grid-builder' ) . '</strong><br>' .
			__( 'The following builder is limited and offers a basic solution if you do not use Gutenberg or a page builder.', 'wp-grid-builder' ) . '<br>' .
			__( 'Using Gutenberg or a page builder will offer more possibilities to build your layout.', 'wp-grid-builder' ),
	],
	// grid_layout.
	[
		'id'      => 'grid_layout',
		'type'    => 'builder',
		'tooltip' => __( 'Drag &#38; drop available facets to compose your grid layout. For each layout area you can set alignment, margins, paddings, and background color.', 'wp-grid-builder' ),
		'fields'  => [
			[
				'id'       => 'facets',
				'type'     => 'select',
				'multiple' => true,
				'validate' => false,
			],
			[
				'id'     => 'margins',
				'type'   => 'group',
				'label'  => __( 'Margin', 'wp-grid-builder' ),
				'fields' => [
					[
						'id'    => 'margin-top',
						'type'  => 'text_number',
						'label' => __( 'Top', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
						'width' => 70,
					],
					[
						'id'    => 'margin-right',
						'type'  => 'text_number',
						'label' => __( 'Right', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
						'width' => 70,
					],
					[
						'id'    => 'margin-bottom',
						'type'  => 'text_number',
						'label' => __( 'Bottom', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
						'width' => 70,
					],
					[
						'id'    => 'margin-left',
						'type'  => 'text_number',
						'label' => __( 'Left', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
						'width' => 70,
					],
				],
			],
			[
				'id'     => 'paddings',
				'type'   => 'group',
				'label'  => __( 'Padding', 'wp-grid-builder' ),
				'fields' => [
					[
						'id'    => 'padding-top',
						'type'  => 'text_number',
						'label' => __( 'Top', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
						'width' => 70,
					],
					[
						'id'    => 'padding-right',
						'type'  => 'text_number',
						'label' => __( 'Right', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
						'width' => 70,
					],
					[
						'id'    => 'padding-bottom',
						'type'  => 'text_number',
						'label' => __( 'Bottom', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
						'width' => 70,
					],
					[
						'id'    => 'padding-left',
						'type'  => 'text_number',
						'label' => __( 'Left', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
						'width' => 70,
					],
				],
			],
			[
				'id'       => 'background',
				'type'     => 'color',
				'label'    => __( 'Background Color', 'wp-grid-builder' ),
				'alpha'    => true,
				'gradient' => true,
			],
			[
				'id'      => 'justify-content',
				'type'    => 'radio',
				'label'   => __( 'Content Alignment', 'wp-grid-builder' ),
				'options' => [
					'flex-start' => __( 'Left', 'wp-grid-builder' ),
					'center'     => __( 'Center', 'wp-grid-builder' ),
					'flex-end'   => __( 'Right', 'wp-grid-builder' ),
				],
			],
		],
	],
];

$cards_aspect = [
	// override_card_sizes.
	[
		'id'      => 'override_card_sizes',
		'type'    => 'toggle',
		'label'   => __( 'Override Card Sizes', 'wp-grid-builder' ),
		'tooltip' => __( 'Override all sizes set for each card in the grid.', 'wp-grid-builder' ),
	],
	// columns.
	[
		'id'                => 'columns',
		'type'              => 'number',
		'label'             => __( 'Columns Number', 'wp-grid-builder' ),
		'min'               => 1,
		'max'               => 12,
		'step'              => 1,
		'width'             => 68,
		'conditional_logic' => [
			[
				'field'   => 'override_card_sizes',
				'compare' => '==',
				'value'   => 1,
			],
		],
	],
	// rows.
	[
		'id'                => 'rows',
		'type'              => 'number',
		'label'             => __( 'Rows Number', 'wp-grid-builder' ),
		'min'               => 1,
		'max'               => 12,
		'step'              => 1,
		'width'             => 68,
		'conditional_logic' => [
			[
				'field'   => 'override_card_sizes',
				'compare' => '==',
				'value'   => 1,
			],
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'metro',
			],
		],
	],
];

$grid_responsivity = [
	// card_sizes.
	[
		'id'      => 'card_sizes',
		'type'    => 'table',
		'class'   => 'wpgb-table-card-sizes',
		'tooltip' => __( 'Card spacing set to "-1" allows to inherit of the previous value and in a recursive way.', 'wp-grid-builder' ),
		'rows'    => [
			[
				'name'  => 'size',
				'icon'  => Helpers::get_icon( 'screen-size-0', true ),
				'label' => '',
			],
			[
				'name'  => 'size',
				'icon'  => Helpers::get_icon( 'screen-size-1', true ),
				'label' => '',
			],
			[
				'name'  => 'size',
				'icon'  => Helpers::get_icon( 'screen-size-2', true ),
				'label' => '',
			],
			[
				'name'  => 'size',
				'icon'  => Helpers::get_icon( 'screen-size-3', true ),
				'label' => '',
			],
			[
				'name'  => 'size',
				'icon'  => Helpers::get_icon( 'screen-size-4', true ),
				'label' => '',
			],
			[
				'name'  => 'size',
				'icon'  => Helpers::get_icon( 'screen-size-5', true ),
				'label' => '',
			],
		],
		'fields'  => [
			[
				'id'    => 'browser',
				'type'  => 'number',
				'label' => __( 'Browser (px)', 'wp-grid-builder' ),
				'min'   => 1,
				'max'   => 9999,
				'step'  => 1,
				'width' => 68,
			],
			[
				'id'    => 'columns',
				'type'  => 'slider',
				'label' => __( 'Columns', 'wp-grid-builder' ),
				'steps' => [ 1 ],
				'units' => [ ' ' . __( 'cols', 'wp-grid-builder' ) ],
				'min'   => 1,
				'max'   => 12,
				'unit'  => false,
				'width' => 80,
			],
			[
				'id'    => 'height',
				'type'  => 'slider',
				'label' => __( 'Row height', 'wp-grid-builder' ),
				'steps' => [ 1 ],
				'units' => [ 'px' ],
				'min'   => 1,
				'max'   => 1000,
				'unit'  => false,
				'width' => 80,
			],
			[
				'id'    => 'gutter',
				'type'  => 'number',
				'label' => __( 'Spacing (px)', 'wp-grid-builder' ),
				'min'   => -1,
				'max'   => 999,
				'step'  => 1,
				'width' => 80,
				'unit'  => 'px',
			],
			[
				'id'          => 'ratio',
				'type'        => 'group',
				'label'       => __( 'Aspect ratio', 'wp-grid-builder' ),
				'group_names' => true,
				'separator'   => '&nbsp;:&nbsp;',
				'fields'      => [
					[
						'id'    => 'x',
						'type'  => 'number',
						'min'   => 1,
						'max'   => 999,
						'step'  => 1,
						'width' => 54,
					],
					[
						'id'    => 'y',
						'type'  => 'number',
						'min'   => 1,
						'max'   => 999,
						'step'  => 1,
						'width' => 54,
					],
				],
			],
		],
	],
];

$default_card = [
	[
		'id'          => 'cards',
		'type'        => 'card',
		'name'        => 'wpgb[cards][default]',
		'value'       => isset( $grid_values['cards']['default'] ) ? $grid_values['cards']['default'] : '',
		'label'       => __( 'Default', 'wp-grid-builder' ),
		'width'       => 240,
		'search'      => true,
		'async'       => 'search_cards',
		'options'     => $card_options,
		'placeholder' => __( 'Default card', 'wp-grid-builder' ),
	],
];

$post_type_cards = [];

foreach ( $post_types as $key => $name ) {

	// Post type cards.
	$post_type_cards[] = [
		'id'                => 'cards',
		'type'              => 'card',
		'name'              => 'wpgb[cards][' . $key . ']',
		'value'             => isset( $grid_values['cards'][ $key ] ) ? $grid_values['cards'][ $key ] : '',
		'label'             => $name,
		'width'             => 240,
		'search'            => true,
		'async'             => 'search_cards',
		'options'           => $card_options,
		'placeholder'       => __( 'Default card', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'post_type',
				'compare' => 'CONTAINS',
				'value'   => $key,
			],
		],
	];

}

$post_format_cards = [];
$post_formats      = get_post_format_strings();
unset( $post_formats['standard'] );

foreach ( $post_formats as $key => $name ) {

	// Post format cards.
	$post_format_cards[] = [
		'id'                => 'cards',
		'type'              => 'card',
		'name'              => 'wpgb[cards][' . $key . ']',
		'value'             => isset( $grid_values['cards'][ $key ] ) ? $grid_values['cards'][ $key ] : '',
		'label'             => $name,
		'width'             => 240,
		'search'            => true,
		'async'             => 'search_cards',
		'options'           => $card_options,
		'placeholder'       => __( 'Default card', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_type',
			],
		],
	];

}

$card_colors = [
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
			'light' => __( 'Light', 'wp-grid-builder' ),
			'dark'  => __( 'Dark', 'wp-grid-builder' ),
		],
	],
];

$cards_animation = [
	// animation.
	[
		'id'          => 'animation',
		'type'        => 'select',
		'label'       => __( 'Animation Type', 'wp-grid-builder' ),
		'placeholder' => __( 'Select an animation', 'wp-grid-builder' ),
		'options'     => Animations::get_list( 'name' ),
		'search'      => true,
		'width'       => 262,
	],
	// timing_function.
	[
		'id'      => 'timing_function',
		'type'    => 'select',
		'label'   => __( 'Animation Easing', 'wp-grid-builder' ),
		'search'  => true,
		'width'   => 262,
		'options' => [
			'ease'                                      => 'Ease',
			'linear'                                    => 'Linear',
			'ease-in'                                   => 'Ease In',
			'ease-out'                                  => 'Ease Out',
			'ease-in-out'                               => 'Ease In Out',
			'cubic-bezier(0.550, 0.055, 0.675, 0.190)'  => 'Ease In Cubic',
			'cubic-bezier(0.215, 0.610, 0.355, 1.000)'  => 'Ease Out Cubic',
			'cubic-bezier(0.645, 0.045, 0.355, 1.000)'  => 'Ease In OutCubic',
			'cubic-bezier(0.600, 0.040, 0.980, 0.335)'  => 'Ease In Circ',
			'cubic-bezier(0.075, 0.820, 0.165, 1.000)'  => 'Ease Out Circ',
			'cubic-bezier(0.785, 0.135, 0.150, 0.860)'  => 'Ease In Out Circ',
			'cubic-bezier(0.950, 0.050, 0.795, 0.035)'  => 'Ease In Expo',
			'cubic-bezier(0.190, 1.000, 0.220, 1.000)'  => 'Ease Out Expo',
			'cubic-bezier(1.000, 0.000, 0.000, 1.000)'  => 'Ease In Out Expo',
			'cubic-bezier(0.550, 0.085, 0.680, 0.530)'  => 'Ease In Quad',
			'cubic-bezier(0.250, 0.460, 0.450, 0.940)'  => 'Ease Out Quad',
			'cubic-bezier(0.455, 0.030, 0.515, 0.955)'  => 'Ease In Out Quad',
			'cubic-bezier(0.895, 0.030, 0.685, 0.220)'  => 'Ease In Quart',
			'cubic-bezier(0.165, 0.840, 0.440, 1.000)'  => 'Ease Out Quart',
			'cubic-bezier(0.770, 0.000, 0.175, 1.000)'  => 'Ease In Out Quart',
			'cubic-bezier(0.755, 0.050, 0.855, 0.060)'  => 'Ease In Quint',
			'cubic-bezier(0.230, 1.000, 0.320, 1.000)'  => 'Ease Out Quint',
			'cubic-bezier(0.860, 0.000, 0.070, 1.000)'  => 'Ease In Out Quint',
			'cubic-bezier(0.470, 0.000, 0.745, 0.715)'  => 'Ease In Sine',
			'cubic-bezier(0.390, 0.575, 0.565, 1.000)'  => 'Ease Out Sine',
			'cubic-bezier(0.445, 0.050, 0.550, 0.950)'  => 'Ease In Out Sine',
			'cubic-bezier(0.600, -0.280, 0.735, 0.045)' => 'Ease In Back',
			'cubic-bezier(0.175,  0.885, 0.320, 1.275)' => 'Ease Out Back',
			'cubic-bezier(0.680, -0.550, 0.265, 1.550)' => 'Ease In Out Back',
			'custom'                                    => __( 'Custom Easing', 'wp-grid-builder' ),
		],
	],
	// cubic_bezier_function.
	[
		'id'                => 'cubic_bezier_function',
		'type'              => 'text',
		'width'             => 262,
		'label'             => __( 'Cubic Bezier Function', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'timing_function',
				'compare' => '===',
				'value'   => 'custom',
			],
		],
	],
	// transition.
	[
		'id'    => 'transition',
		'type'  => 'slider',
		'label' => __( 'Animation Duration', 'wp-grid-builder' ),
		'steps' => [ 1 ],
		'units' => [ 'ms' ],
		'min'   => 0,
		'max'   => 3000,
	],
	// transition_delay.
	[
		'id'      => 'transition_delay',
		'type'    => 'slider',
		'label'   => __( 'Animation Delay', 'wp-grid-builder' ),
		'tooltip' => __( 'Animation delay, in millisecond, between each card in a grid. It allows to stagger card animations.', 'wp-grid-builder' ),
		'steps'   => [ 1 ],
		'units'   => [ 'ms' ],
		'min'     => 0,
		'max'     => 1000,
	],
	// animation_placeholder.
	[
		'id'      => 'animation_placeholder',
		'type'    => 'custom',
		'content' => sprintf(
			'<br><img class="wpgb-animation-placeholder" width="320" height="270" alt="" src="%s"><br>
			<button type="button" class="wpgb-button wpgb-button-icon wpgb-run-animation wpgb-green">%s%s</button>',
			esc_url( WPGB_URL . 'admin/assets/svg/placeholder.svg' ),
			Helpers::get_icon( 'play', false, false ),
			__( 'Play Animation', 'wp-grid-builder' )
		),
	],
];

$images_loading = [
	// loader.
	[
		'id'      => 'lazy_load',
		'type'    => 'toggle',
		'label'   => __( 'Lazy Load Images', 'wp-grid-builder' ),
		'tooltip' => __( 'Defer the loading of images until there are visible in the viewport.', 'wp-grid-builder' ),
	],
	// lazy_load_spinner.
	[
		'id'      => 'lazy_load_spinner',
		'type'    => 'toggle',
		'label'   => __( 'Loading Spinner', 'wp-grid-builder' ),
		'tooltip' => __( 'Show a loading animation until the image is loaded.', 'wp-grid-builder' ),
		'alpha'   => true,
	],
	// lazy_load_blurred_image.
	[
		'id'      => 'lazy_load_blurred_image',
		'type'    => 'toggle',
		'label'   => __( 'Blurred Image', 'wp-grid-builder' ),
		'tooltip' => __( 'Display a blurred image during loading. This option generates and uploads additional tiny image sizes on your WordPress site.', 'wp-grid-builder' ),
	],
	// lazy_load_background.
	[
		'id'    => 'lazy_load_background',
		'type'  => 'color',
		'label' => __( 'Background Color', 'wp-grid-builder' ),
	],
	// lazy_load_blurred_image.
	[
		'id'    => 'lazy_load_spinner_color',
		'type'  => 'color',
		'label' => __( 'Spinner Color', 'wp-grid-builder' ),
	],
];

$content_loading = [
	// loader.
	[
		'id'      => 'loader',
		'type'    => 'toggle',
		'label'   => __( 'Loader', 'wp-grid-builder' ),
		'tooltip' => __( 'Loader will be displayed when loading content in the grid (loading pages, filtering and sorting).', 'wp-grid-builder' ),
	],
	// loader_color.
	[
		'id'    => 'loader_color',
		'type'  => 'color',
		'label' => __( 'Loader Color', 'wp-grid-builder' ),
		'alpha' => true,
	],
	// loader_size.
	[
		'id'    => 'loader_size',
		'type'  => 'slider',
		'label' => __( 'Loader Size', 'wp-grid-builder' ),
		'steps' => [ 0.01 ],
		'units' => [ 'X' ],
		'min'   => 0.1,
		'max'   => 2,
	],
	// loader_type.
	[
		'id'      => 'loader_type',
		'type'    => 'radio',
		'label'   => __( 'Loader Type', 'wp-grid-builder' ),
		'options' => Loaders::get_list(),
		'html'    => Loaders::get_markup(),
		'width'   => 220,
	],
];

$custom_js = [
	'id'     => 'custom_js_section',
	'tab'    => 'customization',
	'type'   => 'section',
	'title'  => __( 'Custom JavaScript', 'wp-grid-builder' ),
	'fields' => [
		// not-allowed.
		[
			'id'      => 'not-allowed',
			'type'    => 'info',
			'content' => '<strong>' . __( 'You are not allowed to add/edit JavaScript code.', 'wp-grid-builder' ) . '</strong><br>' .
						__( 'Only user with <code>edit_plugins</code> capability and <code>DISALLOW_FILE_EDIT</code> constant set to <code>false</code> can add/edit JavaScript code.', 'wp-grid-builder' ) . '<br>' .
						__( 'This behaviour is the same as the WordPress plugin editor.', 'wp-grid-builder' ),
		],
	],
];

if ( current_user_can( 'edit_plugins' ) ) {

	$custom_js = [
		'id'     => 'custom_js_section',
		'tab'    => 'customization',
		'type'   => 'section',
		'title'  => __( 'Custom JavaScript', 'wp-grid-builder' ),
		'fields' => [
			// custom_js.
			[
				'id'    => 'custom_js',
				'type'  => 'code',
				'mode'  => 'javascript',
				'label' => __( 'Enter your JS code:', 'wp-grid-builder' ),
			],
		],
	];

}

$grid_settings = [
	'id'     => 'grid',
	'header' => [
		'toggle'  => true,
		'buttons' => [
			[
				'title'  => __( 'Preview', 'wp-grid-builder' ),
				'icon'   => 'preview',
				'color'  => 'purple',
				'action' => 'preview',
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
			'id'       => 'naming',
			'label'    => __( 'Naming', 'wp-grid-builder' ),
			'title'    => __( 'Naming', 'wp-grid-builder' ),
			'subtitle' => __( 'Define the grid name and messages.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'shortcode', true ),
		],
		[
			'id'       => 'query',
			'label'    => __( 'Content Query', 'wp-grid-builder' ),
			'title'    => __( 'Content Query', 'wp-grid-builder' ),
			'subtitle' => __( 'Set up the content type to query and to display in the grid.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'folder', true ),
		],
		[
			'id'       => 'formats',
			'label'    => __( 'Media Formats', 'wp-grid-builder' ),
			'title'    => __( 'Media Formats', 'wp-grid-builder' ),
			'subtitle' => __( 'Manage the allowed media formats in the cards and their behaviors.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'image', true ),
		],
		[
			'id'       => 'layout',
			'label'    => __( 'Grid Layout', 'wp-grid-builder' ),
			'title'    => __( 'Grid Layout', 'wp-grid-builder' ),
			'subtitle' => __( 'Control the layout of the grid and the card positions.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'grid-outline', true ),
		],
		[
			'id'       => 'carousel',
			'label'    => __( 'Grid Carousel', 'wp-grid-builder' ),
			'title'    => __( 'Grid Carousel', 'wp-grid-builder' ),
			'subtitle' => __( 'Set up the layout and behaviour of slides in the carousel.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'slider', true ),
		],
		[
			'id'       => 'builder',
			'label'    => __( 'Grid Builder', 'wp-grid-builder' ),
			'title'    => __( 'Grid Builder', 'wp-grid-builder' ),
			'subtitle' => __( 'Build the grid layout and add facets to filter the grid content.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'layout', true ),
		],
		[
			'id'       => 'cards',
			'label'    => __( 'Card Styles', 'wp-grid-builder' ),
			'title'    => __( 'Card Styles', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'card', true ),
			'subtitle' => __( 'Assign cards to the different types of content queried in the grid.', 'wp-grid-builder' ) . '<br>' .
			( ! $has_cards ?
				(
					__( 'You don\'t have any card yet. Use the link below to import card demos.', 'wp-grid-builder' ) . '<br><br>' .
					'<a class="wpgb-button wpgb-button-small wpgb-button-icon wpgb-green" href="' . esc_url( $demo_link ) . '" target="_blank">' .
					Helpers::get_icon( 'card', false, false ) .
					__( 'Import Card Demos', 'wp-grid-builder' ) .
					'</a>'
				) : (
					'<br><a class="wpgb-button wpgb-button-small wpgb-button-icon wpgb-green" href="' . esc_url( $card_link ) . '" target="_blank">' .
					Helpers::get_icon( 'card', false, false ) .
					__( 'Create a Card', 'wp-grid-builder' ) .
					'</a>'
				)
			),
		],
		[
			'id'       => 'animations',
			'label'    => __( 'Animations', 'wp-grid-builder' ),
			'title'    => __( 'Animations', 'wp-grid-builder' ),
			'subtitle' => __( 'Add animations to reveal cards when scrolling page.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'animation', true ),
		],
		[
			'id'       => 'loading',
			'label'    => __( 'Loading', 'wp-grid-builder' ),
			'title'    => __( 'Loading', 'wp-grid-builder' ),
			'subtitle' => __( 'Control the loading of images and content in the grid.', 'wp-grid-builder' ),
			'icon'     => Helpers::get_icon( 'load', true ),
		],
		[
			'id'       => 'customization',
			'label'    => __( 'Customization', 'wp-grid-builder' ),
			'title'    => __( 'Customization', 'wp-grid-builder' ),
			'subtitle' => __( 'Customization is not covered by the scope of the support included with the plugin.', 'wp-grid-builder' ) . '<br>' .
						sprintf(
							/* translators: 1: external url, 2: rel external */
							__( 'If you are looking for customization service, we recommend <a href="%1$s" rel="%2$s" target="_blank">Codeable.io</a>.', 'wp-grid-builder' ),
							'https://codeable.io/?ref=paT8V',
							'external noopener noreferrer'
						),
			'icon'     => Helpers::get_icon( 'code', true ),
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
			'id'     => 'messages_section',
			'tab'    => 'naming',
			'type'   => 'section',
			'title'  => __( 'Error Messages', 'wp-grid-builder' ),
			'fields' => $error_messages,
		],
		[
			'id'     => 'source_type_section',
			'tab'    => 'query',
			'type'   => 'section',
			'title'  => __( 'Content Type', 'wp-grid-builder' ),
			'fields' => $content_type,
		],
		[
			'id'     => 'queried_number_section',
			'tab'    => 'query',
			'type'   => 'section',
			'title'  => __( 'Items Number', 'wp-grid-builder' ),
			'fields' => $items_number,
		],
		[
			'id'                => 'queried_order_section',
			'tab'               => 'query',
			'type'              => 'section',
			'title'             => __( 'Ordering', 'wp-grid-builder' ),
			'fields'            => $queried_order,
			'conditional_logic' => [
				[
					'field'   => 'source',
					'compare' => 'IN',
					'value'   => [ 'post_type', 'term', 'user' ],
				],
			],
		],
		[
			'id'                => 'queried_posts_section',
			'tab'               => 'query',
			'type'              => 'section',
			'title'             => __( 'Post Types', 'wp-grid-builder' ),
			'fields'            => $queried_posts,
			'conditional_logic' => [
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'post_type',
				],
			],
		],
		[
			'id'                => 'queried_users_section',
			'tab'               => 'query',
			'type'              => 'section',
			'title'             => __( 'Users & Roles', 'wp-grid-builder' ),
			'fields'            => $queried_users,
			'conditional_logic' => [
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'user',
				],
			],
		],
		[
			'id'                => 'queried_taxonomies_section',
			'tab'               => 'query',
			'type'              => 'section',
			'title'             => __( 'Taxonomies & Terms', 'wp-grid-builder' ),
			'fields'            => $queried_taxonomy_terms,
			'conditional_logic' => [
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'term',
				],
			],
		],
		[
			'id'                => 'queried_terms_section',
			'tab'               => 'query',
			'type'              => 'section',
			'title'             => __( 'Taxonomies', 'wp-grid-builder' ),
			'fields'            => $queried_terms,
			'conditional_logic' => [
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'post_type',
				],
			],
		],
		[
			'id'                => 'queried_custom_fields_section',
			'tab'               => 'query',
			'type'              => 'section',
			'title'             => __( 'Custom Fields', 'wp-grid-builder' ),
			'fields'            => $queried_meta_data,
			'conditional_logic' => [
				[
					'field'   => 'source',
					'compare' => 'IN',
					'value'   => [ 'post_type', 'term', 'user' ],
				],
			],
		],
		[
			'id'     => 'supported_formats_section',
			'tab'    => 'formats',
			'type'   => 'section',
			'title'  => __( 'Supported Formats', 'wp-grid-builder' ),
			'fields' => $media_formats,
		],
		[
			'id'     => 'default_thumbnail_section',
			'tab'    => 'formats',
			'type'   => 'section',
			'title'  => __( 'Default Thumbnail', 'wp-grid-builder' ),
			'fields' => $default_thumbnail,
		],
		[
			'id'                => 'thumbnail_ratio_section',
			'tab'               => 'formats',
			'type'              => 'section',
			'title'             => __( 'Thumbnail Ratio', 'wp-grid-builder' ),
			'fields'            => $thumbnail_ratio,
			'conditional_logic' => [
				[
					'field'   => 'type',
					'compare' => '===',
					'value'   => 'masonry',
				],
			],
		],
		[
			'id'                => 'thumbnail_sizes_section',
			'tab'               => 'formats',
			'type'              => 'section',
			'title'             => __( 'Thumbnail Sizes', 'wp-grid-builder' ),
			'fields'            => $thumbnail_sizes,
			'conditional_logic' => [
				[
					'field'   => 'source',
					'compare' => 'IN',
					'value'   => [ 'post_type', 'term', 'user' ],
				],
			],
		],
		[
			'id'     => 'grid_type_section',
			'tab'    => 'layout',
			'type'   => 'section',
			'title'  => __( 'Layout Type', 'wp-grid-builder' ),
			'fields' => $layout_type,
		],
		[
			'id'                => 'grid_behaviour_section',
			'tab'               => 'layout',
			'type'              => 'section',
			'title'             => __( 'Cards Position', 'wp-grid-builder' ),
			'fields'            => $cards_position,
			'conditional_logic' => [
				[
					'field'   => 'type',
					'compare' => '!==',
					'value'   => 'metro',
				],
			],
		],
		[
			'id'                => 'grid_cards_aspect_section',
			'tab'               => 'layout',
			'type'              => 'section',
			'title'             => __( 'Cards Aspect', 'wp-grid-builder' ),
			'fields'            => $cards_aspect,
			'conditional_logic' => [
				[
					'field'   => 'type',
					'compare' => '!==',
					'value'   => 'justified',
				],
			],
		],
		[
			'id'     => 'responsivity_section',
			'tab'    => 'layout',
			'type'   => 'section',
			'title'  => __( 'Grid Responsivity', 'wp-grid-builder' ),
			'fields' => $grid_responsivity,
		],
		[
			'id'     => 'layout_type_section',
			'tab'    => 'carousel',
			'type'   => 'section',
			'title'  => __( 'Cards Layout', 'wp-grid-builder' ),
			'fields' => $cards_layout,
		],
		[
			'id'                => 'carousel_slides_section',
			'tab'               => 'carousel',
			'type'              => 'section',
			'title'             => __( 'Slide Positions', 'wp-grid-builder' ),
			'fields'            => $slide_positions,
			'conditional_logic' => [
				[
					'field'   => 'layout',
					'compare' => '===',
					'value'   => 'horizontal',
				],
			],
		],
		[
			'id'                => 'carousel_physics_section',
			'tab'               => 'carousel',
			'type'              => 'section',
			'title'             => __( 'Physics Behaviour', 'wp-grid-builder' ),
			'fields'            => $physics_behaviour,
			'conditional_logic' => [
				[
					'field'   => 'layout',
					'compare' => '===',
					'value'   => 'horizontal',
				],
			],
		],
		[
			'id'                => 'carousel_appearance_section',
			'tab'               => 'carousel',
			'type'              => 'section',
			'title'             => __( 'Appearance', 'wp-grid-builder' ),
			'fields'            => $carousel_appearance,
			'conditional_logic' => [
				[
					'field'   => 'layout',
					'compare' => '===',
					'value'   => 'horizontal',
				],
			],
		],
		[
			'id'     => 'grid_builder_section',
			'tab'    => 'builder',
			'type'   => 'section',
			'fields' => $grid_builder,
		],
		[
			'id'     => 'default_card_section',
			'tab'    => 'cards',
			'type'   => 'section',
			'title'  => __( 'Default Card', 'wp-grid-builder' ),
			'fields' => $default_card,
		],
		[
			'id'                => 'post_type_cards_section',
			'tab'               => 'cards',
			'type'              => 'section',
			'title'             => __( 'Post Type Cards', 'wp-grid-builder' ),
			'fields'            => $post_type_cards,
			'conditional_logic' => [
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'post_type',
				],
			],
		],
		[
			'id'                => 'post_format_cards_section',
			'tab'               => 'cards',
			'type'              => 'section',
			'title'             => __( 'Post Format Cards', 'wp-grid-builder' ),
			'fields'            => $post_format_cards,
			'conditional_logic' => [
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'post_type',
				],
			],
		],
		[
			'id'     => 'card_colors_section',
			'tab'    => 'cards',
			'type'   => 'section',
			'title'  => __( 'Card Colors', 'wp-grid-builder' ),
			'fields' => $card_colors,
		],
		[
			'id'     => 'animations_section',
			'tab'    => 'animations',
			'type'   => 'section',
			'title'  => __( 'Cards Animation', 'wp-grid-builder' ),
			'fields' => $cards_animation,
		],
		[
			'id'     => 'lazy_load_section',
			'tab'    => 'loading',
			'type'   => 'section',
			'title'  => __( 'Images Loading', 'wp-grid-builder' ),
			'fields' => $images_loading,
		],
		[
			'id'     => 'loader_section',
			'tab'    => 'loading',
			'type'   => 'section',
			'title'  => __( 'Content Loading', 'wp-grid-builder' ),
			'fields' => $content_loading,
		],
		[
			'id'     => 'customization_section',
			'tab'    => 'customization',
			'type'   => 'section',
			'title'  => __( 'Custom CSS', 'wp-grid-builder' ),
			'fields' => [
				// custom_html.
				[
					'id'    => 'custom_css',
					'type'  => 'code',
					'mode'  => 'css',
					'label' => __( 'Enter your CSS code:', 'wp-grid-builder' ),
				],

			],
		],
		$custom_js,
	],
];

$defaults = require WPGB_PATH . 'admin/settings/defaults/grid.php';

wp_grid_builder()->settings->register( $grid_settings, $defaults );
