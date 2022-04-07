<?php
/**
 * Free Shipping Notice Generator.
 *
 * @package WPDesk\FS\TableRate\FreeShipping
 */

namespace WPDesk\FS\TableRate\FreeShipping;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WC_Cart;
use WC_Session;
use WC_Shipping_Rate;
use WPDesk\FS\TableRate\ShippingMethodSingle;
use WPDesk_Flexible_Shipping;

/**
 * Can generate free shipping notice and save it on session.
 */
class FreeShippingNoticeGenerator implements Hookable {

	const SETTING_METHOD_FREE_SHIPPING = 'method_free_shipping';
	const SESSION_VARIABLE = 'flexible_shipping_free_shipping_amount';
	const META_DATA_FS_METHOD = '_fs_method';
	const PRIORITY = 10;

	/**
	 * @var WC_Cart
	 */
	private $cart;

	/**
	 * @var WC_Session
	 */
	private $session;

	/**
	 * FreeShippingNotice constructor.
	 *
	 * @param WC_Cart    $cart    .
	 * @param WC_Session $session .
	 */
	public function __construct( $cart, $session ) {
		$this->cart    = $cart;
		$this->session = $session;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'woocommerce_package_rates', array( $this, 'add_free_shipping_notice_if_should' ), self::PRIORITY, 2 );
	}

	/**
	 * Triggered by filter. Must return $package_rates.
	 *
	 * @param array $package_rates .
	 * @param array $package       .
	 *
	 * @return array
	 */
	public function add_free_shipping_notice_if_should( $package_rates, $package ) {
		if ( $this->cart->needs_shipping() && $this->has_shipping_rate_with_free_shipping( $package_rates ) && ! $this->has_free_shipping_rate( $package_rates ) && $this->get_shipping_packages_count() === 1 ) {
			$this->add_free_shipping_amount_to_session( $package_rates );
		} else {
			$this->session->set( self::SESSION_VARIABLE, '' );
		}

		return $package_rates;
	}

	/**
	 * @return int
	 */
	private function get_shipping_packages_count() {
		return count( $this->cart->get_shipping_packages() );
	}

	/**
	 * Add free shipping notice.
	 *
	 * @param array $package_rates .
	 */
	private function add_free_shipping_amount_to_session( $package_rates ) {
		$lowest_free_shipping_limit = $this->get_lowest_free_shipping_limit( $package_rates );
		$amount                     = $lowest_free_shipping_limit - $this->get_cart_value();

		$this->session->set( self::SESSION_VARIABLE, $amount );
	}

	/**
	 * Has package free shipping rate?
	 *
	 * @param array $package_rates .
	 *
	 * @return bool
	 */
	private function has_free_shipping_rate( $package_rates ) {
		/** @var WC_Shipping_Rate $package_rate */
		foreach ( $package_rates as $package_rate ) {
			if ( floatval( $package_rate->get_cost() ) === 0.0 && ! $this->is_excluded_shipping_method( $package_rate->get_method_id() ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Is shipping method excluded from free shipping?
	 *
	 * @param string $method_id .
	 *
	 * @return bool
	 */
	private function is_excluded_shipping_method( $method_id ) {
		/**
		 * Exclude methods from free shipping.
		 *
		 * @param array $excluded_methods
		 *
		 * @return array
		 */
		$excluded_methods = apply_filters( 'flexible_shipping_free_shipping_notice_excluded_methods', array( 'local_pickup' ) );

		return in_array( $method_id, $excluded_methods, true );
	}

	/**
	 * Has package rate with free shipping?
	 *
	 * @param array $package_rates .
	 *
	 * @return bool
	 */
	private function has_shipping_rate_with_free_shipping( $package_rates ) {
		/** @var WC_Shipping_Rate $package_rate */
		foreach ( $package_rates as $package_rate ) {
			if ( $this->is_package_rate_from_flexible_shipping( $package_rate ) ) {
				$meta_data = $package_rate->get_meta_data();
				if ( isset( $meta_data[ self::META_DATA_FS_METHOD ] ) ) {
					if ( $this->has_shipping_method_free_shipping_notice_enabled( $meta_data[ self::META_DATA_FS_METHOD ] ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * @param array $fs_method .
	 *
	 * @return bool
	 */
	private function has_shipping_method_free_shipping_notice_enabled( array $fs_method ) {
		return ! empty( $fs_method[ self::SETTING_METHOD_FREE_SHIPPING ] )
			&& isset( $fs_method[ WPDesk_Flexible_Shipping::SETTING_METHOD_FREE_SHIPPING_NOTICE ] )
			&& 'yes' === $fs_method[ WPDesk_Flexible_Shipping::SETTING_METHOD_FREE_SHIPPING_NOTICE ]
			&& apply_filters( 'flexible-shipping/shipping-method/free-shipping-notice-allowed', true, $fs_method );
	}

	/**
	 * Returns current cart value.
	 *
	 * @return float
	 */
	private function get_cart_value() {
		return $this->cart->display_prices_including_tax() ? $this->cart->get_cart_contents_total() + $this->cart->get_cart_contents_tax() : $this->cart->get_cart_contents_total();
	}

	/**
	 * Returns lowest free shipping limit from available rates.
	 *
	 * @param array $package_rates .
	 *
	 * @return float
	 */
	private function get_lowest_free_shipping_limit( $package_rates ) {
		$lowest_free_shipping_limit = null;
		/** @var WC_Shipping_Rate $package_rate */
		foreach ( $package_rates as $package_rate ) {
			if ( $this->is_package_rate_from_flexible_shipping( $package_rate ) ) {
				$meta_data = $package_rate->get_meta_data();
				$fs_method = isset( $meta_data[ self::META_DATA_FS_METHOD ] ) ? $meta_data[ self::META_DATA_FS_METHOD ] : array();
				if ( $this->has_shipping_method_free_shipping_notice_enabled( $fs_method ) ) {
					$method_free_shipping_limit = round( floatval( $fs_method[ self::SETTING_METHOD_FREE_SHIPPING ] ), wc_get_rounding_precision() );
					$lowest_free_shipping_limit = min(
						$method_free_shipping_limit,
						null === $lowest_free_shipping_limit ? $method_free_shipping_limit : $lowest_free_shipping_limit
					);
				}
			}
		}

		return ( null != $lowest_free_shipping_limit ) ? (float) apply_filters( 'flexible_shipping_value_in_currency', $lowest_free_shipping_limit ) : null;
	}

	/**
	 * @param WC_Shipping_Rate $package_rate .
	 *
	 * @return bool
	 */
	private function is_package_rate_from_flexible_shipping( WC_Shipping_Rate $package_rate ) {
		$shipping_methods = array( WPDesk_Flexible_Shipping::METHOD_ID, ShippingMethodSingle::SHIPPING_METHOD_ID );

		return in_array( $package_rate->get_method_id(), $shipping_methods, true );
	}
}
