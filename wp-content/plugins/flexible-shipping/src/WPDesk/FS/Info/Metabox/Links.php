<?php
/**
 * Class Links
 *
 * @package WPDesk\FS\Info
 */

namespace WPDesk\FS\Info\Metabox;

use WPDesk\FS\Info\Metabox;

/**
 * Metabox with links.
 */
abstract class Links extends Metabox {
	/**
	 * Links constructor.
	 *
	 * @param string $id     .
	 * @param string $title  .
	 * @param string $footer .
	 */
	public function __construct( $id, $title, $footer = '' ) {
		$body = $this->generate_body();

		parent::__construct( $id, $title, $body, $footer );
	}

	/**
	 * @return string
	 */
	private function generate_body() {
		$body = '';

		foreach ( $this->get_links() as $link ) {
			$body .= sprintf( '<li><span class="link-arrow">&#9654;</span>&nbsp;&nbsp;<a href="%s" target="_blank">%s</a></li>', esc_url( $link['href'] ), $link['label'] );
		}

		return sprintf( '<ul class="links">%s</ul>', $body );
	}

	/**
	 * @param string $url   .
	 * @param string $label .
	 *
	 * @return string
	 */
	protected function generate_footer( $url, $label ) {
		return sprintf( '<a href="%s" class="read-more" target="_blank">%s</a>', $url, $label );
	}

	/**
	 * @return array[]
	 */
	protected function get_links() {
		return array();
	}
}
