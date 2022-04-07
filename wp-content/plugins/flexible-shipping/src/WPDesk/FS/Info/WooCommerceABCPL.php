<?php
/**
 * Info WooCommerceABCPL.
 *
 * @package WPDesk\FS\Info
 */

namespace WPDesk\FS\Info;

use WPDesk\FS\Info\Metabox\Links;

/**
 * WooCommerceABC in FS Info.
 */
class WooCommerceABCPL extends Links {
	/**
	 * WooCommerceABC constructor.
	 */
	public function __construct() {
		$title        = __( 'WooCommerce ABCs', 'flexible-shipping' );
		$footer_label = __( 'Want to know more about WooCommerce? &rarr;', 'flexible-shipping' );
		$footer_url   = 'https://wpde.sk/fs-info-blog-pl';

		parent::__construct( 'woocommerce-abc', $title, $this->generate_footer( $footer_url, $footer_label ) );
	}

	/**
	 * @return array[]
	 */
	protected function get_links() {
		return array(
			array(
				'label' => __( 'Shipping configuration', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/wysylka',
			),
			array(
				'label' => __( 'Shipping Zones', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/strefy',
			),
			array(
				'label' => __( 'Shipping Classes', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/klasy',
			),
			array(
				'label' => __( 'Free Shipping', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/darmowa-wysylka',
			),
		);
	}
}
