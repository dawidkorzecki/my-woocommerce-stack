<?php
/**
 * Default Builder settings
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	'general' => [
		'name'             => esc_html__( 'New Card', 'wp-grid-builder' ),
		'type'             => 'masonry',
		'card_layout'      => 'vertical',
		'switch_layout'    => '768px',
		'content_position' => 'bottom',
		'media_position'   => 'left',
		'media_width'      => '50%',
		'display_media'    => 1,
		'display_overlay'  => 1,
		'display_footer'   => 1,
		'flex_media'       => 0,
		'card_width'       => '500px',
		'responsive'       => 0,
		'global_css'       => '',
	],
	'layout'  => [],
	'blocks'  => [],
	'layers'  => [],
];
