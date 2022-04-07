<?php
/**
 * Class FSWalkthroughPL.
 *
 * @package WPDesk\FS\Info
 */

namespace WPDesk\FS\Info;

use WPDesk\FS\Info\Metabox\Links;

/**
 * Metabox Flexible Shipping walkthrough.
 */
class FSWalkthroughPL extends Links {
	/**
	 * FSWalkthrough constructor.
	 */
	public function __construct() {
		$title        = __( 'Flexible Shipping walkthrough', 'flexible-shipping' );
		$footer_label = __( 'Learn more about Flexible Shipping &rarr;', 'flexible-shipping' );
		$footer_url   = 'https://wpde.sk/fs-info-docs-pl';

		parent::__construct( 'fs-walkthrough', $title, $this->generate_footer( $footer_url, $footer_label ) );
	}

	/**
	 * @return array[]
	 */
	protected function get_links() {
		return array(
			array(
				'label' => __( 'How to add a new shipping method handled by Flexible Shipping?', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/fs-konfiguracja',
			),
			array(
				'label' => __( 'A complete guide to shipping methods', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/fs-konfiguracja-metody',
			),
			array(
				'label' => __( 'Disable or hide the shipping method', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/fs-wylaczanie-metody',
			),
			array(
				'label' => __( 'Advanced options and customization', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/fs-zaawansowane',
			),
			array(
				'label' => __( 'Combine shipping classes in Flexible Shipping', 'flexible-shipping' ),
				'href'  => 'https://wpde.sk/fs-laczenie-klas-wysylkowych',
			),
		);
	}
}
