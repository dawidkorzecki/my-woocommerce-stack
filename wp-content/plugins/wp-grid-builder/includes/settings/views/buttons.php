<?php
/**
 * Settings header buttons template
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

$buttons = $this->get_header_buttons();

foreach ( $buttons as $button ) {

	echo '<button type="button" class="wpgb-button wpgb-button-icon wpgb-' . sanitize_html_class( $button['color'] ) . '" data-action="' . esc_attr( $button['action'] ) . '">';
	Helpers::get_icon( $button['icon'] );
	echo esc_html( $button['title'] );
	echo '</button>';

}
