<?php

/**
 * Single label file dispatcher.
 *
 * @package Flexible Shipping
 */
namespace FSVendor\WPDesk\FS\Shipment\Label;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can dispatch single label file.
 */
class SingleLabelFileDispatcher implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const PRIORITY_VERY_LOW = 9999999;
    const DEFAULT_CONTENT_TYPE = 'application/octet-stream';
    const ACTION_DOWNLOAD = 'download';
    const ACTION_OPEN = 'open';
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('admin_post_flexible_shipping_get_label', array($this, 'dispatch_label_if_requested'), self::PRIORITY_VERY_LOW);
    }
    /**
     * @param array $label_data .
     *
     * @return string
     */
    private function prepare_content_type_from_label_data(array $label_data)
    {
        if ('html' === $label_data['label_format']) {
            return 'text/html';
        }
        return 'application/' . $label_data['label_format'];
    }
    /**
     * @return string
     */
    private function get_action_parameter()
    {
        return !empty($_GET['label_action']) ? \sanitize_text_field(\wp_unslash($_GET['label_action'])) : self::ACTION_DOWNLOAD;
        // phpcs:ignore
    }
    /**
     * @param array $label_data .
     *
     * @return string
     */
    private function prepare_content_type(array $label_data)
    {
        if ($this->get_action_parameter() === self::ACTION_OPEN) {
            return $this->prepare_content_type_from_label_data($label_data);
        } else {
            return self::DEFAULT_CONTENT_TYPE;
        }
    }
    /**
     * @param string $shipment_id .
     */
    private function dispatch_label_and_die($shipment_id)
    {
        $shipment = fs_get_shipment($shipment_id);
        $label_data = $shipment->get_label();
        \header('Content-type: ' . $this->prepare_content_type($label_data));
        if ($this->get_action_parameter() === self::ACTION_DOWNLOAD) {
            \header('Content-Disposition: attachment; filename=' . $label_data['file_name']);
        }
        echo $label_data['content'];
        // phpcs:ignore
        die;
    }
    /**
     * Output shipping label.
     *
     * @internal
     */
    public function dispatch_label_if_requested()
    {
        \check_admin_referer('flexible_shipping_get_label');
        $shipment_id = \filter_input(\INPUT_GET, 'shipment_id', \FILTER_VALIDATE_INT);
        $integration = \get_post_meta($shipment_id, '_integration', \true);
        if (!$integration) {
            \wp_die(\__('Integration doesn\'t exists.', 'flexible-shipping'));
            // phpcs:ignore
        }
        try {
            $this->dispatch_label_and_die($shipment_id);
        } catch (\Exception $e) {
            \wp_die(\esc_html($e->getMessage()), \esc_html(\__('Label error', 'flexible-shipping')));
        }
    }
}
