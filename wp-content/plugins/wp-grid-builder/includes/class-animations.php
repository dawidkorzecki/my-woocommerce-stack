<?php
/**
 * Animations
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hook into card animations.
 *
 * @class WP_Grid_Builder\Includes\Animations
 * @since 1.0.0
 */
class Animations {

	/**
	 * Holds registered animations
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $animations;

	/**
	 * Default animations
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Holds default animations
	 */
	public static function default_animations() {

		return [
			'wpgb_animation_1'  => [
				'name'    => __( 'Fade in', 'wp-grid-builder' ),
				'visible' => [
					'opacity' => 1,
				],
				'hidden'  => [
					'opacity' => 0,
				],
			],
			'wpgb_animation_2'  => [
				'name'    => __( 'Zoom In', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'scale(0.001)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_3'  => [
				'name'    => __( 'Zoom Out', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'scale(1.5)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_4'  => [
				'name'    => __( 'From Bottom', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'translateY(0)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'translateY(100px)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_5'  => [
				'name'    => __( 'From Top', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'translateY(0)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'translateY(-100px)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_6'  => [
				'name'    => __( 'From Left', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'translateX(0)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'translateX(-100px)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_7'  => [
				'name'    => __( 'From Right', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'translateX(0)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'translateX(100px)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_8'  => [
				'name'    => __( 'From Top Left', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'translate(0,0)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'translate(-100px,-100px)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_9'  => [
				'name'    => __( 'From Top Right', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'translate(0,0)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'translate(100px,-100px)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_10' => [
				'name'    => __( 'From Bottom/left', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'translate(0,0)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'translate(-100px,100px)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_11' => [
				'name'    => __( 'From Bottom/Right', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'translate(0,0)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'translate(100px,100px)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_12' => [
				'name'    => __( 'Rotate in X', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) rotateX(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'perspective(2000px) rotateX(180deg) scale(0.5)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_13' => [
				'name'    => __( 'Rotate in Y', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) rotateY(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'perspective(2000px) rotateY(180deg) scale(0.5)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_14' => [
				'name'    => __( 'Flip in X', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) rotateX(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'perspective(2000px) rotateX(60deg) scale(0.8)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_15' => [
				'name'    => __( 'Flip in Y', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) rotateY(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform' => 'perspective(2000px) rotateY(60deg) scale(0.8)',
					'opacity'   => 0,
				],
			],
			'wpgb_animation_16' => [
				'name'    => __( 'Flip X from Bottom', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) translateY(0) rotateX(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform-origin' => '50% 100%',
					'transform'        => 'perspective(2000px) translateY(200px) rotateX(60deg) scale(0.8)',
					'opacity'          => 0,
				],
			],
			'wpgb_animation_17' => [
				'name'    => __( 'Flip Y from Bottom', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) translateY(0) rotateY(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform-origin' => '50% 100%',
					'transform'        => 'perspective(2000px) translateY(200px) rotateY(60deg) scale(0.8)',
					'opacity'          => 0,
				],
			],
			'wpgb_animation_18' => [
				'name'    => __( 'Flip X from Top', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) translateY(0) rotateX(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform-origin' => '50% 0',
					'transform'        => 'perspective(2000px) translateY(-200px) rotateX(60deg) scale(0.8)',
					'opacity'          => 0,
				],
			],
			'wpgb_animation_19' => [
				'name'    => __( 'Flip Y from Top', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) translateY(0) rotateY(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform-origin' => '50% 0',
					'transform'        => 'perspective(2000px) translateY(-200px) rotateY(60deg) scale(0.8)',
					'opacity'          => 0,
				],
			],
			'wpgb_animation_20' => [
				'name'    => __( 'Flip X from Left', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) translateX(0) rotateX(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform-origin' => '0 50%',
					'transform'        => 'perspective(2000px) translateX(-200px) rotateX(60deg) scale(0.8)',
					'opacity'          => 0,
				],
			],
			'wpgb_animation_21' => [
				'name'    => __( 'Flip Y from Left', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) translateX(0) rotateY(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform-origin' => '0 50%',
					'transform'        => 'perspective(2000px) translateX(-200px) rotateY(60deg) scale(0.8)',
					'opacity'          => 0,
				],
			],
			'wpgb_animation_22' => [
				'name'    => __( 'Flip X from Right', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) translateX(0) rotateX(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform-origin' => '100% 50%',
					'transform'        => 'perspective(2000px) translateX(200px) rotateX(60deg) scale(0.8)',
					'opacity'          => 0,
				],
			],
			'wpgb_animation_23' => [
				'name'    => __( 'Flip Y from Right', 'wp-grid-builder' ),
				'visible' => [
					'transform' => 'perspective(2000px) translateX(0) rotateY(0) scale(1)',
					'opacity'   => 1,
				],
				'hidden'  => [
					'transform-origin' => '100% 50%',
					'transform'        => 'perspective(2000px) translateX(200px) rotateY(60deg) scale(0.8)',
					'opacity'          => 0,
				],
			],
		];

	}

	/**
	 * Register animations
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function register() {

		// if animations already registered.
		if ( self::$animations ) {
			return;
		}

		$defaults = self::default_animations();
		self::$animations = (array) apply_filters( 'wp_grid_builder/card/animations', $defaults );

	}

	/**
	 * Get animation
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $key Animation key slug.
	 * @param string $prop Property to retrun.
	 * @return mixed
	 */
	public static function get( $key = '', $prop = '' ) {

		self::register();

		// Get all animations.
		if ( empty( $key ) && empty( $prop ) ) {
			return self::$animations;
		}

		// If a key set but not exists.
		if ( ! isset( self::$animations[ $key ] ) ) {
			return;
		}

		// If no prop specified return all properties.
		if ( empty( $prop ) ) {
			return self::$animations[ $key ];
		}

		// If prop set but not exists.
		if ( ! isset( self::$animations[ $key ][ $prop ] ) ) {
			return;
		}

		// Return animation property.
		return self::$animations[ $key ][ $prop ];

	}

	/**
	 * Get animations list
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $prop Property to retrun.
	 * @return array
	 */
	public static function get_list( $prop ) {

		$animations = self::get();

		return array_map(
			function( $item ) use ( $prop ) {
				return isset( $item[ $prop ] ) ? $item[ $prop ] : null;
			},
			$animations
		);

	}
}
