<?php

namespace FSVendor\WPDesk\FS\Shipment\RestApi;

/**
 * Defines interface that REST API Order Data Provider should implement.
 */
interface OrderDataProvider
{
    /**
     * Get data from shipment.
     *
     * @param \WPDesk_Flexible_Shipping_Shipment $shipment .
     *
     * @return array
     */
    public function get_data_from_shipment($shipment);
}
