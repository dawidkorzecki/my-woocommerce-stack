<?php
/**
 * ExporterData.
 *
 * @package WPDesk\FS\TableRate\ImporterExporter
 */

namespace WPDesk\FS\TableRate\ImporterExporter;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Class Hooks
 */
class ExporterData implements Hookable {
	/**
	 * @return void|null
	 */
	public function hooks() {
		add_filter( 'flexible-shipping/exporter/data', array( $this, 'prepare_export_data' ), 10, 4 );
	}

	/**
	 * @param array         $flexible_shipping_rates .
	 * @param string        $instance_id             .
	 * @param array         $methods                 .
	 * @param Exporter\JSON $exporter                .
	 *
	 * @return array
	 */
	public function prepare_export_data( $flexible_shipping_rates, $instance_id, $methods, $exporter ) {
		$shipping_classes = array_flip( $exporter->wc_shipping_classes_hashmap );

		foreach ( $flexible_shipping_rates as $id => $flexible_shipping_rate ) {
			foreach ( $flexible_shipping_rate['method_rules'] as $method_rule_id => $method_rule ) {
				foreach ( $method_rule['conditions'] as $condition_id => $condition ) {
					if ( 'shipping_class' !== $condition['condition_id'] ) {
						continue;
					}

					foreach ( $condition['shipping_class'] as $shipping_class_id => $shipping_class ) {

						if ( in_array( $shipping_class, array( 'all', 'any', 'none' ), true ) ) {
							continue;
						}

						$flexible_shipping_rates[ $id ]['method_rules'][ $method_rule_id ]['conditions'][ $condition_id ]['shipping_class'][ $shipping_class_id ] = $shipping_classes[ $shipping_class ];
					}
				}
			}
		}

		return $flexible_shipping_rates;
	}
}
