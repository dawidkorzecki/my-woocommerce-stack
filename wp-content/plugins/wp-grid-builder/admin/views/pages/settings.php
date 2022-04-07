<?php
/**
 * Settings page
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings_values = get_option( WPGB_SLUG . '_global_settings' );

require_once WPGB_PATH . 'admin/settings/global.php';
wp_grid_builder()->settings->render( 'global', $settings_values );

