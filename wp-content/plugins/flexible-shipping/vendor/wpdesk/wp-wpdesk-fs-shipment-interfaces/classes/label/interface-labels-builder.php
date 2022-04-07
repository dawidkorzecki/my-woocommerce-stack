<?php
/**
 * Label builder.
 *
 * @package Flexible Shipping
 */

/**
 * Interface for labels builder.
 */
interface WPDesk_Flexible_Shipping_Labels_Builder {

	/**
	 * Get integration id.
	 *
	 * @return string
	 */
	public function get_integration_id();

	/**
	 * Get labels for shipments.
	 *
	 * @return array
	 * Returns array of labels. Each label is array with fields:
	 *  content: label file content (ie. pdf file content) as string
	 *  file_name: label file name
	 */
	public function get_labels_for_shipments();

	/**
	 * .
	 *
	 * @param WPDesk_Flexible_Shipping_Shipment $shipment .
	 */
	public function add_shipment( WPDesk_Flexible_Shipping_Shipment $shipment );

}
