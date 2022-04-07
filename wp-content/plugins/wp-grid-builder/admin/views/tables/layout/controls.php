<?php
/**
 * Table controls
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
<div class="wpgb-list-table-controls">

	<div class="wpgb-list-table-bulk-actions wpgb-select-apply">

		<label class="wpgb-sr-only" for="wpgb-bulk-action"><?php esc_html_e( 'Select bulk action', 'wp-grid-builder' ); ?></label>
		<select id="wpgb-bulk-action" class="wpgb-select wpgb-select-has-clear">
			<option value="" selected><?php esc_html_e( 'Bulk Actions', 'wp-grid-builder' ); ?></option>
			<option value="duplicate"><?php esc_html_e( 'Duplicate', 'wp-grid-builder' ); ?></option>
			<option value="delete"><?php esc_html_e( 'Delete', 'wp-grid-builder' ); ?></option>
			<option value="export"><?php esc_html_e( 'Export', 'wp-grid-builder' ); ?></option>
		</select>

		<button type="button" class="wpgb-button"><?php esc_html_e( 'Apply', 'wp-grid-builder' ); ?></button>

	</div>

	<div class="wpgb-list-table-search wpgb-search-field">
		<?php Helpers::get_icon( 'search' ); ?>
		<label class="wpgb-sr-only" for="wpgb-search-input"><?php esc_html_e( 'Search items', 'wp-grid-builder' ); ?></label>
		<input type="search" id="wpgb-search-input" class="wpgb-input" placeholder="<?php esc_attr_e( 'Type to search', 'wp-grid-builder' ); ?>">
	</div>

</div>
<?php
