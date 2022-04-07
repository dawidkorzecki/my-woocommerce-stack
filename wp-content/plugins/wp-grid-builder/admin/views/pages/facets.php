<?php
/**
 * Facets overview page
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
	'page'   => WPGB_SLUG . '-facet-settings',
	'create' => 'true',
];

?>
<div class="wpgb-admin-section wpgb-admin-section-action">

	<h2><?php esc_html_e( 'All Facets', 'wp-grid-builder' ); ?></h2>

	<button class="wpgb-button wpgb-button-icon wpgb-purple" data-action="index_all">
		<?php Helpers::get_icon( 'reset' ); ?>
		<?php esc_html_e( 'Re-index All', 'wp-grid-builder' ); ?>
	</button>

	<a class="wpgb-button wpgb-button-icon wpgb-green" href="<?php echo esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) ); ?>">
		<?php Helpers::get_icon( 'facet' ); ?>
		<?php esc_html_e( 'Create a Facet', 'wp-grid-builder' ); ?>
	</a>

</div>
<?php

$items = Database::count_items( 'facets' );

if ( empty( $items ) ) {

	?>
	<div class="wpgb-list-table-empty">

		<h3>
			<?php esc_html_e( 'You don\'t have any facets yet!', 'wp-grid-builder' ); ?>
			<br>
			<?php esc_html_e( 'Please, find below some demos to get started.', 'wp-grid-builder' ); ?>
		</h3>

		<?php
		$demo = 'facets';
		$json = Import::get_demo_content( $demo );
		$list = Import::get_json_content( $json );
		require_once WPGB_PATH . 'admin/views/modules/import-list.php';
		?>

		<p class="wpgb-list-table-nb">
			<?php
			printf(
				/* translators: %s: Create facet document guide url */
				wp_kses_post( __( 'To get started with facets, you can take a look at this <a href="%s" target="_blank">documentation guide</a>.', 'wp-grid-builder' ) ),
				esc_url( 'https://docs.wpgridbuilder.com/resources/guide-create-a-facet/' )
			);
			?>
		</p>

	</div>
	<?php

	return;

}

?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wpgb_facets_form" method="post" enctype="multipart/form-data">

	<input type="hidden" name="action" value="<?php echo esc_attr( WPGB_SLUG . '_form' ); ?>">

	<?php
	wp_nonce_field( WPGB_SLUG . '_actions_facets_bulk', WPGB_SLUG . '_actions_nonce', false );
	$list_table = new List_Table( [ 'id', 'favorite', 'source', 'type', 'name', 'date', 'modified_date' ] );
	require_once WPGB_PATH . 'admin/views/tables/facets.php';
	?>

</form>
<?php
