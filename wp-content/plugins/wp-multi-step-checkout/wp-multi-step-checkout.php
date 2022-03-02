<?php
/**
 * Plugin Name: Multi-Step Checkout for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/wp-multi-step-checkout/
 * Description: Nice multi-step checkout for your WooCommerce store
 * Version: 2.19
 * Author: SilkyPress
 * Author URI: https://www.silkypress.com
 * License: GPL2
 *
 * Text Domain: wp-multi-step-checkout
 * Domain Path: /languages/
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 6.1
 * Requires PHP: 5.2.4
 *
 * @package WPMultiStepCheckout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WPMultiStepCheckout' ) ) :
	/**
	 * Main WPMultiStepCheckout Class
	 *
	 * @class WPMultiStepCheckout
	 */
	final class WPMultiStepCheckout {

		/**
		 * Plugin's version.
		 *
		 * @var string
		 */
		public $version = '2.19';

		/**
		 * Plugin's options.
		 *
		 * @var array
		 */
		public $options = array();

		/**
		 * The instance of the class.
		 *
		 * @var WPMultiStepCheckout
		 */
		protected static $_instance = null;


		/**
		 * Main WPMultiStepCheckout Instance
		 *
		 * Ensures only one instance of WPMultiStepCheckout is loaded or can be loaded
		 *
		 * @static
		 * @return WPMultiStepCheckout - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'An error has occurred. Please reload the page and try again.' ), '1.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'An error has occurred. Please reload the page and try again.' ), '1.0' );
		}

		/**
		 * WPMultiStepCheckout Constructor
		 */
		public function __construct() {

			define( 'WMSC_PLUGIN_FILE', __FILE__ );
			define( 'WMSC_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
			define( 'WMSC_PLUGIN_PATH', plugin_dir_url( '/', __FILE__ ) );
			define( 'WMSC_VERSION', $this->version );

			if ( ! class_exists( 'woocommerce' ) ) {
				add_action( 'admin_notices', array( $this, 'install_woocommerce_admin_notice' ) );
				return false;
			}

			if ( is_admin() ) {
				include_once 'includes/admin-side.php';
			}

			$this->update_14_version();

			add_filter( 'woocommerce_locate_template', array( $this, 'woocommerce_locate_template' ), 30, 3 );

			$this->adjust_hooks();

			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			include_once 'includes/class-wmsc-compatibilities.php';
		}


		/**
		 * Modify the default WooCommerce hooks
		 */
		public function adjust_hooks() {
			// Remove login messages.
			remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
			remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

			// Split the `Order` and the `Payment` tabs.
			remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
			remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
			add_action( 'wpmc-woocommerce_order_review', 'woocommerce_order_review', 20 );
			add_action( 'wpmc-woocommerce_checkout_payment', 'woocommerce_checkout_payment', 10 );

			// Split the `woocommerce_before_checkout_form`.
			remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
			remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
			add_action( 'wpmc-woocommerce_checkout_login_form', 'woocommerce_checkout_login_form', 10 );
			add_action( 'wpmc-woocommerce_checkout_coupon_form', 'woocommerce_checkout_coupon_form', 10 );

			// Add the content functions to the steps.
			add_action( 'wmsc_step_content_login', 'wmsc_step_content_login', 10 );
			add_action( 'wmsc_step_content_shipping', 'wmsc_step_content_shipping', 10 );
			add_action( 'wmsc_step_content_billing', 'wmsc_step_content_billing', 10 );
		}

		/**
		 * Load the form-checkout.php template from this plugin.
		 *
		 * @param string $template      Template name.
		 * @param string $template_name Template name.
		 * @param string $template_path Template path. (default: '').
		 * @return string
		 */
		public function woocommerce_locate_template( $template, $template_name, $template_path ) {
			if ( 'checkout/form-checkout.php' !== $template_name ) {
				return $template;
			}
			$template = plugin_dir_path( __FILE__ ) . 'includes/form-checkout.php';
			return $template;
		}

		/**
		 * Enqueue the JS and CSS assets
		 */
		public function wp_enqueue_scripts() {

			if ( ! is_checkout() ) {
				return;
			}

			$options      = get_option( 'wmsc_options' );
			$keyboard_nav = ( isset( $options['keyboard_nav'] ) && $options['keyboard_nav'] ) ? true : false;
			$color        = ( isset( $options['main_color'] ) ) ? wp_strip_all_tags( $options['main_color'] ) : '#1e85be';
			$url          = plugins_url( '/', __FILE__ ) . 'assets/';
			$prefix       = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// Load scripts.
			wp_register_script( 'wpmc', $url . 'js/script' . $prefix . '.js', array( 'jquery' ), $this->version, true );
			wp_localize_script( 'wpmc', 'WPMC', apply_filters( 'wmsc_js_variables', array( 'keyboard_nav' => $keyboard_nav ) ) );
			wp_register_style( 'wpmc', $url . 'css/style-progress' . $prefix . '.css', array(), $this->version );

			wp_enqueue_script( 'wpmc' );
			wp_enqueue_style( 'wpmc' );

			// Load the inline styles.
			$style  = '.wpmc-tabs-wrapper .wpmc-tab-item.current::before { border-bottom-color:' . $color . '; }';
			$style .= '.wpmc-tabs-wrapper .wpmc-tab-item.current .wpmc-tab-number { border-color: ' . $color . '; }';
			if ( is_rtl() ) {
				$style .= '.wpmc-tabs-list .wpmc-tab-item { float: right; }';
			}
			wp_add_inline_style( 'wpmc', $style );
		}

		/**
		 * Admin notice that WooCommerce is not activated
		 */
		public function install_woocommerce_admin_notice() {
			?>
			<div class="error">
			<p><?php _x( 'The <b>Multi-Step Checkout for WooCommerce</b> plugin is enabled, but it requires WooCommerce in order to work.', 'Alert Message: WooCommerce require', 'wp-multi-step-checkout' ); ?></p>
			</div>
			<?php
		}


		/**
		 * Load the textdomain
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wp-multi-step-checkout', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}


		/**
		 * Update options array for the 1.4 version
		 */
		public function update_14_version() {
			if ( ! $old_options = get_option( 'wpmc-settings' ) ) {
				return;
			}

			require_once 'includes/settings-array.php';
			$defaults = get_wmsc_settings();

			$new_options = array();
			foreach ( $defaults as $_key => $_value ) {
				if ( isset( $old_options[ $_key ] ) ) {
					$new_options[ $_key ] = $old_options[ $_key ][2];
				} else {
					$new_options[ $_key ] = $_value['value'];
				}
			}

			update_option( 'wmsc_options', $new_options );
			delete_option( 'wpmc-settings' );
		}

	}

endif;

/**
 * Returns the main instance of WPMultiStepCheckout
 *
 * @return WPMultiStepCheckout
 */
function WPMultiStepCheckout() {
	return WPMultiStepCheckout::instance();
}

WPMultiStepCheckout();

/**
 * Add Settings link on the Plugins page.
 *
 * @param array $links Currently available links.
 */
function wpmc_plugin_settings_link( $links ) {
	$action_links = array(
		'settings' => '<a href="' . admin_url( 'admin.php?page=wmsc-settings' ) . '" aria-label="' . esc_attr__( 'View plugin\'s settings', 'wp-multi-step-checkout' ) . '">' . esc_html__( 'Settings', 'wp-multi-step-checkout' ) . '</a>',
	);
	return array_merge( $action_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wpmc_plugin_settings_link' );
