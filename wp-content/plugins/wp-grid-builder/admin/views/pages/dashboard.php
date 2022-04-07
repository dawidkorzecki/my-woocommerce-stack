<?php
/**
 * Dashboard page
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( current_user_can( 'manage_options' ) ) {
	?>
	<div class="wpgb-admin-panel">
	<?php require_once WPGB_PATH . 'admin/views/panels/plugin.php'; ?>
	</div>
<?php } ?>

<div class="wpgb-admin-panel">
<?php require_once WPGB_PATH . 'admin/views/panels/export.php'; ?>
</div>

<div class="wpgb-admin-panel">
<?php require_once WPGB_PATH . 'admin/views/panels/import.php'; ?>
</div>

<div class="wpgb-admin-panel">
<?php require_once WPGB_PATH . 'admin/views/panels/system.php'; ?>
</div>

<?php
