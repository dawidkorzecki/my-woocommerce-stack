<?php
/**
 * Settings tab panel template
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

$fields   = $this->get_tab_fields( $args['id'] );
$selected = $active === $args['id'] ? '' : ' hidden';

// Add SVG tags support to sanitize icon (SVG<use>).
$allowed_html_post = wp_kses_allowed_html( 'post' );
$allowed_svg_tags  = Helpers::allowed_svg_tags();
$allowed_html_tags = wp_parse_args( $allowed_svg_tags, $allowed_html_post );

echo '<div class="wpgb-settings-tab-content" id="' . sanitize_html_class( 'wpgb-' . $args['id'] . '-tab' ) . '"' . esc_attr( $selected ) . '>';

if ( $args['title'] || $args['subtitle'] ) {

	echo '<div class="wpgb-admin-section">';
		echo $args['title'] ? '<h2>' . esc_html( $args['title'] ) . '</h2>' : '';
		echo $args['subtitle'] ? '<p>' . wp_kses( $args['subtitle'], $allowed_html_tags ) . '</p>' : '';
	echo '</div>';

}

$this->do_fields( $fields );

echo '</div>';
