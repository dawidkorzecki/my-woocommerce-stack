<?php
/**
 * Wrapper start
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

global $is_IE;

// Inline icons sprite for IE.
if ( $is_IE ) {
	include_once WPGB_PATH . 'admin/assets/svg/sprite.svg';
}

$current_page = Helpers::get_plugin_page();

?>
<div id="wpgb" class="<?php echo sanitize_html_class( WPGB_SLUG . '-' . $current_page . '-page' ); ?>">
<?php
