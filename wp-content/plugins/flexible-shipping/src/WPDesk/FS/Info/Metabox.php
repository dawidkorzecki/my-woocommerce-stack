<?php
/**
 * Info Metabox.
 *
 * @package WPDesk\FS\Info
 */

namespace WPDesk\FS\Info;

/**
 * Metabox in FS Info.
 */
class Metabox {

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $body;

	/**
	 * @var string
	 */
	private $footer;

	/**
	 * Metabox constructor.
	 *
	 * @param string $id     .
	 * @param string $title  .
	 * @param string $body   .
	 * @param string $footer .
	 */
	public function __construct( $id, $title, $body = '', $footer = '' ) {
		$this->id     = $id;
		$this->title  = $title;
		$this->body   = $body;
		$this->footer = $footer;
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function get_body() {
		return $this->body;
	}

	/**
	 * @return string
	 */
	public function get_footer() {
		return $this->footer;
	}

	/**
	 * @return bool
	 */
	public function has_title() {
		return strlen( $this->title ) > 0;
	}

	/**
	 * @return bool
	 */
	public function has_body() {
		return strlen( $this->body ) > 0;
	}

	/**
	 * @return bool
	 */
	public function has_footer() {
		return strlen( $this->footer ) > 0;
	}

	/**
	 * @return string
	 */
	public function get_classes() {
		$classes = array( 'fs-info-metabox' );

		if ( $this->has_title() ) {
			$classes[] = 'has-title';
		}

		if ( $this->has_footer() ) {
			$classes[] = 'has-footer';
		}

		return implode( ' ', $classes );
	}
}
