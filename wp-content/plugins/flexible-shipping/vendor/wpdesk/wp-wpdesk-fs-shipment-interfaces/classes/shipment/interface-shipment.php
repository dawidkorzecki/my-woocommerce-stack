<?php
/**
 * Interface WPDesk_Flexible_Shipping_Shipment_Interface
 *
 * @package Flexible Shipping
 */

/**
 * Shipping.
 */
interface WPDesk_Flexible_Shipping_Shipment_Interface {

	/**
	 * @param string      $meta_key .
	 * @param null|string $default .
	 *
	 * @return array|string|null
	 */
	public function get_meta( $meta_key = '', $default = null );

	/**
	 * @param string                       $meta_key .
	 * @param int|string|array|object|null $value .
	 */
	public function set_meta( $meta_key, $value );

	/**
	 * @return array
	 */
	public function get_meta_data();

	/**
	 * @param array $fs_method .
	 * @param array $package .
	 *
	 * @return void
	 * Executes on woocommerce checkout when order is created
	 */
	public function checkout( array $fs_method, $package );

	/**
	 * Displays metabox in woocommerce order.
	 *
	 * @return void
	 */
	public function order_metabox();

	/**
	 * Returns woocommerce metabox title.
	 *
	 * @return string
	 */
	public function get_order_metabox_title();

	/**
	 * @param string $action .
	 * @param array  $data .
	 *
	 * @return void
	 * Executes on ajax request. $data contains all woocommerce order metabox fields values from metabox generated in order_metabox() method.
	 */
	public function ajax_request( $action, $data );

	/**
	 * @return string
	 * Returns error message
	 */
	public function get_error_message();

	/**
	 * @return string
	 * Returns tracking number for shipment
	 */
	public function get_tracking_number();

	/**
	 * @return string
	 * Returns tracking URL for shipping
	 */
	public function get_tracking_url();

	/**
	 * @return array
	 * Return label data foe shipping in array:
	 *      'label_format' => 'pdf'
	 *      'content' => pdf content,
	 *      'file_name' => file name for label
	 */
	public function get_label();

	/**
	 * @return mixed
	 */
	public function get_after_order_table();

	/**
	 * @return mixed
	 */
	public function get_email_after_order_table();

}
