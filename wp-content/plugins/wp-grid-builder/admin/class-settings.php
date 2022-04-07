<?php
/**
 * Settings
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\File;
use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Indexer;
use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle settings actions
 *
 * @class WP_Grid_Builder\Admin\Settings
 * @since 1.0.0
 */
final class Settings extends Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_global';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct();

		add_action( 'wp_grid_builder/import/settings', [ $this, 'import' ] );

	}

	/**
	 * Save settings
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function save() {

		// Load settings.
		require WPGB_PATH . 'admin/settings/global.php';

		// Sanitize settings.
		$settings = $this->get_var( 'settings' );
		$settings = json_decode( $settings, true );
		$settings = wp_grid_builder()->settings->sanitize( $settings );
		$settings = $this->merge( $settings );

		update_option( WPGB_SLUG . '_global_settings', $settings );

		File::delete( 'grids' );
		File::delete( 'facets' );

		$this->send_response( true, __( 'Settings Saved!', 'wp-grid-builder' ) );

	}

	/**
	 * Merge previous settings
	 * Allows to preserve field with dedicated capability.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds Grid settings.
	 * @return array Grid settings
	 */
	public function merge( $settings ) {

		if ( current_user_can( 'manage_options' ) ) {
			return $settings;
		}

		// Get previous settings.
		$option = get_option( WPGB_SLUG . '_global_settings' );

		if ( isset( $option['uninstall'] ) ) {
			$settings['uninstall'] = $option['uninstall'];
		}

		return $settings;

	}

	/**
	 * Reset settings
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function reset() {

		// Get/delete settings.
		$name   = WPGB_SLUG . '_global_settings';
		$option = get_option( $name );
		$delete = delete_option( $name );

		// If an error occurred when deleting an existing option.
		if ( $option && ! $delete ) {
			$this->unknown_error();
		}

		File::delete( 'grids' );
		File::delete( 'facets' );

		// Get settings panel.
		ob_start();
		require WPGB_PATH . 'admin/settings/global.php';
		require WPGB_PATH . 'admin/views/pages/settings.php';

		$this->send_response(
			true,
			__( 'Settings were correctly reset!', 'wp-grid-builder' ),
			ob_get_clean()
		);

	}

	/**
	 * Import settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds items to import.
	 */
	public function import( $settings ) {

		// Load settings.
		require WPGB_PATH . 'admin/settings/global.php';

		// Only one settings item can be imported.
		$settings = reset( $settings );
		$settings = wp_grid_builder()->settings->sanitize( $settings );

		if ( ! current_user_can( 'manage_options' ) ) {
			unset( $settings['uninstall'] );
		}

		update_option( WPGB_SLUG . '_global_settings', $settings );

		File::delete( 'grids' );
		File::delete( 'facets' );

	}

	/**
	 * Stop indexer and clear all facets in cron queue
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function stop_indexer() {

		( new Indexer() )->cancel();

		$this->send_response( true, __( 'Indexer Stopped!', 'wp-grid-builder' ) );

	}

	/**
	 * Get index stats
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function index_stats() {

		global $wpdb;

		$results = (array) $wpdb->get_results(
			$wpdb->prepare(
				'SELECT TABLE_ROWS, DATA_LENGTH, INDEX_LENGTH
				FROM information_schema.TABLES
				WHERE TABLE_SCHEMA = %s
				AND TABLE_NAME = %s',
				$wpdb->dbname,
				$wpdb->prefix . 'wpgb_index'
			)
		);

		$results = array_shift( $results );

		$rows  = ! empty( $results->TABLE_ROWS ) ? $results->TABLE_ROWS : 0; // @codingStandardsIgnoreLine.
		$data  = ! empty( $results->DATA_LENGTH ) ? $results->DATA_LENGTH : 0; // @codingStandardsIgnoreLine.
		$index = ! empty( $results->INDEX_LENGTH ) ? $results->INDEX_LENGTH : 0; // @codingStandardsIgnoreLine.
		$size  = round( ( $data + $index ) / 1024 / 1024, 2 );

		$stats = sprintf(
			/* translators: %1$s Number of rows in index table, %2$s Index table length. */
			_n( 'Index table: &#126;%1$d row (&#126;%2$.2fMB)', 'Index table: &#126;%1$d rows (&#126;%2$.2fMB)', max( 1, $rows ), 'wp-grid-builder' ),
			(int) $rows,
			(float) $size
		);

		$this->send_response( true, null, esc_html( $stats ) );

	}

	/**
	 * Clear whole index table
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function clear_index() {

		global $wpdb;

		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}wpgb_index" );
		Helpers::delete_transient();

		$this->send_response( true, __( 'Index table cleared!', 'wp-grid-builder' ) );

	}

	/**
	 * Clear all plugin transients
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function clear_cache() {

		Helpers::delete_transient();

		$this->send_response( true, __( 'Cache cleared!', 'wp-grid-builder' ) );

	}

	/**
	 * Delete all dynamic stylesheets generated for each grid.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function delete_stylesheets() {

		// We keep posts folder deletion as fallback.
		File::delete( 'posts' );
		File::delete( 'grids' );
		File::delete( 'facets' );

		$this->send_response( true, __( 'Style sheets deleted!', 'wp-grid-builder' ) );

	}
}
