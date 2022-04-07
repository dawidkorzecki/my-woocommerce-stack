<?php
/**
 * Settings header template
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $this->settings['header'] ) ) {
	return;
}

echo '<div class="wpgb-settings-header">';

	echo '<div class="wpgb-settings-header-left">';
	include 'toggle.php';
	echo '</div>';

	echo '<div class="wpgb-settings-header-right">';
	include 'buttons.php';
	echo '</div>';

echo '</div>';
