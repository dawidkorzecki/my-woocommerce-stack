<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://ilmdesigns.com/
 * @since             1.0.0
 * @package           Square_Thumbnails
 *
 * @wordpress-plugin
 * Plugin Name:       Square Thumbnails
 * Plugin URI:        http://ilmdesigns.com/portfolio_page/square-thumbnails-plugin/
 * Description:       Making Square Thumbnails without cropping the image.  
 * Version:           1.1.0
 * Author:            ILMDESIGNS
 * Author URI:        http://ilmdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       square-thumbnails
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if ( ! defined( 'SQUARE_THUMBNAILS_FILE' ) ) {
	define( 'SQUARE_THUMBNAILS_FILE', plugin_basename( __FILE__ ) );
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-square-thumbnails-activator.php
 */
function activate_square_thumbnails() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-square-thumbnails-activator.php';
	Square_Thumbnails_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-square-thumbnails-deactivator.php
 */
function deactivate_square_thumbnails() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-square-thumbnails-deactivator.php';
	Square_Thumbnails_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_square_thumbnails' );
register_deactivation_hook( __FILE__, 'deactivate_square_thumbnails' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-square-thumbnails.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_square_thumbnails() {

	$plugin = new Square_Thumbnails();
	$plugin->run();

}
run_square_thumbnails();
