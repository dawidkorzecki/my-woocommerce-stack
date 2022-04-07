<?php
/**
 * Class FSIE.
 *
 * @package WPDesk\FS\Info
 */

namespace WPDesk\FS\Info;

/**
 * FSIE metabox.
 */
class FSIE extends Metabox {
	/**
	 * WooCommerceABC constructor.
	 */
	public function __construct() {
		$title = __( 'Import and Export your shipping methods with Flexible Shipping Import/Export plugin', 'flexible-shipping' );

		parent::__construct( 'fsie', $title, $this->get_body_content(), $this->get_footer_content() );
	}

	/**
	 * @return string
	 */
	private function get_body_content() {
		ob_start();

		include 'views/fsie.php';

		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	private function get_footer_content() {
		$url = get_locale() === 'pl_PL' ? 'https://wpde.sk/fs-info-fsie-pl' : 'https://wpde.sk/fs-info-fsie';

		return '<a class="button button-primary" href="' . esc_url( $url ) . '" target="_blank">' . __( 'Buy Flexible Shipping Import/Export &rarr;', 'flexible-shipping' ) . '</a>';
	}
}
