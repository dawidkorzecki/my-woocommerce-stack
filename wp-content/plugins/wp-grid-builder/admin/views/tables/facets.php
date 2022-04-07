<?php
/**
 * Facets overview table
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

$i18n = [
	'wordpress'     => __( 'WordPress Engine', 'wp-grid-builder' ),
	'relevanssi'    => __( 'Relevanssi Engine', 'wp-grid-builder' ),
	'searchwp'      => __( 'SearchWP Engine', 'wp-grid-builder' ),
	'post_meta'     => __( 'Post', 'wp-grid-builder' ) . ' &rsaquo; ' . __( 'Custom Field', 'wp-grid-builder' ),
	'user_meta'     => __( 'User', 'wp-grid-builder' ) . ' &rsaquo; ' . __( 'Custom Field', 'wp-grid-builder' ),
	'term_meta'     => __( 'Term', 'wp-grid-builder' ) . ' &rsaquo; ' . __( 'Custom Field', 'wp-grid-builder' ),
	'post_field'    => __( 'Post', 'wp-grid-builder' ) . ' &rsaquo; ' . __( 'Field', 'wp-grid-builder' ),
	'user_field'    => __( 'User', 'wp-grid-builder' ) . ' &rsaquo; ' . __( 'Field', 'wp-grid-builder' ),
	'term_field'    => __( 'Term', 'wp-grid-builder' ) . ' &rsaquo; ' . __( 'Field', 'wp-grid-builder' ),
	'name'          => __( 'Name', 'wp-grid-builder' ),
	'slug'          => __( 'Slug', 'wp-grid-builder' ),
	'taxonomy'      => __( 'Taxonomy', 'wp-grid-builder' ),
	'term_group'    => __( 'Group', 'wp-grid-builder' ),
	'display_name'  => __( 'Display Name', 'wp-grid-builder' ),
	'first_name'    => __( 'First Name', 'wp-grid-builder' ),
	'last_name'     => __( 'Last Name', 'wp-grid-builder' ),
	'nickname'      => __( 'Nickname', 'wp-grid-builder' ),
	'roles'         => __( 'Roles', 'wp-grid-builder' ),
	'post_type'     => __( 'Type', 'wp-grid-builder' ),
	'post_date'     => __( 'Date', 'wp-grid-builder' ),
	'post_modified' => __( 'Modified Date', 'wp-grid-builder' ),
	'post_title'    => __( 'Title', 'wp-grid-builder' ),
	'post_author'   => __( 'Author', 'wp-grid-builder' ),
];

$facets = apply_filters( 'wp_grid_builder/facets', [] );

$facet_names = array_map(
	function( $facet ) {
		return ! empty( $facet['name'] ) ? $facet['name'] : '';
	},
	$facets
);

$fields = apply_filters( 'wp_grid_builder/custom_fields', [] );
$fields = ! empty( $fields ) ? call_user_func_array( 'array_merge', array_values( $fields ) ) : $fields;

$i18n = array_merge( $i18n, $facet_names );
$i18n = array_merge( $i18n, Helpers::get_taxonomies_list() );
$i18n = array_merge( $i18n, $fields );

$items   = $list_table->query( 'facets' );
$columns = [ 'select', 'favorite', 'type', 'name', 'source', 'shortcode', 'modified_date', 'actions' ];

$list_table->get_controls();

?>
<div class="wpgb-list-table-wrapper">

	<?php $list_table->get_header( $columns ); ?>

	<ul class="wpgb-list-table-rows">
	<?php

	if ( empty( $items ) ) {

		?>
		<li class="wpgb-list-table-row wpgb-list-table-noresult">
			<h4><?php esc_html_e( 'Sorry, no facets were found!', 'wp-grid-builder' ); ?></h4>
		</li>
		<?php

	}

	foreach ( $items as $item ) {

		$list_table->the_item();

		$facet_type = ! empty( $item['type'] ) ? $item['type'] : 'filter';
		$facet_name = isset( $i18n[ $facet_type ] ) ? $i18n[ $facet_type ] : '';
		$facet_icon = ! empty( $facets[ $facet_type ]['icons']['small'] );
		$facet_icon = $facet_icon ? $facets[ $facet_type ]['icons']['small'] : Helpers::get_icon( 'filter-action-small', true );

		$source = $item['source'];
		$fields = explode( '/', $source );
		$object = array_shift( $fields );

		if ( in_array( $object, [ 'post_meta', 'term_meta', 'user_meta' ], true ) ) {

			$meta_key = implode( '/', $fields );

			if ( isset( $i18n[ $meta_key ] ) ) {
				$source = $object . '/' . $i18n[ $meta_key ];
			}
		}

		$source = explode( '/', $source );
		$source = array_map(
			function( $type ) use ( $i18n ) {

				if ( isset( $i18n[ $type ] ) ) {
					$type = $i18n[ $type ];
				}

				return $type;

			},
			$source
		);

		$source = implode( ' &rsaquo; ', $source );
		$source = $source ?: '-';

		$list_table->set( 'type', $facet_name );
		$list_table->set( 'source', $source );
		$list_table->set( 'icon', $facet_icon );
		$list_table->set( 'shortcode', '[wpgb_facet id="' . esc_attr( $item['id'] ) . '" grid="0"]' );

		?>
		<li class="wpgb-list-table-row" data-id="<?php echo esc_attr( $item['id'] ); ?>">
			<?php $list_table->get_columns( $columns ); ?>
		</li>
		<?php
	}

	?>
	</ul>

	<?php $list_table->get_footer(); ?>

</div>
<?php
