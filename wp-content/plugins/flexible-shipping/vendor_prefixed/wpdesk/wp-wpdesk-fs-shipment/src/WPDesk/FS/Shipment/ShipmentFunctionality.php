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
class ShipmentFunctionality implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const LOAD_PRIORITY = -1;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $assets_url;
    /**
     * @var string
     */
    private $scripts_version;
    /**
     * @var bool
     */
    private $already_loaded = \false;
    /**
     * ShipmentFunctionality constructor.
     *
     * @param LoggerInterface $logger .
     * @param string          $assets_url .
     * @param string          $scripts_version .
     */
    public function __construct(\Psr\Log\LoggerInterface $logger, $assets_url, $scripts_version)
    {
        $this->logger = $logger;
        $this->assets_url = $assets_url;
        $this->scripts_version = $scripts_version;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('plugins_loaded', [$this, 'load_functionality_on_init'], self::LOAD_PRIORITY);
        \add_filter('flexible-shipping/shipment/load-functionality', [$this, 'load_functionality_if_not_already_loaded']);
    }
    /**
     * @internal
     */
    public function load_functionality_on_init()
    {
        $this->already_loaded = (bool) \apply_filters('flexible-shipping/shipment/load-functionality', $this->already_loaded);
    }
    /**
     * Load functionality if not already loaded;
     *
     * @param bool $already_loaded .
     *
     * @return bool
     *
     * @internal
     */
    public function load_functionality_if_not_already_loaded($already_loaded)
    {
        $class = 'WPDesk_Flexible_Shipping_Shipment';
        if (!$already_loaded && !\class_exists($class)) {
            $this->load_functionality();
        }
        $this->already_loaded = \true;
        return \true;
    }
    /**
     * Load functionalituy.
     */
    private function load_functionality()
    {
        $this->load_dependencies();
        $class = 'WPDesk_Flexible_Shipping_Shipment';
        $class::set_fs_logger($this->logger);
        $shipment_cpt = new \FSVendor\WPDesk\FS\Shipment\CustomPostType();
        $shipment_cpt->hooks();
        $subscriptions_integration = new \FSVendor\WPDesk\FS\Shipment\Subscriptions\SubscriptionsIntegration($shipment_cpt);
        $subscriptions_integration->hooks();
        $add_shipping_metabox = new \FSVendor\WPDesk\FS\Shipment\Order\AddShippingMetabox();
        $add_shipping_metabox->hooks();
        $single_label_file_dispatcher = new \FSVendor\WPDesk\FS\Shipment\Label\SingleLabelFileDispatcher();
        $single_label_file_dispatcher->hooks();
        $metabox_ajax = new \FSVendor\WPDesk\FS\Shipment\Metabox\Ajax();
        $metabox_ajax->hooks();
        $manifest_cpt = new \FSVendor\WPDesk\FS\Shipment\Manifest\ManifestCustomPostType();
        $manifest_cpt->hooks();
        $rest_api_order_response_data_appender = new \FSVendor\WPDesk\FS\Shipment\RestApi\OrderResponseDataAppender();
        $rest_api_order_response_data_appender->hooks();
        $assets = new \FSVendor\WPDesk\FS\Shipment\Assets($this->assets_url, $this->scripts_version);
        $assets->hooks();
    }
    /**
     * Load dependencies.
     */
    private function load_dependencies()
    {
        $interfaces_dir = __DIR__ . '/../../../../../../../vendor/wpdesk/wp-wpdesk-fs-shipment-interfaces';
        require_once $interfaces_dir . '/classes/shipment/interface-shipment.php';
        require_once $interfaces_dir . '/classes/shipment/class-shipment.php';
        require_once $interfaces_dir . '/classes/shipment/functions.php';
        require_once $interfaces_dir . '/classes/manifest/functions.php';
        require_once $interfaces_dir . '/classes/exception/class-cancel-shipment-exception.php';
        require_once $interfaces_dir . '/classes/exception/class-get-label-exception.php';
        require_once $interfaces_dir . '/classes/exception/class-label-not-available-exception.php';
        require_once $interfaces_dir . '/classes/exception/class-send-shipment-exception.php';
        require_once $interfaces_dir . '/classes/exception/class-shipment-plan-exceeded-exception.php';
        require_once $interfaces_dir . '/classes/exception/class-shipment-unable-to-create-tmp-file-exception.php';
        require_once $interfaces_dir . '/classes/exception/class-shipment-unable-to-create-tmp-zip-file-exception.php';
        require_once $interfaces_dir . '/classes/label/interface-labels-builder.php';
        require_once $interfaces_dir . '/classes/label/class-integration-label-builder.php';
        require_once $interfaces_dir . '/classes/manifest/interface-manifest.php';
        require_once $interfaces_dir . '/classes/manifest/class-manifest.php';
        require_once $interfaces_dir . '/classes/manifest/class-manifest-fs.php';
        require_once $interfaces_dir . '/classes/manifest/functions.php';
    }
}
