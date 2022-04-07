<?php

/**
 * Labels builders.
 *
 * @package Flexible Shipping
 */
namespace FSVendor\WPDesk\FS\Shipment\Label;

/**
 * Can create labels for shipments.
 */
class LabelsBulkActionHandler
{
    /**
     * Builders.
     *
     * @var LabelsBulkActionHandler
     */
    private static $labels_builder;
    /**
     * Labels builders collection.
     *
     * @var \WPDesk_Flexible_Shipping_Labels_Builder[]
     */
    private $builders_collection = array();
    /**
     * Shipments.
     *
     * @var \WPDesk_Flexible_Shipping_Shipment_Interface[]
     */
    private $shipments = array();
    /**
     * Add builder to builders collection.
     *
     * @param \WPDesk_Flexible_Shipping_Labels_Builder $builder .
     */
    public function add_builder($builder)
    {
        $this->builders_collection[$builder->get_integration_id()] = $builder;
    }
    /**
     * Bulk process orders.
     *
     * @param array $orders_ids .
     */
    public function bulk_process_orders(array $orders_ids)
    {
        foreach ($orders_ids as $order_id) {
            $shipments = fs_get_order_shipments($order_id);
            foreach ($shipments as $shipment) {
                $this->add_shipment($shipment);
            }
        }
    }
    /**
     * Add shipment to labels builder.
     *
     * @param \WPDesk_Flexible_Shipping_Shipment $shipment .
     */
    public function add_shipment($shipment)
    {
        if (isset($this->builders_collection[$shipment->get_integration()])) {
            $this->builders_collection[$shipment->get_integration()]->add_shipment($shipment);
        } else {
            $this->shipments[] = $shipment;
        }
    }
    /**
     * Get labels for shipments from builders.
     *
     * @return array
     */
    private function get_labels_for_shipments_from_builders()
    {
        $labels = array();
        foreach ($this->builders_collection as $labels_builder) {
            $labels += $labels_builder->get_labels_for_shipments();
        }
        return $labels;
    }
    /**
     * Get labels for shipments.
     *
     * @return array
     * @throws \Exception
     */
    public function get_labels_for_shipments()
    {
        $labels = $this->get_labels_for_shipments_from_builders();
        foreach ($this->shipments as $shipment) {
            $labels[] = $shipment->get_label();
        }
        return $labels;
    }
    /**
     * Get builders.
     *
     * @return LabelsBulkActionHandler
     */
    public static function get_labels_bulk_actions_handler()
    {
        if (empty(static::$labels_builder)) {
            static::$labels_builder = new self();
        }
        return static::$labels_builder;
    }
}
