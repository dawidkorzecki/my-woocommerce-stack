<?php
/**
 * Settings tab template
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$selected = $active === $args['id'];

printf(
	'<li role="tab" aria-selected="%1$s" aria-controls="wpgb-%3$s-tab" tabindex="%2$s">',
	esc_attr( $selected ? 'true' : 'false' ),
	esc_attr( $selected ? 0 : -1 ),
	esc_attr( $args['id'] )
);

echo '<span>';

if ( ! empty( $args['icon'] ) ) {
	echo '<svg><use xlink:href="' . esc_url( $args['icon'] ) . '"></use></svg>';
}

echo '<span>' . esc_html( $args['label'] ) . '</span>';
echo '</span>';
echo '</li>';
