<?php
namespace OneTeamSoftware\WooCommerce\ShippingPackages\ProductsFinder;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WC_Composite_Products_Finder
{
	private $finderHelper;

    public function __construct()
    {
		include_once __DIR__ . "/FinderHelper.php";

		$this->finderHelper = new FinderHelper();
    }

    public function register()
    {
		add_filter('wc_shipping_packages_item_keys', array($this, 'onItemKeys'), 10, 2);
    }

    public function onItemKeys($inItemKeys, $cartItems)
    {
        if (!class_exists('\WC_Composite_Products')) {
            return $inItemKeys;
        }

		return $this->finderHelper->findByBundleProperty('composite_children', $inItemKeys, $cartItems);
	}
}

(new WC_Composite_Products_Finder())->register();
