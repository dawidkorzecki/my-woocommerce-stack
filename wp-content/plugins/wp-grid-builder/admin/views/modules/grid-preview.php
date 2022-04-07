<?php
/**
 * Grid preview
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
<div class="wpgb-grid-preview-holder">

	<div class="wpgb-grid-preview-overlay"></div>

	<div class="wpgb-grid-preview-inner">

		<div class="wpgb-grid-preview-header">
			<ul>

			<?php
			for ( $i = 0; $i < 6; $i++ ) {

				?>
				<li class="<?php echo ( ! $i ? 'wpgb-active' : '' ); ?>">
					<?php Helpers::get_icon( 'screen-size-' . $i ); ?>
				</li>
				<?php

			}
			?>

			</ul>

			<div class="wpgb-search-field">
				<?php Helpers::get_icon( 'search' ); ?>
				<label class="wpgb-sr-only" for="wpgb-search-card"><?php esc_html_e( 'Search cards', 'wp-grid-builder' ); ?></label>
				<input type="search" id="wpgb-search-card" class="wpgb-input" placeholder="<?php esc_attr_e( 'Type to search cards', 'wp-grid-builder' ); ?>">
			</div>

			<div class="wpgb-grid-preview-close">
				<?php Helpers::get_icon( 'cross' ); ?>
			</div>

			<div class="wpgb-grid-preview-update">
				<?php Helpers::get_icon( 'update' ); ?>
			</div>

		</div>

		<?php include_once WPGB_PATH . 'admin/views/modules/loader.php'; ?>

	</div>

</div>
<?php
