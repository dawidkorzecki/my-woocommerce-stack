<?php
/**
 * Class ShippingMethodManagement
 *
 * @package WPDesk\FS\TableRate\ShippingMethod\Management
 */

namespace WPDesk\FS\TableRate\ShippingMethod\Management;

use WC_Shipping_Zone;
use WC_Shipping_Zones;
use wpdb;
use WPDesk\FS\TableRate\ShippingMethodSingle;

/**
 * @codeCoverageIgnore
 */
class ShippingMethodManagement {

	/**
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * @param int              $instance_id .
	 * @param bool             $status      .
	 * @param WC_Shipping_Zone $zone        .
	 */
	public function set_shipping_method_status( int $instance_id, bool $status, WC_Shipping_Zone $zone ) {
		$status = $status ? 1 : 0;

		if ( $this->update_shipping_method_field( $instance_id, 'is_enabled', $status ) ) {
			$shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );

			do_action( 'woocommerce_shipping_zone_method_status_toggled', $shipping_method->instance_id, $shipping_method->id, $zone->get_id(), $status );
		}
	}

	/**
	 * @param int $zone_id                    .
	 * @param int $method_order               .
	 * @param int $number_of_shipping_methods .
	 */
	public function update_shipping_methods_order( int $zone_id, int $method_order, int $number_of_shipping_methods ) {
		$this->wpdb->query( $this->wpdb->prepare( sprintf( 'UPDATE `%s` SET `method_order` = `method_order`+%%d WHERE `zone_id` = %%d AND `method_order` > %%d', $this->get_table() ), $number_of_shipping_methods, $zone_id, $method_order ) ); // phpcs:ignore
	}

	/**
	 * @param int $instance_id .
	 *
	 * @return int
	 */
	public function get_shipping_method_order( int $instance_id ): int {
		return (int) $this->wpdb->get_var( $this->wpdb->prepare( sprintf( 'SELECT `method_order` FROM `%s` WHERE `instance_id` = %%d ORDER BY `method_order` ASC', $this->get_table() ), $instance_id ) ); // phpcs:ignore
	}

	/**
	 * @param int $instance_id .
	 *
	 * @return bool|ShippingMethodSingle
	 */
	public function get_shipping_method( int $instance_id ) {
		return WC_Shipping_Zones::get_shipping_method( $instance_id );
	}

	/**
	 * @param int $instance_id .
	 *
	 * @return bool|WC_Shipping_Zone
	 */
	public function get_shipping_zone( int $instance_id ) {
		return WC_Shipping_Zones::get_zone_by( 'instance_id', $instance_id );
	}

	/**
	 * @param int    $instance_id .
	 * @param string $key         .
	 * @param string $value       .
	 *
	 * @return bool|int
	 */
	public function update_shipping_method_field( int $instance_id, string $key, string $value ) {
		return $this->wpdb->update( $this->get_table(), [ $key => $value ], [ 'instance_id' => $instance_id ] );
	}

	/**
	 * @return string
	 */
	private function get_table(): string {
		return $this->wpdb->prefix . 'woocommerce_shipping_zone_methods';
	}
}
