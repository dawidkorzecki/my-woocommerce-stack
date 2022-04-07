<?php
/**
 * Class DuplicateAction
 *
 * @package WPDesk\FS\TableRate\ShippingMethod\Duplicate
 */

namespace WPDesk\FS\TableRate\ShippingMethod\Duplicate;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\TableRate\ShippingMethod\Management\ShippingMethodManagement;
use WPDesk\FS\TableRate\ShippingMethodSingle;

/**
 * Duplicate Action.
 */
class DuplicateAction implements Hookable {
	const ACTION   = 'fs_duplicate_method';
	const OPTION   = 'fs_duplicate_method';
	const PARAM_ID = 'instance_id';

	/**
	 * @var ShippingMethodManagement
	 */
	private $shipping_method_management;

	/**
	 * @var DuplicatorChecker
	 */
	private $duplicator_checker;

	/**
	 * @param DuplicatorChecker        $duplicator_checker         .
	 * @param ShippingMethodManagement $shipping_method_management .
	 */
	public function __construct( DuplicatorChecker $duplicator_checker, ShippingMethodManagement $shipping_method_management ) {
		$this->duplicator_checker         = $duplicator_checker;
		$this->shipping_method_management = $shipping_method_management;
	}

	/**
	 * Init hooks (actions and filters).
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_post_' . self::ACTION, [ $this, 'action_duplicate' ] );
	}

	public function action_duplicate() {
		check_admin_referer( self::ACTION );

		$instance_id = (int) ( sanitize_text_field( wp_unslash( $_GET[ self::PARAM_ID ] ?? 0 ) ) );

		if ( ! $instance_id ) {
			wp_die( __( 'Shipping method duplication error. Please try again later.', 'flexible-shipping' ) ); // phpcs:ignore

			return;
		}

		$title = $this->shipping_method_management->get_shipping_method( $instance_id )->get_title();

		if ( ! $this->duplicator_checker->should_duplicate( $instance_id ) ) {
			wp_redirect( $this->get_redirect_url( 'error', $title ) );
			$this->end_request();

			return;
		}

		$zone            = $this->shipping_method_management->get_shipping_zone( $instance_id );
		$new_instance_id = $zone->add_shipping_method( ShippingMethodSingle::SHIPPING_METHOD_ID );

		$options = $this->get_instance_settings( $instance_id );

		if ( empty( $options['method_title'] ?? '' ) ) {
			$options['method_title'] = __( 'Flexible Shipping', 'flexible-shipping' );
		}

		$options['method_title'] .= ' ' . __( '(Copy)', 'flexible-shipping' );

		add_option( $this->get_option_settings_field( $new_instance_id ), $options );

		$this->shipping_method_management->set_shipping_method_status( $new_instance_id, false, $zone );

		$this->update_usage_functionality();

		wp_redirect( $this->get_redirect_url( 'success', $title ) );
		$this->end_request();
	}

	/**
	 * @codeCoverageIgnore
	 */
	protected function end_request() {
		die();
	}

	/**
	 * @param int $instance_id .
	 *
	 * @return array
	 */
	private function get_instance_settings( int $instance_id ): array {
		return (array) get_option( $this->get_option_settings_field( $instance_id ), [] );
	}

	/**
	 * @param int $instance_id .
	 *
	 * @return string
	 */
	private function get_option_settings_field( int $instance_id ): string {
		return sprintf( 'woocommerce_%s_%d_settings', ShippingMethodSingle::SHIPPING_METHOD_ID, $instance_id );
	}

	private function update_usage_functionality() {
		$current = (int) get_option( self::OPTION, 0 );

		update_option( self::OPTION, ++$current );
	}

	/**
	 * @param string $status       .
	 * @param string $method_title .
	 *
	 * @return string
	 */
	private function get_redirect_url( string $status, string $method_title ): string {
		return add_query_arg(
			[
				self::ACTION   => true,
				'status'       => $status,
				'method_title' => urlencode( $method_title ),
			],
			wp_get_referer()
		);
	}
}
