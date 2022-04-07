<?php
/**
 * Class Video.
 *
 * @package WPDesk\FS\Info
 */

namespace WPDesk\FS\Info;

/**
 * Video metabox.
 */
class Video extends Metabox {
	/**
	 * WooCommerceABC constructor.
	 */
	public function __construct() {
		parent::__construct( 'video', '', $this->get_body_content() );
	}

	/**
	 * @return string
	 */
	private function get_body_content() {
		$youtube_url = 'https://www.youtube.com/embed/ov1Ff-_A268';

		return '<p style="text-align:center;"><iframe width="688" height="387" src="' . esc_url( $youtube_url ) . '?rel=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></p>';
	}
}
