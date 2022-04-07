<?php
/**
 * Class NoShippingMethodNotice
 *
 * @package WPDesk\FS\TableRate\Debug
 */

namespace WPDesk\FS\TableRate\Debug;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\ShippingMethodSingle;

/**
 * Can display notice when no shipping methods are defined in shipping zone.
 */
class NoShippingMethodsNotice implements Hookable {

	/**
	 * @var bool
	 */
	private $debug_mode_enabled;

	/**
	 * NoShippingMethodsNotice constructor.
	 *
	 * @param bool $debug_mode_enabled .
	 */
	public function __construct( $debug_mode_enabled ) {
		$this->debug_mode_enabled = $debug_mode_enabled;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'woocommerce_load_shipping_methods', array( $this, 'display_notice_if_needed_for_package' ) );
	}

	/**
	 * .
	 *
	 * @param array $package .
	 */
	public function display_notice_if_needed_for_package( $package ) {
		if ( ! empty( $package ) && $this->debug_mode_enabled ) {
			$shipping_zone    = \WC_Shipping_Zones::get_zone_matching_package( $package );
			$shipping_methods = $shipping_zone->get_shipping_methods( true );
			if ( ! $this->zone_has_flexible_shipping( $shipping_methods ) ) {
				$this->add_notice(
					$shipping_zone->get_zone_name(),
					admin_url( 'admin.php?page=wc-settings&tab=shipping&zone_id=' . $shipping_zone->get_id() )
				);
			} elseif ( $this->are_not_flexible_shipping_methods_defined( $shipping_methods ) ) {
				$this->add_notice(
					$shipping_zone->get_zone_name(),
					admin_url( 'admin.php?page=wc-settings&tab=shipping&instance_id=' . $this->get_flexible_shipping_instance_id( $shipping_methods ) )
				);
			}
		}
	}

	/**
	 * .
	 *
	 * @param string $zone_name .
	 * @param string $link_url .
	 */
	private function add_notice( $zone_name, $link_url ) {
		wc_add_notice(
			sprintf(
				// Translators: shipping zone name and shipping method settings url.
				__( 'No shipping method handled by Flexible Shipping found in the %1$s shipping zone. %2$sAdd shipping method →%3$s', 'flexible-shipping' ),
				$zone_name,
				'<a target="_blank" href="' . $link_url . '">',
				'</a>'
			),
			'notice'
		);
	}

	/**
	 * @param array $shipping_methods .
	 *
	 * @return bool
	 */
	private function zone_has_flexible_shipping( $shipping_methods ) {
		foreach ( $shipping_methods as $shipping_method ) {
			if ( $shipping_method instanceof \WPDesk_Flexible_Shipping || ( class_exists( 'WPDesk\FS\TableRate\ShippingMethodSingle' ) && $shipping_method instanceof ShippingMethodSingle ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array $shipping_methods .
	 *
	 * @return bool
	 */
	private function are_not_flexible_shipping_methods_defined( $shipping_methods ) {
		foreach ( $shipping_methods as $shipping_method ) {
			if ( $shipping_method instanceof \WPDesk_Flexible_Shipping ) {
				$flexible_shipping_methods = $shipping_method->get_shipping_methods();
				if ( 0 === count( $shipping_method->get_shipping_methods() ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param array $shipping_methods .
	 *
	 * @return int
	 */
	private function get_flexible_shipping_instance_id( $shipping_methods ) {
		foreach ( $shipping_methods as $shipping_method ) {
			if ( $shipping_method instanceof \WPDesk_Flexible_Shipping ) {
				return $shipping_method->get_instance_id();
			}
		}

		return 0;
	}

}
