<?php

namespace OneTeamSoftware\WooCommerce\ShippingPackages;

require_once(__DIR__ . '/Shipping/AbstractShippingMethod.php');

class ShippingMethod extends \OneTeamSoftware\WooCommerce\Shipping\AbstractShippingMethod
{
	private $allPostMetaKeys;

    public function __construct()
    {
        $this->id = 'wc_shipping_packages';
        $this->method_title = __('Shipping Packages', $this->id);
        $this->title = $this->method_title;
		$this->method_description = sprintf('%s<br/><br/>%s <a href="%s" target="_blank">%s</a>.<br/>%s <a href="%s" target="_blank">%s</a>.', 
			__('Groups products in the cart into shipping packages based on "group by" condition, which can be shipped with different shipping methods.', $this->id),
			__('Do you have any questions or requests?', $this->id), 
			'https://1teamsoftware.com/contact-us/', 
			__('We are here to help you!', $this->id),
			 __('Will you recommend <strong>Shipping Packages</strong> plugin to others?', $this->id), 
			 'https://wordpress.org/support/plugin/wc-shipping-packages/reviews/', 
			 __('Please take 1 minute to leave your review', $this->id));

		$this->enabled = "yes";
		$this->allPostMetaKeys = array();

        $this->init();
    }

    function init()
    {
        // Load the settings API

        // Initialize form with our settings
        $this->init_form_fields();
        // Loads settings you previously init.
        $this->init_settings();

        $this->shippingRestrictions = isset($this->settings['shippingRestrictions']) ? $this->settings['shippingRestrictions'] : array();

        // Save any settings that may have been submitted.
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * Sets form fields that will be available on this admin form.
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'type'      => 'checkbox',
				'title'     => __('Enable Shipping Package', $this->id),
				'description' => __('Split Cart into Packages, according to the rules defined below, so different shipping method can be selected for each of the Packages.', $this->id),
                'default'   => 'yes'
			),
		);

		if (!class_exists('\\OneTeamSoftware\\WooCommerce\\MergeShippingPackages\\MergeShippingPackages')) {
			$this->form_fields += array(
				'enableMergeShippingPackages' => array(
					'type' => 'checkbox',
					'title' => __('Enable Merge Shipping Packages', $this->id),
					'description' => sprintf('%s %s %s', 
						__('Merges products from one package into another shipping package based on the configured product conditions.', $this->id),
						__('Requires: ', $this->id),
						'<a href="https://1teamsoftware.com/product/woocommerce-merge-shipping-packages/" target="_blank">Merge Shipping Packages for WooCommerce</a>'
					),
					'default' => 'no',
					'custom_attributes' => array(
						'disabled' => 'yes',
					),
				),
			); 	
		}

		if (!class_exists('\\OneTeamSoftware\\WooCommerce\\MarketplaceCart\\MarketplaceCart')) {
			$this->form_fields += array(
				'enableMarketplaceCart' => array(
					'type' => 'checkbox',
					'title' => __('Enable Packages in Cart / Checkout Pages', $this->id),
					'description' => sprintf('%s %s %s', 
						__('Redesigns Cart, Checkout and Order Review pages to show contents grouped into packages with shipping method selection under each package.', $this->id), 
						__('Requires: ', $this->id),
						'<a href="https://1teamsoftware.com/product/woocommerce-marketplace-cart/" target="_blank">WooCommerce Marketplace Cart</a>'
					),
					'default' => 'no',
					'custom_attributes' => array(
						'disabled' => 'yes',
					),
				),
			); 	
		}

		if (!class_exists('\\OneTeamSoftware\\WooCommerce\\MarketplaceCart\\PayForSelectedItems')) {
			$this->form_fields += array(
				'PayForSelectedItems' => array(
					'type' => 'checkbox',
					'title' => __('Enable Pay For Selected Items', $this->id),
					'description' => sprintf('%s %s %s', 
						__('Add-on for Markerplace Cart that allows customers to choose what items to pay for during checkout.', $this->id), 
						__('Requires: ', $this->id),
						'<a href="https://1teamsoftware.com/product/woocommerce-marketplace-cart-pay-for-selected-items/" target="_blank">Pay For Selected Items Add-On for WooCommerce Marketplace Cart</a>'
					),
					'default' => 'no',
					'custom_attributes' => array(
						'disabled' => 'yes',
					),
				),
			); 	
		}

		if (!class_exists('\\OneTeamSoftware\\WooCommerce\\PackageOrders\\PackageOrders')) {
			$this->form_fields += array(
				'enablePackageOrders' => array(
					'type' => 'checkbox',
            		'title' => __('Enable Package Orders', $this->id),
					'description' => sprintf('%s %s %s', 
						__('Create separate order for each Package, so they can be fulfilled independently.', $this->id),
						__('Requires: ', $this->id),
						'<a href="https://1teamsoftware.com/product/woocommerce-package-orders/" target="_blank">WooCommerce Package Orders</a>'
					),
					'default' => 'no',
					'custom_attributes' => array(
						'disabled' => 'yes',
					),
				),
			);	
		}

		$this->form_fields += array(
            'debug' => array(
                'type'      => 'checkbox',
                'title'     => __('Debug', $this->id),
                'default'   => 'yes'
            ),
            'debugType' => array(
                'type'      => 'select',
                'title'     => __('Debug Type', $this->id),
                'options'   => array(
                    'error_log' => __('Error Log', $this->id),
                    'notice' => __('Woocommerce Notice', $this->id)
                ),
                'default'   => 'error_log'
            ),
            'groupBy' => array(
                'type'      => 'multiselect',
                'title'     => __('Group By', $this->id),
                'default'   => 'shipping-class',
                'class'     => 'chosen_select',
                'desc_tip'  => true,
                'options'   => $this->getGroupByOptions(),
			),
            'shippingMethodPer' => array(
                'type'      => 'select',
				'title'     => __('Shipping Method Per', $this->id),
				'options'   => array(
					'package' => __('Package', $this->id),
					'cart' => __('Cart', $this->id),
				),
                'default'   => 'package',
				'description' => sprintf(__('It allows to charge shipping fee per package or per cart. Only %s and %s can take advantage of it when it is set to "Cart"', $this->id), 
					'<a href="https://1teamsoftware.com/product/woocommerce-marketplace-cart/" target="_blank">WooCommerce Marketplace Cart</a>',
					'<a href="https://1teamsoftware.com/product/woocommerce-package-orders/" target="_blank">WooCommerce Package Orders</a>'),
            ),
            'useAutoPackageName' => array(
                'type'      => 'checkbox',
                'title'     => __('Use Auto Package Name', $this->id),
                'default'   => 'no',
				'description' => __('If enabled then package name, that is auto generated based on group by rule, will be displayed in the cart instead of the generic Shipping 1...', $this->id),
            ),
			//'packageNamePartsGlue' => array(
			//	'type'      => 'text',
			//	'title'     => __('Package Name Parts Glue', $this->id),
			//	'default'   => ', ',
			//	'description' => __('It is used to glue package name parts, that are generated based on group by value', $this->id),
			//),
			'allowFreeShipping' => array(
                'type'      => 'select',
				'title'     => __('Allow Free Shipping', $this->id),
				'options'   => array(
                    'coupon' => __('Only With Coupon', $this->id),
                    'always' => __('Without Coupon', $this->id)
                ),
				'default'   => '',
				'description' => __('Do you want to offer built-in Free Shipping method only when coupon applies to all the package items?', $this->id)
			),
		);

		$otherShippingSolutionsDesc = $this->getOtherShippingSolutionsDesc();

		if (!empty($otherShippingSolutionsDesc)) {
			$this->form_fields += array(
				'otherShippingSolutionsTitle' => array(
					'type'      => 'title',
					'title'     => __('Need Better Shipping Solutions?', $this->id),
					'description' => $otherShippingSolutionsDesc,
				),
			);	
		}

		$this->form_fields += array(
            'method_settings' => array(
                'id'        => 'shippingPackages_method_settings',
                'type'      => 'title',
                'title'     => __('Shipping Restrictions for Shipping Classes', $this->id),
			),
			'shippingRestrictionsBehavior' => array(
                'type'      => 'select',
				'title'     => __('Packages Without Shipping Restrictions', $this->id),
				'description' => __('How should we handle packages that do not have shipping restrictions configured?', $this->id),
                'default'   => '',
                'options'   => array(
                    '' => __('Display all available shipping methods', $this->id),
					'display-none' => __('Do not display any shipping method', $this->id),	
                ),
            ),			
            'shippingRestrictions' => array(
                'type'      => 'shippingRestrictions',
            ),
        );

        $this->form_fields = apply_filters($this->id . '_settings', $this->form_fields);
	}
	
	protected function getGroupByOptions()
	{
		// check if we've have cached data from before
		$cacheKey = $this->id . '_groupByOptions';
		$groupByOptions = get_transient($cacheKey);
		if (!empty($groupByOptions)) {
			return $groupByOptions;
		}
		
		$groupByOptions = array(
			'shipping_class' => __('Shipping Class', $this->id),
			'product_id' => __('Product ID', $this->id),
			'type' => __('Product Type', $this->id),
			'attributes' => __('Attributes', $this->id),
			'categories' => __('Categories', $this->id),
			'first_category' => __('First Category', $this->id),
			'tags' => __('Tags', $this->id),
			'first_tag' => __('First Tag', $this->id),
			'post_author' => __('Post Author (Vendor)', $this->id),
			'free_shipping' => __('Free Shipping', $this->id)
		);

        $taxonomies = get_object_taxonomies('product', 'objects');
        foreach($taxonomies as $taxonomy ) {
			$groupByOptions['taxonomy_' . $taxonomy->name] = __('Taxonomy: ', $this->id) . $taxonomy->label;
        }

		$metaKeys = $this->getAllPostMetaKeys();
		foreach ($metaKeys as $metaKey) {
			$groupByOptions['postmeta_' . $metaKey] = __('Post Meta: ', $this->id) . $metaKey;
		}
		
		// cache result for 1 hour, because query might be heavy when there are a lot of meta data
		set_transient($cacheKey, $groupByOptions, 60 * 60);

		return $groupByOptions;
	}
	
	protected function getOtherShippingSolutionsDesc()
	{
		$description = '';

		if (!class_exists('\\OneTeamSoftware\\WooCommerce\\FreeShippingPerPackage\\PluginPro')) {
			$description .= sprintf('<li>%s - %s', 
				'<strong><a href="https://1teamsoftware.com/product/woocommerce-free-shipping-per-package-pro/" target="_blank">Free Shipping Per Package PRO</a></strong>',
				__('advanced Free Shipping scenarios for individual Shipping Packages.', $this->id)
			);
		}

		if (!class_exists('\\OneTeamSoftware\\WooCommerce\\FlexibleShippingPerPackage\\PluginPro')) {
			$description .= sprintf('<li>%s - %s', 
				'<strong><a href="https://1teamsoftware.com/product/woocommerce-flexible-shipping-per-package-pro/" target="_blank">Flexible Shipping Per Package PRO</a></strong>',
				__('advanced Table Rate shipping method conditions for individual Shipping Packages.', $this->id)
			);
		}

		if (!class_exists('\\OneTeamSoftware\\WooCommerce\\Shipping\\Adapter\\EasyPost')) {
			$description .= sprintf('<li>%s - %s', 
				'<strong><a href="https://1teamsoftware.com/product/woocommerce-easypost-shipping-pro/" target="_blank">EasyPost Shipping PRO</a></strong>',
				__('live shipping rates from 100+ carriers, shipping labels, tracking history and automatic emails with shipment status updates.', $this->id)
			);
		}

		if (!empty($description)) {
			$description = '<ul>' . $description . '</ul>';
		}

		return $description;
	}

    /**
     * Returns HTML for the "shippingSestrictions"
     */
    function generate_shippingRestrictions_html($key)
    {
        $shippingMethods = WC()->shipping->get_shipping_methods();
        unset($shippingMethods[$this->id]);

        $shippingClasses = array();
        $shippingClassTerms = WC()->shipping->get_shipping_classes();

        foreach ($shippingClassTerms as $term) {
			if (!empty($term->name)) {
				$shippingClasses[$term->term_id] = $term->name;
			}
        }

        ob_start();
?>
<style type="text/css">
#shippingRestrictions table {
    width: 60%;
    min-width:550px;
}

#shippingRestrictions table th, 
#shippingRestrictions table td {
    text-align: center;
}
#shippingRestrictions .title {
    font-weight: bold; 
    text-align: left;
}
</style>

<table>
    <tr valign="top" id="shippingRestrictions">
        <td class="forminp" id="<?php echo $this->id; ?>_shippingRestrictions">
            <table class="widefat" cellspacing="0">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
<?php 
                        foreach ($shippingMethods as $shippingMethod) { 
?>
                        <th><?php _e($shippingMethod->method_title, $this->id); ?></th>
<?php 
                        } 
?>
                    </tr>
                </thead>

                <tfoot>
                    <tr>
                        <td colspan="<?php echo count($shippingMethods) + 1; ?>"><?php _e('If nothing is selected then all shipping methods will be available for each shipping class', $this->id);?></td>
                    </tr>
                </tfoot>

                <tbody>
<?php
                if (empty($shippingClasses)) {
?>
                <tr><td colspan="<?php echo count($shippingMethods) + 1; ?>"><?php _e( 'No shipping classes have been created yet...', $this->id);?></td></tr>
<?php
                }
                foreach ($shippingClasses as $shippingClassId => $shippingClassName) {
?>
                <tr>
                    <td class="title"><?php echo $shippingClassName; ?></td>
<?php 
                    foreach ($shippingMethods as $shippingMethodId => $notUsed) { 
                        $checked = (isset($this->shippingRestrictions[$shippingClassId]) && in_array(sanitize_title($shippingMethodId), $this->shippingRestrictions[$shippingClassId]) ) ? 'checked="checked"' : '';
?>
                        <td><input type="checkbox" name="shippingRestrictions[<?php echo $shippingClassId; ?>][<?php echo sanitize_title($shippingMethodId); ?>]" <?php echo $checked; ?> /></td>
<?php 
                    } 
?>
                </tr>
<?php
                }
?>
                </tbody>
            </table>
        </td>
    </tr>
</table>

<?php
        return ob_get_clean();
    }

    /**
     * Validates shippingRestrictions
     */
    public function validate_shippingRestrictions_field($key)
    {
        $shippingRestrictions = array();
        if (isset($_POST['shippingRestrictions'])) {
            foreach ($_POST['shippingRestrictions'] as $key => $value) {
                $key = intval($key);

                foreach ($value as $keyMethod => $valueMethod) {
                    $shippingRestrictions[$key][] = sanitize_title($keyMethod);
                }
            }
        }

        return $shippingRestrictions;
	}
	
	/**
	 * Returns the an array of available meta keys
	 */
	private function getAllPostMetaKeys()
	{
		if (!empty($this->allPostMetaKeys)) {
			return $this->allPostMetaKeys;
		}

		// check if we've have cached data from before
		$cacheKey = $this->id . '_AllPostMetaKeys';
		$metaKeys = get_transient($cacheKey);
		if (!empty($metaKeys)) {
			return $metaKeys;
		}

		global $wpdb;
		$query = "SELECT distinct meta_key FROM $wpdb->postmeta LIMIT 1000";
		
		$metaKeys = $wpdb->get_col($query);
		if (empty($metaKeys)) {
			$metaKeys = array();
		}

		// cache result for 1 hour, because query might be heavy when there are a lot of meta data
		set_transient($cacheKey, $metaKeys, 60 * 60);

		$this->allPostMetaKeys = $metaKeys;

		return $metaKeys;
	}
}
