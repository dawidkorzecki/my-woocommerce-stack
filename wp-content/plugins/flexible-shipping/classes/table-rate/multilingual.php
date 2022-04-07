<?php
/**
 * Class WPDesk_Flexible_Shipping_Multilingual
 *
 * @package Flexible Shipping
 */

/**
 * Handles multilangual.
 */
class WPDesk_Flexible_Shipping_Multilingual {

	/**
	 * @var Flexible_Shipping_Plugin
	 */
	private $plugin;

	/**
	 * WPDesk_Flexible_Shipping_Multilingual constructor.
	 *
	 * @param Flexible_Shipping_Plugin $plugin .
	 */
	public function __construct( Flexible_Shipping_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * .
	 */
	private function hooks() {
		add_action( 'woocommerce_init', array( $this, 'init_polylang' ) );
		add_action( 'admin_init', array( $this, 'init_wpml' ) );
	}

	/**
	 * .
	 */
	public function init_polylang() {
		if ( function_exists( 'pll_register_string' ) ) {
			$all_shipping_methods    = flexible_shipping_get_all_shipping_methods();
			$flexible_shipping       = $all_shipping_methods['flexible_shipping'];
			$flexible_shipping_rates = $flexible_shipping->get_all_rates();
			foreach ( $flexible_shipping_rates as $flexible_shipping_rate ) {
				if ( isset( $flexible_shipping_rate['method_title'] ) ) {
					pll_register_string( $flexible_shipping_rate['method_title'], $flexible_shipping_rate['method_title'], __( 'Flexible Shipping', 'flexible-shipping' ) );
				}
				if ( isset( $flexible_shipping_rate['method_description'] ) ) {
					pll_register_string( $flexible_shipping_rate['method_description'], $flexible_shipping_rate['method_description'], __( 'Flexible Shipping', 'flexible-shipping' ) );
				}
				if ( isset( $flexible_shipping_rate['method_free_shipping_label'] ) ) {
					pll_register_string( $flexible_shipping_rate['method_free_shipping_label'], $flexible_shipping_rate['method_free_shipping_label'], __( 'Flexible Shipping', 'flexible-shipping' ) );
				}
			}
		}
	}

	/**
	 * .
	 */
	public function init_wpml() {
		if ( function_exists( 'icl_register_string' ) ) {
			$icl_language_code       = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : get_bloginfo( 'language' );
			$all_shipping_methods    = flexible_shipping_get_all_shipping_methods();
			$flexible_shipping       = $all_shipping_methods['flexible_shipping'];
			$flexible_shipping_rates = $flexible_shipping->get_all_rates();
			foreach ( $flexible_shipping_rates as $flexible_shipping_rate ) {
				if ( isset( $flexible_shipping_rate['method_title'] ) ) {
					icl_register_string( 'flexible-shipping', $flexible_shipping_rate['method_title'], $flexible_shipping_rate['method_title'], false, $icl_language_code );
				}
				if ( isset( $flexible_shipping_rate['method_description'] ) ) {
					icl_register_string( 'flexible-shipping', $flexible_shipping_rate['method_description'], $flexible_shipping_rate['method_description'], false, $icl_language_code );
				}
				if ( isset( $flexible_shipping_rate['method_free_shipping_label'] ) ) {
					icl_register_string( 'flexible-shipping', $flexible_shipping_rate['method_free_shipping_label'], $flexible_shipping_rate['method_free_shipping_label'], false, $icl_language_code );
				}
			}
		}
	}

}
