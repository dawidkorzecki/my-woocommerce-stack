<?php
/**
 * Labels builder.
 *
 * @package Flexible Shipping
 */

/**
 * Abstract for labels builders.
 */
abstract class WPDesk_Flexible_Shipping_Integration_Label_Builder implements WPDesk_Flexible_Shipping_Labels_Builder {

	/**
	 * Integration ID.
	 *
	 * @var string
	 */
	private $integration_id;

	/**
	 * Shipments.
	 *
	 * @var WPDesk_Flexible_Shipping_Shipment_Interface[]
	 */
	protected $shipments = array();

	/**
	 * .
	 *
	 * @param string $integration_id .
	 */
	public function __construct( $integration_id ) {
		$this->integration_id = $integration_id;
	}

	/**
	 * Get integration id.
	 *
	 * @return string
	 */
	public function get_integration_id() {
		return $this->integration_id;
	}

	/**
	 * .
	 *
	 * @param WPDesk_Flexible_Shipping_Shipment $shipment .
	 */
	public function add_shipment( WPDesk_Flexible_Shipping_Shipment $shipment ) {
		$this->shipments[] = $shipment;
	}

	/**
	 * Get labels for shipments.
	 *
	 * @return array
	 */
	abstract public function get_labels_for_shipments();

}
