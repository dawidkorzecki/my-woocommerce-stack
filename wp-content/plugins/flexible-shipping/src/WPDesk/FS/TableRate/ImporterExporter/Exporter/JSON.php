<?php
/**
 * JSON Exporter.
 *
 * @package WPDesk\FS\TableRate\Exporter
 */

namespace WPDesk\FS\TableRate\ImporterExporter\Exporter;

use WPDesk\FS\TableRate\ImporterExporter\ShippingClassTrait;
use WPDesk_Flexible_Shipping;

/**
 * Class JSON
 */
class JSON {
	use ShippingClassTrait;

	/**
	 * @var string
	 */
	private $instance_id;

	/**
	 * @var int[]
	 */
	private $methods;

	/**
	 * JSON constructor.
	 *
	 * @param string $instance_id Instance ID.
	 * @param int[]  $methods     Method IDs.
	 */
	public function __construct( $instance_id, $methods ) {
		$this->instance_id = $instance_id;
		$this->methods     = $methods;

		$this->init_shipping_class();
	}

	/**
	 * @return array
	 */
	public function get_exported_data() {
		$all_shipping_methods = flexible_shipping_get_all_shipping_methods();

		/** @var WPDesk_Flexible_Shipping $flexible_shipping */
		$flexible_shipping       = $all_shipping_methods['flexible_shipping'];
		$flexible_shipping_rates = $flexible_shipping->get_all_rates();

		$this->filter_by_instance_id( $flexible_shipping_rates, $this->instance_id );
		$this->filter_by_methods( $flexible_shipping_rates, $this->methods );

		$flexible_shipping_rates = $this->get_prepared_shipping_rates( $flexible_shipping_rates );

		/**
		 * Filters whether shipping rates for export.
		 *
		 * @param array  $flexible_shipping_rates .
		 * @param string $instance_id             .
		 * @param array  $methods                 .
		 * @param JSON   $exporter                .
		 *
		 * @since 3.17.0
		 */
		$flexible_shipping_rates = apply_filters( 'flexible-shipping/exporter/data', $flexible_shipping_rates, $this->instance_id, $this->methods, $this );

		return array_values( $flexible_shipping_rates );
	}

	/**
	 * Preparing fields to export.
	 *
	 * @param array $flexible_shipping_rates .
	 *
	 * @return array
	 */
	private function get_prepared_shipping_rates( $flexible_shipping_rates ) {
		$allowed_fields = array(
			'id',
			'method_title',
			'method_description',
			'method_free_shipping_requires',
			'method_free_shipping',
			'method_free_shipping_ignore_discounts',
			'method_free_shipping_cart_notice',
			'method_max_cost',
			'method_calculation_method',
			'cart_calculation',
			'method_visibility',
			'method_default',
			'method_debug_mode',
			'method_integration',
			'method_rules',
		);

		/**
		 * Filters whether allowed fields for export.
		 *
		 * @param array $allowed_fields .
		 *
		 * @since 3.17.0
		 */
		$allowed_fields = apply_filters( 'flexible-shipping/exporter/allowed_fields', $allowed_fields );

		foreach ( $flexible_shipping_rates as $id => $flexible_shipping_rate ) {
			$row = array();

			foreach ( $allowed_fields as $field ) {
				$row[ $field ] = isset( $flexible_shipping_rate[ $field ] ) ? $flexible_shipping_rate[ $field ] : '';
			}

			if ( isset( $flexible_shipping_rate['method_free_shipping_label'] ) ) {
				$row['method_free_shipping_label'] = $flexible_shipping_rate['method_free_shipping_label'];
			}

			/**
			 * Filters whether fields for export.
			 *
			 * @param array  $row                    .
			 * @param array  $flexible_shipping_rate .
			 * @param string $id                     .
			 *
			 * @since 3.17.0
			 */
			$row = apply_filters( 'flexible-shipping/exporter/rate/data', $row, $flexible_shipping_rate, $id );

			$flexible_shipping_rates[ $id ] = $row;
		}

		return $flexible_shipping_rates;
	}

	/**
	 * @param array  $flexible_shipping_rates Shipping Rates.
	 * @param string $instance_id             Instance ID.
	 */
	private function filter_by_instance_id( &$flexible_shipping_rates, $instance_id ) {
		$flexible_shipping_rates = array_filter(
			$flexible_shipping_rates,
			function ( $shipping_rate ) use ( $instance_id ) {
				return isset( $shipping_rate['instance_id'] ) && (int) $shipping_rate['instance_id'] === (int) $instance_id;
			}
		);
	}

	/**
	 * @param array $flexible_shipping_rates Shipping Rates.
	 * @param int[] $methods                 Method IDs.
	 */
	private function filter_by_methods( &$flexible_shipping_rates, $methods ) {
		$flexible_shipping_rates = array_filter(
			$flexible_shipping_rates,
			function ( $shipping_rate ) use ( $methods ) {
				return in_array( (int) $shipping_rate['id'], wp_parse_id_list( $methods ), true );
			}
		);
	}

	/**
	 * @param string $filename Name of exported file.
	 * @param array  $data     Data to export.
	 */
	public function download_file( $filename, $data ) {
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . sanitize_file_name( $filename ) );

		echo wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		die();
	}

	/**
	 * @param array $data .
	 *
	 * @return string
	 */
	public function get_filename( $data ) {
		$host = wp_parse_url( site_url(), PHP_URL_HOST );

		$filename = 'fs_' . $host . '_fs-' . $this->instance_id . '-' . join( '_', wp_list_pluck( $data, 'id' ) );

		return sanitize_file_name( $filename . '.json' );
	}
}
