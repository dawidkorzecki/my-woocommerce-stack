<?php
/**
 * Admin notices.
 *
 * @package Flexible Shipping
 */

/**
 * Can display notices in admin.
 */
class WPDesk_Flexible_Shipping_Admin_Notices {

	const SETTINGS_CHECKED_OPTION_VALUE_SHOW_MESSAGE = '1';

	const SETTINGS_CHECKED_OPTION_VALUE_DO_NOT_SHOW_MESSAGE = '2';

	const BASED_ON_VALUE = 'value';

	/**
	 * @var Flexible_Shipping_Plugin
	 */
	private $plugin;

	/**
	 * WPDesk_Flexible_Shipping_Admin_Notices constructor.
	 *
	 * @param Flexible_Shipping_Plugin $plugin .
	 */
	public function __construct( Flexible_Shipping_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Hooks.
	 */
	private function hooks() {
		add_action( 'admin_notices', array( $this, 'admin_notices_plugin_activepayments' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices_plugin_enadawca' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices_plugin_pwr' ) );
	}


	/**
	 * @param WC_Shipping_Method $shipping_method .
	 *
	 * @return bool
	 */
	private function has_value_based_rule( $shipping_method ) {
		$methods = get_option( 'flexible_shipping_methods_' . $shipping_method->instance_id, array() );
		if ( is_array( $methods ) ) {
			foreach ( $methods as $method_settings ) {
				if ( isset( $method_settings['method_rules'] ) && is_array( $method_settings['method_rules'] ) ) {
					foreach ( $method_settings['method_rules'] as $rule ) {
						if ( isset( $rule['based_on'] ) && self::BASED_ON_VALUE === $rule['based_on'] ) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function is_in_zones() {
		if ( isset( $_GET['page'] ) && 'wc-settings' === sanitize_key( $_GET['page'] )
			&& isset( $_GET['tab'] ) && 'shipping' === sanitize_key( $_GET['tab'] )
			&& ( ! isset( $_GET['section'] ) || '' === $_GET['section'] )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Active payments notice.
	 */
	public function admin_notices_plugin_activepayments() {
		if ( is_plugin_active( 'woocommerce-active-payments/activepayments.php' ) ) {
			$plugin_activepayments = get_plugin_data( WP_PLUGIN_DIR . '/woocommerce-active-payments/activepayments.php' );
			$version_compare       = version_compare( $plugin_activepayments['Version'], '2.7' );
			if ( $version_compare < 0 ) {
				$class   = 'notice notice-error';
				$message = __( 'Flexible Shipping requires at least version 2.7 of Active Payments plugin.', 'flexible-shipping' );
				$this->print_notice( $class, $message );
			}
		}
	}

	/**
	 * Enadawca notice.
	 */
	public function admin_notices_plugin_enadawca() {
		if ( is_plugin_active( 'woocommerce-enadawca/woocommerce-enadawca.php' ) ) {
			$plugin_enadawca = get_plugin_data( WP_PLUGIN_DIR . '/woocommerce-enadawca/woocommerce-enadawca.php' );
			$version_compare = version_compare( $plugin_enadawca['Version'], '1.2' );
			if ( $version_compare < 0 ) {
				$class   = 'notice notice-error';
				$message = __( 'Flexible Shipping requires at least version 1.2 of eNadawca plugin.', 'flexible-shipping' );
				$this->print_notice( $class, $message );
			}
		}
	}

	/**
	 * Paczka w Ruchu notice.
	 */
	public function admin_notices_plugin_pwr() {
		if ( is_plugin_active( 'woocommerce-paczka-w-ruchu/woocommerce-paczka-w-ruchu.php' ) ) {
			$plugin_pwr      = get_plugin_data( WP_PLUGIN_DIR . '/woocommerce-paczka-w-ruchu/woocommerce-paczka-w-ruchu.php' );
			$version_compare = version_compare( $plugin_pwr['Version'], '1.1' );
			if ( $version_compare < 0 ) {
				$class   = 'notice notice-error';
				$message = __( 'Flexible Shipping requires at least version 1.1 of Orlen Paczka plugin.', 'flexible-shipping' );
				$this->print_notice( $class, $message );
			}
		}
	}

	/**
	 * @param string $class .
	 * @param string $message .
	 */
	private function print_notice( $class, $message ) {
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); // phpcs:ignore
	}

}
