<?php
/**
 * Default Preview settings
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
	'id'                    => 'cards-preview',
	'name'                  => 'cards-preview',
	'is_dynamic'            => true,
	'is_overview'           => true,
	'no_posts_msg'          => esc_html__( 'Sorry, no cards were found!', 'wp-grid-builder' ),
	'source'                => 'card',
	'type'                  => 'masonry',
	'cards'                 => [
		'card' => 'kampala',
	],
	'thumbnail_aspect'      => 1,
	'thumbnail_ratio'       => [
		'x' => 4,
		'y' => 3,
	],
	'card_sizes'            => [
		[
			'browser' => 9999,
			'columns' => 4,
			'height'  => 240,
			'gutter'  => 32,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
		[
			'browser' => 1920,
			'columns' => 4,
			'height'  => 240,
			'gutter'  => -1,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
		[
			'browser' => 1680,
			'columns' => 3,
			'height'  => 220,
			'gutter'  => -1,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
		[
			'browser' => 1280,
			'columns' => 2,
			'height'  => 220,
			'gutter'  => -1,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
		[
			'browser' => 680,
			'columns' => 1,
			'height'  => 200,
			'gutter'  => -1,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
		[
			'browser' => 320,
			'columns' => 1,
			'height'  => 200,
			'gutter'  => -1,
			'ratio'   => [
				'x' => 4,
				'y' => 3,
			],
		],
	],
	'horizontal_order'      => true,
	'animation'             => 'wpgb_animation_1',
	'timing_function'       => 'custom',
	'cubic_bezier_function' => 'cubic-bezier(0.1,0.3,0.2,1)',
	'transition'            => 700,
	'transition_delay'      => 100,
];
