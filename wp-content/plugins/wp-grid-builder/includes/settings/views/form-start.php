<?php
/**
 * Form start template
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $this->has_form() ) {
	return;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$object_id = ! empty( $_GET['id'] ) ? (int) $_GET['id'] : 0;
$collapsed = $this->is_collapsed() ? ' wpgb-settings-collapsed' : '';

printf(
	'<form action="%2$s" id="%1$s_form" class="%1$s_%3$s_settings_form%4$s" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="option_page" value="%1$s_%3$s">
		<input type="hidden" name="%1$s_id" value="%5$d">',
	esc_attr( $this->slug ),
	esc_url( admin_url( 'admin-post.php' ) ),
	sanitize_html_class( $this->settings['id'] ),
	esc_attr( $collapsed ),
	esc_attr( $object_id )
);

wp_nonce_field( WPGB_SLUG . '_' . $this->settings['id'] . '_settings', WPGB_SLUG . '_fields_nonce', true );
