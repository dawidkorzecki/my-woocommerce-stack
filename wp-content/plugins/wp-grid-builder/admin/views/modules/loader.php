<?php
/**
 * Popup
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wpgb-preloader">
	<?php include_once WPGB_PATH . 'admin/assets/svg/icon.svg'; ?>
	<span><?php esc_html_e( 'Please wait, loading...', 'wp-grid-builder' ); ?></span>
</div>
<?php
