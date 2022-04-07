<?php
/**
 * Cards overview table
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = $list_table->query( 'cards' );
$list_table->get_controls();

?>
<div class="wpgb-list-table-wrapper">

	<?php $list_table->get_header( [ 'select', 'name', 'type', 'favorite', 'modified_date' ] ); ?>

	<ul class="wpgb-list-table-rows">

		<?php
		if ( empty( $items ) ) {

			?>
			<li class="wpgb-list-table-row wpgb-list-table-noresult">
				<h4><?php esc_html_e( 'Sorry, no cards were found!', 'wp-grid-builder' ); ?></h4>
			</li>
			<?php

		}
		?>

		<li class="wpgb-list-table-cards">
			<div class="wpgb-grid-preview-inner">
				<?php include_once WPGB_PATH . 'admin/views/modules/loader.php'; ?>
			</div>
		</li>

	</ul>

	<?php $list_table->get_footer(); ?>

</div>
<?php
