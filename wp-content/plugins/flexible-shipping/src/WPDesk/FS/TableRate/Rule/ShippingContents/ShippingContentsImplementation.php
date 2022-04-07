<?php
/**
 * Class ShippingContentsImplementation
 *
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate\Rule\ShippingContents;

use WPDesk\FS\TableRate\Rule\ContentsFilter;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;

/**
 * Can provide shipping contents.
 */
class ShippingContentsImplementation implements ShippingContents {

	/**
	 * @var array
	 */
	private $non_filtered_contents = array();

	/**
	 * @var array
	 */
	private $contents = array();

	/**
	 * @var bool
	 */
	private $prices_includes_tax;

	/**
	 * @var float
	 */
	private $contents_cost;

	/**
	 * @var float
	 */
	private $contents_weight;

	/**
	 * @var int
	 */
	private $cost_rounding_precision;

	/**
	 * @var DestinationAddress
	 */
	private $destination_address;

	/**
	 * @var string
	 */
	private $currency;

	/**
	 * @var int
	 */
	private $weight_rounding_precision = 6;

	/**
	 * ShippingContents constructor.
	 *
	 * @param array              $contents                .
	 * @param bool               $prices_includes_tax     .
	 * @param int                $cost_rounding_precision .
	 * @param DestinationAddress $destination_address     .
	 * @param string             $currency                .
	 */
	public function __construct( $contents, $prices_includes_tax, $cost_rounding_precision, $destination_address, $currency ) {
		$this->contents                = $contents;
		$this->non_filtered_contents   = $contents;
		$this->prices_includes_tax     = $prices_includes_tax;
		$this->cost_rounding_precision = $cost_rounding_precision;
		$this->destination_address     = $destination_address;
		$this->currency                = $currency;
	}

	/**
	 * @param int $weight_rounding_precision .
	 */
	public function set_weight_rounding_precision( $weight_rounding_precision ) {
		$this->weight_rounding_precision = $weight_rounding_precision;
	}

	/**
	 * @param \WC_Cart $cart .
	 */
	private function prepare_contents( $cart ) {
		return $cart->cart_contents;
	}

	/**
	 * @return array
	 */
	public function get_contents() {
		return $this->contents;
	}

	/**
	 * @return float
	 */
	public function get_contents_cost() {
		if ( ! isset( $this->contents_cost ) ) {
			$this->contents_cost = $this->calculate_contents_cost();
		}

		return round( $this->contents_cost, $this->cost_rounding_precision );
	}

	/**
	 * @param bool $round .
	 *
	 * @return float
	 */
	public function get_contents_weight( $round = true ) {
		if ( ! isset( $this->contents_weight ) ) {
			$this->contents_weight = $this->calculate_contents_weight();
		}

		if ( $round ) {
			return round( $this->contents_weight, $this->weight_rounding_precision );
		}

		return $this->contents_weight;
	}

	/**
	 * @return float
	 */
	private function calculate_contents_weight() {
		$weight = 0.0;
		foreach ( $this->contents as $item ) {
			$weight += $this->get_item_weight( $item );
		}

		return $weight;
	}

	/**
	 * @param array $item .
	 *
	 * @return float
	 */
	private function get_item_weight( $item ) {
		return (float) ( (float) $item['data']->get_weight() * (float) $item['quantity'] );
	}

	/**
	 * @return float
	 */
	private function calculate_contents_cost() {
		$cost = 0.0;
		foreach ( $this->contents as $item ) {
			$cost += $this->get_item_cost( $item );
		}

		return $cost;
	}

	/**
	 * @param array $item .
	 *
	 * @return float
	 */
	private function get_item_cost( $item ) {
		$line_total = 0.0;
		if ( $this->prices_includes_tax ) {
			if ( isset( $item['line_total'] ) ) {
				$line_total = floatval( $item['line_total'] );
			}
			if ( isset( $item['line_tax'] ) ) {
				$line_total += floatval( $item['line_tax'] );
			}
		} else {
			if ( isset( $item['line_total'] ) ) {
				$line_total = floatval( $item['line_total'] );
			}
		}

		return $line_total;
	}

	/**
	 * @return int
	 */
	public function get_contents_items_count() {
		$items_count = 0;
		foreach ( $this->contents as $item ) {
			$items_count += $item['quantity'];
		}

		return $items_count;
	}


	/**
	 * @param ContentsFilter $contents_filter .
	 */
	public function filter_contents( ContentsFilter $contents_filter ) {
		$this->contents        = $contents_filter->get_filtered_contents( $this->contents );
		$this->contents_cost   = null;
		$this->contents_weight = null;
	}

	/**
	 * Returns non filtered contents.
	 *
	 * @return array
	 */
	public function get_non_filtered_contents() {
		return $this->non_filtered_contents;
	}

	/**
	 * Reset contents to non filtered.
	 *
	 * @return void
	 */
	public function reset_contents() {
		$this->contents        = $this->non_filtered_contents;
		$this->contents_cost   = null;
		$this->contents_weight = null;
	}

	/**
	 * @return array
	 */
	public function get_destination_address() {
		return $this->destination_address;
	}

	/**
	 * @return string
	 */
	public function get_currency() {
		return $this->currency;
	}

}
