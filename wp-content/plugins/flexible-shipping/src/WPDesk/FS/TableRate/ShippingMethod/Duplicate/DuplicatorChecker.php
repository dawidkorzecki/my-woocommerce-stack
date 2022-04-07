<?php
/**
 * Class DuplicatorChecker
 *
 * @package WPDesk\FS\TableRate\ShippingMethod\Duplicate
 */

namespace WPDesk\FS\TableRate\ShippingMethod\Duplicate;

use WC_Shipping_Zone;
use WPDesk\FS\TableRate\ShippingMethod\Management\ShippingMethodManagement;
use WPDesk\FS\TableRate\ShippingMethodSingle;

/**
 * Checker duplicating.
 */
class DuplicatorChecker {

	/**
	 * @var ShippingMethodManagement
	 */
	private $shipping_method_management;

	/**
	 * @param ShippingMethodManagement $shipping_method_management
	 */
	public function __construct( ShippingMethodManagement $shipping_method_management ) {
		$this->shipping_method_management = $shipping_method_management;
	}

	/**
	 * @param int $instance_id
	 *
	 * @return bool
	 */
	public function should_duplicate( int $instance_id ): bool {
		if ( ! $instance_id ) {
			return false;
		}

		$shipping_method = $this->shipping_method_management->get_shipping_method( $instance_id );
		$zone            = $this->shipping_method_management->get_shipping_zone( $instance_id );

		return $shipping_method instanceof ShippingMethodSingle && $zone instanceof WC_Shipping_Zone;
	}
}
