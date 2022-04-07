<?php

namespace FSVendor\WPDesk\FS\Shipment\RestApi;

use WC_Order;
use WP_REST_Request;
use WP_REST_Response;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can append shipments data to WooCommerce REST API Order response.
 */
class OrderResponseDataAppender implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const REST_API_DATA_KEY = 'fs_shipping_lines';
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_filter('woocommerce_rest_prepare_shop_order_object', array($this, 'maybe_append_shipment_to_order_data'), 10, 3);
    }
    /**
     * Appends shipment data if exists to order in REST API response.
     *
     * @param WP_REST_Response $response .
     * @param WC_Order $order .
     * @param WP_REST_Request $request .
     *
     * @return WP_REST_Response
     */
    public function maybe_append_shipment_to_order_data($response, $order, $request)
    {
        $shipments = fs_get_order_shipments($order->get_id());
        if (!empty($shipments)) {
            return $this->append_shipment_to_order_data($response, $order, $request, $shipments);
        }
        return $response;
    }
    /**
     * Appends shipment data to order in REST API response.
     *
     * @param WP_REST_Response $response .
     * @param WC_Order $order .
     * @param WP_REST_Request $request .
     * @param \WPDesk_Flexible_Shipping_Shipment[] $shipments .
     *
     * @return WP_REST_Response
     */
    private function append_shipment_to_order_data($response, $order, $request, $shipments)
    {
        $response_data = $response->get_data();
        if (empty($response_data[self::REST_API_DATA_KEY])) {
            $response_data[self::REST_API_DATA_KEY] = array();
        }
        $providers = \FSVendor\WPDesk\FS\Shipment\RestApi\OrderDataProvidersFactory::get_providers();
        foreach ($shipments as $shipment) {
            $integration = $shipment->get_integration();
            $data_provider = $providers->get_provider_for_integration($integration);
            $response_data[self::REST_API_DATA_KEY][] = $data_provider->get_data_from_shipment($shipment);
        }
        $response->set_data($response_data);
        return $response;
    }
}
