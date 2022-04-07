<?php
/**
 * Uninstall
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Uninstall class
 *
 * @class WPGB_Uninstall
 * @since 1.0.0
 */
class WPGB_Uninstall {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		if ( is_multisite() ) {
			$this->uninstall_sites();
		} else {
			$this->uninstall_site();
		}
	}

	/**
	 * Process uninstall on each sites (multisite)
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function uninstall_sites() {

		global $wpdb;

		// Save current blog ID.
		$current  = $wpdb->blogid;
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

		// Create tables for each blog ID.
		foreach ( $blog_ids as $blog_id ) {

			switch_to_blog( $blog_id );
			$this->uninstall_site();

		}

		// Go back to current blog.
		switch_to_blog( $current );

	}

	/**
	 * Process uninstall on current site
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function uninstall_site() {

		$settings = get_option( 'wpgb_global_settings' );

		if ( empty( $settings['uninstall'] ) ) {
			return;
		}

		$this->drop_tables();
		$this->delete_meta();
		$this->delete_transients();
		$this->delete_options();
		$this->delete_files();

	}

	/**
	 * Drop plugin custom tables from current site
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function drop_tables() {

		global $wpdb;

		$wpdb->query(
			"DROP TABLE IF EXISTS
			{$wpdb->prefix}wpgb_grids,
			{$wpdb->prefix}wpgb_cards,
			{$wpdb->prefix}wpgb_index,
			{$wpdb->prefix}wpgb_facets"
		);

	}

	/**
	 * Delete plugin metadata from current site
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function delete_meta() {

		global $wpdb;

		$wpdb->query( "DELETE from $wpdb->postmeta WHERE meta_key IN('_wpgb', '_wpgb_oembed')" );
		$wpdb->query( "DELETE from $wpdb->termmeta WHERE meta_key IN('_wpgb', '_wpgb_oembed')" );
		$wpdb->query( "DELETE from $wpdb->usermeta WHERE meta_key IN('_wpgb', '_wpgb_oembed')" );

	}

	/**
	 * Delete plugin transients from current site
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function delete_transients() {

		global $wpdb;

		// Delete all plugin metadata.
		$wpdb->query( "DELETE from $wpdb->options WHERE option_name LIKE '_transient_wpgb_%'" );
		$wpdb->query( "DELETE from $wpdb->options WHERE option_name LIKE '_site_transient_wpgb_%'" );

	}

	/**
	 * Delete plugin options from current site
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function delete_options() {

		delete_option( 'wpgb_global_settings' );
		delete_option( 'wpgb_plugin_info' );
		delete_option( 'wpgb_db_version' );

	}

	/**
	 * Delete plugin files
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function delete_files() {

		global $wp_filesystem;

		// Get filesystem.
		if ( empty( $wp_filesystem ) ) {

			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
			}

			WP_Filesystem();

		}

		$wp_content = $wp_filesystem->wp_content_dir();

		$wp_filesystem->delete( $wp_content . '/wp-grid-builder', true );
		$wp_filesystem->delete( $wp_content . '/wpgb', true );

	}
}

new WPGB_Uninstall();
