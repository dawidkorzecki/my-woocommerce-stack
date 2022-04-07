<?php

/**
 * Class ShipmentFunctionality
 *
 * @package WPDesk\FS\Shipment
 */
namespace FSVendor\WPDesk\FS\Shipment;

use Psr\Log\LoggerInterface;
use FSVendor\WPDesk\FS\Shipment\Label\SingleLabelFileDispatcher;
use FSVendor\WPDesk\FS\Shipment\Manifest\ManifestCustomPostType;
use FSVendor\WPDesk\FS\Shipment\Metabox\Ajax;
use FSVendor\WPDesk\FS\Shipment\Order\AddShippingMetabox;
use FSVendor\WPDesk\FS\Shipment\RestApi\OrderResponseDataAppender;
use FSVendor\WPDesk\FS\Shipment\Subscriptions\SubscriptionsIntegration;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can load shipment functionality.
 */
class Assets implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var string
     */
    private $assets_url;
    /**
     * @var string
     */
    private $scripts_version;
    /**
     * Assets constructor.
     *
     * @param string $assets_url .
     * @param string $scripts_version .
     */
    public function __construct($assets_url, $scripts_version)
    {
        $this->assets_url = $assets_url;
        $this->scripts_version = $scripts_version;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    }
    /**
     * Admin enqueue scripts.
     */
    public function admin_enqueue_scripts()
    {
        if ($this->should_enqueue_admin_scripts()) {
            \wp_register_script('fs_shipment_admin', \trailingslashit($this->assets_url) . 'js/admin.js', array('jquery'), $this->scripts_version);
            \wp_localize_script('fs_shipment_admin', 'fs_shipment_admin', array('ajax_url' => \admin_url('admin-ajax.php')));
            \wp_enqueue_script('fs_shipment_admin');
            \wp_enqueue_style('fs_shipment_admin', \trailingslashit($this->assets_url) . 'css/admin.css', array(), $this->scripts_version);
        }
    }
    /**
     * Should enqueue admin scripts?
     */
    private function should_enqueue_admin_scripts()
    {
        $current_screen = \get_current_screen();
        $wc_screen_id = \sanitize_title(\__('WooCommerce', 'woocommerce'));
        if (\in_array($current_screen->post_type, array('shop_order', 'shop_subscription'), \true) || $wc_screen_id . '_page_wc-settings' === $current_screen->id) {
            return \true;
        }
        return \false;
    }
}
