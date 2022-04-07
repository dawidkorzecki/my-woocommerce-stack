<?php
/**
 * Settings tabs template
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active = $this->get_active_tab();

echo '<div class="wpgb-settings-tabs">';
echo '<ul role="tablist">';

foreach ( $this->get_tabs() as $args ) {
	include 'tab.php';
}

echo '</ul>';
echo '</div>';
