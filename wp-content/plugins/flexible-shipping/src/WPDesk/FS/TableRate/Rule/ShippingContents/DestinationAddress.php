<?php
/**
 * Class DestinationAddress
 *
 * @package WPDesk\FS\TableRate\Rule\ShippingContents
 */

namespace WPDesk\FS\TableRate\Rule\ShippingContents;

/**
 * Destination address.
 */
class DestinationAddress {

	/**
	 * @var string
	 */
	private $country;

	/**
	 * @var string
	 */
	private $state;

	/**
	 * @var string
	 */
	private $postcode;

	/**
	 * @var string
	 */
	private $city;

	/**
	 * @var string
	 */
	private $address;

	/**
	 * @var string
	 */
	private $address_1;

	/**
	 * @var string
	 */
	private $address_2;

	/**
	 * DestinationAddress constructor.
	 *
	 * @param string $country .
	 * @param string $state .
	 * @param string $postcode .
	 * @param string $city .
	 * @param string $address .
	 * @param string $address_1 .
	 * @param string $address_2 .
	 */
	public function __construct( $country, $state, $postcode, $city, $address, $address_1, $address_2 ) {
		$this->country   = $country;
		$this->state     = $state;
		$this->postcode  = $postcode;
		$this->city      = $city;
		$this->address   = $address;
		$this->address_1 = $address_1;
		$this->address_2 = $address_2;
	}

	/**
	 * @return string
	 */
	public function get_country() {
		return $this->country;
	}

	/**
	 * @return string
	 */
	public function get_state() {
		return $this->state;
	}

	/**
	 * @return string
	 */
	public function get_postcode() {
		return $this->postcode;
	}

	/**
	 * @return string
	 */
	public function get_city() {
		return $this->city;
	}

	/**
	 * @return string
	 */
	public function get_address() {
		return $this->address;
	}

	/**
	 * @return string
	 */
	public function get_address_1() {
		return $this->address_1;
	}

	/**
	 * @return string
	 */
	public function get_address_2() {
		return $this->address_2;
	}

	/**
	 * @return string
	 */
	public function prepare_data_for_log() {
		return sprintf(
			'country: %1$s, state: %2$s, postcode: %3$s, city: %4$s, address: %5$s, address_1: %6$s, address_2: %7$s',
			$this->country,
			$this->state,
			$this->postcode,
			$this->city,
			$this->address,
			$this->address_1,
			$this->address_2
		);
	}

}
