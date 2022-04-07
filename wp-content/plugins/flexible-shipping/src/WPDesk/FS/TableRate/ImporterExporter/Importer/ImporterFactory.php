<?php
/**
 * Class ImporterFactory
 *
 * @package WPDesk\FS\TableRate\Importer
 */

namespace WPDesk\FS\TableRate\ImporterExporter\Importer;

use WPDesk_Flexible_Shipping;

/**
 * Class ImporterFactory
 *
 * @package WPDesk\FS\TableRate\Importer
 */
class ImporterFactory {
	/**
	 * @var array
	 */
	private $file;

	/**
	 * @var WPDesk_Flexible_Shipping
	 */
	private $flexible_shipping_method;

	/**
	 * @var array
	 */
	private $shipping_methods;

	/**
	 * ImporterFactory constructor.
	 *
	 * @param array                    $file                     .
	 * @param WPDesk_Flexible_Shipping $flexible_shipping_method Flexible shipping method.
	 * @param array                    $shipping_methods         .
	 */
	public function __construct( $file, $flexible_shipping_method, $shipping_methods ) {
		$this->file                     = $file;
		$this->shipping_methods         = $shipping_methods;
		$this->flexible_shipping_method = $flexible_shipping_method;
	}

	/**
	 * @return AbstractImporter
	 *
	 * @throws UnsupportedFileFormat .
	 */
	public function get_importer() {
		switch ( $this->file['type'] ) {
			case 'application/json':
				return new JSON( $this->file, $this->flexible_shipping_method, $this->shipping_methods );
			default:
				return new CSV( $this->file, $this->flexible_shipping_method, $this->shipping_methods );
		}
	}
}
