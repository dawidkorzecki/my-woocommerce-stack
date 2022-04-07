<?php
/**
 * Class FSWalkthrough.
 *
 * @package WPDesk\FS\Info
 */

namespace WPDesk\FS\Info;

use WPDesk\FS\Info\Metabox\Links;

/**
 * Metabox Flexible Shipping walkthrough.
 */
class FSWalkthrough extends Links {
	/**
	 * FSWalkthrough constructor.
	 */
	public function __construct() {
		$title        = __( 'Flexible Shipping walkthrough', 'flexible-shipping' );
		$footer_label = __( 'Learn more about Flexible Shipping &rarr;', 'flexible-shipping' );
		$footer_url   = 'https://wpde.sk/fs-info-docs';

		parent::__construct( 'fs-walkthrough', $title, $this->generate_footer( $footer_url, $footer_label ) );
	}

	/**
	 * @return array[]
	 */
	protected function get_links() {
		return array(
			array(
				'label' => __( 'How to add a new shipping method handled by Flexible Shipping?', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/fs-new-method',
			),
			array(
				'label' => __( 'A complete guide to shipping methods', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/fs-complete-guide',
			),
			array(
				'label' => __( 'Disable or hide the shipping method', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/fs-disable-method',
			),
			array(
				'label' => __( 'Advanced options and customization', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/fs-advanced-options',
			),
			array(
				'label' => __( 'Combine shipping classes in Flexible Shipping', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/fs-combine-shipping-classes',
			),
		);
	}
}
