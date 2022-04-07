<?php
/**
 * Exporter.
 *
 * @package WPDesk\FS\TableRate\ImporterExporter
 */

namespace WPDesk\FS\TableRate\ImporterExporter;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\ImporterExporter\Exporter\JSON;

/**
 * Class Hooks
 */
class Exporter implements Hookable {
	/**
	 * @return void|null
	 */
	public function hooks() {
		add_action( 'wp_ajax_flexible_shipping_export', array( $this, 'flexible_shipping_export' ) );
	}

	/**
	 * Preparing data to export.
	 */
	public function flexible_shipping_export() {
		check_ajax_referer( 'flexible_shipping', 'flexible_shipping_nonce' );

		$instance_id = filter_input( INPUT_GET, 'instance_id' );
		$methods     = array_filter( wp_parse_id_list( filter_input( INPUT_GET, 'methods' ) ) );

		$exporter    = new JSON( sanitize_key( $instance_id ), $methods );
		$export_data = $exporter->get_exported_data();

		$exporter->download_file( $exporter->get_filename( $export_data ), $export_data );
	}
}
