<?php
/**
 * WP Grid Builder Plugin
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @link      https://www.wpgridbuilder.com
 * @copyright 2019-2022 Loïc Blascos
 *
 * @wordpress-plugin
 * Plugin Name:  WP Grid Builder
 * Plugin URI:   https://www.wpgridbuilder.com
 * Description:  Build advanced grid layouts with real time faceted search for your eCommerce, blog, portfolio, and more...
 * Version:      1.6.4
 * Author:       Loïc Blascos
 * Author URI:   https://www.wpgridbuilder.com
 * License:      GPL-3.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:  wp-grid-builder
 * Domain Path:  /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPGB_VERSION', '1.6.4' );
define( 'WPGB_MIN_PHP', '5.6.0' );
define( 'WPGB_MIN_WP', '4.7' );
define( 'WPGB_SLUG', 'wpgb' );
define( 'WPGB_NAME', 'Gridbuilder ᵂᴾ' );
define( 'WPGB_FILE', __FILE__ );
define( 'WPGB_BASE', plugin_basename( WPGB_FILE ) );
define( 'WPGB_PATH', plugin_dir_path( WPGB_FILE ) );
define( 'WPGB_URL', plugin_dir_url( WPGB_FILE ) );

/**
 * Load plugin text domain.
 *
 * @since 1.0.0
 */
function wp_grid_builder_textdomain() {

	load_plugin_textdomain(
		'wp-grid-builder',
		false,
		basename( dirname( WPGB_FILE ) ) . '/languages'
	);

}
add_action( 'plugins_loaded', 'wp_grid_builder_textdomain' );

// Init compatibility class.
require_once WPGB_PATH . 'compatibility.php';

// Check PHP and WP compatibility.
if ( ! $compatibility->check() ) {
	return;
}

// Include autoloader.
require_once WPGB_PATH . 'includes/class-autoload.php';

/**
 * Get and initialize the plugin instance.
 *
 * @since 1.0.0
 * @return \WP_Grid_Builder\Includes\Plugin Plugin instance
 */
function wp_grid_builder() {

	// To prevent parse error for PHP prior to 5.3.0.
	$class = '\WP_Grid_Builder\Includes\Plugin';
	return $class::get_instance();

}

// Initialize plugin.
wp_grid_builder();
