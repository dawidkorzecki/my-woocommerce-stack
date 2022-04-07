<?php
/**
 * Tracker.
 *
 * @package WPDesk\FS\TableRate\Tax
 */

namespace WPDesk\FS\TableRate\Tax;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can add Tax data to tracker.
 */
class Tracker implements Hookable {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'flexible-shipping/tracker/method-settings', [ $this, 'append_tax_data' ], 10, 2 );
	}

	/**
	 * @param array $data
	 * @param array $settings
	 *
	 * @return array
	 */
	public function append_tax_data( array $data, array $settings ) {
		$tax_status = isset( $settings['tax_status'] ) ? $settings['tax_status'] : 'not_set';
		$data = $this->append_data( $data, 'tax_status', $tax_status );

		$prices_include_tax = isset( $settings['prices_include_tax'] ) ? $settings['prices_include_tax'] : 'not_set';
		$data = $this->append_data( $data, 'prices_include_tax', $prices_include_tax );

		return $data;
	}

	/**
	 * @param array  $data
	 * @param string $setting
	 * @param string $value
	 *
	 * @return array
	 */
	private function append_data( array $data, $setting, $value ) {
		if ( ! isset( $data[ $setting ] ) || ! is_array( $data[ $setting ] ) ) {
			$data[ $setting ] = [];
		}
		if ( ! isset( $data[ $setting ][ $value ] ) ) {
			$data[ $setting ][ $value ] = 0;
		}
		$data[ $setting ][ $value ]++;

		return $data;
	}

}
