<?php
/**
 * Settings template
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include 'form-start.php';
include 'header.php';
include 'wrapper-start.php';

if ( empty( $this->settings['tabs'] ) ) {
	$this->do_fields( $this->settings['fields'] );
} else {

	include 'tabs.php';
	include 'panels.php';

}

include 'wrapper-end.php';
include 'form-end.php';
