<?php
/**
 * Initialize plugin
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes;

use WP_Grid_Builder\Admin;
use WP_Grid_Builder\FrontEnd;
use WP_Grid_Builder\Includes;
use WP_Grid_Builder\Includes\Third_Party;
use WP_Grid_Builder\Includes\Settings\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Inctance of the plugin
 *
 * @class WP_Grid_Builder\Includes\Plugin
 * @since 1.0.0
 */
final class Plugin {

	use Includes\Singleton;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		global $wp_version;

		$is_wp_5_1 = version_compare( $wp_version, '5.1', '>=' );
		$site_hook = $is_wp_5_1 ? 'wp_initialize_site' : 'wpmu_new_blog';

		add_action( 'plugins_loaded', [ $this, 'init' ], 0 );
		add_action( $site_hook, [ $this, 'insert_site' ] );
		add_action( 'wpmu_drop_tables', [ $this, 'delete_site' ] );
		add_action( 'upgrader_process_complete', [ $this, 'update' ], 10, 2 );

		register_activation_hook( WPGB_FILE, [ $this, 'activation' ] );
		register_deactivation_hook( WPGB_FILE, [ $this, 'deactivation' ] );

	}

	/**
	 * Init instances
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		// Trigger action when plugin is loaded before initialization.
		do_action( 'wp_grid_builder/loaded' );

		$this->register();
		$this->includes();
		$this->init_front();
		$this->init_admin();

		// Trigger action when plugin is initialized.
		do_action( 'wp_grid_builder/init' );

	}

	/**
	 * Register plugin/add-ons licenses
	 *
	 * @since 1.1.5
	 * @access public
	 */
	public function register() {

		if ( ! is_admin() ) {
			return;
		}

		array_map(
			function( $plugin ) {

				new Includes\Updater(
					new Includes\License( $plugin )
				);

			},
			apply_filters( 'wp_grid_builder/register', [ [] ] )
		);

	}

	/**
	 * Includes main helpers
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function includes() {

		new Third_Party\ACF();
		new Third_Party\EDD();
		new Third_Party\Jetpack();
		new Third_Party\SearchWP();
		new Third_Party\WP_Rocket();
		new Third_Party\Relevanssi();
		new Third_Party\WooCommerce();

		new Includes\I18n();
		new Includes\Extend();
		new Includes\Indexer();
		new Includes\REST_API();
		new Includes\Gutenberg();

	}

	/**
	 * Init frontend plugin
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init_front() {

		new FrontEnd\Init();
		new FrontEnd\Localize();
		new FrontEnd\REST_API();
		new FrontEnd\Intercept();

		FrontEnd\Filter::get_instance();
		FrontEnd\Styles::get_instance();
		FrontEnd\Scripts::get_instance();

	}

	/**
	 * Init backend plugin
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init_admin() {

		if ( ! is_admin() ) {
			return;
		}

		$this->settings = Settings::get_instance();

		new Admin\Menu();
		new Admin\posts();
		new Admin\Grids();
		new Admin\Cards();
		new Admin\Index();
		new Admin\Facets();
		new Admin\Search();
		new Admin\Import();
		new Admin\Export();
		new Admin\Plugin();
		new Admin\Preview();
		new Admin\Actions();
		new Admin\TinyMCE();
		new Admin\Settings();
		new Admin\MetaBox();
		new Admin\Enqueue();
		new Admin\Localize();

	}

	/**
	 * Create custom tables and delete transients on plugin update
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $upgrader_object Holds upgrader arguments.
	 * @param array $options Holds plugin options.
	 */
	public function update( $upgrader_object, $options ) {

		if ( 'update' !== $options['action'] || 'plugin' !== $options['type'] ) {
			return;
		}

		if ( empty( $options['plugins'] ) ) {
			return;
		}

		foreach ( $options['plugins'] as $plugin ) {

			if ( WPGB_BASE === $plugin ) {

				$network_wide = is_plugin_active_for_network( WPGB_BASE );

				Includes\Database::create_tables( $network_wide, true );
				Includes\Helpers::delete_transient();

				// Trigger action when plugin is updated.
				do_action( 'wp_grid_builder/updated' );
				break;

			}
		}
	}

	/**
	 * Create custom tables and delete transients on plugin activation
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param boolean $network_wide Whether to enable the plugin for all sites in the network.
	 */
	public function activation( $network_wide ) {

		Includes\Database::create_tables( $network_wide, true );
		Includes\Helpers::delete_transient();

		// Trigger action when plugin is activated.
		do_action( 'wp_grid_builder/activated' );

	}

	/**
	 * Delete transients on plugin deactivation
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function deactivation() {

		Includes\Helpers::delete_transient();
		wp_clear_scheduled_hook( 'wpgb_cron' );

		// Trigger action when plugin is deactivated.
		do_action( 'wp_grid_builder/deactivated' );

	}

	/**
	 * Create custom tables whenever a new site is created (multisite)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_Site|integer $new_site New site object | New site id.
	 */
	public function insert_site( $new_site ) {

		global $wp_version;

		if ( ! is_plugin_active_for_network( WPGB_BASE ) ) {
			return;
		}

		if ( 'wpmu_new_blog' === current_action() ) {
			$site_id = $new_site;
		} else {
			$site_id = $new_site->id;
		}

		switch_to_blog( $site_id );
		Includes\Database::create_tables( true, true );
		restore_current_blog();

	}

	/**
	 * Delete custom tables whenever a site is delete (multisite)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $tables New site object.
	 */
	public function delete_site( $tables ) {

		global $wpdb;

		return array_merge(
			[
				"{$wpdb->prefix}wpgb_grids",
				"{$wpdb->prefix}wpgb_cards",
				"{$wpdb->prefix}wpgb_index",
				"{$wpdb->prefix}wpgb_facets",
			],
			$tables
		);

	}
}
