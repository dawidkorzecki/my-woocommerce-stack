<?php
/**
 * Info WooCommerceABC.
 *
 * @package WPDesk\FS\Info
 */

namespace WPDesk\FS\Info;

use WPDesk\FS\Info\Metabox\Links;

/**
 * WooCommerceABC in FS Info.
 */
class WooCommerceABC extends Links {
	/**
	 * WooCommerceABC constructor.
	 */
	public function __construct() {
		$title        = __( 'WooCommerce ABCs', 'flexible-shipping' );
		$footer_label = __( 'Want to know more about WooCommerce? &rarr;', 'flexible-shipping' );
		$footer_url   = 'https://wpde.sk/fs-info-blog';

		parent::__construct( 'woocommerce-abc', $title, $this->generate_footer( $footer_url, $footer_label ) );
	}

	/**
	 * @return array[]
	 */
	protected function get_links() {
		return array(
			array(
				'label' => __( 'Shipping Zones', 'flexible-shipping' ),
				'href'  => 'https://flexibleshipping.com/woocommerce-shipping-zones-explained/?utm_source=shipping-zones&utm_medium=link&utm_campaign=fs-info',
			),
			array(
				'label' => __( 'Shipping Tax', 'flexible-shipping' ),
				'href'  => 'https://flexibleshipping.com/woocommerce-shipping-tax/?utm_source=tax&utm_medium=link&utm_campaign=fs-info',
			),
			array(
				'label' => __( 'Shipping Methods', 'flexible-shipping' ),
				'href'  => 'https://flexibleshipping.com/woocommerce-shipping-methods/?utm_source=shipping-methods&utm_medium=link&utm_campaign=fs-info',
			),
			array(
				'label' => __( 'Shipping Classes', 'flexible-shipping' ),
				'href'  => 'https://flexibleshipping.com/woocommerce-shipping-classes/?utm_source=shipping-classes&utm_medium=link&utm_campaign=fs-info',
			),
			array(
				'label' => __( 'Table Rate Shipping', 'flexible-shipping' ),
				'href'  => 'https://flexibleshipping.com/what-is-table-rate-shipping/?utm_source=table-rate&utm_medium=link&utm_campaign=fs-info',
			),
		);
	}
}
