<?php
/**
 * Cards overview page
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
	'page'   => WPGB_SLUG . '-card-builder',
	'create' => 'true',
];

?>
<div class="wpgb-admin-section wpgb-admin-section-action">

	<h2><?php esc_html_e( 'All Cards', 'wp-grid-builder' ); ?></h2>

	<a class="wpgb-button wpgb-button-icon wpgb-green" href="<?php echo esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) ); ?>">
		<?php Helpers::get_icon( 'card' ); ?>
		<?php esc_html_e( 'Create a Card', 'wp-grid-builder' ); ?>
	</a>

</div>
<?php

$items = Database::count_items( 'cards' );

if ( empty( $items ) ) {

	?>
	<div class="wpgb-list-table-empty">

	<?php
	$demo = 'cards';
	$json = Import::get_demo_content( $demo );
	?>

		<h3>
			<?php esc_html_e( 'You don\'t have any cards yet!', 'wp-grid-builder' ); ?>
			<br>
			<?php
			if ( $json ) {
				esc_html_e( 'Please, find below some demos to get started.', 'wp-grid-builder' );
			} else {
				esc_html_e( 'Please, click on "create a card" to get started.', 'wp-grid-builder' );
			}
			?>
		</h3>

		<?php

		if ( $json ) {

			$list = Import::get_json_content( $json );
			require_once WPGB_PATH . 'admin/views/modules/import-list.php';

		}

		?>

		<p class="wpgb-list-table-nb">
			<?php
			printf(
				/* translators: %s: Create facet document guide url */
				wp_kses_post( __( 'To get started with cards, you can take a look at this <a href="%s" target="_blank">documentation guide</a>.', 'wp-grid-builder' ) ),
				esc_url( 'https://docs.wpgridbuilder.com/resources/guide-create-a-card/' )
			);
			?>
		</p>

	</div>
	<?php

	return;

}

?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wpgb_card_form" method="post" enctype="multipart/form-data">

	<input type="hidden" name="action" value="<?php echo esc_attr( WPGB_SLUG . '_form' ); ?>">

	<?php
	wp_nonce_field( WPGB_SLUG . '_actions_cards_bulk', WPGB_SLUG . '_actions_nonce', false );
	$list_table = new List_Table( [ 'id' ] );
	require_once WPGB_PATH . 'admin/views/tables/cards.php';
	?>

</form>
<?php
