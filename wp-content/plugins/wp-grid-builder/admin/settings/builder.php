<?php
/**
 * Builder settings
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get meta keys.
$meta_options = apply_filters( 'wp_grid_builder/custom_fields', [], 'name' );
$meta_options = ! empty( $meta_options ) ? call_user_func_array( 'array_merge', array_values( $meta_options ) ) : $meta_options;

// Get custom blocks.
$custom_blocks = apply_filters( 'wp_grid_builder/blocks', [] );
$custom_blocks = array_filter(
	array_map(
		function( $args ) {
			return empty( $args['type'] ) ? $args : '';
		},
		$custom_blocks
	)
);

if ( ! empty( $custom_blocks ) ) {

	$custom_blocks = [
		// block_name.
		[
			'id'                => 'block_name',
			'tab'               => 'content',
			'type'              => 'select',
			'label'             => esc_html__( 'Block Name', 'wp-grid-builder' ),
			'placeholder'       => esc_html__( 'Select a block', 'wp-grid-builder' ),
			'options'           => array_combine(
				array_keys( $custom_blocks ),
				array_column( $custom_blocks, 'name' )
			),
			'conditional_logic' => [
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'custom_block',
				],
			],
		],
	];

}

$block_position = [
	[
		'id'     => 'position_accordion',
		'tab'    => 'idle',
		'title'  => esc_html__( 'Position', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// position.
			[
				'id'      => 'position',
				'type'    => 'select',
				'label'   => esc_html_x( 'Position', 'CSS Position property', 'wp-grid-builder' ),
				'value'   => 'relative',
				'options' => [
					'relative' => esc_html__( 'Relative', 'wp-grid-builder' ),
					'absolute' => esc_html__( 'Absolute', 'wp-grid-builder' ),
				],
			],
			// display.
			[
				'id'                => 'display',
				'type'              => 'select',
				'label'             => esc_html_x( 'Display', 'CSS Display property', 'wp-grid-builder' ),
				'value'             => 'block',
				'options'           => [
					'block'        => esc_html__( 'Block', 'wp-grid-builder' ),
					'inline'       => esc_html__( 'Inline', 'wp-grid-builder' ),
					'inline-block' => esc_html__( 'Inline-block', 'wp-grid-builder' ),
				],
				'conditional_logic' => [
					[
						'field'   => 'position',
						'compare' => '===',
						'value'   => 'relative',
					],
				],
			],
			// overflow.
			[
				'id'          => 'overflow',
				'type'        => 'select',
				'label'       => esc_html__( 'Overflow', 'wp-grid-builder' ),
				'width'       => 320,
				'tooltip'     => esc_html__( 'Overflow property is only applied in preview mode.', 'wp-grid-builder' ),
				'placeholder' => esc_html_x( 'None', 'Overflow default value', 'wp-grid-builder' ),
				'options'     => [
					'visible' => esc_html__( 'Visible', 'wp-grid-builder' ),
					'hidden'  => esc_html__( 'Hidden', 'wp-grid-builder' ),
				],
			],
			// vertical-align.
			[
				'id'                => 'vertical-align',
				'type'              => 'select',
				'label'             => esc_html__( 'Vertical Alignment', 'wp-grid-builder' ),
				'placeholder'       => esc_html_x( 'None', 'Vertical Alignment default value', 'wp-grid-builder' ),
				'options'           => [
					'top'    => esc_html__( 'Top', 'wp-grid-builder' ),
					'middle' => esc_html__( 'Middle', 'wp-grid-builder' ),
					'bottom' => esc_html__( 'Bottom', 'wp-grid-builder' ),
				],
				'conditional_logic' => [
					[
						'field'   => 'display',
						'compare' => '===',
						'value'   => 'inline-block',
					],
					[
						'field'   => 'position',
						'compare' => '===',
						'value'   => 'relative',
					],
				],
			],
			// float.
			[
				'id'                => 'float',
				'type'              => 'select',
				'label'             => esc_html__( 'Float', 'wp-grid-builder' ),
				'placeholder'       => esc_html_x( 'None', 'Float default value', 'wp-grid-builder' ),
				'options'           => [
					'left'  => esc_html__( 'Left', 'wp-grid-builder' ),
					'right' => esc_html__( 'Right', 'wp-grid-builder' ),
				],
				'conditional_logic' => [
					[
						'field'   => 'display',
						'compare' => '===',
						'value'   => 'inline-block',
					],
					[
						'field'   => 'position',
						'compare' => '===',
						'value'   => 'relative',
					],
				],
			],
			// clear.
			[
				'id'                => 'clear',
				'type'              => 'select',
				'label'             => esc_html__( 'Clear Floats', 'wp-grid-builder' ),
				'placeholder'       => esc_html_x( 'None', 'Clear default value', 'wp-grid-builder' ),
				'options'           => [
					'none'  => esc_html__( 'None', 'wp-grid-builder' ),
					'left'  => esc_html__( 'Left', 'wp-grid-builder' ),
					'right' => esc_html__( 'Right', 'wp-grid-builder' ),
					'both'  => esc_html_x( 'Both', 'Clear Floats default value', 'wp-grid-builder' ),
				],
				'conditional_logic' => [
					[
						'field'   => 'position',
						'compare' => '===',
						'value'   => 'relative',
					],
				],
			],
			// positions.
			[
				'id'                => 'positions',
				'type'              => 'group',
				'label'             => esc_html__( 'Positions', 'wp-grid-builder' ),
				'fields'            => [
					// top.
					[
						'id'    => 'top',
						'type'  => 'text_number',
						'label' => esc_html__( 'Top', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
					],
					// right.
					[
						'id'    => 'right',
						'type'  => 'text_number',
						'label' => esc_html__( 'Right', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
					],
					// bottom.
					[
						'id'    => 'bottom',
						'type'  => 'text_number',
						'label' => esc_html__( 'Bottom', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
					],
					// left.
					[
						'id'    => 'left',
						'type'  => 'text_number',
						'label' => esc_html__( 'Left', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
					],
				],
				'conditional_logic' => [
					[
						'field'   => 'position',
						'compare' => '===',
						'value'   => 'absolute',
					],
				],
			],
			// z-index.
			[
				'id'    => 'z-index',
				'type'  => 'number',
				'label' => esc_html__( 'Z-index (Stacking Order)', 'wp-grid-builder' ),
				'min'   => 0,
				'max'   => 100,
				'step'  => 1,
			],
		],
	],
];

$layer_position = [
	[
		'id'     => 'position_accordion',
		'tab'    => 'idle',
		'title'  => esc_html__( 'Position', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// overflow.
			[
				'id'          => 'overflow',
				'type'        => 'select',
				'label'       => esc_html__( 'Overflow', 'wp-grid-builder' ),
				'width'       => 320,
				'tooltip'     => esc_html__( 'Overflow property is only applied in preview mode.', 'wp-grid-builder' ),
				'placeholder' => esc_html_x( 'None', 'Overflow default value', 'wp-grid-builder' ),
				'options'     => [
					'visible' => esc_html__( 'Visible', 'wp-grid-builder' ),
					'hidden'  => esc_html__( 'Hidden', 'wp-grid-builder' ),
				],
			],
			// z-index.
			[
				'id'    => 'z-index',
				'type'  => 'number',
				'label' => esc_html__( 'Z-index (Stacking Order)', 'wp-grid-builder' ),
				'min'   => 0,
				'max'   => 100,
				'step'  => 1,
			],
		],
	],
];

$sizing = [
	[
		'id'     => 'sizing_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Sizing', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// width.
			[
				'id'    => 'width',
				'type'  => 'text_number',
				'label' => esc_html__( 'Width', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
				'units' => [ 'px', '%', 'em', 'rem' ],
				'min'   => 0,
				'max'   => 999,
			],
			// height.
			[
				'id'    => 'height',
				'type'  => 'text_number',
				'label' => esc_html__( 'Height', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
				'units' => [ 'px', '%', 'em', 'rem' ],
				'min'   => 0,
				'max'   => 999,
			],
		],
	],
];

$spacing = [
	[
		'id'     => 'spacing_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Spacing', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// margins.
			[
				'id'     => 'margins',
				'type'   => 'group',
				'label'  => esc_html__( 'Margin', 'wp-grid-builder' ),
				'fields' => [
					// margin-top.
					[
						'id'    => 'margin-top',
						'type'  => 'text_number',
						'label' => esc_html__( 'Top', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
					],
					// margin-right.
					[
						'id'    => 'margin-right',
						'type'  => 'text_number',
						'label' => esc_html__( 'Right', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
					],
					// margin-bottom.
					[
						'id'    => 'margin-bottom',
						'type'  => 'text_number',
						'label' => esc_html__( 'Bottom', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
					],
					// margin-left.
					[
						'id'    => 'margin-left',
						'type'  => 'text_number',
						'label' => esc_html__( 'Left', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
					],
				],
			],
			// paddings.
			[
				'id'     => 'paddings',
				'type'   => 'group',
				'label'  => esc_html__( 'Padding', 'wp-grid-builder' ),
				'fields' => [
					// padding-top.
					[
						'id'    => 'padding-top',
						'type'  => 'text_number',
						'label' => esc_html__( 'Top', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
					// padding-right.
					[
						'id'    => 'padding-right',
						'type'  => 'text_number',
						'label' => esc_html__( 'Right', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
					// padding-bottom.
					[
						'id'    => 'padding-bottom',
						'type'  => 'text_number',
						'label' => esc_html__( 'Bottom', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
					// padding-left.
					[
						'id'    => 'padding-left',
						'type'  => 'text_number',
						'label' => esc_html__( 'Left', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
				],
			],
		],
	],
];

$border = [
	[
		'id'     => 'border_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Border', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// Nota bene.
			[
				'id'    => 'nb-4',
				'type'  => 'notabene',
				'value' => esc_html__( 'In order to apply border radius, you generally need to set "overflow" property to "hidden" under "position" tab.', 'wp-grid-builder' ),
			],
			// border_radius.
			[
				'id'     => 'border_radius',
				'type'   => 'group',
				'label'  => esc_html__( 'Border Radius', 'wp-grid-builder' ),
				'fields' => [
					// border-top-left-radius.
					[
						'id'    => 'border-top-left-radius',
						'type'  => 'text_number',
						'label' => esc_html__( 'Top/Left', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
					// border-top-right-radius.
					[
						'id'    => 'border-top-right-radius',
						'type'  => 'text_number',
						'label' => esc_html__( 'Top/Right', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
					// border-bottom-right-radius.
					[
						'id'    => 'border-bottom-right-radius',
						'type'  => 'text_number',
						'label' => esc_html__( 'Bottom/Right', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
					// border-bottom-left-radius.
					[
						'id'    => 'border-bottom-left-radius',
						'type'  => 'text_number',
						'label' => esc_html__( 'Bottom/Left', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
				],
			],
			// border_widths.
			[
				'id'     => 'border_widths',
				'type'   => 'group',
				'label'  => esc_html__( 'Border Width', 'wp-grid-builder' ),
				'fields' => [
					// border-top-width.
					[
						'id'    => 'border-top-width',
						'type'  => 'text_number',
						'label' => esc_html__( 'Top', 'wp-grid-builder' ),
						'steps' => [ 1, 0.0001, 0.0001 ],
						'units' => [ 'px', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
					// border-right-width.
					[
						'id'    => 'border-right-width',
						'type'  => 'text_number',
						'label' => esc_html__( 'Right', 'wp-grid-builder' ),
						'steps' => [ 1, 0.0001, 0.0001 ],
						'units' => [ 'px', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
					// border-bottom-width.
					[
						'id'    => 'border-bottom-width',
						'type'  => 'text_number',
						'label' => esc_html__( 'Bottom', 'wp-grid-builder' ),
						'steps' => [ 1, 0.0001, 0.0001 ],
						'units' => [ 'px', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
					// border-left-width.
					[
						'id'    => 'border-left-width',
						'type'  => 'text_number',
						'label' => esc_html__( 'Left', 'wp-grid-builder' ),
						'steps' => [ 1, 0.0001, 0.0001 ],
						'units' => [ 'px', 'em', 'rem' ],
						'min'   => 0,
						'max'   => 999,
					],
				],
			],
			// border-style.
			[
				'id'          => 'border-style',
				'type'        => 'select',
				'label'       => esc_html__( 'Border Style', 'wp-grid-builder' ),
				'placeholder' => esc_html_x( 'None', 'Border Style default value', 'wp-grid-builder' ),
				'options'     => [
					'solid'  => esc_html__( 'Solid', 'wp-grid-builder' ),
					'dotted' => esc_html__( 'Dotted', 'wp-grid-builder' ),
					'dashed' => esc_html__( 'Dashed', 'wp-grid-builder' ),
					'double' => esc_html__( 'Double', 'wp-grid-builder' ),
					'groove' => esc_html__( 'Groove', 'wp-grid-builder' ),
					'ridge'  => esc_html__( 'Ridge', 'wp-grid-builder' ),
					'inset'  => esc_html__( 'Inset', 'wp-grid-builder' ),
				],
			],
			// border-color.
			[
				'id'    => 'border-color',
				'type'  => 'color',
				'label' => esc_html__( 'Border Color', 'wp-grid-builder' ),
				'clear' => esc_html__( 'Clear', 'wp-grid-builder' ),
				'alpha' => true,
			],
		],
	],
];

$box_shadow = [
	[
		'id'     => 'box_shadow_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Box Shadow', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// box-shadow-horizontal.
			[
				'id'    => 'box-shadow-horizontal',
				'type'  => 'slider',
				'label' => esc_html__( 'Shadow Horizontal Position', 'wp-grid-builder' ),
				'steps' => [ 1, 0.0001, 0.0001 ],
				'units' => [ 'px', 'em', 'rem' ],
				'unit'  => true,
				'min'   => -100,
				'max'   => 100,
				'value' => '',
			],
			// box-shadow-vertical.
			[
				'id'    => 'box-shadow-vertical',
				'type'  => 'slider',
				'label' => esc_html__( 'Shadow Vertical Position', 'wp-grid-builder' ),
				'steps' => [ 1, 0.0001, 0.0001 ],
				'units' => [ 'px', 'em', 'rem' ],
				'unit'  => true,
				'min'   => -100,
				'max'   => 100,
				'value' => '',
			],
			// box-shadow-blur.
			[
				'id'    => 'box-shadow-blur',
				'type'  => 'slider',
				'label' => esc_html__( 'Shadow Blur Radius', 'wp-grid-builder' ),
				'steps' => [ 1, 0.0001, 0.0001 ],
				'units' => [ 'px', 'em', 'rem' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 100,
				'value' => '',
			],
			// box-shadow-spread.
			[
				'id'    => 'box-shadow-spread',
				'type'  => 'slider',
				'label' => esc_html__( 'Shadow Spread Radius', 'wp-grid-builder' ),
				'steps' => [ 1, 0.0001, 0.0001 ],
				'units' => [ 'px', 'em', 'rem' ],
				'unit'  => true,
				'min'   => -100,
				'max'   => 100,
				'value' => '',
			],
			// box-shadow-type.
			[
				'id'      => 'box-shadow-type',
				'type'    => 'radio',
				'label'   => esc_html__( 'Shadow Type', 'wp-grid-builder' ),
				'options' => [
					''      => esc_html__( 'Outer Shadow', 'wp-grid-builder' ),
					'inset' => esc_html__( 'Inner Shadow', 'wp-grid-builder' ),
				],
			],
			// border-shadow-color.
			[
				'id'    => 'box-shadow-color',
				'type'  => 'color',
				'label' => esc_html__( 'Shadow Color', 'wp-grid-builder' ),
				'clear' => esc_html__( 'Clear', 'wp-grid-builder' ),
				'alpha' => true,
			],
		],
	],
];

$background = [
	[
		'id'     => 'background_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Background', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// background.
			[
				'id'       => 'background',
				'type'     => 'color',
				'label'    => esc_html__( 'Background Color', 'wp-grid-builder' ),
				'clear'    => esc_html__( 'Clear', 'wp-grid-builder' ),
				'alpha'    => true,
				'gradient' => true,
			],
			// background_image.
			[
				'id'     => 'background_image',
				'type'   => 'group',
				'label'  => esc_html__( 'Background Image', 'wp-grid-builder' ),
				'fields' => [
					// background-url.
					[
						'id'          => 'background-url',
						'type'        => 'file',
						'label'       => esc_html__( 'Image URL', 'wp-grid-builder' ),
						'placeholder' => esc_html__( 'Enter an URL or select a file', 'wp-grid-builder' ),
						'mime_type'   => 'image',
						'width'       => 340,
					],
					// background-size.
					[
						'id'          => 'background-size',
						'type'        => 'select',
						'label'       => esc_html__( 'Size', 'wp-grid-builder' ),
						'placeholder' => esc_html_x( 'None', 'Size default value', 'wp-grid-builder' ),
						'options'     => [
							'contain' => esc_html__( 'Contain', 'wp-grid-builder' ),
							'cover'   => esc_html__( 'Cover', 'wp-grid-builder' ),
						],
					],
					// background-repeat.
					[
						'id'          => 'background-repeat',
						'type'        => 'select',
						'label'       => esc_html__( 'Repeat', 'wp-grid-builder' ),
						'placeholder' => esc_html_x( 'None', 'Repeat default value', 'wp-grid-builder' ),
						'options'     => [
							'no-repeat' => esc_html__( 'No repeat', 'wp-grid-builder' ),
							'repeat'    => esc_html__( 'Repeat', 'wp-grid-builder' ),
							'repeat-x'  => esc_html__( 'Repeat X', 'wp-grid-builder' ),
							'repeat-y'  => esc_html__( 'Repeat Y', 'wp-grid-builder' ),
							'round'     => esc_html__( 'Round', 'wp-grid-builder' ),
							'space'     => esc_html__( 'Space', 'wp-grid-builder' ),
						],
					],
					// background-position-x.
					[
						'id'    => 'background-position-x',
						'type'  => 'text_number',
						'label' => esc_html__( 'Position X', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
					],
					// background-position-y.
					[
						'id'    => 'background-position-y',
						'type'  => 'text_number',
						'label' => esc_html__( 'Position Y', 'wp-grid-builder' ),
						'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
						'units' => [ 'px', '%', 'em', 'rem' ],
						'min'   => -999,
						'max'   => 999,
					],
				],
			],
		],
	],
];

$alignment = [
	[
		'id'     => 'alignment_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Alignment', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// text-align.
			[
				'id'      => 'text-align',
				'type'    => 'radio',
				'label'   => esc_html__( 'Blocks Alignment', 'wp-grid-builder' ),
				'options' => [
					'left'   => esc_html__( 'Left', 'wp-grid-builder' ),
					'center' => esc_html__( 'Center', 'wp-grid-builder' ),
					'right'  => esc_html__( 'Right', 'wp-grid-builder' ),
				],
				'icons'   => [
					'left'   => Includes\Helpers::get_icon( 'align-left', true ),
					'center' => Includes\Helpers::get_icon( 'align-center', true ),
					'right'  => Includes\Helpers::get_icon( 'align-right', true ),
				],
				'value'   => '',
			],
		],
	],
];

$font = [
	[
		'id'     => 'font_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Font', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// font-family.
			[
				'id'          => 'font-family',
				'type'        => 'select',
				'label'       => esc_html__( 'Font Family', 'wp-grid-builder' ),
				'search'      => true,
				'validate'    => false,
				'placeholder' => esc_html__( 'Default', 'wp-grid-builder' ),
				// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned, WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow
				'options'     => [
					esc_html__( 'Default Fonts', 'wp-grid-builder' ) => [
						'Arial, Helvetica, sans-serif'                         => 'Arial, Helvetica',
						"'Arial Black', Gadget, sans-serif"                    => 'Arial Black',
						"'Comic Sans MS', cursive, sans-serif"                 => 'Comic Sans MS',
						"'Courier New', Courier, monospace"                    => 'Courier New',
						'Georgia, serif'                                       => 'Georgia, serif',
						'Impact, Charcoal, sans-serif'                         => 'Impact',
						"'Lucida Console', Monaco, monospace"                  => 'Lucida Console',
						"'Lucida Sans Unicode', 'Lucida Grande', sans-serif"   => 'Lucida Sans Unicode',
						"'Palatino Linotype', 'Book Antiqua', Palatino, serif" => 'Palatino Linotype',
						'Tahoma, Geneva, sans-serif'                           => 'Tahoma',
						"'Times New Roman', Times, serif"                      => 'Times New Roman',
						"'Trebuchet MS', Helvetica, sans-serif"                => 'Trebuchet MS',
						'Verdana, Geneva, sans-serif'                          => 'Verdana',
					],
					'Google Fonts'                                   => [],
				],
				// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned, WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow
			],
			// font-weight.
			[
				'id'      => 'font-weight',
				'type'    => 'select',
				'label'   => esc_html__( 'Font Weight', 'wp-grid-builder' ),
				'value'   => '400',
				'options' => [
					'100' => esc_html__( '100 - Thin', 'wp-grid-builder' ),
					'200' => esc_html__( '200 - Extra Light', 'wp-grid-builder' ),
					'300' => esc_html__( '300 - Light', 'wp-grid-builder' ),
					'400' => esc_html__( '400 - Normal', 'wp-grid-builder' ),
					'500' => esc_html__( '500 - Medium', 'wp-grid-builder' ),
					'600' => esc_html__( '600 - Semi Bold', 'wp-grid-builder' ),
					'700' => esc_html__( '700 - Bold', 'wp-grid-builder' ),
					'800' => esc_html__( '800 - Extra Bold', 'wp-grid-builder' ),
					'900' => esc_html__( '900 - Black', 'wp-grid-builder' ),
				],
			],
			// font_alignment.
			[
				'id'     => 'font_alignment',
				'type'   => 'group',
				'label'  => esc_html__( 'Font Alignment', 'wp-grid-builder' ),
				'fields' => [
					// text-align.
					[
						'id'      => 'text-align',
						'type'    => 'radio',
						'options' => [
							'left'    => esc_html__( 'Left', 'wp-grid-builder' ),
							'center'  => esc_html__( 'Center', 'wp-grid-builder' ),
							'right'   => esc_html__( 'Right', 'wp-grid-builder' ),
							'justify' => esc_html__( 'Justify', 'wp-grid-builder' ),
						],
						'icons'   => [
							'left'    => Includes\Helpers::get_icon( 'text-left', true ),
							'center'  => Includes\Helpers::get_icon( 'text-center', true ),
							'right'   => Includes\Helpers::get_icon( 'text-right', true ),
							'justify' => Includes\Helpers::get_icon( 'text-justify', true ),
						],
						'value'   => '',
					],
				],
			],
			// font_style.
			[
				'id'     => 'font_style',
				'type'   => 'group',
				'label'  => esc_html__( 'Font Style', 'wp-grid-builder' ),
				'fields' => [
					// font-style.
					[
						'id'      => 'font-style',
						'type'    => 'radio',
						'options' => [
							'italic' => esc_html__( 'Italic', 'wp-grid-builder' ),
						],
						'icons'   => [
							'italic' => Includes\Helpers::get_icon( 'text-italic', true ),
						],
					],
					// text-transform.
					[
						'id'      => 'text-transform',
						'type'    => 'radio',
						'options' => [
							'uppercase'  => esc_html__( 'Uppercase', 'wp-grid-builder' ),
							'capitalize' => esc_html__( 'Capitalize', 'wp-grid-builder' ),
						],
						'icons'   => [
							'uppercase'  => Includes\Helpers::get_icon( 'uppercase', true ),
							'capitalize' => Includes\Helpers::get_icon( 'capitalize', true ),
						],
					],
					// text-decoration.
					[
						'id'      => 'text-decoration',
						'type'    => 'radio',
						'options' => [
							'underline'    => esc_html__( 'Underline', 'wp-grid-builder' ),
							'line-through' => esc_html__( 'Line Through', 'wp-grid-builder' ),
						],
						'icons'   => [
							'underline'    => Includes\Helpers::get_icon( 'underline', true ),
							'line-through' => Includes\Helpers::get_icon( 'line-through', true ),
						],
					],
				],
			],
			// color_scheme.
			[
				'id'      => 'color_scheme',
				'type'    => 'radio',
				'label'   => esc_html__( 'Font Color Scheme', 'wp-grid-builder' ),
				'tooltip' => esc_html__( 'Color scheme will be overridden if a font color is set.', 'wp-grid-builder' ),
				'options' => [
					'scheme-1' => esc_html__( 'Primary', 'wp-grid-builder' ),
					'scheme-2' => esc_html__( 'Secondary', 'wp-grid-builder' ),
					'scheme-3' => esc_html__( 'Tertiary', 'wp-grid-builder' ),
					'accent-1' => esc_html__( 'Accent', 'wp-grid-builder' ),
				],
			],
			// color.
			[
				'id'    => 'color',
				'type'  => 'color',
				'label' => esc_html__( 'Font Color', 'wp-grid-builder' ),
				'clear' => esc_html__( 'Clear', 'wp-grid-builder' ),
				'alpha' => true,
			],
			// font-size.
			[
				'id'    => 'font-size',
				'type'  => 'slider',
				'label' => esc_html__( 'Font Size', 'wp-grid-builder' ),
				'steps' => [ 1, 0.0001, 0.0001 ],
				'units' => [ 'px', 'em', 'rem' ],
				'unit'  => true,
				'value' => '',
				'min'   => 0,
				'max'   => 100,
			],
			// line-height.
			[
				'id'    => 'line-height',
				'type'  => 'slider',
				'label' => esc_html__( 'Line Height', 'wp-grid-builder' ),
				'steps' => [ 0.001, 1, 0.0001, 0.0001 ],
				'units' => [ '', 'px', 'em', 'rem' ],
				'unit'  => true,
				'value' => '',
				'min'   => 0,
				'max'   => 100,
			],
			// letter-spacing.
			[
				'id'    => 'letter-spacing',
				'type'  => 'slider',
				'label' => esc_html__( 'Letter Spacing', 'wp-grid-builder' ),
				'steps' => [ 0.01, 0.0001, 0.0001 ],
				'units' => [ 'px', 'em', 'rem' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 100,
				'value' => '',
			],
		],
	],
];

$text_shadow = [
	[
		'id'     => 'text_shadow_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Text Shadow', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// text-shadow-horizontal.
			[
				'id'    => 'text-shadow-horizontal',
				'type'  => 'slider',
				'label' => esc_html__( 'Shadow Horizontal Position', 'wp-grid-builder' ),
				'steps' => [ 1, 0.0001, 0.0001 ],
				'units' => [ 'px', 'em', 'rem' ],
				'unit'  => true,
				'min'   => -50,
				'max'   => 50,
				'value' => '',
			],
			// text-shadow-vertical.
			[
				'id'    => 'text-shadow-vertical',
				'type'  => 'slider',
				'label' => esc_html__( 'Shadow Vertical Position', 'wp-grid-builder' ),
				'steps' => [ 1, 0.0001, 0.0001 ],
				'units' => [ 'px', 'em', 'rem' ],
				'unit'  => true,
				'min'   => -50,
				'max'   => 50,
				'value' => '',
			],
			// text-shadow-blur.
			[
				'id'    => 'text-shadow-blur',
				'type'  => 'slider',
				'label' => esc_html__( 'Shadow Blur Radius', 'wp-grid-builder' ),
				'steps' => [ 1, 0.0001, 0.0001 ],
				'units' => [ 'px', 'em', 'rem' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 50,
				'value' => '',
			],
			// text-shadow-color.
			[
				'id'    => 'text-shadow-color',
				'type'  => 'color',
				'label' => esc_html__( 'Shadow Color', 'wp-grid-builder' ),
				'clear' => esc_html__( 'Clear', 'wp-grid-builder' ),
				'alpha' => true,
			],
		],
	],
];

$filters = [
	[
		'id'     => 'filters_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Filters', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// Nota bene.
			[
				'id'    => 'nb-1',
				'type'  => 'notabene',
				'value' => esc_html__( 'Filter properties are only applied in preview mode to prevent any conflicts. Filters properties will only work in modern browsers.', 'wp-grid-builder' ),
			],
			// mix-blend-mode.
			[
				'id'          => 'mix-blend-mode',
				'type'        => 'select',
				'search'      => true,
				'label'       => esc_html__( 'Blend Mode', 'wp-grid-builder' ),
				'placeholder' => esc_html_x( 'None', 'Blend Mode default value', 'wp-grid-builder' ),
				'options'     => [
					'normal'      => esc_html__( 'Normal', 'wp-grid-builder' ),
					'multiply'    => esc_html__( 'Multiply', 'wp-grid-builder' ),
					'screen'      => esc_html__( 'Screen', 'wp-grid-builder' ),
					'overlay'     => esc_html__( 'Overlay', 'wp-grid-builder' ),
					'darken'      => esc_html__( 'Darken', 'wp-grid-builder' ),
					'lighten'     => esc_html__( 'Lighten', 'wp-grid-builder' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'wp-grid-builder' ),
					'color-burn'  => esc_html__( 'Color Burn', 'wp-grid-builder' ),
					'hard-light'  => esc_html__( 'Hard Light', 'wp-grid-builder' ),
					'soft-light'  => esc_html__( 'Soft Light', 'wp-grid-builder' ),
					'difference'  => esc_html__( 'Difference', 'wp-grid-builder' ),
					'exclusion'   => esc_html__( 'Exclusion', 'wp-grid-builder' ),
					'hue'         => esc_html__( 'Hue', 'wp-grid-builder' ),
					'saturation'  => esc_html__( 'Saturation', 'wp-grid-builder' ),
					'color'       => esc_html__( 'Color', 'wp-grid-builder' ),
					'luminosity'  => esc_html__( 'Luminosity', 'wp-grid-builder' ),
				],
			],
			// filter-contrast.
			[
				'id'    => 'filter-contrast',
				'type'  => 'slider',
				'label' => esc_html__( 'Contrast', 'wp-grid-builder' ),
				'steps' => [ 0.1 ],
				'units' => [ '%' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 200,
				'value' => '',
			],
			// filter-brightness.
			[
				'id'    => 'filter-brightness',
				'type'  => 'slider',
				'label' => esc_html__( 'Brightness', 'wp-grid-builder' ),
				'steps' => [ 0.1 ],
				'units' => [ '%' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 200,
				'value' => '',
			],
			// filter-saturate.
			[
				'id'    => 'filter-saturate',
				'type'  => 'slider',
				'label' => esc_html__( 'Saturation', 'wp-grid-builder' ),
				'steps' => [ 0.1 ],
				'units' => [ '%' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 200,
				'value' => '',
			],
			// filter-sepia.
			[
				'id'    => 'filter-sepia',
				'type'  => 'slider',
				'label' => esc_html__( 'Sepia', 'wp-grid-builder' ),
				'steps' => [ 0.1 ],
				'units' => [ '%' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 100,
				'value' => '',
			],
			// filter-grayscale.
			[
				'id'    => 'filter-grayscale',
				'type'  => 'slider',
				'label' => esc_html__( 'Greyscale', 'wp-grid-builder' ),
				'steps' => [ 0.1 ],
				'units' => [ '%' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 100,
				'value' => '',
			],
			// filter-invert.
			[
				'id'    => 'filter-invert',
				'type'  => 'slider',
				'label' => esc_html__( 'Invert', 'wp-grid-builder' ),
				'steps' => [ 0.1 ],
				'units' => [ '%' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 100,
				'value' => '',
			],
			// filter-hue.
			[
				'id'    => 'filter-hue-rotate',
				'type'  => 'slider',
				'label' => esc_html__( 'Hue Rotate', 'wp-grid-builder' ),
				'steps' => [ 1 ],
				'units' => [ 'deg' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 360,
				'value' => '',
			],
			// filter-blur.
			[
				'id'    => 'filter-blur',
				'type'  => 'slider',
				'label' => esc_html__( 'Blur', 'wp-grid-builder' ),
				'steps' => [ 0.01 ],
				'units' => [ 'px' ],
				'unit'  => true,
				'min'   => 0,
				'max'   => 50,
				'value' => '',
			],
		],
	],
];

$visibility = [
	[
		'id'     => 'visibility_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Visibility', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// Nota bene.
			[
				'id'    => 'nb-2',
				'type'  => 'notabene',
				'value' => esc_html__( 'Visibility properties are only applied in preview mode to prevent any conflicts.', 'wp-grid-builder' ),
			],
			// visibility.
			[
				'id'          => 'visibility',
				'type'        => 'select',
				'label'       => esc_html__( 'Visibility', 'wp-grid-builder' ),
				'placeholder' => esc_html_x( 'None', 'Visibility default value', 'wp-grid-builder' ),
				'options'     => [
					'visible' => esc_html__( 'Visible', 'wp-grid-builder' ),
					'hidden'  => esc_html__( 'Hidden', 'wp-grid-builder' ),
				],
			],
			// opacity.
			[
				'id'    => 'opacity',
				'type'  => 'slider',
				'label' => esc_html__( 'Opacity', 'wp-grid-builder' ),
				'steps' => [ 0.01 ],
				'units' => [ '' ],
				'min'   => 0,
				'max'   => 1,
				'value' => '',
			],
		],
	],
];

$custom_css = [
	[
		'id'     => 'custom_css_accordion',
		'tab'    => [ 'idle', 'hover' ],
		'title'  => esc_html__( 'Custom CSS', 'wp-grid-builder' ),
		'type'   => 'accordion',
		'fields' => [
			// custom_css.
			[
				'id'           => 'custom_css',
				'type'         => 'code',
				'mode'         => 'css',
				'label'        => esc_html__( 'CSS Declarations', 'wp-grid-builder' ),
				'declarations' => true,
				'placeholder'  => "\n\n" . esc_html__( 'Only enter CSS declarations:', 'wp-grid-builder' ) . "\n\n" .
					'margin: 0 auto;' . "\n" .
					'padding: 10px 20px;' . "\n" .
					'color: green;' . "\n",
			],
			// custom_css_before.
			[
				'id'           => 'custom_css_before',
				'type'         => 'code',
				'mode'         => 'css',
				'label'        => esc_html__( '::before CSS Declarations', 'wp-grid-builder' ),
				'declarations' => true,
				'placeholder'  => "\n\n" . esc_html__( 'Only enter CSS declarations:', 'wp-grid-builder' ) . "\n\n" .
					'content: "before";' . "\n" .
					'position: absolute;' . "\n" .
					'top: 0;' . "\n" .
					'left: 50%;' . "\n",
			],
			// custom_css_after.
			[
				'id'           => 'custom_css_after',
				'type'         => 'code',
				'mode'         => 'css',
				'label'        => esc_html__( '::after CSS Declarations', 'wp-grid-builder' ),
				'declarations' => true,
				'placeholder'  => "\n\n" . esc_html__( 'Only enter CSS declarations:', 'wp-grid-builder' ) . "\n\n" .
					'content: "after";' . "\n" .
					'position: absolute;' . "\n" .
					'bottom: 0;' . "\n" .
					'left: 50%;' . "\n",
			],
		],
	],
];

$block_hover_selector = [
	// hover_selector.
	[
		'id'          => 'hover_selector',
		'tab'         => 'hover',
		'type'        => 'select',
		'label'       => esc_html__( 'Apply hover state from', 'wp-grid-builder' ),
		'placeholder' => esc_html__( 'None', 'wp-grid-builder' ),
		'options'     => [
			'itself' => esc_html__( 'Block', 'wp-grid-builder' ),
			'parent' => esc_html__( 'Parent', 'wp-grid-builder' ),
			'card'   => esc_html__( 'Card', 'wp-grid-builder' ),
		],
	],
];

$layer_hover_selector = [
	// hover_selector.
	[
		'id'          => 'hover_selector',
		'tab'         => 'hover',
		'type'        => 'select',
		'label'       => esc_html__( 'Apply hover state from', 'wp-grid-builder' ),
		'placeholder' => esc_html__( 'None', 'wp-grid-builder' ),
		'options'     => [
			'itself' => esc_html__( 'Layer', 'wp-grid-builder' ),
			'parent' => esc_html__( 'Parent', 'wp-grid-builder' ),
			'card'   => esc_html__( 'Card', 'wp-grid-builder' ),
		],
	],
];

$class_name = [
	// class.
	[
		'id'    => 'class',
		'tab'   => 'content',
		'type'  => 'text',
		'label' => esc_html__( 'Class Name', 'wp-grid-builder' ),
		'value' => '',
	],
];

$html_tag = [
	// tag.
	[
		'id'      => 'tag',
		'tab'     => 'content',
		'type'    => 'radio',
		'label'   => esc_html__( 'HTML tag', 'wp-grid-builder' ),
		'options' => [
			''     => esc_html__( 'Auto', 'wp-grid-builder' ),
			'div'  => 'DIV',
			'span' => 'SPAN',
			'p'    => 'P',
			'h2'   => 'H2',
			'h3'   => 'H3',
			'h4'   => 'H4',
			'h5'   => 'H5',
			'h6'   => 'H6',
		],
	],
];

$sources = [
	'post_field'         => esc_html__( 'Post Field', 'wp-grid-builder' ),
	'product_field'      => esc_html__( 'Product Field', 'wp-grid-builder' ),
	'user_field'         => esc_html__( 'User Field', 'wp-grid-builder' ),
	'term_field'         => esc_html__( 'Term Field', 'wp-grid-builder' ),
	'metadata'           => esc_html__( 'Custom Field', 'wp-grid-builder' ),
	'raw_content_block'  => esc_html__( 'Raw Content', 'wp-grid-builder' ),
	'media_button_block' => esc_html__( 'Lightbox &#38; Play Button', 'wp-grid-builder' ),
	'social_share_block' => esc_html__( 'Social Share Button', 'wp-grid-builder' ),
	'svg_icon_block'     => esc_html__( 'SVG Icon', 'wp-grid-builder' ),
];

if ( ! empty( $custom_blocks ) ) {
	$sources['custom_block'] = esc_html__( 'Custom Block', 'wp-grid-builder' );
}

$source = [
	// source.
	[
		'id'      => 'source',
		'tab'     => 'content',
		'type'    => 'select',
		'label'   => esc_html__( 'Source Type', 'wp-grid-builder' ),
		'options' => apply_filters( 'wp_grid_builder/block/sources', $sources ),
	],
];

$post_field = [
	// post_field.
	[
		'id'                => 'post_field',
		'tab'               => 'content',
		'type'              => 'select',
		'label'             => esc_html__( 'Post Field', 'wp-grid-builder' ),
		'options'           => [
			'the_id'            => esc_html__( 'Post ID', 'wp-grid-builder' ),
			'the_title'         => esc_html__( 'Title', 'wp-grid-builder' ),
			'the_name'          => esc_html__( 'Name', 'wp-grid-builder' ),
			'the_content'       => esc_html__( 'Content', 'wp-grid-builder' ),
			'the_excerpt'       => esc_html__( 'Excerpt', 'wp-grid-builder' ),
			'the_post_type'     => esc_html__( 'Post Type', 'wp-grid-builder' ),
			'the_post_format'   => esc_html__( 'Post Format', 'wp-grid-builder' ),
			'the_post_status'   => esc_html__( 'Post Status', 'wp-grid-builder' ),
			'the_date'          => esc_html__( 'Date', 'wp-grid-builder' ),
			'the_modified_date' => esc_html__( 'Modified Date', 'wp-grid-builder' ),
			'the_terms'         => esc_html__( 'Taxonomy Terms', 'wp-grid-builder' ),
			'the_author'        => esc_html__( 'Post Author', 'wp-grid-builder' ),
			'the_avatar'        => esc_html__( 'Author Avatar', 'wp-grid-builder' ),
			'comments_number'   => esc_html__( 'Comment Count', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_field',
			],
		],
	],
];

$product_field = [
	// product_field.
	[
		'id'                => 'product_field',
		'tab'               => 'content',
		'type'              => 'select',
		'label'             => esc_html__( 'Product Field', 'wp-grid-builder' ),
		'options'           => [
			'the_full_price'         => esc_html__( 'Full Price', 'wp-grid-builder' ),
			'the_regular_price'      => esc_html__( 'Regular price', 'wp-grid-builder' ),
			'the_price'              => esc_html__( 'Active Price', 'wp-grid-builder' ),
			'the_sale_price'         => esc_html__( 'Sale Price', 'wp-grid-builder' ),
			'the_cart_button'        => esc_html__( 'Cart Button', 'wp-grid-builder' ),
			'the_star_rating'        => esc_html__( 'Star Rating', 'wp-grid-builder' ),
			'the_text_rating'        => esc_html__( 'Text Rating', 'wp-grid-builder' ),
			'the_on_sale_badge'      => esc_html__( 'On Sale Badge', 'wp-grid-builder' ),
			'the_in_stock_badge'     => esc_html__( 'In Stock Badge', 'wp-grid-builder' ),
			'the_out_of_stock_badge' => esc_html__( 'Out of Stock Badge', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'product_field',
			],
		],
	],
];

$user_field = [
	// user_field.
	[
		'id'                => 'user_field',
		'tab'               => 'content',
		'type'              => 'select',
		'label'             => esc_html__( 'User Field', 'wp-grid-builder' ),
		'options'           => [
			'the_user_id'           => esc_html__( 'User ID', 'wp-grid-builder' ),
			'the_user_display_name' => esc_html__( 'Display Name', 'wp-grid-builder' ),
			'the_user_first_name'   => esc_html__( 'First Name', 'wp-grid-builder' ),
			'the_user_last_name'    => esc_html__( 'Last Name', 'wp-grid-builder' ),
			'the_user_nickname'     => esc_html__( 'Nickname', 'wp-grid-builder' ),
			'the_user_login'        => esc_html__( 'Username', 'wp-grid-builder' ),
			'the_user_description'  => esc_html__( 'Biography', 'wp-grid-builder' ),
			'the_user_email'        => esc_html__( 'Email', 'wp-grid-builder' ),
			'the_user_url'          => esc_html__( 'Website', 'wp-grid-builder' ),
			'the_user_roles'        => esc_html__( 'Roles', 'wp-grid-builder' ),
			'the_user_post_count'   => esc_html__( 'Post Count', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'user_field',
			],
		],
	],
];

$term_field = [
	// term_field.
	[
		'id'                => 'term_field',
		'tab'               => 'content',
		'type'              => 'select',
		'label'             => esc_html__( 'Term Field', 'wp-grid-builder' ),
		'options'           => [
			'the_term_id'          => esc_html__( 'Term ID', 'wp-grid-builder' ),
			'the_term_name'        => esc_html__( 'Name', 'wp-grid-builder' ),
			'the_term_slug'        => esc_html__( 'Slug', 'wp-grid-builder' ),
			'the_term_taxonomy'    => esc_html__( 'Taxonomy', 'wp-grid-builder' ),
			'the_term_parent'      => esc_html__( 'Parent Term', 'wp-grid-builder' ),
			'the_term_description' => esc_html__( 'Description', 'wp-grid-builder' ),
			'the_term_count'       => esc_html__( 'Post Count', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'term_field',
			],
		],
	],
];

$post_excerpt = [
	// excerpt_length.
	[
		'id'                => 'excerpt_length',
		'tab'               => 'content',
		'type'              => 'number',
		'label'             => esc_html__( 'Excerpt Length', 'wp-grid-builder' ),
		'value'             => 35,
		'min'               => -1,
		'max'               => 999,
		'conditional_logic' => [
			'relation' => 'OR',
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'post_field',
				],
				[
					'field'   => 'post_field',
					'compare' => '===',
					'value'   => 'the_excerpt',
				],
			],
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'user_field',
				],
				[
					'field'   => 'user_field',
					'compare' => '===',
					'value'   => 'the_user_description',
				],
			],
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'term_field',
				],
				[
					'field'   => 'term_field',
					'compare' => '===',
					'value'   => 'the_term_description',
				],
			],
		],
	],
	// excerpt_suffix.
	[
		'id'                => 'excerpt_suffix',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Excerpt Suffix', 'wp-grid-builder' ),
		'value'             => '',
		'conditional_logic' => [
			'relation' => 'OR',
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'post_field',
				],
				[
					'field'   => 'post_field',
					'compare' => '===',
					'value'   => 'the_excerpt',
				],
			],
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'user_field',
				],
				[
					'field'   => 'user_field',
					'compare' => '===',
					'value'   => 'the_user_description',
				],
			],
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'term_field',
				],
				[
					'field'   => 'term_field',
					'compare' => '===',
					'value'   => 'the_term_description',
				],
			],
		],
	],
];

$post_date = [
	// date_format.
	[
		'id'                => 'date_format',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Date Format', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_field',
			],
			[
				'field'   => 'post_field',
				'compare' => 'IN',
				'value'   => [ 'the_date', 'the_modified_date' ],
			],
		],
	],
	// Nota bene.
	[
		'id'                => 'nb-3',
		'tab'               => 'content',
		'type'              => 'notabene',
		'value'             => esc_html__( 'You can enter any PHP date format. Enter "ago" to display human readable format such as "1 hour ago".', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_field',
			],
			[
				'field'   => 'post_field',
				'compare' => 'IN',
				'value'   => [ 'the_date', 'the_modified_date' ],
			],
		],
	],
];

$post_terms = [
	// taxonomy.
	[
		'id'                => 'taxonomy',
		'tab'               => 'content',
		'type'              => 'select',
		'label'             => esc_html__( 'Include Taxonomies', 'wp-grid-builder' ),
		'placeholder'       => esc_html_x( 'Any', 'Include Taxonomies default value', 'wp-grid-builder' ),
		'options'           => Includes\Helpers::get_taxonomies_list(),
		'multiple'          => true,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_field',
			],
			[
				'field'   => 'post_field',
				'compare' => '===',
				'value'   => 'the_terms',
			],
		],
	],
	// term_glue.
	[
		'id'                => 'term_glue',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Separator', 'wp-grid-builder' ),
		'value'             => '',
		'white_spaces'      => true,
		'angle_quotes'      => true,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_field',
			],
			[
				'field'   => 'post_field',
				'compare' => '===',
				'value'   => 'the_terms',
			],
		],
	],
	// margins.
	[
		'id'                => 'margins',
		'tab'               => 'content',
		'type'              => 'group',
		'label'             => esc_html__( 'Spacing', 'wp-grid-builder' ),
		'fields'            => [
			// margin-top.
			[
				'id'    => 'margin-top',
				'type'  => 'text_number',
				'label' => esc_html__( 'Top', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
				'units' => [ 'px', '%', 'em', 'rem' ],
				'min'   => 0,
				'max'   => 999,
			],
			// margin-right.
			[
				'id'    => 'margin-right',
				'type'  => 'text_number',
				'label' => esc_html__( 'Right', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
				'units' => [ 'px', '%', 'em', 'rem' ],
				'min'   => 0,
				'max'   => 999,
			],
			// margin-bottom.
			[
				'id'    => 'margin-bottom',
				'type'  => 'text_number',
				'label' => esc_html__( 'Bottom', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
				'units' => [ 'px', '%', 'em', 'rem' ],
				'min'   => 0,
				'max'   => 999,
			],
			// margin-left.
			[
				'id'    => 'margin-left',
				'type'  => 'text_number',
				'label' => esc_html__( 'Left', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
				'units' => [ 'px', '%', 'em', 'rem' ],
				'min'   => 0,
				'max'   => 999,
			],
		],
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_field',
			],
			[
				'field'   => 'post_field',
				'compare' => '===',
				'value'   => 'the_terms',
			],
		],
	],
	// term_color.
	[
		'id'                => 'term_color',
		'tab'               => 'content',
		'type'              => 'toggle',
		'label'             => esc_html__( 'Term Colors', 'wp-grid-builder' ),
		'tooltip'           => esc_html__( 'Apply colors on each term in the list defined from plugin options.', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_field',
			],
			[
				'field'   => 'post_field',
				'compare' => '===',
				'value'   => 'the_terms',
			],
		],
	],
	// term_link.
	[
		'id'                => 'term_link',
		'tab'               => 'content',
		'type'              => 'toggle',
		'label'             => esc_html__( 'Link to Archive template', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_field',
			],
			[
				'field'   => 'post_field',
				'compare' => '===',
				'value'   => 'the_terms',
			],
		],
	],
];

$post_author = [
	// author_prefix.
	[
		'id'                => 'author_prefix',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Author Prefix', 'wp-grid-builder' ),
		'white_spaces'      => true,
		'angle_quotes'      => true,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'post_field',
			],
			[
				'field'   => 'post_field',
				'compare' => '===',
				'value'   => 'the_author',
			],
		],
	],
];

$count_format = [
	// count_format.
	[
		'id'                => 'count_format',
		'tab'               => 'content',
		'type'              => 'radio',
		'label'             => esc_html__( 'Format Type', 'wp-grid-builder' ),
		'value'             => 'text',
		'options'           => [
			'text'   => esc_html__( 'Text', 'wp-grid-builder' ),
			'number' => esc_html__( 'Number', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			'relation' => 'OR',
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'post_field',
				],
				[
					'field'   => 'post_field',
					'compare' => '===',
					'value'   => 'comments_number',
				],
			],
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'user_field',
				],
				[
					'field'   => 'user_field',
					'compare' => '===',
					'value'   => 'the_user_post_count',
				],
			],
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'term_field',
				],
				[
					'field'   => 'term_field',
					'compare' => '===',
					'value'   => 'the_term_count',
				],
			],
		],
	],
];

$user_website = [
	// website_text.
	[
		'id'                => 'website_text',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Website Text', 'wp-grid-builder' ),
		'value'             => '',
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'user_field',
			],
			[
				'field'   => 'user_field',
				'compare' => '===',
				'value'   => 'the_user_url',
			],
		],
	],
];

$sale_badge = [
	// badge_type.
	[
		'id'                => 'badge_type',
		'tab'               => 'content',
		'type'              => 'radio',
		'label'             => esc_html__( 'Badge Type', 'wp-grid-builder' ),
		'value'             => 'text',
		'options'           => [
			'text' => esc_html__( 'Text', 'wp-grid-builder' ),
			'icon' => esc_html__( 'Icon', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'product_field',
			],
			[
				'field'   => 'product_field',
				'compare' => '===',
				'value'   => 'the_on_sale_badge',
			],
		],
	],
	// badge_label.
	[
		'id'                => 'badge_label',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Badge Label', 'wp-grid-builder' ),
		'placeholder'       => esc_html__( 'Enter a label', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'product_field',
			],
			[
				'relation' => 'OR',
				[
					[
						'field'   => 'product_field',
						'compare' => '===',
						'value'   => 'the_on_sale_badge',
					],
					[
						'field'   => 'badge_type',
						'compare' => '===',
						'value'   => 'text',
					],
				],
				[
					'field'   => 'product_field',
					'compare' => '===',
					'value'   => 'the_in_stock_badge',
				],
				[
					'field'   => 'product_field',
					'compare' => '===',
					'value'   => 'the_out_of_stock_badge',
				],
			],
		],
	],
	// badge_icon.
	[
		'id'                => 'badge_icon',
		'tab'               => 'content',
		'type'              => 'icons',
		'label'             => esc_html__( 'Badge Icon', 'wp-grid-builder' ),
		'value'             => 'wpgb/business/cart-1',
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'product_field',
			],
			[
				'field'   => 'product_field',
				'compare' => '===',
				'value'   => 'the_on_sale_badge',
			],
			[
				'field'   => 'badge_type',
				'compare' => '===',
				'value'   => 'icon',
			],
		],
	],
];

$social_network = [
	// social_network.
	[
		'id'                => 'social_network',
		'tab'               => 'content',
		'type'              => 'select',
		'label'             => esc_html__( 'Social Network', 'wp-grid-builder' ),
		'value'             => 'facebook',
		'search'            => true,
		'options'           => [
			'blogger'     => 'Blogger',
			'buffer'      => 'Buffer',
			'email'       => esc_html__( 'Email', 'wp-grid-builder' ),
			'evernote'    => 'Evernote',
			'facebook'    => 'Facebook',
			'google-plus' => 'Google+',
			'linkedin'    => 'LinkedIn',
			'pinterest'   => 'Pinterest',
			'reddit'      => 'Reddit',
			'tumblr'      => 'Tumblr',
			'twitter'     => 'Twitter',
			'vkontakte'   => 'VKontakte',
			'whatsapp'    => 'Whatsapp',
		],
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'social_share_block',
			],
		],
	],
];

$metadata = [
	// meta_key.
	[
		'id'                => 'meta_key',
		'tab'               => 'content',
		'type'              => 'select',
		'label'             => esc_html__( 'Custom Field', 'wp-grid-builder' ),
		'placeholder'       => __( 'Enter a field name', 'wp-grid-builder' ),
		'search'            => true,
		'async'             => 'search_custom_fields',
		'options'           => $meta_options,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
		],
	],
	// meta_type.
	[
		'id'                => 'meta_type',
		'tab'               => 'content',
		'type'              => 'radio',
		'label'             => esc_html__( 'Field Type', 'wp-grid-builder' ),
		'value'             => 'text',
		'options'           => [
			'text'   => esc_html__( 'Text', 'wp-grid-builder' ),
			'number' => esc_html__( 'Number', 'wp-grid-builder' ),
			'date'   => esc_html__( 'Date', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
		],
	],
	// meta_input_date.
	[
		'id'                => 'meta_input_date',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Input Date Format', 'wp-grid-builder' ),
		'placeholder'       => esc_html__( 'Auto (e.g: y-m-d)', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
			[
				'field'   => 'meta_type',
				'compare' => '===',
				'value'   => 'date',
			],
		],
	],
	// meta_output_date.
	[
		'id'                => 'meta_output_date',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Output Date Format', 'wp-grid-builder' ),
		'placeholder'       => esc_html__( 'Default (e.g: F j, Y)', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
			[
				'field'   => 'meta_type',
				'compare' => '===',
				'value'   => 'date',
			],
		],
	],
	// meta_decimal_places.
	[
		'id'                => 'meta_decimal_places',
		'tab'               => 'content',
		'type'              => 'number',
		'label'             => esc_html__( 'Decimal Places', 'wp-grid-builder' ),
		'value'             => 0,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
			[
				'field'   => 'meta_type',
				'compare' => '===',
				'value'   => 'number',
			],
		],
	],
	// meta_decimal_separator.
	[
		'id'                => 'meta_decimal_separator',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Decimal Separator', 'wp-grid-builder' ),
		'value'             => '.',
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
			[
				'field'   => 'meta_type',
				'compare' => '===',
				'value'   => 'number',
			],
		],
	],
	// meta_thousands_separator.
	[
		'id'                => 'meta_thousands_separator',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Thousands Separator', 'wp-grid-builder' ),
		'white_spaces'      => true,
		'value'             => '',
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
			[
				'field'   => 'meta_type',
				'compare' => '===',
				'value'   => 'number',
			],
		],
	],
	// meta_prefix.
	[
		'id'                => 'meta_prefix',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Prefix', 'wp-grid-builder' ),
		'white_spaces'      => true,
		'angle_quotes'      => true,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
		],
	],
	// meta_suffix.
	[
		'id'                => 'meta_suffix',
		'tab'               => 'content',
		'type'              => 'text',
		'label'             => esc_html__( 'Suffix', 'wp-grid-builder' ),
		'white_spaces'      => true,
		'angle_quotes'      => true,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'metadata',
			],
		],
	],
];

$svg_icon = [
	// svg_name.
	[
		'id'                => 'svg_name',
		'tab'               => 'content',
		'type'              => 'icons',
		'label'             => esc_html__( 'SVG Icon', 'wp-grid-builder' ),
		'value'             => 'wpgb/animals/bug',
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'svg_icon_block',
			],
		],
	],
	// stroke-width.
	[
		'id'                => 'stroke-width',
		'tab'               => 'content',
		'type'              => 'slider',
		'label'             => esc_html__( 'Icon Stroke Width', 'wp-grid-builder' ),
		'steps'             => [ 0.01 ],
		'units'             => [ '' ],
		'value'             => 1,
		'min'               => 0.1,
		'max'               => 10,
		'conditional_logic' => [
			'relation' => 'OR',
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'svg_icon_block',
				],
			],
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'media_button_block',
				],
			],
			[
				[
					'field'   => 'source',
					'compare' => '===',
					'value'   => 'product_field',
				],
				[
					'relation' => 'OR',
					[
						'field'   => 'product_field',
						'compare' => '===',
						'value'   => 'the_star_rating',
					],
					[
						[
							'field'   => 'product_field',
							'compare' => '===',
							'value'   => 'the_on_sale_badge',
						],
						[
							'field'   => 'badge_type',
							'compare' => '===',
							'value'   => 'icon',
						],
					],
				],
			],
		],
	],
];

$media_button = [
	// lightbox_icon.
	[
		'id'                => 'lightbox_icon',
		'tab'               => 'content',
		'type'              => 'icons',
		'label'             => esc_html__( 'Lightbox Icon', 'wp-grid-builder' ),
		'value'             => 'wpgb/user-interface/add',
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'media_button_block',
			],
		],
	],
	// play_icon.
	[
		'id'                => 'play_icon',
		'tab'               => 'content',
		'type'              => 'icons',
		'label'             => esc_html__( 'Play Button Icon', 'wp-grid-builder' ),
		'value'             => 'wpgb/multimedia/button-play-2',
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'media_button_block',
			],
		],
	],
];

$raw_content = [
	// raw_content.
	[
		'id'                => 'raw_content',
		'tab'               => 'content',
		'type'              => 'code',
		'label'             => esc_html__( 'Raw Content (text/HTML)', 'wp-grid-builder' ),
		'mode'              => 'text/html',
		'height'            => 350,
		'conditional_logic' => [
			[
				'field'   => 'source',
				'compare' => '===',
				'value'   => 'raw_content_block',
			],
		],
	],
];

$layer_action = [
	// action_type.
	[
		'id'          => 'action_type',
		'tab'         => 'action',
		'type'        => 'select',
		'label'       => esc_html__( 'On Click', 'wp-grid-builder' ),
		'placeholder' => esc_html__( 'None', 'wp-grid-builder' ),
		'options'     => [
			'link'       => esc_html__( 'Redirect To', 'wp-grid-builder' ),
			'open_media' => esc_html__( 'Open lightbox or play media', 'wp-grid-builder' ),
		],
	],
	// link_target.
	[
		'id'                => 'link_target',
		'tab'               => 'action',
		'type'              => 'radio',
		'label'             => esc_html__( 'Open Link in', 'wp-grid-builder' ),
		'value'             => '_self',
		'options'           => [
			'_self'  => esc_html__( 'Same Window', 'wp-grid-builder' ),
			'_blank' => esc_html__( 'New Window', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			[
				'field'   => 'action_type',
				'compare' => '===',
				'value'   => 'link',
			],
		],
	],
	// link_rel.
	[
		'id'                => 'link_rel',
		'tab'               => 'action',
		'type'              => 'select',
		'label'             => esc_html__( 'Rel Attribute', 'wp-grid-builder' ),
		'value'             => '',
		'multiple'          => true,
		'search'            => true,
		'placeholder'       => esc_html__( 'None', 'wp-grid-builder' ),
		'options'           => [
			'alternate'  => 'alternate',
			'author'     => 'author',
			'bookmark'   => 'bookmark',
			'external'   => 'external',
			'help'       => 'help',
			'license'    => 'license',
			'next'       => 'next',
			'nofollow'   => 'nofollow',
			'noreferrer' => 'noreferrer',
			'noopener'   => 'noopener',
			'prev'       => 'prev',
			'search'     => 'search',
			'tag'        => 'tag',
		],
		'conditional_logic' => [
			[
				'field'   => 'action_type',
				'compare' => '===',
				'value'   => 'link',
			],
		],
	],
	// link_aria_label.
	[
		'id'                => 'link_aria_label',
		'tab'               => 'action',
		'type'              => 'text',
		'label'             => esc_html__( 'Aria Label', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'action_type',
				'compare' => '===',
				'value'   => 'link',
			],
		],
	],
	// link_url.
	[
		'id'                => 'link_url',
		'tab'               => 'action',
		'type'              => 'select',
		'label'             => esc_html__( 'URL', 'wp-grid-builder' ),
		'options'           => [
			'single_post' => esc_html__( 'Single Post/Page', 'wp-grid-builder' ),
			'author_page' => esc_html__( 'Author Archive Page', 'wp-grid-builder' ),
			'custom_url'  => esc_html__( 'Custom URL', 'wp-grid-builder' ),
			'metadata'    => esc_html__( 'Custom Field', 'wp-grid-builder' ),
		],
		'conditional_logic' => [
			[
				'field'   => 'action_type',
				'compare' => '===',
				'value'   => 'link',
			],
		],
	],
	// custom_url.
	[
		'id'                => 'custom_url',
		'tab'               => 'action',
		'type'              => 'url',
		'label'             => esc_html__( 'Custom URL', 'wp-grid-builder' ),
		'conditional_logic' => [
			[
				'field'   => 'action_type',
				'compare' => '===',
				'value'   => 'link',
			],
			[
				'field'   => 'link_url',
				'compare' => '===',
				'value'   => 'custom_url',
			],
		],
	],
	// meta_key.
	[
		'id'                => 'meta_key',
		'tab'               => 'action',
		'type'              => 'select',
		'label'             => esc_html__( 'Custom Field', 'wp-grid-builder' ),
		'placeholder'       => __( 'Enter a field name', 'wp-grid-builder' ),
		'search'            => true,
		'async'             => 'search_custom_fields',
		'options'           => $meta_options,
		'conditional_logic' => [
			[
				'field'   => 'action_type',
				'compare' => '===',
				'value'   => 'link',
			],
			[
				'field'   => 'link_url',
				'compare' => '===',
				'value'   => 'metadata',
			],
		],
	],
];

$block_action = array_merge(
	[
		// Nota bene no action.
		[
			'id'    => 'no-action',
			'tab'   => 'action',
			'type'  => 'notabene',
			'value' => esc_html__( "This block natively has an action applied on click.\nIf you need to set an action, please change the source type under Content panel.", 'wp-grid-builder' ),
		],
	],
	$layer_action
);


$animation = [
	// presets.
	[
		'id'      => 'presets',
		'tab'     => 'animation',
		'type'    => 'radio',
		'style'   => 'list',
		'label'   => esc_html__( 'Animation Presets', 'wp-grid-builder' ),
		'options' => [
			'custom'      => '',
			''            => esc_html__( 'None', 'wp-grid-builder' ),
			'fade'        => esc_html__( 'Fade', 'wp-grid-builder' ),
			'from-left'   => esc_html__( 'From left', 'wp-grid-builder' ),
			'from-right'  => esc_html__( 'From right', 'wp-grid-builder' ),
			'from-top'    => esc_html__( 'From top', 'wp-grid-builder' ),
			'from-bottom' => esc_html__( 'From bottom', 'wp-grid-builder' ),
			'zoom-out'    => esc_html__( 'Zoom out', 'wp-grid-builder' ),
			'zoom-in'     => esc_html__( 'Zoom in', 'wp-grid-builder' ),
			'fold-up'     => esc_html__( 'Fold up', 'wp-grid-builder' ),
			'flip'        => esc_html__( 'Flip', 'wp-grid-builder' ),
			'rotate'      => esc_html__( 'Rotate', 'wp-grid-builder' ),
			'bounce'      => esc_html__( 'Bounce', 'wp-grid-builder' ),
		],
		'icons'   => [
			''            => Includes\Helpers::get_icon( 'none', true ),
			'fade'        => Includes\Helpers::get_icon( 'fade', true ),
			'from-left'   => Includes\Helpers::get_icon( 'from-left', true ),
			'from-right'  => Includes\Helpers::get_icon( 'from-right', true ),
			'from-top'    => Includes\Helpers::get_icon( 'from-top', true ),
			'from-bottom' => Includes\Helpers::get_icon( 'from-bottom', true ),
			'zoom-out'    => Includes\Helpers::get_icon( 'zoom-out', true ),
			'zoom-in'     => Includes\Helpers::get_icon( 'zoom-in', true ),
			'fold-up'     => Includes\Helpers::get_icon( 'fold-up', true ),
			'flip'        => Includes\Helpers::get_icon( 'flip', true ),
			'rotate'      => Includes\Helpers::get_icon( 'rotate', true ),
			'bounce'      => Includes\Helpers::get_icon( 'bounce', true ),
		],
		'value'   => '',
	],
	// animation_behaviour.
	[
		'id'                => 'animation_behaviour',
		'tab'               => 'animation',
		'type'              => 'group',
		'label'             => esc_html__( 'Animation Behaviour', 'wp-grid-builder' ),
		'fields'            => [
			// selector.
			[
				'id'      => 'selector',
				'type'    => 'select',
				'label'   => esc_html__( 'Animate From', 'wp-grid-builder' ),
				'options' => [
					'parent' => esc_html__( 'Parent Container', 'wp-grid-builder' ),
					'card'   => esc_html__( 'Whole Card', 'wp-grid-builder' ),
				],
			],
			// revert.
			[
				'id'    => 'reverse',
				'type'  => 'toggle',
				'label' => esc_html__( 'Reverse Animation', 'wp-grid-builder' ),
				'value' => 0,
			],
		],
		'conditional_logic' => [
			[
				'field'   => 'presets',
				'compare' => '!=',
				'value'   => '',
			],
		],
	],
	// animation_timing.
	[
		'id'     => 'animation_timing',
		'tab'    => 'animation',
		'type'   => 'group',
		'label'  => esc_html__( 'Animation Timing', 'wp-grid-builder' ),
		'fields' => [
			// transition-easing.
			[
				'id'      => 'transition-easing',
				'type'    => 'select',
				'label'   => esc_html__( 'Easing', 'wp-grid-builder' ),
				'search'  => true,
				// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned, WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow
				'options' => [
					'ease'                                      => 'Ease',
					'linear'                                    => 'Linear',
					'ease-in'                                   => 'Ease In',
					'ease-out'                                  => 'Ease Out',
					'ease-in-out'                               => 'Ease In Out',
					'cubic-bezier(0.550, 0.055, 0.675, 0.190)'  => 'Ease In Cubic',
					'cubic-bezier(0.215, 0.610, 0.355, 1.000)'  => 'Ease Out Cubic',
					'cubic-bezier(0.645, 0.045, 0.355, 1.000)'  => 'Ease In OutCubic',
					'cubic-bezier(0.600, 0.040, 0.980, 0.335)'  => 'Ease In Circ',
					'cubic-bezier(0.075, 0.820, 0.165, 1.000)'  => 'Ease Out Circ',
					'cubic-bezier(0.785, 0.135, 0.150, 0.860)'  => 'Ease In Out Circ',
					'cubic-bezier(0.950, 0.050, 0.795, 0.035)'  => 'Ease In Expo',
					'cubic-bezier(0.190, 1.000, 0.220, 1.000)'  => 'Ease Out Expo',
					'cubic-bezier(1.000, 0.000, 0.000, 1.000)'  => 'Ease In Out Expo',
					'cubic-bezier(0.550, 0.085, 0.680, 0.530)'  => 'Ease In Quad',
					'cubic-bezier(0.250, 0.460, 0.450, 0.940)'  => 'Ease Out Quad',
					'cubic-bezier(0.455, 0.030, 0.515, 0.955)'  => 'Ease In Out Quad',
					'cubic-bezier(0.895, 0.030, 0.685, 0.220)'  => 'Ease In Quart',
					'cubic-bezier(0.165, 0.840, 0.440, 1.000)'  => 'Ease Out Quart',
					'cubic-bezier(0.770, 0.000, 0.175, 1.000)'  => 'Ease In Out Quart',
					'cubic-bezier(0.755, 0.050, 0.855, 0.060)'  => 'Ease In Quint',
					'cubic-bezier(0.230, 1.000, 0.320, 1.000)'  => 'Ease Out Quint',
					'cubic-bezier(0.860, 0.000, 0.070, 1.000)'  => 'Ease In Out Quint',
					'cubic-bezier(0.470, 0.000, 0.745, 0.715)'  => 'Ease In Sine',
					'cubic-bezier(0.390, 0.575, 0.565, 1.000)'  => 'Ease Out Sine',
					'cubic-bezier(0.445, 0.050, 0.550, 0.950)'  => 'Ease In Out Sine',
					'cubic-bezier(0.600, -0.280, 0.735, 0.045)' => 'Ease In Back',
					'cubic-bezier(0.175,  0.885, 0.320, 1.275)' => 'Ease Out Back',
					'cubic-bezier(0.680, -0.550, 0.265, 1.550)' => 'Ease In Out Back',
					'custom'                                    => esc_html__( 'Custom Easing', 'wp-grid-builder' ),
				],
				// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned, WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow
			],
			// cubic-bezier-function.
			[
				'id'                => 'cubic-bezier-function',
				'type'              => 'text',
				'label'             => esc_html__( 'Cubic Bezier', 'wp-grid-builder' ),
				'conditional_logic' => [
					[
						'field'   => 'transition-easing',
						'compare' => '===',
						'value'   => 'custom',
					],
				],
			],
			// transition-duration.
			[
				'id'    => 'transition-duration',
				'type'  => 'slider',
				'label' => esc_html__( 'Duration', 'wp-grid-builder' ),
				'steps' => [ 1 ],
				'units' => [ 'ms' ],
				'unit'  => true,
				'value' => 0,
				'min'   => 0,
				'max'   => 5000,
			],
			// transition-delay.
			[
				'id'    => 'transition-delay',
				'type'  => 'slider',
				'label' => esc_html__( 'Delay', 'wp-grid-builder' ),
				'steps' => [ 1 ],
				'units' => [ 'ms' ],
				'unit'  => true,
				'value' => 0,
				'min'   => 0,
				'max'   => 5000,
			],
		],
	],
	// opacity.
	[
		'id'                => 'opacity',
		'tab'               => 'animation',
		'type'              => 'slider',
		'label'             => esc_html__( 'Starting Opacity', 'wp-grid-builder' ),
		'steps'             => [ 0.01 ],
		'units'             => [ '' ],
		'value'             => 1,
		'min'               => 0,
		'max'               => 1,
		'conditional_logic' => [
			[
				'field'   => 'presets',
				'compare' => '!=',
				'value'   => '',
			],
		],
	],
	// transform_origins.
	[
		'id'                => 'transform_origins',
		'tab'               => 'animation',
		'type'              => 'group',
		'label'             => esc_html__( 'Transform Origin', 'wp-grid-builder' ),
		'fields'            => [
			// transform-origin-x.
			[
				'id'    => 'transform-origin-x',
				'type'  => 'text_number',
				'label' => esc_html__( 'X axis', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01 ],
				'units' => [ 'px', '%' ],
				'min'   => -999,
				'max'   => 999,
			],
			// transform-origin-y.
			[
				'id'    => 'transform-origin-y',
				'type'  => 'text_number',
				'label' => esc_html__( 'Y axis', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01 ],
				'units' => [ 'px', '%' ],
				'min'   => -999,
				'max'   => 999,
			],
			// transform-origin-z.
			[
				'id'    => 'transform-origin-z',
				'type'  => 'text_number',
				'label' => esc_html__( 'Z axis', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01 ],
				'units' => [ 'px', '%' ],
				'min'   => -999,
				'max'   => 999,
			],
		],
		'conditional_logic' => [
			[
				'field'   => 'presets',
				'compare' => '!=',
				'value'   => '',
			],
		],
	],
	// translate_values.
	[
		'id'                => 'translate_values',
		'tab'               => 'animation',
		'type'              => 'group',
		'label'             => esc_html__( 'Translate', 'wp-grid-builder' ),
		'fields'            => [
			// translateX.
			[
				'id'    => 'translateX',
				'type'  => 'text_number',
				'label' => esc_html__( 'X axis', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
				'units' => [ 'px', '%', 'em', 'rem' ],
				'min'   => -999,
				'max'   => 999,
			],
			// translateY.
			[
				'id'    => 'translateY',
				'type'  => 'text_number',
				'label' => esc_html__( 'Y axis', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
				'units' => [ 'px', '%', 'em', 'rem' ],
				'min'   => -999,
				'max'   => 999,
			],
			// translateZ.
			[
				'id'    => 'translateZ',
				'type'  => 'text_number',
				'label' => esc_html__( 'Z axis', 'wp-grid-builder' ),
				'steps' => [ 1, 0.01, 0.0001, 0.0001 ],
				'units' => [ 'px', '%', 'em', 'rem' ],
				'min'   => -999,
				'max'   => 999,
			],
		],
		'conditional_logic' => [
			[
				'field'   => 'presets',
				'compare' => '!==',
				'value'   => '',
			],
		],
	],
	// rotate_values.
	[
		'id'                => 'rotate_values',
		'tab'               => 'animation',
		'type'              => 'group',
		'label'             => esc_html__( 'Rotate', 'wp-grid-builder' ),
		'fields'            => [
			// rotateX.
			[
				'id'    => 'rotateX',
				'type'  => 'text_number',
				'label' => esc_html__( 'X axis', 'wp-grid-builder' ),
				'steps' => [ 1 ],
				'units' => [ 'deg' ],
				'min'   => -360,
				'max'   => 360,
			],
			// rotateY.
			[
				'id'    => 'rotateY',
				'type'  => 'text_number',
				'label' => esc_html__( 'Y axis', 'wp-grid-builder' ),
				'steps' => [ 1 ],
				'units' => [ 'deg' ],
				'min'   => -360,
				'max'   => 360,
			],
			// rotateZ.
			[
				'id'    => 'rotateZ',
				'type'  => 'text_number',
				'label' => esc_html__( 'Z axis', 'wp-grid-builder' ),
				'steps' => [ 1 ],
				'units' => [ 'deg' ],
				'min'   => -360,
				'max'   => 360,
			],
		],
		'conditional_logic' => [
			[
				'field'   => 'presets',
				'compare' => '!=',
				'value'   => '',
			],
		],
	],
	// scale_values.
	[
		'id'                => 'scale_values',
		'tab'               => 'animation',
		'type'              => 'group',
		'label'             => esc_html__( 'Scale', 'wp-grid-builder' ),
		'fields'            => [
			// scaleX.
			[
				'id'    => 'scaleX',
				'type'  => 'text_number',
				'label' => esc_html__( 'X axis', 'wp-grid-builder' ),
				'steps' => [ 0.01 ],
				'units' => [ '' ],
				'min'   => -10,
				'max'   => 10,
			],
			// scaleY.
			[
				'id'    => 'scaleY',
				'type'  => 'text_number',
				'label' => esc_html__( 'Y axis', 'wp-grid-builder' ),
				'steps' => [ 0.01 ],
				'units' => [ '' ],
				'min'   => -10,
				'max'   => 10,
			],
			// scaleZ.
			[
				'id'    => 'scaleZ',
				'type'  => 'text_number',
				'label' => esc_html__( 'Z axis', 'wp-grid-builder' ),
				'steps' => [ 0.01 ],
				'units' => [ '' ],
				'min'   => -10,
				'max'   => 10,
			],
		],
		'conditional_logic' => [
			[
				'field'   => 'presets',
				'compare' => '!=',
				'value'   => '',
			],
		],
	],
	// skew_values.
	[
		'id'                => 'skew_values',
		'tab'               => 'animation',
		'type'              => 'group',
		'label'             => esc_html__( 'skew', 'wp-grid-builder' ),
		'fields'            => [
			// skewX.
			[
				'id'    => 'skewX',
				'type'  => 'text_number',
				'label' => esc_html__( 'X axis', 'wp-grid-builder' ),
				'steps' => [ 1 ],
				'units' => [ 'deg' ],
				'min'   => -360,
				'max'   => 360,
			],
			// skewY.
			[
				'id'    => 'skewY',
				'type'  => 'text_number',
				'label' => esc_html__( 'Y axis', 'wp-grid-builder' ),
				'steps' => [ 1 ],
				'units' => [ 'deg' ],
				'min'   => -360,
				'max'   => 360,
			],
		],
		'conditional_logic' => [
			[
				'field'   => 'presets',
				'compare' => '!=',
				'value'   => '',
			],
		],
	],
	// transform_perspective.
	[
		'id'                => 'transform_perspective',
		'tab'               => 'animation',
		'type'              => 'group',
		'label'             => esc_html__( 'Perspective', 'wp-grid-builder' ),
		'fields'            => [
			// transform-perspective.
			[
				'id'    => 'transform-perspective',
				'type'  => 'text_number',
				'steps' => [ 1, 0.0001, 0.0001 ],
				'units' => [ 'px', 'em', 'rem' ],
				'min'   => 0,
				'max'   => 9999,
			],
		],
		'conditional_logic' => [
			[
				'field'   => 'presets',
				'compare' => '!=',
				'value'   => '',
			],
		],
	],
];

$layout = [
	// title.
	[
		'id'    => 'card_name_title',
		'tab'   => 'general',
		'type'  => 'title',
		'title' => esc_html__( 'Card Name', 'wp-grid-builder' ),
	],
	// name.
	[
		'id'          => 'name',
		'tab'         => 'general',
		'type'        => 'text',
		'title'       => esc_html__( 'Card Name', 'wp-grid-builder' ),
		'value'       => 'New Card',
		'placeholder' => __( 'Enter a card name', 'wp-grid-builder' ),
	],
	// title.
	[
		'id'    => 'card_layout_title',
		'tab'   => 'general',
		'type'  => 'title',
		'title' => esc_html__( 'Card Layout', 'wp-grid-builder' ),
	],
	// type.
	[
		'id'      => 'type',
		'tab'     => 'general',
		'type'    => 'radio',
		'style'   => 'list',
		'label'   => esc_html__( 'Card Type', 'wp-grid-builder' ),
		'options' => [
			'masonry' => esc_html__( 'Masonry', 'wp-grid-builder' ),
			'metro'   => esc_html__( 'Metro / Justified', 'wp-grid-builder' ),
		],
		'icons'   => [
			'masonry' => Includes\Helpers::get_icon( 'masonry-grid-large', true ),
			'metro'   => Includes\Helpers::get_icon( 'metro-grid-large', true ),
		],
		'value'   => 'masonry',
	],
	// card_layout.
	[
		'id'                => 'card_layout',
		'tab'               => 'general',
		'type'              => 'radio',
		'label'             => esc_html__( 'Card Alignment', 'wp-grid-builder' ),
		'options'           => [
			'vertical'   => esc_html__( 'Vertical', 'wp-grid-builder' ),
			'horizontal' => esc_html__( 'Horizontal', 'wp-grid-builder' ),
		],
		'value'             => 'vertical',
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'masonry',
			],
		],
	],
	// content_position.
	[
		'id'                => 'content_position',
		'tab'               => 'general',
		'type'              => 'radio',
		'label'             => esc_html__( 'Content Position', 'wp-grid-builder' ),
		'options'           => [
			''       => esc_html__( 'None', 'wp-grid-builder' ),
			'top'    => esc_html__( 'Top', 'wp-grid-builder' ),
			'bottom' => esc_html__( 'Bottom', 'wp-grid-builder' ),
			'both'   => esc_html_x( 'Both', 'Content Position', 'wp-grid-builder' ),
		],
		'value'             => 'bottom',
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'masonry',
			],
			[
				'field'   => 'card_layout',
				'compare' => '===',
				'value'   => 'vertical',
			],
		],
	],
	// media_position.
	[
		'id'                => 'media_position',
		'tab'               => 'general',
		'type'              => 'radio',
		'label'             => esc_html__( 'Content Position', 'wp-grid-builder' ),
		'options'           => [
			'right' => esc_html__( 'Left', 'wp-grid-builder' ),
			'left'  => esc_html__( 'Right', 'wp-grid-builder' ),
		],
		'value'             => 'left',
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'masonry',
			],
			[
				'field'   => 'card_layout',
				'compare' => '===',
				'value'   => 'horizontal',
			],
		],
	],
	// media_width.
	[
		'id'                => 'media_width',
		'tab'               => 'general',
		'type'              => 'slider',
		'label'             => esc_html__( 'Media Width', 'wp-grid-builder' ),
		'steps'             => [ 0.1 ],
		'units'             => [ '%' ],
		'unit'              => true,
		'min'               => 10,
		'max'               => 90,
		'value'             => 50,
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'masonry',
			],
			[
				'field'   => 'card_layout',
				'compare' => '===',
				'value'   => 'horizontal',
			],
		],
	],
	// switch_layout.
	[
		'id'                => 'switch_layout',
		'tab'               => 'general',
		'type'              => 'slider',
		'label'             => esc_html__( 'Switch to Vertical Alignment (browser width)', 'wp-grid-builder' ),
		'steps'             => [ 1 ],
		'units'             => [ 'px' ],
		'unit'              => 'px',
		'min'               => 0,
		'max'               => 2560,
		'value'             => '768px',
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'masonry',
			],
			[
				'field'   => 'card_layout',
				'compare' => '===',
				'value'   => 'horizontal',
			],
		],
	],
	// title.
	[
		'id'    => 'card_layers_title',
		'tab'   => 'general',
		'type'  => 'title',
		'title' => esc_html__( 'Card Layers', 'wp-grid-builder' ),
	],
	// display_media.
	[
		'id'                => 'display_media',
		'tab'               => 'general',
		'type'              => 'toggle',
		'label'             => esc_html__( 'Card Media', 'wp-grid-builder' ),
		'value'             => 1,
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'masonry',
			],
			[
				'field'   => 'card_layout',
				'compare' => '===',
				'value'   => 'vertical',
			],
			[
				'field'   => 'content_position',
				'compare' => '!==',
				'value'   => '',
			],
		],
	],
	// flex_media.
	[
		'id'                => 'flex_media',
		'tab'               => 'general',
		'type'              => 'toggle',
		'label'             => esc_html__( 'Flexible Media', 'wp-grid-builder' ),
		'tooltip'           => esc_html__( 'Media thumbnail will fit content height. In this case thumbnail will be cropped.', 'wp-grid-builder' ),
		'value'             => 0,
		'conditional_logic' => [
			[
				[
					'field'   => 'type',
					'compare' => '===',
					'value'   => 'masonry',
				],
				[
					'relation' => 'OR',
					[
						'field'   => 'card_layout',
						'compare' => '===',
						'value'   => 'horizontal',
					],
					[
						'field'   => 'display_media',
						'compare' => '==',
						'value'   => 1,
					],
					[
						'field'   => 'content_position',
						'compare' => '===',
						'value'   => '',
					],
				],
			],
		],
	],
	// display_overlay.
	[
		'id'                => 'display_overlay',
		'tab'               => 'general',
		'type'              => 'toggle',
		'label'             => esc_html__( 'Media Overlay', 'wp-grid-builder' ),
		'value'             => 1,
		'conditional_logic' => [
			'relation' => 'OR',
			[
				[
					'field'   => 'type',
					'compare' => '===',
					'value'   => 'masonry',
				],
				[
					'relation' => 'OR',
					[
						'field'   => 'display_media',
						'compare' => '==',
						'value'   => 1,
					],
					[
						'field'   => 'card_layout',
						'compare' => '===',
						'value'   => 'horizontal',
					],
				],
			],
			[
				[
					'field'   => 'type',
					'compare' => '===',
					'value'   => 'metro',
				],
			],
		],
	],
	// display_footer.
	[
		'id'                => 'display_footer',
		'tab'               => 'general',
		'type'              => 'toggle',
		'label'             => esc_html__( 'Card Footer', 'wp-grid-builder' ),
		'value'             => 1,
		'conditional_logic' => [
			[
				'field'   => 'type',
				'compare' => '===',
				'value'   => 'masonry',
			],
			[
				'relation' => 'OR',
				[
					'field'   => 'content_position',
					'compare' => 'IN',
					'value'   => [ 'bottom', 'both' ],
				],
				[
					'field'   => 'card_layout',
					'compare' => '===',
					'value'   => 'horizontal',
				],
			],
		],
	],
	// title.
	[
		'id'    => 'card_sizing_title',
		'tab'   => 'general',
		'type'  => 'title',
		'title' => esc_html__( 'Card Sizing', 'wp-grid-builder' ),
	],
	// responsive.
	[
		'id'    => 'responsive',
		'tab'   => 'general',
		'type'  => 'toggle',
		'label' => esc_html__( 'Responsive Font', 'wp-grid-builder' ),
		'value' => 0,
	],
	// card_width.
	[
		'id'      => 'card_width',
		'tab'     => 'general',
		'type'    => 'slider',
		'label'   => esc_html__( 'Card Width', 'wp-grid-builder' ),
		'tooltip' => esc_html__( 'The card width is a builder helper and will not be applied in your grid.', 'wp-grid-builder' ),
		'steps'   => [ 1 ],
		'units'   => [ 'px' ],
		'unit'    => true,
		'min'     => 300,
		'max'     => 1200,
		'value'   => '500px',
	],
];

$global_css = [
	// global_css.
	[
		'id'          => 'global_css',
		'tab'         => 'custom_css',
		'type'        => 'code',
		'label'       => esc_html__( 'Custom CSS', 'wp-grid-builder' ),
		'mode'        => 'css',
		'height'      => 650,
		'placeholder' => "\n" .
			'.wpgb-block-1 {' . "\n" .
			'    margin: 0 auto;' . "\n" .
			'    padding: 10px 20px;' . "\n" .
			'    color: green;' . "\n" .
			'}',
	],
];

$blocks = [
	[
		'id'          => 'available-blocks',
		'tab'         => 'blocks',
		'type'        => 'blocks',
		'placeholder' => esc_html__( 'Search blocks', 'wp-grid-builder' ),
		'icons'       => [
			'the_id'                 => Includes\Helpers::get_icon( 'post-id', true ),
			'the_title'              => Includes\Helpers::get_icon( 'post-title', true ),
			'the_name'               => Includes\Helpers::get_icon( 'post-title', true ),
			'the_content'            => Includes\Helpers::get_icon( 'post-content', true ),
			'the_excerpt'            => Includes\Helpers::get_icon( 'post-excerpt', true ),
			'the_post_type'          => Includes\Helpers::get_icon( 'post-type', true ),
			'the_post_format'        => Includes\Helpers::get_icon( 'post-format', true ),
			'the_post_status'        => Includes\Helpers::get_icon( 'post-status', true ),
			'the_date'               => Includes\Helpers::get_icon( 'post-date', true ),
			'the_modified_date'      => Includes\Helpers::get_icon( 'post-modified-date', true ),
			'the_terms'              => Includes\Helpers::get_icon( 'post-terms', true ),
			'the_author'             => Includes\Helpers::get_icon( 'post-author', true ),
			'the_avatar'             => Includes\Helpers::get_icon( 'post-avatar', true ),
			'comments_number'        => Includes\Helpers::get_icon( 'post-comments', true ),
			'the_price'              => Includes\Helpers::get_icon( 'product-price', true ),
			'the_full_price'         => Includes\Helpers::get_icon( 'product-price', true ),
			'the_regular_price'      => Includes\Helpers::get_icon( 'product-price', true ),
			'the_sale_price'         => Includes\Helpers::get_icon( 'product-price', true ),
			'the_star_rating'        => Includes\Helpers::get_icon( 'product-star-rating', true ),
			'the_text_rating'        => Includes\Helpers::get_icon( 'product-text-rating', true ),
			'the_on_sale_badge'      => Includes\Helpers::get_icon( 'product-sale', true ),
			'the_cart_button'        => Includes\Helpers::get_icon( 'product-cart-button', true ),
			'the_in_stock_badge'     => Includes\Helpers::get_icon( 'product-in-stock', true ),
			'the_out_of_stock_badge' => Includes\Helpers::get_icon( 'product-out-of-stock', true ),
			'the_term_id'            => Includes\Helpers::get_icon( 'post-id', true ),
			'the_term_name'          => Includes\Helpers::get_icon( 'term-name', true ),
			'the_term_slug'          => Includes\Helpers::get_icon( 'term-name', true ),
			'the_term_taxonomy'      => Includes\Helpers::get_icon( 'term-taxonomy', true ),
			'the_term_parent'        => Includes\Helpers::get_icon( 'term-parent', true ),
			'the_term_description'   => Includes\Helpers::get_icon( 'term-description', true ),
			'the_term_count'         => Includes\Helpers::get_icon( 'term-count', true ),
			'the_user_id'            => Includes\Helpers::get_icon( 'post-id', true ),
			'the_user_display_name'  => Includes\Helpers::get_icon( 'user', true ),
			'the_user_first_name'    => Includes\Helpers::get_icon( 'user', true ),
			'the_user_last_name'     => Includes\Helpers::get_icon( 'user', true ),
			'the_user_nickname'      => Includes\Helpers::get_icon( 'user', true ),
			'the_user_login'         => Includes\Helpers::get_icon( 'user', true ),
			'the_user_description'   => Includes\Helpers::get_icon( 'post-author', true ),
			'the_user_email'         => Includes\Helpers::get_icon( 'user-email', true ),
			'the_user_url'           => Includes\Helpers::get_icon( 'user-website', true ),
			'the_user_roles'         => Includes\Helpers::get_icon( 'user-roles', true ),
			'the_user_post_count'    => Includes\Helpers::get_icon( 'user-posts-count', true ),
			'metadata'               => Includes\Helpers::get_icon( 'custom-field', true ),
			'social_share_block'     => Includes\Helpers::get_icon( 'social-share', true ),
			'svg_icon_block'         => Includes\Helpers::get_icon( 'svg-path', true ),
			'media_button_block'     => Includes\Helpers::get_icon( 'media-button', true ),
			'raw_content_block'      => Includes\Helpers::get_icon( 'raw-content', true ),
			'custom_block'           => Includes\Helpers::get_icon( 'custom-block', true ),
		],
	],
];

$dynamic_fields = [
	// box-shadow.
	[
		'id'   => 'box-shadow',
		'type' => 'text',
	],
	// background-image.
	[
		'id'   => 'background-image',
		'type' => 'text',
	],
	// text-shadow.
	[
		'id'   => 'text-shadow',
		'type' => 'text',
	],
	// transition-timing-function.
	[
		'id'   => 'transition-timing-function',
		'type' => 'text',
	],
	// transform-origin.
	[
		'id'   => 'transform-origin',
		'type' => 'text',
	],
	// transform.
	[
		'id'   => 'transform',
		'type' => 'text',
	],
	// filter.
	[
		'id'   => 'filter',
		'type' => 'text',
	],
	// css.
	[
		'id'       => 'css',
		'type'     => 'code',
		'mode'     => 'css',
		'compress' => 2,
	],
	// fonts.
	[
		'id'   => 'google',
		'type' => 'fonts',
	],
	// idle_scheme.
	[
		'id'   => 'idle_scheme',
		'type' => 'text',
	],
	// hover_scheme.
	[
		'id'   => 'hover_scheme',
		'type' => 'text',
	],
];

$builder_settings = [
	'animations' => [
		''            => [
			'opacity'               => 1,
			'easing'                => 'ease',
			'cubic-bezier-function' => '',
			'transform-perspective' => '',
			'transform-origin-x'    => '',
			'transform-origin-y'    => '',
			'transform-origin-z'    => '',
			'translateX'            => '',
			'translateY'            => '',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '',
			'rotateZ'               => '',
			'scaleX'                => '',
			'scaleY'                => '',
			'scaleZ'                => '',
			'skewX'                 => '',
			'skewY'                 => '',
		],
		'fade'        => [
			'opacity'               => 0,
			'transition-duration'   => '350ms',
			'transition-easing'     => 'linear',
			'cubic-bezier-function' => '',
			'transform-perspective' => '',
			'transform-origin-x'    => '',
			'transform-origin-y'    => '',
			'transform-origin-z'    => '',
			'translateX'            => '',
			'translateY'            => '',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '',
			'rotateZ'               => '',
			'scaleX'                => '',
			'scaleY'                => '',
			'scaleZ'                => '',
			'skewX'                 => '',
			'skewY'                 => '',
		],
		'from-left'   => [
			'opacity'               => 0,
			'transition-duration'   => '500ms',
			'transition-easing'     => 'ease-in-out',
			'cubic-bezier-function' => '',
			'transform-perspective' => '',
			'transform-origin-x'    => '',
			'transform-origin-y'    => '',
			'transform-origin-z'    => '',
			'translateX'            => '-100%',
			'translateY'            => '',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '',
			'rotateZ'               => '',
			'scaleX'                => '',
			'scaleY'                => '',
			'scaleZ'                => '',
			'skewX'                 => '',
			'skewY'                 => '',
		],
		'from-right'  => [
			'opacity'               => 0,
			'transition-duration'   => '500ms',
			'transition-easing'     => 'ease-in-out',
			'cubic-bezier-function' => '',
			'transform-perspective' => '',
			'transform-origin-x'    => '',
			'transform-origin-y'    => '',
			'transform-origin-z'    => '',
			'translateX'            => '100%',
			'translateY'            => '',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '',
			'rotateZ'               => '',
			'scaleX'                => '',
			'scaleY'                => '',
			'scaleZ'                => '',
			'skewX'                 => '',
			'skewY'                 => '',
		],
		'from-top'    => [
			'opacity'               => 0,
			'transition-duration'   => '500ms',
			'transition-easing'     => 'ease-in-out',
			'cubic-bezier-function' => '',
			'transform-perspective' => '',
			'transform-origin-x'    => '',
			'transform-origin-y'    => '',
			'transform-origin-z'    => '',
			'translateX'            => '',
			'translateY'            => '-100%',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '',
			'rotateZ'               => '',
			'scaleX'                => '',
			'scaleY'                => '',
			'scaleZ'                => '',
			'skewX'                 => '',
			'skewY'                 => '',
		],
		'from-bottom' => [
			'opacity'               => 0,
			'transition-duration'   => '500ms',
			'transition-easing'     => 'ease-in-out',
			'cubic-bezier-function' => '',
			'transform-perspective' => '',
			'transform-origin-x'    => '',
			'transform-origin-y'    => '',
			'transform-origin-z'    => '',
			'translateX'            => '',
			'translateY'            => '100%',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '',
			'rotateZ'               => '',
			'scaleX'                => '',
			'scaleY'                => '',
			'scaleZ'                => '',
			'skewX'                 => '',
			'skewY'                 => '',
		],
		'zoom-out'    => [
			'opacity'               => 0,
			'transition-duration'   => '400ms',
			'transition-easing'     => 'ease-in-out',
			'cubic-bezier-function' => '',
			'transform-perspective' => '',
			'transform-origin-x'    => '',
			'transform-origin-y'    => '',
			'transform-origin-z'    => '',
			'translateX'            => '',
			'translateY'            => '',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '',
			'rotateZ'               => '',
			'scaleX'                => 0.5,
			'scaleY'                => 0.5,
			'scaleZ'                => '',
			'skewX'                 => '',
			'skewY'                 => '',
		],
		'zoom-in'     => [
			'opacity'               => 0,
			'transition-duration'   => '400ms',
			'transition-easing'     => 'ease-in-out',
			'cubic-bezier-function' => '',
			'transform-perspective' => '',
			'transform-origin-x'    => '',
			'transform-origin-y'    => '',
			'transform-origin-z'    => '',
			'translateX'            => '',
			'translateY'            => '',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '',
			'rotateZ'               => '',
			'scaleX'                => 1.5,
			'scaleY'                => 1.5,
			'scaleZ'                => '',
			'skewX'                 => '',
			'skewY'                 => '',
		],
		'fold-up'     => [
			'opacity'               => 0,
			'transition-duration'   => '400ms',
			'transition-easing'     => 'ease-in-out',
			'cubic-bezier-function' => '',
			'transform-perspective' => '1000px',
			'transform-origin-x'    => '50%',
			'transform-origin-y'    => '100%',
			'transform-origin-z'    => '',
			'translateX'            => '',
			'translateY'            => '',
			'translateZ'            => '',
			'rotateX'               => '60deg',
			'rotateY'               => '',
			'rotateZ'               => '',
			'scaleX'                => '',
			'scaleY'                => '',
			'scaleZ'                => '',
			'skewX'                 => '',
			'skewY'                 => '',
		],
		'flip'        => [
			'opacity'               => 0,
			'transition-duration'   => '400ms',
			'transition-easing'     => 'ease-in-out',
			'cubic-bezier-function' => '',
			'transform-perspective' => '1000px',
			'transform-origin-x'    => '50%',
			'transform-origin-y'    => '50%',
			'transform-origin-z'    => '',
			'translateX'            => '',
			'translateY'            => '',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '60deg',
			'rotateZ'               => '',
			'scaleX'                => '',
			'scaleY'                => '',
			'scaleZ'                => '',
			'skewX'                 => '',
			'skewY'                 => '',
		],
		'rotate'      => [
			'opacity'               => 0,
			'transition-duration'   => '400ms',
			'transition-easing'     => 'ease-in-out',
			'cubic-bezier-function' => '',
			'transform-perspective' => '',
			'transform-origin-x'    => '',
			'transform-origin-y'    => '',
			'transform-origin-z'    => '',
			'translateX'            => '',
			'translateY'            => '',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '',
			'rotateZ'               => '-60deg',
			'scaleX'                => '',
			'scaleY'                => '',
			'scaleZ'                => '',
			'skewX'                 => '10deg',
			'skewY'                 => '10deg',
		],
		'bounce'      => [
			'opacity'               => 0,
			'transition-duration'   => '700ms',
			'transition-easing'     => 'custom',
			'cubic-bezier-function' => 'cubic-bezier(0.35, 2.5, 0.1,.25)',
			'transform-perspective' => '',
			'transform-origin-x'    => '',
			'transform-origin-y'    => '',
			'transform-origin-z'    => '',
			'translateX'            => '-15px',
			'translateY'            => '-80px',
			'translateZ'            => '',
			'rotateX'               => '',
			'rotateY'               => '',
			'rotateZ'               => '',
			'scaleX'                => 0.8,
			'scaleY'                => 0.8,
			'scaleZ'                => '',
			'skewX'                 => '-5deg',
			'skewY'                 => '-5deg',
		],
	],
	'L10n'       => [
		'labels'      => [
			'icon_picker'   => esc_html__( 'Select Icon', 'wp-grid-builder' ),
			'color_picker'  => esc_html__( 'Select Color', 'wp-grid-builder' ),
			'file_uploader' => esc_html__( 'Upload', 'wp-grid-builder' ),
		],
		'blocks'      => [
			'the_title'              => esc_html__( 'The post title', 'wp-grid-builder' ),
			'the_name'               => esc_html__( 'The post name', 'wp-grid-builder' ),
			'the_post_type'          => esc_html__( 'Post Type', 'wp-grid-builder' ),
			'the_post_format'        => esc_html__( 'Post Format', 'wp-grid-builder' ),
			'the_post_status'        => esc_html__( 'Post Status', 'wp-grid-builder' ),
			'the_author'             => esc_html__( 'Author Name', 'wp-grid-builder' ),
			'the_date'               => get_option( 'date_format' ),
			/* translators: %s: Human time diff */
			'the_date_ago'           => sprintf( __( '%s ago', 'wp-grid-builder' ), human_time_diff( 3601, 1 ) ),
			'the_terms'              => esc_html__( 'Term', 'wp-grid-builder' ),
			'comments_number'        => esc_html__( '1 Comment', 'wp-grid-builder' ),
			'the_text_rating'        => esc_html__( '4.5 out of 5', 'wp-grid-builder' ),
			'the_on_sale_badge'      => esc_html__( 'Sale!', 'wp-grid-builder' ),
			'the_in_stock_badge'     => esc_html__( 'In stock', 'wp-grid-builder' ),
			'the_out_of_stock_badge' => esc_html__( 'Out of stock', 'wp-grid-builder' ),
			'the_cart_button'        => esc_html__( 'Add to Cart', 'wp-grid-builder' ),
			'metadata'               => esc_html__( 'Custom Field', 'wp-grid-builder' ),
			'the_user_display_name'  => esc_html__( 'Display Name', 'wp-grid-builder' ),
			'the_user_first_name'    => esc_html__( 'First Name', 'wp-grid-builder' ),
			'the_user_last_name'     => esc_html__( 'Last Name', 'wp-grid-builder' ),
			'the_user_nickname'      => esc_html__( 'Nickname', 'wp-grid-builder' ),
			'the_user_login'         => esc_html__( 'Username', 'wp-grid-builder' ),
			'the_user_email'         => esc_html__( 'Email', 'wp-grid-builder' ),
			'the_user_roles'         => esc_html__( 'Author', 'wp-grid-builder' ),
			'the_user_post_count'    => esc_html__( '10 posts', 'wp-grid-builder' ),
			'the_term_name'          => esc_html__( 'Term Name', 'wp-grid-builder' ),
			'the_term_slug'          => esc_html__( 'Term Slug', 'wp-grid-builder' ),
			'the_term_taxonomy'      => esc_html__( 'Term Taxonomy', 'wp-grid-builder' ),
			'the_term_parent'        => esc_html__( 'Term Parent', 'wp-grid-builder' ),
			'the_term_count'         => esc_html__( '10 posts', 'wp-grid-builder' ),
			'not_founded'            => esc_html__( 'Block not founded', 'wp-grid-builder' ),
			'custom_block'           => [
				'not_founded'  => esc_html__( 'No custom blocks were founded', 'wp-grid-builder' ),
				'not_selected' => esc_html__( 'No custom block selected', 'wp-grid-builder' ),
			],
		],
		'layers'      => [
			'wpgb-card-inner'           => esc_html__( 'Card Holder', 'wp-grid-builder' ),
			'wpgb-card-header'          => esc_html__( 'Header Holder', 'wp-grid-builder' ),
			'wpgb-card-media'           => esc_html__( 'Media Holder', 'wp-grid-builder' ),
			'wpgb-card-media-thumbnail' => esc_html__( 'Media Thumbnail', 'wp-grid-builder' ),
			'wpgb-card-media-overlay'   => esc_html__( 'Media Overlay', 'wp-grid-builder' ),
			'wpgb-card-media-content'   => esc_html__( 'Media Content', 'wp-grid-builder' ),
			'wpgb-card-body'            => esc_html__( 'Body Holder', 'wp-grid-builder' ),
			'wpgb-card-footer'          => esc_html__( 'Footer Holder', 'wp-grid-builder' ),
		],
		'contextMenu' => [
			'edit'      => esc_html__( 'Edit Settings', 'wp-grid-builder' ),
			'duplicate' => esc_html__( 'Duplicate Block', 'wp-grid-builder' ),
			'delete'    => esc_html__( 'Delete Block', 'wp-grid-builder' ),
		],
		'messages'    => [
			'no_results' => esc_html__( 'Sorry, no blocks were found!', 'wp-grid-builder' ),
		],
	],
	'icons'      => [
		'chevron-up'   => Includes\Helpers::get_icon( 'chevron-up', true ),
		'chevron-down' => Includes\Helpers::get_icon( 'chevron-down', true ),
		'settings'     => Includes\Helpers::get_icon( 'settings', true ),
		'delete'       => Includes\Helpers::get_icon( 'delete', true ),
		'clipboard'    => Includes\Helpers::get_icon( 'clipboard', true ),
		'cross'        => Includes\Helpers::get_icon( 'cross', true ),
		'block'        => Includes\Helpers::get_icon( 'block', true ),
		'custom_block' => Includes\Helpers::get_icon( 'custom-block', true ),
		'layer'        => Includes\Helpers::get_icon( 'layer', true ),
		'info'         => Includes\Helpers::get_icon( 'info', true ),
		'plus'         => Includes\Helpers::get_icon( 'plus', true ),
		'search'       => Includes\Helpers::get_icon( 'search', true ),
		'rating-stars' => Includes\Helpers::get_icon( 'rating-stars', true ),
	],
];

$card_settings = [
	'id'     => 'card',
	'tabs'   => [
		[
			'id'    => 'general',
			'label' => esc_html__( 'General', 'wp-grid-builder' ),
			'icon'  => Includes\Helpers::get_icon( 'settings', true ),
		],
		[
			'id'    => 'editor',
			'label' => esc_html__( 'Editor', 'wp-grid-builder' ),
			'icon'  => Includes\Helpers::get_icon( 'pencil', true ),
		],
		[
			'id'    => 'blocks',
			'label' => esc_html__( 'Blocks', 'wp-grid-builder' ),
			'icon'  => Includes\Helpers::get_icon( 'block', true ),
		],
		[
			'id'    => 'custom_css',
			'label' => esc_html__( 'CSS', 'wp-grid-builder' ),
			'icon'  => Includes\Helpers::get_icon( 'code', true ),
		],
	],
	'fields' => array_merge(
		$layout,
		$blocks,
		$global_css,
		$dynamic_fields
	),
];

$block_settings = [
	'id'     => 'block',
	'title'  => esc_html__( 'Edit Block', 'wp-grid-builder' ),
	'footer' => [
		'buttons' => [
			[
				'icon'  => 'delete',
				'class' => WPGB_SLUG . '-delete-block',
				'label' => esc_html__( 'Delete Block', 'wp-grid-builder' ),
			],
			[
				'icon'  => 'clipboard',
				'class' => WPGB_SLUG . '-duplicate-block',
				'label' => esc_html__( 'Duplicate Block', 'wp-grid-builder' ),
			],
			[
				'icon'  => 'chevron-up',
				'class' => WPGB_SLUG . '-move-up-block',
				'label' => esc_html__( 'Move Up', 'wp-grid-builder' ),
			],
			[
				'icon'  => 'chevron-down',
				'class' => WPGB_SLUG . '-move-down-block',
				'label' => esc_html__( 'Move Down', 'wp-grid-builder' ),
			],
		],
	],
	'tabs'   => [
		[
			'id'    => 'content',
			'label' => __( 'Content', 'wp-grid-builder' ),
		],
		[
			'id'    => 'action',
			'label' => esc_html__( 'Action', 'wp-grid-builder' ),
		],
		[
			'id'    => 'style',
			'label' => esc_html__( 'Appearance', 'wp-grid-builder' ),
			'tabs'  => [
				[
					'id'    => 'idle',
					'label' => esc_html__( 'Idle State', 'wp-grid-builder' ),
					'icon'  => Includes\Helpers::get_icon( 'idle-state', true ),
				],
				[
					'id'    => 'hover',
					'label' => esc_html__( 'Hover State', 'wp-grid-builder' ),
					'icon'  => Includes\Helpers::get_icon( 'hover-state', true ),
				],
			],
		],
		[
			'id'    => 'animation',
			'label' => esc_html__( 'Animation', 'wp-grid-builder' ),
		],
	],
	'fields' => array_merge(
		$class_name,
		$html_tag,
		$source,
		$post_field,
		$product_field,
		$user_field,
		$term_field,
		$post_excerpt,
		$post_date,
		$post_terms,
		$post_author,
		$count_format,
		$user_website,
		$sale_badge,
		$metadata,
		$media_button,
		$social_network,
		$svg_icon,
		$raw_content,
		$custom_blocks,
		$block_action,
		$block_hover_selector,
		$block_position,
		$sizing,
		$spacing,
		$border,
		$box_shadow,
		$background,
		$font,
		$text_shadow,
		$filters,
		$visibility,
		$custom_css,
		$animation
	),
];

$layer_settings = [
	'id'     => 'layer',
	'title'  => esc_html__( 'Edit Layer', 'wp-grid-builder' ),
	'tabs'   => [
		[
			'id'    => 'action',
			'label' => esc_html__( 'Action', 'wp-grid-builder' ),
		],
		[
			'id'    => 'style',
			'label' => esc_html__( 'Appearance', 'wp-grid-builder' ),
			'tabs'  => [
				[
					'id'    => 'idle',
					'label' => esc_html__( 'Idle State', 'wp-grid-builder' ),
					'icon'  => Includes\Helpers::get_icon( 'idle-state', true ),
				],
				[
					'id'    => 'hover',
					'label' => esc_html__( 'Hover State', 'wp-grid-builder' ),
					'icon'  => Includes\Helpers::get_icon( 'hover-state', true ),
				],
			],
		],
		[
			'id'    => 'animation',
			'label' => esc_html__( 'Animation', 'wp-grid-builder' ),
		],
	],
	'fields' => array_merge(
		$layer_action,
		$layer_hover_selector,
		$layer_position,
		$alignment,
		$spacing,
		$border,
		$box_shadow,
		$background,
		$filters,
		$visibility,
		$custom_css,
		$animation
	),
];

// To sanitize fields and to tigger registry hooks for 3rd party scripts.
wp_grid_builder()->settings->register( $block_settings );
wp_grid_builder()->settings->register( $layer_settings );
wp_grid_builder()->settings->register( $card_settings );

$builder_settings['panels'] = [
	'blocks'  => wp_grid_builder()->settings->get( 'block' ),
	'layers'  => wp_grid_builder()->settings->get( 'layer' ),
	'general' => wp_grid_builder()->settings->get( 'card' ),
];

return $builder_settings;
