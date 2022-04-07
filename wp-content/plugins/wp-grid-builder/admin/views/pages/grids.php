<?php
/**
 * Grids overview page
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Admin\Import;
use WP_Grid_Builder\Admin\List_Table;
use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query_args = [
	'page'   => WPGB_SLUG . '-grid-settings',
	'create' => 'true',
];

?>
<div class="wpgb-admin-section wpgb-admin-section-action">

	<h2><?php esc_html_e( 'All Grids', 'wp-grid-builder' ); ?></h2>

	<a class="wpgb-button wpgb-button-icon wpgb-green" href="<?php echo esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) ); ?>">
		<?php Helpers::get_icon( 'grid' ); ?>
		<?php esc_html_e( 'Create a Grid', 'wp-grid-builder' ); ?>
	</a>

</div>
<?php

$items = Database::count_items( 'grids' );

if ( empty( $items ) ) {

	?>
	<div class="wpgb-list-table-empty">

		<h3>
			<?php esc_html_e( 'You don\'t have any grids yet!', 'wp-grid-builder' ); ?>
			<br>
			<?php esc_html_e( 'Please, find below some demos to get started.', 'wp-grid-builder' ); ?>
		</h3>

		<?php
		$demo = 'grids';
		$json = Import::get_demo_content( $demo );
		$list = Import::get_json_content( $json );
		require_once WPGB_PATH . 'admin/views/modules/import-list.php';
		?>

		<p class="wpgb-list-table-nb">
			<?php esc_html_e( 'Above demos are just for layout settings purpose. Content displayed in demos will depend of your posts.', 'wp-grid-builder' ); ?>
			<br>
			<?php
			printf(
				/* translators: %s: Create grid document guide url */
				wp_kses_post( __( 'To get started with grids, you can take a look at this <a href="%s" target="_blank">documentation guide</a>.', 'wp-grid-builder' ) ),
				esc_url( 'https://docs.wpgridbuilder.com/resources/guide-create-a-grid/' )
			);
			?>
		</p>

	</div>
	<?php

	return;

}

?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wpgb_grids_form" method="post" enctype="multipart/form-data">

	<input type="hidden" name="action" value="<?php echo esc_attr( WPGB_SLUG . '_form' ); ?>">

	<?php
	wp_nonce_field( WPGB_SLUG . '_actions_grids_bulk', WPGB_SLUG . '_actions_nonce', false );
	$list_table = new List_Table( [ 'id', 'favorite', 'source', 'type', 'name', 'date', 'modified_date' ] );
	require_once WPGB_PATH . 'admin/views/tables/grids.php';
	?>

</form>
<?php

require_once WPGB_PATH . 'admin/views/modules/grid-preview.php';
