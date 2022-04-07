<?php
/**
 * WP_Rocket support
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
 * Handle WP_Rocket
 *
 * @class WP_Grid_Builder\Includes\Third_Party\WP_Rocket
 * @since 1.3.1
 */
class WP_Rocket {

	/**
	 * Constructor
	 *
	 * @since 1.3.1
	 * @access public
	 */
	public function __construct() {

		add_filter( 'rocket_lazyload_excluded_attributes', [ $this, 'exclude_noscript_img' ] );
		add_filter( 'rocket_exclude_async_css', [ $this, 'exclude_async_css' ] );

	}

	/**
	 * Exclude image in noscript tags from WP Rocket lazy load feature
	 *
	 * @since 1.3.1
	 * @access public
	 *
	 * @param array $attributes Holds attributes to exclude from lazy load.
	 * @return array
	 */
	public function exclude_noscript_img( $attributes ) {

		$attributes[] = 'class="wpgb-noscript-img"';

		return $attributes;

	}

	/**
	 * Exclude styles from async
	 *
	 * @since 1.3.1
	 * @access public
	 *
	 * @param array $files Holds files to exclude.
	 * @return array
	 */
	public function exclude_async_css( $files ) {

		$files[] = WPGB_URL . 'frontend/assets/css/(.*).css';

		return $files;

	}
}
