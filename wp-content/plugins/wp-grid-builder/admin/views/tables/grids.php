<?php
/**
 * Grids overview table
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

$items   = $list_table->query( 'grids' );
$columns = [ 'select', 'favorite', 'type', 'name', 'source', 'shortcode', 'modified_date', 'actions' ];
$i18n    = [
	'masonry'   => __( 'Masonry', 'wp-grid-builder' ),
	'justified' => __( 'Justified', 'wp-grid-builder' ),
	'metro'     => __( 'Metro', 'wp-grid-builder' ),
	'post_type' => __( 'Post Type', 'wp-grid-builder' ),
	'user'      => __( 'User', 'wp-grid-builder' ),
	'term'      => __( 'Term', 'wp-grid-builder' ),
];

$list_table->get_controls();

?>
<div class="wpgb-list-table-wrapper">

	<?php $list_table->get_header( $columns ); ?>

	<ul class="wpgb-list-table-rows">
	<?php

	if ( empty( $items ) ) {

		?>
		<li class="wpgb-list-table-row wpgb-list-table-noresult">
			<h4><?php esc_html_e( 'Sorry, no grids were found!', 'wp-grid-builder' ); ?></h4>
		</li>
		<?php

	}

	foreach ( $items as $item ) {

		$list_table->the_item();

		$layout = $item['type'] ?: 'masonry';
		$layout = ! empty( $i18n[ $layout ] ) ? $i18n[ $layout ] : $layout;
		$source = $item['source'] ?: 'post_type';
		$source = ! empty( $i18n[ $source ] ) ? $i18n[ $source ] : $source;

		$list_table->set( 'type', $layout );
		$list_table->set( 'source', $source );
		$list_table->set( 'icon', Helpers::get_icon( $item['type'] . '-grid', true ) );
		$list_table->set( 'shortcode', '[wpgb_grid id="' . esc_attr( $item['id'] ) . '"]' );

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
