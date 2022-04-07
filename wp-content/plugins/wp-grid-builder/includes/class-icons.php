<?php
/**
 * Icons
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
 * Hook into svg icons
 *
 * @class WP_Grid_Builder\Includes\Icons
 * @since 1.0.0
 */
class Icons {

	/**
	 * Holds registered icons
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $icons;

	/**
	 * Defaults icons.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	private static function defaults() {

		return [
			'wpgb' => [
				'path'   => WPGB_PATH . 'frontend/assets/svg/',
				'url'    => WPGB_URL . 'frontend/assets/svg/',
				'sprite' => WPGB_URL . 'frontend/assets/svg/sprite.svg',
				'icons'  => [
					'animals/bug',
					'animals/bear',
					'animals/cat',
					'animals/chicken',
					'animals/crab',
					'animals/dog',
					'animals/octopus',
					'animals/panda',
					'animals/paw',
					'animals/rat',
					'clothes/cap',
					'clothes/corset',
					'clothes/dress',
					'clothes/shirt',
					'clothes/shoe',
					'clothes/slacks',
					'clothes/sock',
					'clothes/tie',
					'clothes/tshirt',
					'clothes/underwear',
					'social-media/behance',
					'social-media/blogger',
					'social-media/buffer',
					'social-media/dribbble',
					'social-media/dropbox',
					'social-media/email',
					'social-media/evernote',
					'social-media/facebook',
					'social-media/flickr',
					'social-media/github',
					'social-media/google-plus',
					'social-media/instagram',
					'social-media/linkedin',
					'social-media/messenger',
					'social-media/paypal',
					'social-media/pinterest',
					'social-media/reddit',
					'social-media/rss',
					'social-media/skype',
					'social-media/slack',
					'social-media/soundcloud',
					'social-media/spotify',
					'social-media/tumblr',
					'social-media/twitter',
					'social-media/vimeo',
					'social-media/vine',
					'social-media/vkontakte',
					'social-media/whatsapp',
					'social-media/wordpress',
					'social-media/yelp',
					'social-media/youtube',
					'business/bag-1',
					'business/bag-2',
					'business/bag-3',
					'business/bag-add',
					'business/basket-1',
					'business/basket-2',
					'business/basket-add',
					'business/cart-1',
					'business/cart-2',
					'business/cart-3',
					'business/cart-add',
					'business/coupon',
					'business/credit-card',
					'business/currency-dollar',
					'business/currency-euro',
					'business/currency-pound',
					'business/currency-yen',
					'business/delivery-fast',
					'business/discount-1',
					'business/discount-2',
					'business/gift',
					'business/money-coins',
					'business/piggy-bank',
					'business/shop',
					'business/tag-content',
					'business/tag-cut',
					'business/wallet-1',
					'business/wallet-2',
					'technology/android',
					'technology/battery-level',
					'technology/controller',
					'technology/cursor',
					'technology/desktop-screen',
					'technology/disk-reader',
					'technology/engine-start',
					'technology/headphones',
					'technology/keyboard',
					'technology/laptop',
					'technology/mobile',
					'technology/mouse',
					'technology/printer',
					'technology/sim-card',
					'technology/tv-old',
					'technology/watch-circle',
					'technology/watch',
					'technology/wifi',
					'design/box',
					'design/code',
					'design/drop',
					'design/image-1',
					'design/image-2',
					'design/layers',
					'design/oval-shape',
					'design/paint-brush',
					'design/paint-bucket-1',
					'design/paint-bucket-2',
					'design/paint-roller',
					'design/palette',
					'design/polygon-shape',
					'design/rectangle-shape',
					'design/ruler',
					'design/triangle-shape',
					'files/file-download',
					'files/file-upload',
					'files/folder-1',
					'files/folder-2',
					'files/single-content',
					'files/single-copies',
					'files/single-copy',
					'user-interface/add-circle-glyph',
					'user-interface/add-circle',
					'user-interface/add-outline',
					'user-interface/add-square-glyph',
					'user-interface/add-square',
					'user-interface/add',
					'user-interface/calendar-1',
					'user-interface/calendar-2',
					'user-interface/calendar-3',
					'user-interface/calendar-4',
					'user-interface/calendar-5-glyph',
					'user-interface/clip-1',
					'user-interface/clip-2',
					'user-interface/clock-1',
					'user-interface/clock-2',
					'user-interface/clock-3-glyph',
					'user-interface/comment-1-glyph',
					'user-interface/comment-1',
					'user-interface/comment-2-glyph',
					'user-interface/comment-2',
					'user-interface/comment-3-glyph',
					'user-interface/comment-3',
					'user-interface/comment-4-glyph',
					'user-interface/comment-4',
					'user-interface/comment-5-glyph',
					'user-interface/comment-5',
					'user-interface/cross-circle-glyph',
					'user-interface/cross-circle',
					'user-interface/cross-outline',
					'user-interface/cross-square-glyph',
					'user-interface/cross-square',
					'user-interface/cross',
					'user-interface/curved-next-glyph',
					'user-interface/curved-next',
					'user-interface/curved-previous-glyph',
					'user-interface/curved-previous',
					'user-interface/download-cloud',
					'user-interface/download-square',
					'user-interface/email',
					'user-interface/filter-1-glyph',
					'user-interface/filter-1',
					'user-interface/filter-2-glyph',
					'user-interface/filter-2',
					'user-interface/heart-add-glyph',
					'user-interface/heart-add',
					'user-interface/heart-glyph',
					'user-interface/heart-remove-glyph',
					'user-interface/heart-remove',
					'user-interface/heart',
					'user-interface/link-1',
					'user-interface/link-2',
					'user-interface/link-3',
					'user-interface/link-4',
					'user-interface/link-5',
					'user-interface/link-6',
					'user-interface/link-7',
					'user-interface/link-8',
					'user-interface/padlock-close',
					'user-interface/padlock-glyph',
					'user-interface/padlock-open-glyph',
					'user-interface/padlock-open',
					'user-interface/quote-1-glyph',
					'user-interface/quote-2',
					'user-interface/quote-3-glyph',
					'user-interface/settings-glyph',
					'user-interface/settings',
					'user-interface/share-1-glyph',
					'user-interface/share-1',
					'user-interface/share-2',
					'user-interface/share-3',
					'user-interface/star-add-glyph',
					'user-interface/star-add',
					'user-interface/star-glyph',
					'user-interface/star-remove-glyph',
					'user-interface/star-remove',
					'user-interface/star',
					'user-interface/upload-cloud',
					'user-interface/zoom-1',
					'user-interface/zoom-2',
					'user-interface/zoom-3',
					'user-interface/zoom-4',
					'user-interface/zoom-in-1',
					'user-interface/zoom-in-2',
					'user-interface/zoom-in-3',
					'user-interface/zoom-in-4',
					'user-interface/zoom-out-1',
					'user-interface/zoom-out-2',
					'user-interface/zoom-out-3',
					'user-interface/zoom-out-4',
					'arrows/arrow-back',
					'arrows/arrow-down',
					'arrows/arrow-left',
					'arrows/arrow-loop',
					'arrows/arrow-right',
					'arrows/arrow-up',
					'arrows/enlarge-1',
					'arrows/enlarge-2',
					'arrows/enlarge-diagonal-1',
					'arrows/enlarge-diagonal-2',
					'arrows/enlarge-diagonal-3',
					'arrows/enlarge-diagonal-4',
					'arrows/tail-down',
					'arrows/tail-left',
					'arrows/tail-right',
					'arrows/tail-up',
					'emoticons/emoticon-puzzled',
					'emoticons/emoticon-sad',
					'emoticons/emoticon-smile',
					'emoticons/emoticon-speechless',
					'emoticons/finger-like-glyph',
					'emoticons/finger-like',
					'emoticons/finger-unlike-glyph',
					'emoticons/finger-unlike',
					'multimedia/action',
					'multimedia/audio-mic',
					'multimedia/button-play-1-glyph',
					'multimedia/button-play-1',
					'multimedia/button-play-2-glyph',
					'multimedia/button-play-2',
					'multimedia/button-play-3-glyph',
					'multimedia/button-play-3',
					'multimedia/camera',
					'maps/bookmark',
					'maps/compass',
					'maps/flag-points',
					'maps/flag',
					'maps/gps',
					'maps/map',
					'maps/marker',
					'maps/pin',
					'home/air-conditioner',
					'home/apartment',
					'home/armchair',
					'home/bath-tub',
					'home/bed-2',
					'home/bed-side',
					'home/bed',
					'home/bedroom',
					'home/books',
					'home/broom',
					'home/cabinet',
					'home/cactus',
					'home/chair-2',
					'home/chair',
					'home/coat-hanger',
					'home/coffee',
					'home/cradle',
					'home/crane',
					'home/curtains',
					'home/desk-drawer',
					'home/desk',
					'home/detached-property',
					'home/door',
					'home/drawer-2',
					'home/drawer',
					'home/escalator',
					'home/fridge',
					'home/furnished-property',
					'home/gym',
					'home/hanger-clothes',
					'home/hanger',
					'home/heater',
					'home/iron',
					'home/lamp-2',
					'home/lamp-floor',
					'home/lamp',
					'home/library',
					'home/lift',
					'home/light-2',
					'home/light',
					'home/mixer',
					'home/new-construction',
					'home/oven',
					'home/photo-frame',
					'home/safe',
					'home/shower',
					'home/sink-wash',
					'home/sink',
					'home/smart-house',
					'home/sofa',
					'home/spray',
					'home/stairs',
					'home/storage-hanger',
					'home/storage',
					'home/switcher',
					'home/table',
					'home/telephone',
					'home/terrace',
					'home/time-alarm',
					'home/time-clock',
					'home/toilet-paper',
					'home/toilet',
					'home/trash',
					'home/tv-stand',
					'home/wardrobe',
					'home/wash-2',
					'home/wash',
					'home/weight-scale',
				],
			],
		];

	}

	/**
	 * Register icons
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public static function register() {

		// if icons already registered.
		if ( self::$icons ) {
			return;
		}

		$defaults = self::defaults();
		self::$icons = (array) apply_filters( 'wp_grid_builder/icon_sets', $defaults );

	}

	/**
	 * Get icon(s)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $key Icon key slug.
	 * @return mixed
	 */
	public static function get( $key = '' ) {

		self::register();

		// Get all icons.
		if ( empty( $key ) ) {
			return self::$icons;
		}

		$set  = explode( '/', $key );
		$set  = reset( $set );
		$icon = str_replace( $set . '/', '', $key );

		if ( isset( self::$icons[ $set ]['icons'] ) && in_array( $icon, self::$icons[ $set ]['icons'], true ) ) {

			return [
				'path' => self::$icons[ $set ]['path'] . $icon . '.svg',
				'url'  => self::$icons[ $set ]['url'] . $icon . '.svg#' . $set . '-icon',
			];

		}

		return '';

	}

	/**
	 * Display icon
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string  $key     Icon key slug.
	 * @param boolean $svg_use Display icon as svg use.
	 * @return mixed
	 */
	public static function display( $key = '', $svg_use = true ) {

		global $is_IE, $is_edge;

		$svg_use = $svg_use && ! $is_IE && ! $is_edge;
		$svg_use = apply_filters( 'wp_grid_builder/svg_use', $svg_use );

		if ( empty( $key ) ) {
			return;
		}

		$icon = self::get( $key );

		if ( empty( $icon ) ) {
			return;
		}

		if ( $svg_use ) {

			echo '<svg><use xlink:href="' . esc_url( $icon['url'] ) . '"/></svg>';
			return;

		}

		if ( ! file_exists( $icon['path'] ) ) {
			return;
		}

		$icon = wp_normalize_path( $icon['path'] );

		ob_start();
		include $icon;

		$icon = ob_get_clean();
		$icon = preg_replace( '#\s(id)="[^"]+"#', '', $icon );

		echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
}
