<?php

class WPDesk_Flexible_Shipping_Manifest_FS extends WPDesk_Flexible_Shipping_Manifest implements WPDesk_Flexible_Shipping_Manifest_Interface {

	/**
	 * @return array
	 * Returns manifest data in array
	 *      file_name => file name for manifest
	 *      content   => pdf content
	 */
	public function get_manifest() {
		return null;
	}

	/**
	 * @return string
	 * Returns manifest number
	 */
	public function get_number() {
		return null;
	}

	/**
	 * @return null
	 * Generates manifest (ie. in API)
	 */
	public function generate() {
		return null;
	}

	/**
	 * @return null
	 * Cancels manifest (ie. in API)
	 */
	public function cancel() {
		return null;
	}

}
