<?php
/**
 * Class DestinationAddressFactory
 *
 * @package WPDesk\FS\TableRate\Rule\ShippingContents
 */

namespace WPDesk\FS\TableRate\Rule\ShippingContents;

/**
 * Can create destination address.
 */
class DestinationAddressFactory {

	/**
	 * @param array $destination .
	 *
	 * @return DestinationAddress
	 */
	public static function create_from_package_destination( array $destination ) {
		return new DestinationAddress(
			isset( $destination['country'] ) ? $destination['country'] : '',
			isset( $destination['state'] ) ? $destination['state'] : '',
			isset( $destination['postcode'] ) ? $destination['postcode'] : '',
			isset( $destination['city'] ) ? $destination['city'] : '',
			isset( $destination['address'] ) ? $destination['address'] : '',
			isset( $destination['address_1'] ) ? $destination['address_1'] : '',
			isset( $destination['address_2'] ) ? $destination['address_2'] : ''
		);
	}

}
