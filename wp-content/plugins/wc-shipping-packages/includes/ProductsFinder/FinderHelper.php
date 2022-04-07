<?php
namespace OneTeamSoftware\WooCommerce\ShippingPackages\ProductsFinder;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class FinderHelper
{
	protected $id;
	protected $settings;
	
	public function __construct()
    {
        $this->id = 'wc_shipping_packages';
        $this->settings = get_option('woocommerce_' . $this->id . '_settings', array(
            'enabled' => 'no',
            'shippingRestrictions' => array(),
            'groupBy' => array('shipping_class'),
            'debug' => 'no',
        ));
	}
	
    public function findByBundleProperty($bundleProperty, $inItemKeys, $cartItems)
    {
		$this->debug("inItemKeys: " . implode(", ", $inItemKeys));

        // 1. we need to find products that are bundled (children)
        // 2. we need to find bundle product (parent) of the child
		$this->debug("Bundle property: $bundleProperty");

		$outItemKeys = array();
		$inItemKeysCount = count($inItemKeys);
		
		if (empty($cartItems)) {
			$cartItems = WC()->cart->get_cart();
		}

        foreach ($cartItems as $parentKey => $cartItem) {
			// try to see if item is a bundle
            $children = empty($cartItem[$bundleProperty]) ? array() : $cartItem[$bundleProperty];

            foreach ($children as $childKey) {
				// if in-item is a parent and child is not in in-items list and parent item has to be shipped, but child item doesn't need to be shipped
                if (isset($inItemKeys[$parentKey]) && empty($inItemKeys[$childKey]) && $cartItems[$parentKey]['data']->needs_shipping() && $cartItems[$childKey]['data']->needs_shipping() == false) {
					// we've found a child item
                    if (empty($outItemKeys[$parentKey])) {
						// parent should be the first item in the list
						$outItemKeys[$parentKey] = $parentKey;

						$this->debug("Add parent #1: $parentKey");
                    }

					$outItemKeys[$childKey] = $childKey;

					$this->debug("Add child #1: $childKey");
				// if in-item is a child and parent is not in in-items list and parent does not need to be shipped
                } else if (isset($inItemKeys[$childKey]) && empty($inItemKeys[$parentKey]) && $cartItems[$parentKey]['data']->needs_shipping() == false) { 
					// we've found a parent item
                    $outItemKeys[$parentKey] = $parentKey;
					$outItemKeys[$childKey] = $childKey;

					unset($inItemKeys[$childKey]);

					$this->debug("Add parent #2: $parentKey");
					$this->debug("Add child #2: $childKey");
                }
            }

			// if parent items is still present in in-items list
			if (isset($inItemKeys[$parentKey])) {
				// make sure that item will be present in out-items list
				if (empty($outItemKeys[$parentKey])) {
					$outItemKeys[$parentKey] = $parentKey;

					$this->debug("Move #3: $parentKey to outItemKeys");
				}

				// we don't want it in the in-items list anymore
				unset($inItemKeys[$parentKey]);
			}

            if (empty($inItemKeys)) { // all in-items are processed, so we can stop
                break;
            }
        }

        return $outItemKeys;
	}
	
    protected function debug($message, $type = 'notice')
    {
        if ($this->settings['debug'] != 'yes') {
            return;
        }

        if (!empty($this->settings['debugType']) && $this->settings['debugType'] === 'notice' && function_exists('wc_add_notice')) {
            wc_add_notice($message, $type);
        } else {
            error_log("$type: $message\n");
        }
    }
}

