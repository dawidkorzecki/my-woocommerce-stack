<?php
/**
 * Jetpack support
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Third_Party;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Jetpack
 *
 * @class WP_Grid_Builder\Includes\Third_Party\Jetpack
 * @since 1.3.1
 */
class Jetpack {

	/**
	 * Constructor
	 *
	 * @since 1.3.1
	 * @access public
	 */
	public function __construct() {

		// We check if Jetpack is enabled.
		if ( is_admin() || ! method_exists( '\Jetpack', 'is_module_active' ) ) {
			return;
		}

		// We check if the lazy-images module is activated and enabled.
		if ( ! \Jetpack::is_module_active( 'lazy-images' ) || ! apply_filters( 'lazyload_is_enabled', true ) ) {
			return;
		}

		add_filter( 'jetpack_lazy_images_blocked_classes', [ $this, 'exclude_noscript_img' ] );

	}

	/**
	 * To hook after Jetpack
	 *
	 * @since 1.3.1
	 * @access public
	 */
	public function setup_filter() {

		add_filter( 'the_content', [ $this, 'remove_duplicate_noscript' ], PHP_INT_MAX );

	}

	/**
	 * Exclude image in noscript tags from Jetpack lazy load feature
	 *
	 * @since 1.3.1
	 * @access public
	 *
	 * @param array $classes Holds classes to exclude from lazy load.
	 * @return array
	 */
	public function exclude_noscript_img( $classes ) {

		$classes[] = 'wpgb-noscript-img';

		return $classes;

	}

	/**
	 * Remove duplicate nested noscript tags introduced by Jetpack
	 *
	 * @since 1.3.1
	 * @access public
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	public function remove_duplicate_noscript( $content ) {

		return preg_replace(
			'/<noscript><img(.*?)class="wpgb-noscript-img"(.*?)>(<noscript>.*?<\/noscript><\/noscript>)/i',
			'<noscript><img$1class="wpgb-noscript-img"$2></noscript>',
			$content
		);

	}
}
