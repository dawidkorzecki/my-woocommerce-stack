<?php
/**
 * ImporterData.
 *
 *  @package WPDesk\FS\TableRate\ImporterExporter
 */

namespace WPDesk\FS\TableRate\ImporterExporter;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\ImporterExporter\Importer\JSON;

/**
 * Class Hooks
 */
class ImporterData implements Hookable {
	/**
	 * @return void|null
	 */
	public function hooks() {
		add_filter( 'flexible-shipping/importer/json/data', array( $this, 'prepare_import_data' ), 10, 3 );
	}

	/**
	 * @param array $new_shipping_method .
	 * @param array $shipping_method     .
	 * @param JSON  $importer            .
	 *
	 * @return array
	 */
	public function prepare_import_data( $new_shipping_method, $shipping_method, $importer ) {
		foreach ( $new_shipping_method['method_rules'] as $method_rule_id => $method_rule ) {
			if ( ! isset( $method_rule['conditions'] ) ) {
				continue;
			}

			foreach ( $method_rule['conditions'] as $condition_id => $condition ) {
				if ( 'shipping_class' !== $condition['condition_id'] ) {
					continue;
				}

				foreach ( $condition['shipping_class'] as $shipping_class_id => $shipping_class ) {
					if ( in_array( $shipping_class, array( 'all', 'any', 'none' ), true ) ) {
						continue;
					}

					if ( isset( $importer->wc_shipping_classes_hashmap[ $shipping_class ] ) ) {
						$imported_shipping_class_id = $importer->wc_shipping_classes_hashmap[ $shipping_class ];
					} else {
						$imported_shipping_class_id = $importer->create_shipping_class( $shipping_class, $shipping_class );
					}

					$new_shipping_method['method_rules'][ $method_rule_id ]['conditions'][ $condition_id ]['shipping_class'][ $shipping_class_id ] = $imported_shipping_class_id;
				}
			}
		}

		return $new_shipping_method;
	}
}
