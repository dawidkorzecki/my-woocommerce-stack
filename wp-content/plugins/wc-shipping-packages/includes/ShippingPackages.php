<?php

namespace OneTeamSoftware\WooCommerce\ShippingPackages;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ShippingPackages
{
	protected $id;
	protected $mainMenuId;

	protected $settings;
	protected $shippingPackages;
	protected $extraPackages;

    public function __construct()
    {
		$this->id = 'wc_shipping_packages';
		$this->mainMenuId = 'oneteamsoftware';
		$this->settings = get_option('woocommerce_' . $this->id . '_settings', array());
		$this->settings = array_merge(array(
			'enabled' => 'no',
			'shippingMethodPer' => 'package',
            'shippingRestrictions' => array(),
            'groupBy' => array('shipping_class'),
			'debug' => 'no',
			'allowFreeShipping' => 'coupon',
			'useAutoPackageName' => 'no',
			'packageNamePartsGlue' => ', '
		), $this->settings);
		
		$this->shippingPackages = array();
		$this->extraPackages = array();
	}

    public function register()
    {
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');

		// do not register when WooCommerce is not enabled
		if (!is_plugin_active('woocommerce/woocommerce.php')) {
			return false;
		}

		add_filter('plugin_action_links_' . plugin_basename(realpath(__DIR__ . '/../wc-shipping-packages.php')), array($this, 'onPluginActionLinks'), 1, 1);

        // The settings are needed only when in the admin area.
        if (is_admin()) {
			require_once(__DIR__ . '/Admin/OneTeamSoftware.php');
			\OneTeamSoftware\WooCommerce\Admin\OneTeamSoftware::instance()->register();

            add_action('woocommerce_shipping_init', array($this, 'onShippingInit'));

            // Add it to the shipping methods, so we can admin it
			add_filter('woocommerce_shipping_methods', array($this, 'onShippingMethods'));
			add_action('admin_menu', array($this, 'onAdminMenu'));

        } else if ($this->settings['enabled'] == 'yes') {
			add_filter('woocommerce_package_rates', array($this, 'onPackageRates'), PHP_INT_MAX, 2);
			add_filter('woocommerce_cart_shipping_packages', array($this, 'onCartShippingPackages'), PHP_INT_MAX, 1);
			add_filter('woocommerce_shipping_packages', array($this, 'onShippingPackages'), PHP_INT_MAX, 1);
			add_filter('woocommerce_shipping_package_name', array($this, 'onPackageName'), 1000, 3);

			add_action('woocommerce_calculate_totals', array($this, 'onCalculateTotals'), PHP_INT_MAX, 1);

			// used to get all possible packages we've generated
			add_filter('woocommerce_cart_packages', array($this, 'onCartPackages'), 1, 2);

            include_once __DIR__ . '/ProductsFinder/WC_Composite_Products.php';
            include_once __DIR__ . '/ProductsFinder/WC_Product_Bundle.php';
        }
    }

	public function onAdminMenu()
	{
		add_submenu_page($this->mainMenuId, __('Shipping Packages', $this->id), __('Shipping Packages', $this->id), 'manage_options', 'admin.php?page=wc-settings&tab=shipping&section=wc_shipping_packages');
	}

	public function onPluginActionLinks($links)
	{
		$link = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wc-settings&tab=shipping&section=wc_shipping_packages'), __('Settings', $this->id));
		array_unshift($links, $link);

		return $links;
	}

    public function onShippingInit()
    {
        include_once __DIR__ . '/ShippingMethod.php';
    }

    public function onShippingMethods($methods)
    {
        $methods['ShippingPackages'] = '\OneTeamSoftware\WooCommerce\ShippingPackages\ShippingMethod';
        return $methods;
    }

	public function onPackageName($packageName, $idx, $package)
	{
		if ($this->settings['useAutoPackageName'] == 'yes' && isset($package['packageName'])) {
			$packageName = $package['packageName'];
		}
		
		return $packageName;
	}

	public function onPackageRates($rates, $package)
	{
		$this->debug('onPackageRates');

		$vendorId = 0;
		if (!empty($package['seller_id'])) {
			$vendorId = $package['seller_id'];
		} else if (!empty($package['vendor_id'])) {
			$vendorId = $package['vendor_id'];
		}

		if (!empty($vendorId)) {
			$this->debug('Add vendor ID: ' . $vendorId . ' to rates');

			foreach ($rates as &$rate) {
				if (is_a($rate, 'WC_Shipping_Rate')) {
					$rate->add_meta_data('seller_id', $vendorId);
					$rate->add_meta_data('vendor_id', $vendorId);
				}
			}
		}

		return $rates;
	}

	public function onCartPackages($packages, $cartItems = array())
	{
		$this->debug("onCartPackages");
		
		$packages = $this->onCartShippingPackages($packages, $cartItems);

		if (WC()->shipping) {
			if (empty(WC()->shipping->packages)) {
				WC()->shipping->calculate_shipping($packages);
			} else {
				$this->onShippingPackages(WC()->shipping->packages);
			}
		}

		return array_values(array_merge($this->shippingPackages, $this->extraPackages));
	}

	public function onCalculateTotals()
	{
		$this->debug("onCalculateTotals");

		if (!did_action('woocommerce_set_cart_packages')) {
			$cartPackages = array_merge($this->shippingPackages, $this->extraPackages);

			do_action('woocommerce_set_cart_packages', array_values($cartPackages));	
		}
	}

	public function onShippingPackages($packages)
	{
		$this->debug("onShippingPackages");

		// at this point packages will have shipping methods set, so update them
		foreach ($packages as $package) {
			if (isset($package['packageKey']) && !empty($package['rates'])) {
				$packageKey = $package['packageKey'];
				
				$this->shippingPackages[$packageKey]['rates'] = $package['rates'];
			}
		}

		return $packages;
	}

	protected function buildPackages($cartItems)
	{
		$this->debug("buildPackages");

		$shippingPackages = array();
		$extraPackages = array();

		$packageItemKeys = $this->getPackageItemKeys($cartItems);
		
		// find all related item keys and add to the package
		foreach ($packageItemKeys as $packageKey => $item) {
			$package = $this->createPackage($packageKey, $item['packageName']);
			$package = $this->addItemsToPackage($package, $cartItems, $item['itemKeys']);

			if ($package['needs_shipping']) {
				$shippingPackages[$packageKey] = $package;
			} else {
				$extraPackages[$packageKey] = $package;
			}
		}

		$this->debug("We have prepared " . count($shippingPackages) . " shipping packages and " . count($extraPackages) . " extra packages");

		return array($shippingPackages, $extraPackages);
	}

	protected function combinePackages($inPackages)
	{
		$outPackage = array();

		foreach ($inPackages as $package) {
			if (empty($outPackage)) {
				$outPackage = $package;
			} else {
				$outPackage['contents'] += $package['contents'];
				$outPackage['contents_cost'] += $package['contents_cost'];
			}
		}

		return array($outPackage);
	}

    public function onCartShippingPackages($packages, $cartItems = array())
    {
        $this->debug("onCartShippingPackages: " . count($packages) . ", " . count($cartItems));

        if ($this->settings['enabled'] != 'yes') {
            return $packages;
        }

		if (empty($cartItems)) {
			$cartItems = WC()->cart->get_cart();
		}

		list($this->shippingPackages, $this->extraPackages) = $this->buildPackages($cartItems);

		if ($this->settings['shippingMethodPer'] == 'cart') {			
			return $this->combinePackages($this->shippingPackages);
		}

        return array_values($this->shippingPackages);
	}

	protected function getPackageItemKeys($cartItems)
	{
		$this->debug("getPackageItemKeys");

		$packageItemKeys = array();

		// pre-build list of item keys per package
        foreach ($cartItems as $itemKey => $cartItem) {
			if (isset($cartItem['data'])) {
				$packageKeyParts = $this->getPackageKeyParts($cartItem);
				$packageKey = $this->getPackageKeyAsString($packageKeyParts, 'key', '-');
				
				$packageItemKeys[$packageKey]['packageKey'] = $packageKey;
				$packageItemKeys[$packageKey]['packageName'] = $this->getPackageKeyAsString($packageKeyParts, 'name', $this->settings['packageNamePartsGlue']);
				$packageItemKeys[$packageKey]['itemKeys'][$itemKey] = $itemKey;	
			}
		}

		$packageItemKeys = apply_filters($this->id . '_package_item_keys', $packageItemKeys, $cartItems);

		// sort by key so order will be always consistent
		ksort($packageItemKeys);

		$this->debug("Package keys: " . print_r(array_keys($packageItemKeys), true));

		return $packageItemKeys;
	}

	protected function applyCouponsToCartItem($cartItem)
	{
		//$this->debug("applyCouponsToCartItem");

		$product = $cartItem['data'];

		foreach (WC()->cart->applied_coupons as $couponCode) {
			$this->debug("try coupon: $couponCode");
			$coupon = new \WC_Coupon($couponCode);

			if (!isset($cartItem['applied_coupons'])) {
				$cartItem['applied_coupons'] = array();
			}

			if ($coupon->is_valid_for_product($product)) {
				$this->debug("Coupon is valid for the item");

				$cartItem['applied_coupons'][] = $couponCode;

				if ($coupon->get_free_shipping()) {
					$this->debug("Coupon offers free shipping");

					$cartItem['free_shipping'] = true;
				}
			}
		}

		return $cartItem;
	}

	protected function getAllItemKeys($cartItems, $itemKeys)
	{
		$this->debug("getAllItemKeys");

		// we will count iterations to prevent possibility of infinite loops, in case of bogus implementations of the item_keys filter
		$iterations = 0;
		$maxIterations = 5;
		do {
			$itemKeysCount = count($itemKeys);

			$itemKeys = apply_filters($this->id . '_item_keys', $itemKeys, $cartItems);

			$newItemKeysCount = count($itemKeys);
			$this->debug("Original number of items: $itemKeysCount, new number of items: $newItemKeysCount");

			if ($newItemKeysCount > $itemKeysCount) {
				$this->debug("Number of items has increased, try to find more");
			}

		} while ($newItemKeysCount > $itemKeysCount && ++$iterations < $maxIterations);

		return $itemKeys;
	}

	protected function getPackageKeyAsString($packageKeyParts, $partKey, $glue)
	{
		$value = '';

		foreach ((array)$packageKeyParts as $packageKeyPart) {
			if (!empty($packageKeyPart[$partKey])) {
				if (!empty($value)) {
					$value .= $glue;
				}
	
				$value .= implode($glue, $packageKeyPart[$partKey]);	
			}
		}

		//$this->debug("Package $partKey: $value");

		return $value;
	}

    protected function getPackageKeyParts($cartItem)
    {
		$packageKeyParts = array();

        foreach ((array) $this->settings['groupBy'] as $groupBy) {
            $packageKeyPart = $this->getPackageKeyPart($cartItem, $groupBy);
            if (!empty($packageKeyPart)) {
				$packageKeyParts[] = $packageKeyPart;
            }
		}

		$packageKeyParts = apply_filters($this->id . '_package_key_parts', $packageKeyParts, $cartItem);

		//$this->debug("Package Key Parts: " . print_r($packageKeyParts, true));

		return $packageKeyParts;
    }

    protected function getPackageKeyPart($cartItem, $groupBy)
    {
		$product = $cartItem['data'];
		$parentProduct = null;
		if (method_exists($product, 'get_parent_id') && $product->get_parent_id() != 0) {
			$parentProduct = wc_get_product($product->get_parent_id());
		}

		if (empty($product)) {
			return false;
		}

		$packageKey = array();
		$packageName = array();

        switch ($groupBy) {
			default:
				if (strpos($groupBy, 'postmeta_') === 0) {
					$metaKey = substr($groupBy, strlen('postmeta_'));
					$postMetas = get_post_meta($product->get_id(), $metaKey);
					// try to find metas in the parent product
					if (empty($postMetas) && method_exists($product, 'get_parent_id')) {
						$parentId = $product->get_parent_id();
						if (!empty($parentId)) {
							$postMetas = get_post_meta($parentId, $metaKey); 
						}
					}

					if (!empty($postMetas)) {
						$packageName = $packageKey = array_values($postMetas);
					}

					break;
				} else if (strpos($groupBy, 'taxonomy_') === 0) {
					$taxonomyName = substr($groupBy, strlen('taxonomy_'));
					$terms = get_the_terms($product->get_id(), $taxonomyName);
				
					if (empty($terms)) {
						$parentId = $product->get_parent_id();
						if (!empty($parentId)) {
							$terms = get_the_terms($parentId, $taxonomyName);
						}
					}

					if (!empty($terms)) {
						foreach($terms as $term) {
							if (is_object($term)) {
								$packageKey[] = $term->slug;
								$packageName[] = $term->name;	
							}
						}
					}

					break;
				}

            case 'shipping_class':
				$slug = $product->get_shipping_class();
				if (empty($slug) && !empty($parentProduct)) {
					$slug = $parentProduct->get_shipping_class();
				}

				$packageKey[] = $slug;

				$term = get_term_by('slug', $slug, 'product_shipping_class');
				if (is_object($term)) {
					$packageName[] = $term->name;
				}
                break;

            case 'product_id':
				$packageName[] = $packageKey[] = $product->get_id(); 
                break;

			case 'type':
                $packageName[] = $packageKey[] = $product->get_type();
                break;

            case 'attributes':
				$attributeKeys = array_keys($product->get_attributes());

				foreach ($attributeKeys as $attributeKey) {
					$attribueValue = $product->get_attribute($attributeKey);
					$packageKey[] = $attributeKey;

					$name = wc_attribute_label($attributeKey);
					if (!empty($name)) {
						$packageName[] = $name;
					}
					
					if (!empty($attribueValue)) {
						$packageKey[] = $attribueValue;
						$packageName[] = $attribueValue;
					}
				}

                break;

			case 'categories':
				$categoryIds = $product->get_category_ids();
				if (empty($categoryIds) && !empty($parentProduct)) {
					$categoryIds = $parentProduct->get_category_ids();
				}

				foreach ($categoryIds as $categoryId) {
					$term = get_term_by('id', $categoryId, 'product_cat');
					if (is_object($term)) {
						$packageKey[] = $term->slug;
						$packageName[] = $term->name;
					}
				}
                break;

            case 'first_category':
				$categoryIds = $product->get_category_ids();
				if (empty($categoryIds) && !empty($parentProduct)) {
					$categoryIds = $parentProduct->get_category_ids();
				}
				
                if (!empty($categoryIds)) {
					$categoryId = $categoryIds[0];

					$term = get_term_by('id', $categoryId, 'product_cat');
					if (is_object($term)) {
						$packageKey[] = $term->slug;
						$packageName[] = $term->name;
					}
                }
                break;

			case 'tags':
				$tagIds = $product->get_tag_ids();
				if (empty($tagIds) && !empty($parentProduct)) {
					$tagIds = $parentProduct->get_tag_ids();
				}

				foreach ($tagIds as $tagId) {
					$term = get_term_by('id', $tagId, 'product_tag');
					if (is_object($term)) {
						$packageKey[] = $term->slug;
						$packageName[] = $term->name;
					}
				}
                break;

            case 'first_tag':
				$tagIds = $product->get_tag_ids();
				if (empty($tagIds) && !empty($parentProduct)) {
					$tagIds = $parentProduct->get_tag_ids();
				}
				
                if (!empty($tagIds)) {
					$tagId = $tagIds[0];
					$term = get_term_by('id', $tagId, 'product_tag');
					if (is_object($term)) {
						$packageKey[] = $term->slug;
						$packageName[] = $term->name;
					}
                }
                break;

			case 'post_author';
				$productId = 0;
				if (!empty($parentProduct)) {
					$productId = $parentProduct->get_id();
				} else {
					$productId = $product->get_id();
				}
				$post = get_post($productId);

                if (isset($post) && isset($post->post_author)) {
					$packageKey[] = $post->post_author;

					$sellerName = $this->getSellerName($post->post_author);
					if (!empty($sellerName)) {
						$packageName[] = $sellerName;
					}
				}
				
				break;

			case 'free_shipping':
				if (!empty($cartItem['free_shipping'])) {
					$packageKey[] = 'free_shipping';
					$packageName[] = __('Free Shipping', $this->id);
				}
				break;
        }

		$packageKey = apply_filters($this->id . '_package_key_part', $packageKey, $cartItem, $groupBy);		
		$packageName = apply_filters($this->id . '_package_name_part', $packageName, $cartItem, $groupBy);

		if (empty($packageKey)) {
			return false;
		}

        return array('key' => $packageKey, 'name' => $packageName);
    }

	protected function getSellerName($sellerId)
	{
		$this->debug('getSellerName');

		$sellerName = null;

		$meta = get_user_meta($sellerId);

		if (method_exists('WC_Product_Vendors_Utils', 'get_sold_by_link')) {
			$this->debug('WooCommerce Product Vendors is detected');

			$seller = \WC_Product_Vendors_Utils::get_sold_by_link($sellerId);
			
			if (!empty($seller['name'])) {
				$sellerName = $seller['name'];
			}
		} else if (function_exists('dokan_get_store_info')) {
			$this->debug('Dokan is detected');

			$seller = dokan_get_store_info($sellerId);
			
			if (!empty($seller['store_name'])) {
				$sellerName = $seller['store_name'];
			}
		} else if (function_exists('get_wcmp_vendor')) {
			$this->debug('WCMp is detected');

			$seller = get_wcmp_vendor($sellerId);
			if (is_object($seller) && !empty($seller->page_title)) {
				$sellerName = $seller->page_title;
			}
		} else if (function_exists('wcfm_get_vendor_id_by_post')) {
			$sellerData = get_user_meta($sellerId, 'wcfmmp_profile_settings', true);
			if (!empty($sellerData['store_name'])) {
				$sellerName = $sellerData['store_name'];
			}
		}
		
		if (empty($sellerName)) {
			$this->debug('No seller name has been found, so try nickname instead');

			$meta = get_user_meta($sellerId);
			if (!empty($meta['nickname'][0])) {
				$sellerName = $meta['nickname'][0];
			}
		}

		return $sellerName;
	}

    protected function createPackage($packageKey, $packageName)
    {
        $this->debug("createPackage: $packageKey, $packageName");

        $package = array(
			'packageKey' => $packageKey,
			'packageName' => $packageName,
			'needs_shipping' => false,
			// compatibility with dokan
			'user' => array(
                'ID' => get_current_user_id(),
            ),
            // 'contents' is the array of products in the package.
            'contents' => array(),
            'contents_cost' => 0,
            'applied_coupons' => array(),
            'destination' => array(
                'country' => WC()->customer->get_shipping_country(),
                'state' => WC()->customer->get_shipping_state(),
                'postcode' => WC()->customer->get_shipping_postcode(),
                'city' => WC()->customer->get_shipping_city(),
                'address' => WC()->customer->get_shipping_address(),
                'address_2' => WC()->customer->get_shipping_address_2(),
            ),
		);
		
		if (!empty($this->settings['shippingRestrictions'])) {
			if (empty($this->settings['shippingRestrictionsBehavior'])) {
				$package['ship_via'] = array();
			} else {
				$package['ship_via'] = array($this->getNoDisplayShippingMethod());
			}
		}

		return $package;
	}
	
	protected function getNoDisplayShippingMethod()
	{
		return $this->id . '_do_not_display_any_shipping_method';		
	}

	protected function addItemsToPackage($package, $cartItems, $itemKeys)
	{
		$itemKeys = $this->getAllItemKeys($cartItems, $itemKeys);

		foreach ($itemKeys as $itemKey) {
			$cartItem = $cartItems[$itemKey];
			$package = $this->addItemToPackage($package, $itemKey, $cartItem);
		}

		$this->debug("Package " . $package['packageKey'] . " has " . count($package['contents']) . " items ");

		return $package;
	}

    protected function addItemToPackage($package, $itemKey, $cartItem)
    {
		if (empty($cartItem['data'])) {
			return $package;
		}

		$cartItem = $this->applyCouponsToCartItem($cartItem);

		$product = $cartItem['data'];

        $this->debug("addItemToPackage, packageKey:  " . $package['packageKey'] . ", itemKey: $itemKey, product_name: " . $product->get_name() . ", shipping class: " . $product->get_shipping_class() . ", needs shipping: " . (int)$product->needs_shipping());

        if (isset($package['contents'][$itemKey])) {
            $this->debug("Item is already present in the package");
            return;
        }

        if (empty($cartItem['quantity'])) {
            $cartItem['quantity'] = 0;
        }

		$package = $this->setPackageShipping($package, $cartItem);
		$package = $this->setSellerId($package, $cartItem);

        // Add the item to the package.
        $package['contents'][$itemKey] = $cartItem;

        // Add on the line total to the package.
		$package['contents_cost'] += $product->get_price() * $cartItem['quantity'];
		
		if (!empty($cartItem['applied_coupons'])) {
			$package['applied_coupons'] = array_unique(array_merge($package['applied_coupons'], $cartItem['applied_coupons']));
		}

		return $package;
	}

	// used for compatibility with dokan, requires group by to have post_author to work as expected
	protected function setSellerId($package, $cartItem)
	{
		$product = $cartItem['data'];
		$sellerId = get_post_field('post_author', $product->get_id());

		// seller should be consistent for all the products or otherwise we can't use it
		if (!isset($package['seller_id'])) {
			$this->debug("Set package " . $package['packageKey'] . " seller ID to $sellerId");

			$package['seller_id'] = $sellerId;
			$package['vendor_id'] = $sellerId;
		} else if ($sellerId != $package['seller_id']) {
			$this->debug("Seller ID is different from previous, so reset it for " . $package['packageKey']);

			$package['seller_id'] = 0;
		}

		return $package;
	}

	protected function setPackageShipping($package, $cartItem)
	{
		$product = $cartItem['data'];

		if (!$product->needs_shipping()) {
			$this->debug("Product does not need shipping, so skip it");
			
			return $package;
		}

		$package['needs_shipping'] = true;

		if (empty($package['ship_via'])) {
			$package['ship_via'] = array();
		}

		$shippingClassId = $product->get_shipping_class_id();
		if (!empty($this->settings['shippingRestrictions'][$shippingClassId])) {
			$this->debug("Package " . $package['packageKey'] . " shipping restrictions for " . $product->get_shipping_class() . " are " . print_r($this->settings['shippingRestrictions'][$shippingClassId], true));

			$package['ship_via'] = array_merge($package['ship_via'], $this->settings['shippingRestrictions'][$shippingClassId]);
		}

		if (!empty($cartItem['free_shipping'])) {
			$this->debug("Add FREE SHIPPING");

			$package['ship_via'][] = 'free_shipping';
		} else if ($this->settings['allowFreeShipping'] == 'coupon') {
			$idx = array_search('free_shipping', $package['ship_via']);
			if ($idx !== false) {
				$this->debug("Remove FREE SHIPPING");

				unset($package['ship_via'][$idx]);
			}
		}

		$package['ship_via'] = array_unique($package['ship_via']);

		// first item might no display class which if kept will kill all shipping methods, so we have to remove it
		if (!empty($package['ship_via']) && count($package['ship_via']) > 1) {
			if ($package['ship_via'][0] === $this->getNoDisplayShippingMethod()) {
				unset($package['ship_via'][0]);
			}	
		}

		$this->debug("Set package " . $package['packageKey'] . " shipping to: " . print_r($package['ship_via'], true));

		return $package;
	}

    protected function debug($message, $type = 'notice')
    {
        if ($this->settings['debug'] != 'yes' || !current_user_can('administrator')) {
            return;
        }

        if (!empty($this->settings['debugType']) && $this->settings['debugType'] === 'notice' && function_exists('wc_add_notice')) {
            wc_add_notice($message, $type);
        } else {
            error_log("$type: $message\n");
        }
    }
}
