<?php
/**
 * Settings wrapper end template
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $pagenow;

if ( 'term.php' === $pagenow || 'user-edit.php' === $pagenow ) {
	echo '<table id="wpgb" class="form-table"><tbody>';
} else {
	echo '<div class="wpgb-settings">';
}
