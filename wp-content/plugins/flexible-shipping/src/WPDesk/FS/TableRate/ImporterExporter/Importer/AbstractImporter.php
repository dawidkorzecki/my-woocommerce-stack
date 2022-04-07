<?php
/**
 * Abstract Importer
 *
 * @package WPDesk\FS\TableRate\Importer
 */

namespace WPDesk\FS\TableRate\ImporterExporter\Importer;

use WPDesk\FS\TableRate\ImporterExporter\ShippingClassTrait;
use WPDesk_Flexible_Shipping;

/**
 * Class AbstractImporter
 *
 * @package WPDesk\FS\TableRate\Importer
 */
abstract class AbstractImporter implements Importer {
	use ShippingClassTrait;

	/**
	 * @var array .
	 */
	protected $file;

	/**
	 * @var WPDesk_Flexible_Shipping .
	 */
	protected $flexible_shipping_method;

	/**
	 * @var array .
	 */
	protected $shipping_methods;

	/**
	 * WPDesk_Flexible_Shipping_Csv_Importer constructor.
	 *
	 * @param array                    $file                     .
	 * @param WPDesk_Flexible_Shipping $flexible_shipping_method Flexible shipping method.
	 * @param array                    $shipping_methods         .
	 */
	public function __construct( array $file, WPDesk_Flexible_Shipping $flexible_shipping_method, array $shipping_methods ) {
		$this->file                     = $file;
		$this->shipping_methods         = $shipping_methods;
		$this->flexible_shipping_method = $flexible_shipping_method;

		$this->init_shipping_class();
	}

	/**
	 * Process import data.
	 */
	abstract public function import();

	/**
	 * @return array
	 */
	public function get_shipping_methods() {
		return $this->shipping_methods;
	}

	/**
	 * @param string $method_title .
	 *
	 * @return string
	 */
	protected function get_new_method_title( $method_title ) {
		$count = 0;

		$new_method_title = $method_title;

		while ( $this->flexible_shipping_method->shipping_method_title_used( $new_method_title, $this->shipping_methods ) ) {
			if ( 0 === $count ) {
				$new_method_title = sprintf( '%s (%s)', $method_title, __( 'import', 'flexible-shipping' ) );
			} else {
				$new_method_title = sprintf( '%s (%s %d)', $method_title, __( 'import', 'flexible-shipping' ), $count );
			}

			$count ++;
		}

		return $new_method_title;
	}
}
