<?php
/**
 * Loaders
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
 * Hook into grid loaders.
 *
 * @class WP_Grid_Builder\Includes\Loaders
 * @since 1.0.0
 */
class Loaders {

	/**
	 * Holds registered loaders
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $loaders;

	/**
	 * Default loaders
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Holds default loaders
	 */
	public static function default_loaders() {

		return [
			'wpgb-loader-1'  => [
				'name' => __( 'Square Grid Pulse', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 9 ),
				'css'  => '@-webkit-keyframes wpgb-loader-1{0%{-webkit-transform:scale(1)}50%{-webkit-transform:scale(.5);opacity:.7}100%{-webkit-transform:scale(1);opacity:1}}@keyframes wpgb-loader-1{0%{transform:scale(1)}50%{transform:scale(.5);opacity:.7}100%{transform:scale(1);opacity:1}}.wpgb-loader-1{width:48px;height:48px;margin:1px}.wpgb-loader-1>div{display:inline-block;float:left;width:12px;height:12px;margin:2px;-webkit-animation-fill-mode:both;animation-fill-mode:both;-webkit-animation-name:wpgb-loader-1;animation-name:wpgb-loader-1;-webkit-animation-iteration-count:infinite;animation-iteration-count:infinite;-webkit-animation-delay:0s;animation-delay:0s}.wpgb-loader-1>div:nth-child(1){-webkit-animation-delay:.73s;animation-delay:.73s;-webkit-animation-duration:1.3s;animation-duration:1.3s}.wpgb-loader-1>div:nth-child(2){-webkit-animation-delay:.32s;animation-delay:.32s;-webkit-animation-duration:1.3s;animation-duration:1.3s}.wpgb-loader-1>div:nth-child(3){-webkit-animation-delay:.71s;animation-delay:.71s;-webkit-animation-duration:.88s;animation-duration:.88s}.wpgb-loader-1>div:nth-child(4){-webkit-animation-delay:.62s;animation-delay:.62s;-webkit-animation-duration:1.06s;animation-duration:1.06s}.wpgb-loader-1>div:nth-child(5){-webkit-animation-delay:.31s;animation-delay:.31s;-webkit-animation-duration:.62s;animation-duration:.62s}.wpgb-loader-1>div:nth-child(6){-webkit-animation-delay:-.14s;animation-delay:-.14s;-webkit-animation-duration:1.48s;animation-duration:1.48s}.wpgb-loader-1>div:nth-child(7){-webkit-animation-delay:-.1s;animation-delay:-.1s;-webkit-animation-duration:1.47s;animation-duration:1.47s}.wpgb-loader-1>div:nth-child(8){-webkit-animation-delay:.4s;animation-delay:.4s;-webkit-animation-duration:1.49s;animation-duration:1.49s}.wpgb-loader-1>div:nth-child(9){-webkit-animation-delay:.73s;animation-delay:.73s;-webkit-animation-duration:.7s;animation-duration:.7s}',
			],
			'wpgb-loader-2'  => [
				'name' => __( 'Ball Grid Pulse', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 9 ),
				'css'  => '@-webkit-keyframes wpgb-loader-2{0%{-webkit-transform:scale(1)}50%{-webkit-transform:scale(.5);opacity:.7}100%{-webkit-transform:scale(1);opacity:1}}@keyframes wpgb-loader-2{0%{transform:scale(1)}50%{transform:scale(.5);opacity:.7}100%{transform:scale(1);opacity:1}}.wpgb-loader-2{width:48px;height:48px;margin:1px}.wpgb-loader-2>div{display:inline-block;float:left;width:12px;height:12px;margin:2px;border-radius:100%;-webkit-animation-fill-mode:both;animation-fill-mode:both;-webkit-animation-name:wpgb-loader-2;animation-name:wpgb-loader-2;-webkit-animation-iteration-count:infinite;animation-iteration-count:infinite;-webkit-animation-delay:0s;animation-delay:0s}.wpgb-loader-2>div:nth-child(1){-webkit-animation-delay:.73s;animation-delay:.73s;-webkit-animation-duration:1.3s;animation-duration:1.3s}.wpgb-loader-2>div:nth-child(2){-webkit-animation-delay:.32s;animation-delay:.32s;-webkit-animation-duration:1.3s;animation-duration:1.3s}.wpgb-loader-2>div:nth-child(3){-webkit-animation-delay:.71s;animation-delay:.71s;-webkit-animation-duration:.88s;animation-duration:.88s}.wpgb-loader-2>div:nth-child(4){-webkit-animation-delay:.62s;animation-delay:.62s;-webkit-animation-duration:1.06s;animation-duration:1.06s}.wpgb-loader-2>div:nth-child(5){-webkit-animation-delay:.31s;animation-delay:.31s;-webkit-animation-duration:.62s;animation-duration:.62s}.wpgb-loader-2>div:nth-child(6){-webkit-animation-delay:-.14s;animation-delay:-.14s;-webkit-animation-duration:1.48s;animation-duration:1.48s}.wpgb-loader-2>div:nth-child(7){-webkit-animation-delay:-.1s;animation-delay:-.1s;-webkit-animation-duration:1.47s;animation-duration:1.47s}.wpgb-loader-2>div:nth-child(8){-webkit-animation-delay:.4s;animation-delay:.4s;-webkit-animation-duration:1.49s;animation-duration:1.49s}.wpgb-loader-2>div:nth-child(9){-webkit-animation-delay:.73s;animation-delay:.73s;-webkit-animation-duration:.7s;animation-duration:.7s}',
			],
			'wpgb-loader-3'  => [
				'name' => __( 'Ball Clip Rotate', 'wp-grid-builder' ),
				'html' => '<div></div>',
				'css'  => '@-webkit-keyframes wpgb-loader-3{0%{-webkit-transform:rotate(0) scale(1)}50%{-webkit-transform:rotate(180deg) scale(.6)}100%{-webkit-transform:rotate(360deg) scale(1)}}@keyframes wpgb-loader-3{0%{transform:rotate(0) scale(1)}50%{transform:rotate(180deg) scale(.6)}100%{transform:rotate(360deg) scale(1)}}.wpgb-loader-3>div{display:inline-block;height:50px;width:50px;background-color:transparent!important;border-radius:100%;border:3px solid;border-bottom-color:transparent!important;-webkit-animation:wpgb-loader-3 .75s 0s linear infinite;animation:wpgb-loader-3 .75s 0s linear infinite}',
			],
			'wpgb-loader-4'  => [
				'name' => __( 'Square Spin', 'wp-grid-builder' ),
				'html' => '<div></div>',
				'css'  => '@-webkit-keyframes wpgb-loader-4{25%{-webkit-transform:perspective(100px) rotateX(180deg) rotateY(0)}50%{-webkit-transform:perspective(100px) rotateX(180deg) rotateY(180deg)}75%{-webkit-transform:perspective(100px) rotateX(0) rotateY(180deg)}100%{-webkit-transform:perspective(100px) rotateX(0) rotateY(0)}}@keyframes wpgb-loader-4{25%{transform:perspective(100px) rotateX(180deg) rotateY(0)}50%{transform:perspective(100px) rotateX(180deg) rotateY(180deg)}75%{transform:perspective(100px) rotateX(0) rotateY(180deg)}100%{transform:perspective(100px) rotateX(0) rotateY(0)}}.wpgb-loader-4>div{width:50px;height:50px;-webkit-animation:wpgb-loader-4 3s 0s cubic-bezier(.09,.57,.49,.9) infinite;animation:wpgb-loader-4 3s 0s cubic-bezier(.09,.57,.49,.9) infinite}',
			],
			'wpgb-loader-5'  => [
				'name' => __( 'Ball Pulse Sync', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 3 ),
				'css'  => '@-webkit-keyframes wpgb-loader-5{33%{-webkit-transform:translateY(10px)}66%{-webkit-transform:translateY(-10px)}100%{-webkit-transform:translateY(0)}}@keyframes wpgb-loader-5{33%{transform:translateY(10px)}66%{transform:translateY(-10px)}100%{transform:translateY(0)}}.wpgb-loader-5{width:48px;height:48px;margin:1px}.wpgb-loader-5>div{display:inline-block;float:left;width:12px;height:12px;margin:19px 2px;border-radius:100%;-webkit-animation-fill-mode:both;animation-fill-mode:both}.wpgb-loader-5>div:nth-child(0){-webkit-animation:wpgb-loader-5 .6s -.21s infinite ease-in-out;animation:wpgb-loader-5 .6s -.21s infinite ease-in-out}.wpgb-loader-5>div:nth-child(1){-webkit-animation:wpgb-loader-5 .6s -.14s infinite ease-in-out;animation:wpgb-loader-5 .6s -.14s infinite ease-in-out}.wpgb-loader-5>div:nth-child(2){-webkit-animation:wpgb-loader-5 .6s -.07s infinite ease-in-out;animation:wpgb-loader-5 .6s -.07s infinite ease-in-out}.wpgb-loader-5>div:nth-child(3){-webkit-animation:wpgb-loader-5 .6s 0s infinite ease-in-out;animation:wpgb-loader-5 .6s 0s infinite ease-in-out}',
			],
			'wpgb-loader-6'  => [
				'name' => __( 'Ball Beat', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 3 ),
				'css'  => '@-webkit-keyframes wpgb-loader-6{50%{opacity:.2;-webkit-transform:scale(.75)}100%{opacity:1;-webkit-transform:scale(1)}}@keyframes wpgb-loader-6{50%{opacity:.2;transform:scale(.75)}100%{opacity:1;transform:scale(1)}}.wpgb-loader-6{width:48px;height:48px;margin:1px}.wpgb-loader-6>div{display:inline-block;float:left;margin:19px 2px;width:12px;height:12px;border-radius:100%;-webkit-animation:wpgb-loader-6 .7s 0s infinite linear;animation:wpgb-loader-6 .7s 0s infinite linear}.wpgb-loader-6>div:nth-child(2n-1){-webkit-animation-delay:-.35s!important;animation-delay:-.35s!important}',
			],
			'wpgb-loader-7'  => [
				'name' => __( 'Line Scale', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 5 ),
				'css'  => '@-webkit-keyframes wpgb-loader-7{0%,100%{-webkit-transform:scaley(1)}50%{-webkit-transform:scaley(.4)}}@keyframes wpgb-loader-7{0%,100%{transform:scaley(1)}50%{transform:scaley(.4)}}.wpgb-loader-7{width:50px;height:50px;padding:0 5px}.wpgb-loader-7>div:nth-child(1){-webkit-animation:wpgb-loader-7 1s -.4s infinite cubic-bezier(.2,.68,.18,1.08);animation:wpgb-loader-7 1s -.4s infinite cubic-bezier(.2,.68,.18,1.08)}.wpgb-loader-7>div:nth-child(2){-webkit-animation:wpgb-loader-7 1s -.3s infinite cubic-bezier(.2,.68,.18,1.08);animation:wpgb-loader-7 1s -.3s infinite cubic-bezier(.2,.68,.18,1.08)}.wpgb-loader-7>div:nth-child(3){-webkit-animation:wpgb-loader-7 1s -.2s infinite cubic-bezier(.2,.68,.18,1.08);animation:wpgb-loader-7 1s -.2s infinite cubic-bezier(.2,.68,.18,1.08)}.wpgb-loader-7>div:nth-child(4){-webkit-animation:wpgb-loader-7 1s -.1s infinite cubic-bezier(.2,.68,.18,1.08);animation:wpgb-loader-7 1s -.1s infinite cubic-bezier(.2,.68,.18,1.08)}.wpgb-loader-7>div:nth-child(5){-webkit-animation:wpgb-loader-7 1s 0s infinite cubic-bezier(.2,.68,.18,1.08);animation:wpgb-loader-7 1s 0s infinite cubic-bezier(.2,.68,.18,1.08)}.wpgb-loader-7>div{display:inline-block;float:left;width:4px;height:34px;margin:8px 2px;border-radius:2px;-webkit-animation-fill-mode:both;animation-fill-mode:both}',
			],
			'wpgb-loader-8'  => [
				'name' => __( 'Cube Transition', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 2 ),
				'css'  => '@-webkit-keyframes wpgb-loader-8{25%{-webkit-transform:translateX(44px) scale(.5) rotate(-90deg)}50%{-webkit-transform:translate(54px,54px) rotate(-180deg)}75%{-webkit-transform:translateY(54px) scale(.5) rotate(-270deg)}100%{-webkit-transform:rotate(-360deg)}}@keyframes wpgb-loader-8{25%{transform:translateX(38px) scale(.5) rotate(-90deg)}50%{transform:translate(38px,38px) rotate(-180deg)}75%{transform:translateY(38px) scale(.5) rotate(-270deg)}100%{transform:rotate(-360deg)}}.wpgb-loader-8>div{width:12px;height:12px;position:absolute;margin-left:0;margin-top:0;-webkit-animation:wpgb-loader-8 1.6s -10ms infinite ease-in-out;animation:wpgb-loader-8 1.6s -10ms infinite ease-in-out}.wpgb-loader-8>div:last-child{-webkit-animation-delay:-.8s;animation-delay:-.8s}',
			],
			'wpgb-loader-9'  => [
				'name' => __( 'Ball Zig Zag', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 2 ),
				'css'  => '@-webkit-keyframes wpgb-loader-9-1{33%{-webkit-transform:translate(-10px,-19px)}66%{-webkit-transform:translate(10px,-19px)}100%{-webkit-transform:translate(0,0)}}@keyframes wpgb-loader-9-1{33%{transform:translate(-10px,-19px)}66%{transform:translate(10px,-19px)}100%{transform:translate(0,0)}}@-webkit-keyframes wpgb-loader-9-2{33%{-webkit-transform:translate(10px,19px)}66%{-webkit-transform:translate(-10px,19px)}100%{-webkit-transform:translate(0,0)}}@keyframes wpgb-loader-9-2{33%{transform:translate(10px,19px)}66%{transform:translate(-10px,19px)}100%{transform:translate(0,0)}}.wpgb-loader-9{position:relative;width:50px;height:50px}.wpgb-loader-9>div{position:absolute;top:50%;left:50%;margin:-6px;width:12px;height:12px;border-radius:100%;-webkit-animation-fill-mode:both;animation-fill-mode:both}.wpgb-loader-9>div:first-child{-webkit-animation:wpgb-loader-9-1 .7s 0s infinite linear;animation:wpgb-loader-9-1 .7s 0s infinite linear}.wpgb-loader-9>div:last-child{-webkit-animation:wpgb-loader-9-2 .7s 0s infinite linear;animation:wpgb-loader-9-2 .7s 0s infinite linear}',
			],
			'wpgb-loader-10' => [
				'name' => __( 'Ball Scale', 'wp-grid-builder' ),
				'html' => '<div></div>',
				'css'  => '@-webkit-keyframes wpgb-loader-10{0%{-webkit-transform:scale(0)}100%{-webkit-transform:scale(1);opacity:0}}@keyframes wpgb-loader-10{0%{transform:scale(0)}100%{transform:scale(1);opacity:0}}.wpgb-loader-10>div{display:inline-block;height:50px;width:50px;border-radius:100%;-webkit-animation:wpgb-loader-10 1s 0s ease-in-out infinite;animation:wpgb-loader-10 1s 0s ease-in-out infinite}',
			],
			'wpgb-loader-11' => [
				'name' => __( 'Ball Spin Fade', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 8 ),
				'css'  => '@-webkit-keyframes wpgb-loader-11{50%{opacity:.3;-webkit-transform:scale(.4)}100%{opacity:1;-webkit-transform:scale(1)}}@keyframes wpgb-loader-11{50%{opacity:.3;transform:scale(.4)}100%{opacity:1;transform:scale(1)}}.wpgb-loader-11{position:relative;width:50px;height:50px}.wpgb-loader-11>div{position:absolute;width:10px;height:10px;border-radius:100%;-webkit-animation-fill-mode:both;animation-fill-mode:both}.wpgb-loader-11>div:nth-child(1){top:0;left:20px;-webkit-animation:wpgb-loader-11 1s -.84s infinite linear;animation:wpgb-loader-11 1s -.84s infinite linear}.wpgb-loader-11>div:nth-child(2){top:5px;left:35px;-webkit-animation:wpgb-loader-11 1s -.72s infinite linear;animation:wpgb-loader-11 1s -.72s infinite linear}.wpgb-loader-11>div:nth-child(3){top:20px;left:40px;-webkit-animation:wpgb-loader-11 1s -.6s infinite linear;animation:wpgb-loader-11 1s -.6s infinite linear}.wpgb-loader-11>div:nth-child(4){top:35px;left:35px;-webkit-animation:wpgb-loader-11 1s -.48s infinite linear;animation:wpgb-loader-11 1s -.48s infinite linear}.wpgb-loader-11>div:nth-child(5){top:40px;left:20px;-webkit-animation:wpgb-loader-11 1s -.36s infinite linear;animation:wpgb-loader-11 1s -.36s infinite linear}.wpgb-loader-11>div:nth-child(6){top:35px;left:5px;-webkit-animation:wpgb-loader-11 1s -.24s infinite linear;animation:wpgb-loader-11 1s -.24s infinite linear}.wpgb-loader-11>div:nth-child(7){top:20px;left:0;-webkit-animation:wpgb-loader-11 1s -.12s infinite linear;animation:wpgb-loader-11 1s -.12s infinite linear}.wpgb-loader-11>div:nth-child(8){top:5px;left:5px;-webkit-animation:wpgb-loader-11 1s infinite linear;animation:wpgb-loader-11 1s infinite linear}',
			],
			'wpgb-loader-12' => [
				'name' => __( 'Line Spin Fade', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 8 ),
				'css'  => '@-webkit-keyframes wpgb-loader-12{50%{opacity:.3}100%{opacity:1}}@keyframes wpgb-loader-12{50%{opacity:.3}100%{opacity:1}}.wpgb-loader-12{position:relative}.wpgb-loader-12>div{position:absolute;width:4px;height:14px;border-radius:2px;-webkit-animation-fill-mode:both;animation-fill-mode:both}.wpgb-loader-12>div:nth-child(1){top:0;left:23px;-webkit-animation:wpgb-loader-12 1.2s -.84s infinite ease-in-out;animation:wpgb-loader-12 1.2s -.84s infinite ease-in-out}.wpgb-loader-12>div:nth-child(2){top:6px;left:35px;-webkit-transform:rotate(45deg);transform:rotate(45deg);-webkit-animation:wpgb-loader-12 1.2s -.72s infinite ease-in-out;animation:wpgb-loader-12 1.2s -.72s infinite ease-in-out}.wpgb-loader-12>div:nth-child(3){top:18px;left:41px;-webkit-transform:rotate(90deg);transform:rotate(90deg);-webkit-animation:wpgb-loader-12 1.2s -.6s infinite ease-in-out;animation:wpgb-loader-12 1.2s -.6s infinite ease-in-out}.wpgb-loader-12>div:nth-child(4){top:31px;left:35px;-webkit-transform:rotate(-45deg);transform:rotate(-45deg);-webkit-animation:wpgb-loader-12 1.2s -.48s infinite ease-in-out;animation:wpgb-loader-12 1.2s -.48s infinite ease-in-out}.wpgb-loader-12>div:nth-child(5){top:36px;left:23px;-webkit-animation:wpgb-loader-12 1.2s -.36s infinite ease-in-out;animation:wpgb-loader-12 1.2s -.36s infinite ease-in-out}.wpgb-loader-12>div:nth-child(6){top:31px;left:10px;-webkit-transform:rotate(45deg);transform:rotate(45deg);-webkit-animation:wpgb-loader-12 1.2s -.24s infinite ease-in-out;animation:wpgb-loader-12 1.2s -.24s infinite ease-in-out}.wpgb-loader-12>div:nth-child(7){top:18px;left:6px;-webkit-transform:rotate(90deg);transform:rotate(90deg);-webkit-animation:wpgb-loader-12 1.2s -.12s infinite ease-in-out;animation:wpgb-loader-12 1.2s -.12s infinite ease-in-out}.wpgb-loader-12>div:nth-child(8){top:6px;left:10px;-webkit-transform:rotate(-45deg);transform:rotate(-45deg);-webkit-animation:wpgb-loader-12 1.2s infinite ease-in-out;animation:wpgb-loader-12 1.2s infinite ease-in-out}',
			],
			'wpgb-loader-13' => [
				'name' => __( 'Pacman', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 5 ),
				'css'  => '@-webkit-keyframes wpgb-loader-13-1{0%,100%{-webkit-transform:rotate(270deg)}50%{-webkit-transform:rotate(360deg)}}@keyframes wpgb-loader-13-1{0%,100%{transform:rotate(270deg)}50%{transform:rotate(360deg)}}@-webkit-keyframes wpgb-loader-13-2{0%,100%{-webkit-transform:rotate(90deg)}50%{-webkit-transform:rotate(0)}}@keyframes wpgb-loader-13-2{0%,100%{transform:rotate(90deg)}50%{transform:rotate(0)}}@-webkit-keyframes wpgb-pacman-balls{0%{opacity:0;-webkit-transform:translate3d(0,0,0)}100%{opacity:1;-webkit-transform:translate3d(-100px,0,0)}}@keyframes wpgb-pacman-balls{0%{opacity:0;transform:translate3d(0,0,0)}100%{opacity:1;transform:translate3d(-100px,0,0)}}.wpgb-pacman{position:relative;width:50px;height:50px}.wpgb-loader-13>div:first-of-type,.wpgb-loader-13>div:nth-child(2){width:0;height:0;border-style:solid;border-width:25px;border-right-color:transparent!important;border-radius:100%;background-color:transparent!important;outline:transparent solid 1px}.wpgb-loader-13>div:nth-child(3),.wpgb-loader-13>div:nth-child(4),.wpgb-loader-13>div:nth-child(5){position:absolute;display:block;left:100px;top:50%;width:12px;height:12px;margin-top:-6px;margin-left:12.5px;border-radius:100%}.wpgb-loader-13>div:first-of-type{-webkit-animation:wpgb-loader-13-1 .5s 0s infinite;animation:wpgb-loader-13-1 .5s 0s infinite}.wpgb-loader-13>div:nth-child(2){-webkit-animation:wpgb-loader-13-2 .5s 0s infinite;animation:wpgb-loader-13-2 .5s 0s infinite;margin-top:-50px}.wpgb-loader-13>div:nth-child(3){-webkit-animation:wpgb-pacman-balls 1s -1s infinite linear;animation:wpgb-pacman-balls 1s -1s infinite linear}.wpgb-loader-13>div:nth-child(4){-webkit-animation:wpgb-pacman-balls 1s -.5s infinite linear;animation:wpgb-pacman-balls 1s -.5s infinite linear}.wpgb-loader-13>div:nth-child(5){-webkit-animation:wpgb-pacman-balls 1s -10ms infinite linear;animation:wpgb-pacman-balls 1s -10ms infinite linear}',
			],
			'wpgb-loader-14' => [
				'name' => __( 'Square Jelly Box', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 2 ),
				'css'  => '@-webkit-keyframes wpgb-loader-14-1{17%{border-bottom-right-radius:10%}25%{-webkit-transform:translateY(11px) rotate(22.5deg)}50%{border-bottom-right-radius:100%;-webkit-transform:translateY(22px) scale(1,.9) rotate(45deg)}75%{-webkit-transform:translateY(11px) rotate(67.5deg)}100%{-webkit-transform:translateY(0) rotate(90deg)}}@keyframes wpgb-loader-14-1{17%{border-bottom-right-radius:10%}25%{transform:translateY(11px) rotate(22.5deg)}50%{border-bottom-right-radius:100%;transform:translateY(22px) scale(1,.9) rotate(45deg)}75%{transform:translateY(11px) rotate(67.5deg)}100%{transform:translateY(0) rotate(90deg)}}@-webkit-keyframes wpgb-loader-14-2{50%{-webkit-transform:scale(1.25,1)}}@keyframes wpgb-loader-14-2{50%{transform:scale(1.25,1)}}.wpgb-loader-14,.wpgb-loader-14>div{position:relative}.wpgb-loader-14{width:44px;height:44px;margin:3px}.wpgb-loader-14>div{position:absolute;left:0;width:100%}.wpgb-loader-14>div:nth-child(1){top:-11px;z-index:1;height:100%;border-radius:10%;-webkit-animation:wpgb-loader-14-1 .6s -.1s linear infinite;animation:wpgb-loader-14-1 .6s -.1s linear infinite}.wpgb-loader-14>div:nth-child(2){bottom:-9%;height:10%;border-radius:50%;opacity:.2;-webkit-animation:wpgb-loader-14-2 .6s -.1s linear infinite;animation:wpgb-loader-14-2 .6s -.1s linear infinite}',
			],
			'wpgb-loader-15' => [
				'name' => __( 'Ball Climbing', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 4 ),
				'css'  => '@-webkit-keyframes wpgb-loader-15-1{0%,100%{-webkit-transform:scale(1,.7)}20%,80%,90%{-webkit-transform:scale(.7,1.2)}40%,46%{-webkit-transform:scale(1,1)}50%{bottom:125%}}@keyframes wpgb-loader-15-1{0%,100%{transform:scale(1,.7)}20%,80%,90%{transform:scale(.7,1.2)}40%,46%{transform:scale(1,1)}50%{bottom:125%}}@-webkit-keyframes wpgb-loader-15-2{0%{top:0;right:0;opacity:0}50%{opacity:1}100%{top:100%;right:100%;opacity:0}}@keyframes wpgb-loader-15-2{0%{top:0;right:0;opacity:0}50%{opacity:1}100%{top:100%;right:100%;opacity:0}}.wpgb-loader-15{width:50px;height:50px}.wpgb-loader-15>div:nth-child(1){position:absolute;bottom:32%;left:18%;width:14px;height:14px;border-radius:100%;-webkit-transform-origin:center bottom;transform-origin:center bottom;-webkit-animation:wpgb-loader-15-1 .6s ease-in-out infinite;animation:wpgb-loader-15-1 .6s ease-in-out infinite}.wpgb-loader-15>div:not(:nth-child(1)){position:absolute;top:0;right:0;width:14px;height:2px;border-radius:0;-webkit-transform:translate(60%,0);transform:translate(60%,0);-webkit-animation:wpgb-loader-15-2 1.8s linear infinite;animation:wpgb-loader-15-2 1.8s linear infinite}.wpgb-loader-15>div:not(:nth-child(1)):nth-child(2){-webkit-animation-delay:0s;animation-delay:0s}.wpgb-loader-15>div:not(:nth-child(1)):nth-child(3){-webkit-animation-delay:-.6s;animation-delay:-.6s}.wpgb-loader-15>div:not(:nth-child(1)):nth-child(4){-webkit-animation-delay:-1.2s;animation-delay:-1.2s}',
			],
			'wpgb-loader-16' => [
				'name' => __( 'Square Loader', 'wp-grid-builder' ),
				'html' => '<div><div></div></div>',
				'css'  => '@-webkit-keyframes wpgb-loader-16-1{0%{-webkit-transform:rotate(0)}25%,50%{-webkit-transform:rotate(180deg)}100%,75%{-webkit-transform:rotate(360deg)}}@keyframes wpgb-loader-16-1{0%{transform:rotate(0)}25%,50%{transform:rotate(180deg)}100%,75%{transform:rotate(360deg)}}@-webkit-keyframes wpgb-loader-16-2{0%,100%,25%{height:0}50%,75%{height:100%}}@keyframes wpgb-loader-16-2{0%,100%,25%{height:0}50%,75%{height:100%}}.wpgb-loader-16{width:50px;height:50px}.wpgb-loader-16>div{width:100%;height:100%;background:0 0!important;border:2px solid;border-radius:0;-webkit-animation:wpgb-loader-16-1 2s infinite ease;animation:wpgb-loader-16-1 2s infinite ease}.wpgb-loader-16>div>div{display:inline-block;width:100%;vertical-align:top;content:"";-webkit-animation:wpgb-loader-16-2 2s infinite ease-in;animation:wpgb-loader-16-2 2s infinite ease-in}',
			],
			'wpgb-loader-17' => [
				'name' => __( 'Timer', 'wp-grid-builder' ),
				'html' => '<div><div></div><div></div></div>',
				'css'  => '.wpgb-loader-17,.wpgb-loader-17>div{width:50px;height:50px}@-webkit-keyframes wpgb-loader-17{0%{-webkit-transform:rotate(0)}100%{-webkit-transform:rotate(360deg)}}@keyframes wpgb-loader-17{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}.wpgb-loader-17>div{position:relative;background:0 0!important;border:2px solid;border-radius:100%}.wpgb-loader-17>div>div{position:absolute;top:23px;left:23px;display:block;width:2px;margin-top:-1px;margin-left:-1px;border-radius:2px;-webkit-transform-origin:1px 1px 0;transform-origin:1px 1px 0;-webkit-animation:wpgb-loader-17 1.25s infinite linear;animation:wpgb-loader-17 1.25s infinite linear;-webkit-animation-delay:-625ms;animation-delay:-625ms}.wpgb-loader-17>div>div:first-child{height:20px}.wpgb-loader-17>div>div:last-child{height:12px;-webkit-animation-duration:15s;animation-duration:15s;-webkit-animation-delay:-7.5s;animation-delay:-7.5s}',
			],
			'wpgb-loader-18' => [
				'name' => __( 'Ball Scale Mulitple', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 4 ),
				'css'  => '@-webkit-keyframes wpgb-loader-18{0%{opacity:0;-webkit-transform:scale(0)}5%{opacity:.75}100%{opacity:0;-webkit-transform:scale(1)}}@keyframes wpgb-loader-18{0%{opacity:0;transform:scale(0)}5%{opacity:.75}100%{opacity:0;transform:scale(1)}}.wpgb-loader-18>div{position:absolute;top:0;left:0;width:50px;height:50px;border-radius:100%;opacity:0;-webkit-animation:wpgb-loader-18 1s 0s linear infinite;animation:wpgb-loader-18 1s 0s linear infinite}.wpgb-loader-18>div:nth-child(2){-webkit-animation-delay:.2s;animation-delay:.2s}.wpgb-loader-18>div:nth-child(3){-webkit-animation-delay:.4s;animation-delay:.4s}',
			],
			'wpgb-loader-19' => [
				'name' => __( 'Fire Diamonds', 'wp-grid-builder' ),
				'html' => str_repeat( '<div></div>', 3 ),
				'css'  => '@-webkit-keyframes wpgb-loader-19{0%{-webkit-transform:translateY(75%) translateX(-50%) rotate(45deg) scale(0)}50%{-webkit-transform:translateY(-87.5%) translateX(-50%) rotate(45deg) scale(1)}100%{-webkit-transform:translateY(-212.5%) translateX(-50%) rotate(45deg) scale(0)}}@keyframes wpgb-loader-19{0%{transform:translateY(75%) translateX(-50%) rotate(45deg) scale(0)}50%{transform:translateY(-87.5%) translateX(-50%) rotate(45deg) scale(1)}100%{transform:translateY(-212.5%) translateX(-50%) rotate(45deg) scale(0)}}.wpgb-loader-19{width:50px;height:50px}.wpgb-loader-19>div{position:absolute;bottom:0;left:50%;width:18px;height:18px;border-radius:2px;-webkit-transform:translateY(0) translateX(-50%) rotate(45deg) scale(0);transform:translateY(0) translateX(-50%) rotate(45deg) scale(0);-webkit-animation:wpgb-loader-19 1.5s infinite linear;animation:wpgb-loader-19 1.5s infinite linear}.wpgb-loader-19>div:nth-child(1){-webkit-animation-delay:-.85s;animation-delay:-.85s}.wpgb-loader-19>div:nth-child(2){-webkit-animation-delay:-1.85s;animation-delay:-1.85s}.wpgb-loader-19>div:nth-child(3){-webkit-animation-delay:-2.85s;animation-delay:-2.85s}',
			],
			'wpgb-loader-20' => [
				'name' => __( 'Ball Spin Rotate', 'wp-grid-builder' ),
				'html' => '<div><div></div><div></div></div>',
				'css'  => '@-webkit-keyframes wpgb-loader-20-1{100%{-webkit-transform:rotate(360deg)}}@keyframes wpgb-loader-20-1{100%{transform:rotate(360deg)}}@-webkit-keyframes wpgb-loader-20-2{0%,100%{-webkit-transform:scale(0)}50%{-webkit-transform:scale(1)}}@keyframes wpgb-loader-20-2{0%,100%{transform:scale(0)}50%{transform:scale(1)}}.wpgb-loader-20>div{width:50px;height:50px;background-color:transparent!important;-webkit-animation:wpgb-loader-20-1 2s infinite linear;animation:wpgb-loader-20-1 2s infinite linear}.wpgb-loader-20>div>div{position:absolute;top:0;width:50%;height:50%;border-radius:100%;-webkit-animation:wpgb-loader-20-2 2s infinite ease-in-out;animation:wpgb-loader-20-2 2s infinite ease-in-out}.wpgb-loader-20>div>div:last-child{top:auto;bottom:0;-webkit-animation-delay:-1s;animation-delay:-1s}',
			],
		];

	}

	/**
	 * Register loaders
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function register() {

		// if loaders already registered.
		if ( self::$loaders ) {
			return;
		}

		$defaults = self::default_loaders();
		self::$loaders = (array) apply_filters( 'wp_grid_builder/grid/loaders', $defaults );

	}

	/**
	 * Get loader
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $key Loader key slug.
	 * @param string $prop Property to retrun.
	 * @return mixed
	 */
	public static function get( $key = '', $prop = '' ) {

		self::register();

		// Get all loaders.
		if ( empty( $key ) && empty( $prop ) ) {
			return self::$loaders;
		}

		// If a key set but not exists.
		if ( ! isset( self::$loaders[ $key ] ) ) {
			return;
		}

		// If no prop specified return all properties.
		if ( empty( $prop ) ) {
			return self::$loaders[ $key ];
		}

		// If prop set but not exists.
		if ( ! isset( self::$loaders[ $key ][ $prop ] ) ) {
			return;
		}

		// Return loader property.
		return self::$loaders[ $key ][ $prop ];

	}

	/**
	 * Get loaders list
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function get_list() {

		self::register();

		return array_map(
			function( $item ) {
				return $item['name'];
			},
			self::$loaders
		);

	}

	/**
	 * Get loaders markup
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function get_markup() {

		self::register();

		$markup = [];

		array_walk(
			self::$loaders,
			function( &$val, $key ) use ( &$markup ) {

				$html = isset( $val['html'] ) ? $val['html'] : '';

				$markup[ $key ] =
					'<div class="wpgb-loader-holder">' .
						'<div class="wpgb-loader ' . sanitize_html_class( $key ) . '">' .
							wp_kses_post( $html ) .
						'</div>' .
					'</div>';

			}
		);

		return $markup;

	}

	/**
	 * Add inline loaders style
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function add_inline_styles() {

		self::register();

		$css = array_reduce(
			self::$loaders,
			function( $return, $arr ) {

				$return .= $arr['css'];
				return $return;

			}
		);

		wp_add_inline_style( WPGB_SLUG . '-admin', $css );

	}
}
