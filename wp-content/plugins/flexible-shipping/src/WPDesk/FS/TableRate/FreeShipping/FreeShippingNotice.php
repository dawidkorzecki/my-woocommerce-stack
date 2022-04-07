<?php
/**
 * Free Shipping Notice.
 *
 * @package WPDesk\FS\TableRate\FreeShipping
 */

namespace WPDesk\FS\TableRate\FreeShipping;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WC_Cart;
use WC_Session;
use WP;

/**
 * Can display free shipping notice.
 */
class FreeShippingNotice implements Hookable {

	const FLEXIBLE_SHIPPING_FREE_SHIPPING_NOTICE = 'flexible_shipping_free_shipping_notice';
	const NOTICE_TYPE = 'notice';
	const NOTICE_CONTAINER_CLASS = 'flexible-shipping-notice-container';

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
	public function __construct( WC_Cart $cart, WC_Session $session ) {
		$this->cart    = $cart;
		$this->session = $session;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		// Checkout.
		add_action( 'woocommerce_before_checkout_form', [ $this, 'add_checkout_notice_container' ] );
		add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'add_checkout_notice_to_fragments' ] );

		// Cart.
		add_action( 'woocommerce_before_cart', [ $this, 'add_checkout_notice_container' ] );
		add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'add_checkout_notice_to_fragments' ] );
	}

	/**
	 * @return void
	 */
	public function add_checkout_notice_container() {
		echo wp_kses_post( $this->get_left_free_shipping_notice_container() );
	}

	/**
	 * @param array $fragments .
	 *
	 * @return array
	 */
	public function add_checkout_notice_to_fragments( $fragments ) {
		if ( ! $this->cart->needs_shipping() ) {
			$notice = '';
		} else {
			$notice = '';

			$notice_message = $this->get_notice_message();
			if ( $notice_message ) {
				ob_start();
				wc_print_notice( $notice_message, self::NOTICE_TYPE, [ self::FLEXIBLE_SHIPPING_FREE_SHIPPING_NOTICE => 'yes' ] );
				$notice = ob_get_clean();
			}

			if ( ! is_string( $notice ) ) {
				$notice = '';
			}
		}

		$fragments[ '.' . self::NOTICE_CONTAINER_CLASS ] = $this->get_left_free_shipping_notice_container( $notice );

		return $fragments;
	}

	/**
	 * @param string $content .
	 *
	 * @return string
	 */
	private function get_left_free_shipping_notice_container( $content = '' ) {
		return sprintf( '<div class="%s">%s</div>', self::NOTICE_CONTAINER_CLASS, $content );
	}

	/**
	 * @return string
	 */
	private function get_notice_message() {
		$amount = $this->get_notice_amount();

		if ( $amount === 0.0 ) {
			return '';
		}

		return $this->prepare_notice_text( $amount );
	}

	/**
	 * @return float
	 */
	private function get_notice_amount() {
		return (float) $this->session->get( FreeShippingNoticeGenerator::SESSION_VARIABLE, 0.0 );
	}

	/**
	 * @param float $amount .
	 *
	 * @return string
	 */
	private function prepare_notice_text( $amount ) {
		$notice_text = sprintf(
		// Translators: cart value and shop link.
			__( 'You only need %1$s more to get free shipping! %2$sContinue shopping%3$s', 'flexible-shipping' ),
			wc_price( $amount ),
			'<a class="button" href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ) . '">',
			'</a>'
		);

		/**
		 * Notice text for Free Shipping.
		 *
		 * @param string $notice_text Notice text.
		 * @param float  $amount      Amount left to free shipping.
		 *
		 * @return string Message text.
		 */
		return apply_filters( 'flexible_shipping_free_shipping_notice_text', $notice_text, $amount );
	}
}
