<?php
/**
 * Import panel
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

?>
<div class="wpgb-admin-section">

	<h2><?php esc_html_e( 'Import Content', 'wp-grid-builder' ); ?></h2>

	<p class="wpgb-admin-section-subtitle">
		<?php esc_html_e( 'Import your grids, cards, facets or settings from a .json file.', 'wp-grid-builder' ); ?>
		<br>
		<?php esc_html_e( 'This file can be obtained by exporting your content using the form above.', 'wp-grid-builder' ); ?>
	</p>

</div>

<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

	<input type="hidden" name="action" value="<?php echo esc_attr( WPGB_SLUG . '_form' ); ?>">
	<?php wp_nonce_field( WPGB_SLUG . '_import_upload', WPGB_SLUG . '_upload_field' ); ?>

	<button type="button" class="wpgb-button wpgb-button-icon wpgb-button-small wpgb-browse-demo" data-demo="cards">
	<?php
		Helpers::get_icon( 'card' );
		esc_html_e( 'Browse demos', 'wp-grid-builder' );
	?>
	</button>

	<div class="wpgb-uploader">
		<input type="file" id="wpgb-import-items" class="wpgb-file-input" aria-label="<?php esc_attr_e( 'Upload file', 'wp-grid-builder' ); ?>">
		<label for="wpgb-import-items">
			<?php Helpers::get_icon( 'upload' ); ?>
			<span class="wpgb-uploader-message"><?php esc_html_e( 'Chose a file or drag it here.', 'wp-grid-builder' ); ?></span>
		</label>
		<button type="button" class="wpgb-button wpgb-red wpgb-uploader-remove" data-tooltip aria-label="<?php esc_attr_e( 'Go Back', 'wp-grid-builder' ); ?>" tabindex="-1">
			<?php Helpers::get_icon( 'back' ); ?>
		</button>
	</div>

</form>

<?php
