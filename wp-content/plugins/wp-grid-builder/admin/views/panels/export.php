<?php
/**
 * Export panel
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

?>
<div class="wpgb-admin-section">

	<h2><?php esc_html_e( 'Export Content', 'wp-grid-builder' ); ?></h2>

	<p class="wpgb-admin-section-subtitle">
		<?php esc_html_e( 'Export your grids, cards, facets, or settings for this site as a .json file.', 'wp-grid-builder' ); ?>
		<br>
		<?php esc_html_e( 'This allows you to easily save/backup your current data and import them into another site.', 'wp-grid-builder' ); ?>
	</p>

</div>

<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

	<input type="hidden" name="action" value="<?php echo esc_attr( WPGB_SLUG . '_form' ); ?>">
	<?php wp_nonce_field( WPGB_SLUG . '_export_items', WPGB_SLUG . '_export_field' ); ?>

	<div class="wpgb-select-apply wpgb-bulk-export">

		<?php
		$grids  = Database::count_items( 'grids' );
		$cards  = Database::count_items( 'cards' );
		$facets = Database::count_items( 'facets' );
		?>

		<label class="wpgb-sr-only" for="wpgb-export-items"><?php esc_html_e( 'Export items', 'wp-grid-builder' ); ?></label>
		<select id="wpgb-export-items" class="wpgb-select wpgb-select-has-clear" aria-label="<?php esc_attr_e( 'Select content', 'wp-grid-builder' ); ?>">

			<option value="" selected><?php esc_html_e( 'Content Type', 'wp-grid-builder' ); ?></option>

			<?php if ( $grids ) { ?>
				<option value="grids"><?php esc_html_e( 'All Grids', 'wp-grid-builder' ); ?> (<?php echo esc_html( $grids ); ?>)</option>
			<?php } ?>

			<?php if ( $cards ) { ?>
				<option value="cards"><?php esc_html_e( 'All Cards', 'wp-grid-builder' ); ?> (<?php echo esc_html( $cards ); ?>)</option>
			<?php } ?>

			<?php if ( $facets ) { ?>
				<option value="facets"><?php esc_html_e( 'All Facets', 'wp-grid-builder' ); ?> (<?php echo esc_html( $facets ); ?>)</option>
			<?php } ?>

			<option value="settings"><?php esc_html_e( 'Settings', 'wp-grid-builder' ); ?></option>

		</select>

		<button type="button" class="wpgb-button">
			<?php
			Helpers::get_icon( 'export' );
			esc_html_e( 'Export', 'wp-grid-builder' );
			?>
		</button>

	</div>
</form>
<?php
