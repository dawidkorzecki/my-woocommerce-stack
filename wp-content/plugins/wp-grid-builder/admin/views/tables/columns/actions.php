<?php
/**
 * Table action buttons
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

$nonce_action    = 'wpgb_actions_' . $this->table . '_%s_' . $this->item['id'];
$duplicate_nonce = wp_create_nonce( sprintf( $nonce_action, 'duplicate' ) );
$delete_nonce    = wp_create_nonce( sprintf( $nonce_action, 'delete' ) );
$edit_link       = $this->get_edit_link();

?>
<div class="wpgb-list-table-column" data-colname="actions">
	<div class="wpgb-list-table-actions">

		<?php if ( 'grids' === $this->table ) { ?>

			<button type="button" data-action="preview" aria-label="<?php esc_attr_e( 'Preview', 'wp-grid-builder' ); ?>" data-tooltip>
				<?php Helpers::get_icon( 'preview' ); ?>
			</button>

		<?php } ?>

		<button type="button" data-action="export" aria-label="<?php esc_attr_e( 'Export', 'wp-grid-builder' ); ?>"  data-tooltip>
			<?php Helpers::get_icon( 'export' ); ?>
		</button>

		<button type="button" data-action="delete" data-nonce="<?php echo esc_attr( $delete_nonce ); ?>" aria-label="<?php esc_attr_e( 'Delete', 'wp-grid-builder' ); ?>" data-tooltip>
			<?php Helpers::get_icon( 'delete' ); ?>
		</button>

		<button type="button" data-action="duplicate" data-nonce="<?php echo esc_attr( $duplicate_nonce ); ?>" aria-label="<?php esc_attr_e( 'Duplicate', 'wp-grid-builder' ); ?>" data-tooltip>
			<?php Helpers::get_icon( 'duplicate' ); ?>
		</button>

		<a href="<?php echo esc_url( $edit_link ); ?>" aria-label="<?php esc_attr_e( 'Edit', 'wp-grid-builder' ); ?>" data-tooltip>
			<?php Helpers::get_icon( 'settings' ); ?>
		</a>

	</div>
</div>
<?php
