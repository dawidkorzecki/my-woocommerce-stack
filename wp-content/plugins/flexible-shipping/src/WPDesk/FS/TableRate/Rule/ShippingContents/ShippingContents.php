<?php
/**
 * Interface ShippingContents
 *
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate\Rule\ShippingContents;

use WPDesk\FS\TableRate\Rule\ContentsFilter;

/**
 * Can provide shipping contents.
 */
interface ShippingContents {

	/**
	 * @param int $weight_rounding_precision .
	 */
	public function set_weight_rounding_precision( $weight_rounding_precision );

	/**
	 * @return array
	 */
	public function get_contents();

	/**
	 * @return float
	 */
	public function get_contents_cost();

	/**
	 * @param bool $round .
	 *
	 * @return float
	 */
	public function get_contents_weight( $round = true );

	/**
	 * @return int
	 */
	public function get_contents_items_count();

	/**
	 * @param ContentsFilter $contents_filter .
	 */
	public function filter_contents( ContentsFilter $contents_filter );

	/**
	 * Returns non filtered contents.
	 *
	 * @return array
	 */
	public function get_non_filtered_contents();

	/**
	 * Reset contents to non filtered.
	 *
	 * @return array
	 */
	public function reset_contents();

	/**
	 * @return DestinationAddress
	 */
	public function get_destination_address();

	/**
	 * @return string
	 */
	public function get_currency();

}
