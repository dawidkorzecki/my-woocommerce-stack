<?php
/**
 * JSON Importer
 *
 * @package WPDesk\FS\TableRate\Importer
 */

namespace WPDesk\FS\TableRate\ImporterExporter\Importer;

use WC_Admin_Settings;
use WPDesk\FS\TableRate\ImporterExporter\Importer\Exception\FileNotExists;
use WPDesk\FS\TableRate\ImporterExporter\Importer\Exception\InvalidFile;

/**
 * Class JSON
 *
 * @package WPDesk\FS\TableRate\Importer
 */
class JSON extends AbstractImporter {
	/**
	 * @return array|void
	 * @throws FileNotExists .
	 * @throws InvalidFile .
	 */
	public function import() {
		if ( ! file_exists( $this->file['tmp_name'] ) ) {
			throw new FileNotExists( 'Uploaded file not exist.' );
		}

		$imported_data = file_get_contents( $this->file['tmp_name'] );

		if ( ! $imported_data ) {
			throw new FileNotExists( __( 'Uploaded file not exist.', 'flexible-shipping' ) );
		}

		$imported_shipping_methods = json_decode( $imported_data, true );

		if ( empty( $imported_shipping_methods ) ) {
			throw new InvalidFile( __( 'Sorry, there has been an error. The JSON file is invalid or incorrect file type.', 'flexible-shipping' ) );
		}

		foreach ( $imported_shipping_methods as $data ) {
			if ( ! is_array( $data ) || ! isset( $data['id'] ) ) {
				throw new InvalidFile( __( 'Sorry, there has been an error. The JSON file is invalid or incorrect file type.', 'flexible-shipping' ) );
			}

			$imported_shipping_method = $this->get_new_shipping_method_params( $data );

			$this->shipping_methods[ $imported_shipping_method['id'] ] = map_deep( $imported_shipping_method, 'sanitize_text_field' );

			WC_Admin_Settings::add_message(
				sprintf(
				// Translators: imported method title and method title.
					__( 'Shipping method %1$s imported as %2$s.', 'flexible-shipping' ),
					esc_html( $data['method_title'] ),
					esc_html( $imported_shipping_method['method_title'] )
				)
			);
		}

		return $this->shipping_methods;
	}

	/**
	 * Create new shipping method.
	 *
	 * @param array $shipping_method CSV row.
	 *
	 * @return array
	 */
	private function get_new_shipping_method_params( array $shipping_method ) {
		$new_shipping_method = array();

		$new_shipping_method['method_enabled'] = 'no';

		$method_title                        = $this->get_field_value( $shipping_method, 'method_title', __( '(no title)', 'flexible-shipping' ) );
		$new_shipping_method['method_title'] = $this->get_new_method_title( $method_title );

		$new_shipping_method['id']                                    = $this->flexible_shipping_method->shipping_method_next_id( $this->shipping_methods );
		$new_shipping_method['id_for_shipping']                       = $this->flexible_shipping_method->id . '_' . $this->flexible_shipping_method->instance_id . '_' . $new_shipping_method['id'];
		$new_shipping_method['instance_id']                           = $this->flexible_shipping_method->instance_id;
		$new_shipping_method['woocommerce_method_instance_id']        = $this->flexible_shipping_method->instance_id;
		$new_shipping_method['method_description']                    = $this->get_field_value( $shipping_method, 'method_description' );
		$new_shipping_method['method_free_shipping_requires']         = $this->get_field_value( $shipping_method, 'method_free_shipping_requires' );
		$new_shipping_method['method_free_shipping']                  = $this->get_field_price( $shipping_method, 'method_free_shipping' );
		$new_shipping_method['method_free_shipping_ignore_discounts'] = $this->get_field_value( $shipping_method, 'method_free_shipping_ignore_discounts' );
		$new_shipping_method['method_free_shipping_cart_notice']      = $this->get_field_value( $shipping_method, 'method_free_shipping_cart_notice' );
		$new_shipping_method['method_max_cost']                       = $this->get_field_price( $shipping_method, 'method_max_cost' );
		$new_shipping_method['method_calculation_method']             = $this->get_field_value( $shipping_method, 'method_calculation_method' );
		$new_shipping_method['cart_calculation']                      = $this->get_field_value( $shipping_method, 'cart_calculation' );
		$new_shipping_method['method_visibility']                     = $this->get_field_value( $shipping_method, 'method_visibility', 'no' );
		$new_shipping_method['method_default']                        = $this->get_field_value( $shipping_method, 'method_default', 'no' );
		$new_shipping_method['method_debug_mode']                     = $this->get_field_value( $shipping_method, 'method_debug_mode', 'no' );
		$new_shipping_method['method_integration']                    = $this->get_field_value( $shipping_method, 'method_integration', 'no' );
		$new_shipping_method['method_rules']                          = $this->get_field_value( $shipping_method, 'method_rules', array() );

		$method_free_shipping_label = $this->get_field_value( $shipping_method, 'method_free_shipping_label', null );

		if ( null !== $method_free_shipping_label ) {
			$new_shipping_method['method_free_shipping_label'] = $method_free_shipping_label;
		}

		/**
		 * Filters whether shipping rates for export.
		 *
		 * @param array  $new_shipping_method .
		 * @param string $shipping_method     .
		 * @param JSON   $this                .
		 *
		 * @since 3.17.0
		 */
		return apply_filters( 'flexible-shipping/importer/json/data', $new_shipping_method, $shipping_method, $this );
	}

	/**
	 * @param array  $data    .
	 * @param string $key     .
	 * @param mixed  $default .
	 *
	 * @return mixed
	 */
	private function get_field_value( $data, $key, $default = '' ) {
		return isset( $data[ $key ] ) ? $data[ $key ] : $default;
	}

	/**
	 * @param array  $data .
	 * @param string $key  .
	 *
	 * @return string
	 */
	private function get_field_price( $data, $key ) {
		return str_replace( ',', '.', $this->get_field_value( $data, $key, '' ) );
	}
}
