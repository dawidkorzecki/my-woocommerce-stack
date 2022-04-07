<?php
/**
 * Class ConvertNotice
 *
 * @package WPDesk\FS\TableRate\ShippingMethod\Convert
 */

namespace WPDesk\FS\TableRate\ShippingMethod\Convert;

use FSVendor\WPDesk\Notice\Notice;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WC_Shipping_Method;
use WC_Shipping_Zones;
use WPDesk_Flexible_Shipping;

/**
 * Display admin notices.
 */
class ConvertNotice implements Hookable {
	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'woocommerce_settings_shipping', array( $this, 'add_admin_notice' ) );
	}

	/**
	 * Add admin notices.
	 */
	public function add_admin_notice() {
		$this->display_required_convert_message();
		$this->display_success_converted_message();
	}

	/**
	 * @return bool
	 */
	private function is_limited_notice_width() {
		return ! wpdesk_is_plugin_active( 'flexible-shipping-pro/flexible-shipping-pro.php' ) || ! wpdesk_is_plugin_active( 'flexible-shipping-import-export/flexible-shipping-import-export.php' );
	}

	/**
	 * Display notice on shipping method page.
	 */
	private function display_required_convert_message() {
		$zone_id     = filter_input( INPUT_GET, 'zone_id', FILTER_VALIDATE_INT );
		$instance_id = filter_input( INPUT_GET, 'instance_id', FILTER_VALIDATE_INT );

		if ( $zone_id && ! $instance_id ) {
			return;
		}

		/** @var WPDesk_Flexible_Shipping $shipping_method */
		$shipping_method = WC_Shipping_Zones::get_shipping_method( $instance_id );

		if ( ! $shipping_method instanceof WPDesk_Flexible_Shipping ) {
			return;
		}

		$convert_url = $this->get_convert_url( $instance_id );

		$is_limited_notice_width = $this->is_limited_notice_width();

		if ( $shipping_method->is_converted() ) {
			$convert_url = add_query_arg( 'converting_again', 'true', $convert_url );

			$this->add_notice(
				sprintf(
				// Translators: URL.
					__( 'Flexible Shipping group method you are currently viewing has been converted to its new single version and deactivated for safety reasons. Once you make sure everything was converted properly, you can safely delete this group method. If you notice any discrepancies please %1$srun the conversion process once again%2$s.%3$sIf you use any custom functions or plugins targeting the specific shipping methods based on their IDs, e.g. %4$sFlexible Checkout Fields PRO%5$s, %6$sActive Payments%7$s, %8$sShopMagic%9$s or similar, please re-check their configuration in order to maintain their proper functioning after the conversion.', 'flexible-shipping' ),
					'<a href="' . esc_url( $convert_url ) . '">',
					'</a>',
					'<br/>',
					'<a href="' . esc_url( $this->get_admin_url_fsf_pro_settings() ) . '" target="_blank">',
					'</a>',
					'<a href="' . esc_url( $this->get_admin_url_ap_settings() ) . '" target="_blank">',
					'</a>',
					'<a href="' . esc_url( $this->get_admin_url_sm_settings() ) . '" target="_blank">',
					'</a>'
				),
				'error',
				$is_limited_notice_width
			);

			return;
		}

		$learn_more_url = 'pl_PL' === get_locale() ? 'https://wpde.sk/fs-migration-pl' : 'https://wpde.sk/fs-migration';

		if ( count( $shipping_method->get_shipping_methods() ) > 0 ) {
			$this->add_notice(
				sprintf(
				// Translators: URL.
					__( 'Flexible Shipping group methods are no longer supported. Despite the fact that they still remain editable, no other new features are going to be added to them. It is highly recommended to convert them to the supported single ones. %1$sStart converting%2$s or %3$slearn more about it &rarr;%4$s.%5$sPlease mind that if you use any custom functions or plugins targeting the specific shipping methods based on their IDs, e.g. %6$sFlexible Checkout Fields PRO%7$s, %8$sActive Payments%9$s, %10$sShopMagic%11$s or similar, you may need to reconfigure them after the conversion to remain their proper functioning.', 'flexible-shipping' ),
					'<a href="' . esc_url( $convert_url ) . '">',
					'</a>',
					'<a href="' . esc_url( $learn_more_url ) . '" target="_blank">',
					'</a>',
					'<br/>',
					'<a href="' . esc_url( $this->get_admin_url_fsf_pro_settings() ) . '" target="_blank">',
					'</a>',
					'<a href="' . esc_url( $this->get_admin_url_ap_settings() ) . '" target="_blank">',
					'</a>',
					'<a href="' . esc_url( $this->get_admin_url_sm_settings() ) . '" target="_blank">',
					'</a>'
				),
				'error',
				$is_limited_notice_width
			);
		} else {
			$this->add_notice(
				sprintf(
				// Translators: URL.
					__( 'Flexible Shipping group method is no longer supported. %1$sLearn more about it &rarr;%2$s.', 'flexible-shipping' ),
					'<a href="' . esc_url( $learn_more_url ) . '" target="_blank">',
					'</a>'
				),
				'error',
				$is_limited_notice_width
			);
		}
	}

	/**
	 * @param string $message                 .
	 * @param string $type                    .
	 * @param bool   $is_limited_notice_width .
	 */
	private function add_notice( $message, $type = 'error', $is_limited_notice_width = false ) {
		$classes = array( 'fs-notice js--move-notice inline' );

		if ( $is_limited_notice_width ) {
			$classes[] = 'is-limited-width';
		}

		new Notice( $message, $type, false, 10, array( 'class' => implode( ' ', $classes ) ) );

		if ( ! has_action( 'admin_footer', array( $this, 'move_notice_script' ) ) ) {
			add_action( 'admin_footer', array( $this, 'move_notice_script' ) );
		}
	}

	/**
	 * Script to move notices.
	 */
	public function move_notice_script() {
		include __DIR__ . '/views/move-notices.php';
	}

	/**
	 * @param int $instance_id .
	 *
	 * @return string
	 */
	private function get_convert_url( $instance_id ) {
		$convert_url = admin_url( 'admin-ajax.php' );
		$convert_url = add_query_arg( 'instance_id', $instance_id, $convert_url );
		$convert_url = add_query_arg( 'action', ConvertAction::AJAX_ACTION, $convert_url );

		return wp_nonce_url( $convert_url, ConvertAction::AJAX_NONCE );
	}

	/**
	 * Display notice when shipping method has been converted.
	 */
	private function display_success_converted_message() {
		$converted = filter_input( INPUT_GET, 'converted' );

		if ( $converted ) {
			new Notice( __( 'Flexible Shipping group method has been converted to its new single version and deactivated for safety reasons. Once you make sure everything was converted properly, you can safely delete the previous group method.', 'flexible-shipping' ), 'success' );
		}
	}

	/**
	 * @return string
	 */
	private function get_admin_url_fsf_pro_settings() {
		return $this->get_admin_url( 'admin.php', 'page', 'inspire_checkout_fields_settings' );
	}

	/**
	 * @return string
	 */
	private function get_admin_url_ap_settings() {
		return $this->get_admin_url( 'admin.php', 'page', 'woocommerce_activepayments' );
	}

	/**
	 * @return string
	 */
	private function get_admin_url_sm_settings() {
		return $this->get_admin_url( 'edit.php', 'post_type', 'shopmagic_automation' );
	}

	/**
	 * @param string $page  .
	 * @param string $key   .
	 * @param string $value .
	 *
	 * @return string
	 */
	private function get_admin_url( $page, $key, $value ) {
		return add_query_arg( $key, $value, admin_url( $page ) );
	}
}
