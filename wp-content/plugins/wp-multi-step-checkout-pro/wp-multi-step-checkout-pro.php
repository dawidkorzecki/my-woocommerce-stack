<?php
/**
 * Plugin Name: Multi-Step Checkout Pro for WooCommerce
 * Plugin URI: https://www.silkypress.com/woocommerce-multi-step-checkout-pro/
 * Description: Nice multi-step checkout for your WooCommerce store
 * Version: 2.27
 * Author: SilkyPress
 * Author URI: https://www.silkypress.com
 * License: GPL2
 *
 * Text Domain: wp-multi-step-checkout-pro
 * Domain Path: /languages/
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 6.2
 * Requires PHP: 5.2.4
 *
 * @package WPMultiStepCheckoutPro
 */

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'WPMultiStepCheckoutPro' ) ) :
	/**
	 * Main WPMultiStepCheckoutPro Class
	 *
	 * @class WPMultiStepCheckoutPro
	 */
	final class WPMultiStepCheckoutPro {

		/**
		 * Plugin's version.
		 *
		 * @var string
		 */
		public $version = '2.27';

		/**
		 * Plugin's options.
		 *
		 * @var array
		 */
		public $options = array();

		/**
		 * The instance of the class.
		 *
		 * @var WPMultiStepCheckoutPro
		 */
		protected static $_instance = null;

		/**
		 * Main WPMultiStepCheckoutPro Instance
		 *
		 * Ensures only one instance of WPMultiStepCheckoutPro is loaded or can be loaded
		 *
		 * @static
		 * @return WPMultiStepCheckoutPro - Main instance
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

			define( 'WMSC_PLUGIN_PRO_FILE', __FILE__ );
			define( 'WMSC_PLUGIN_PRO_URL', plugins_url( '/', __FILE__ ) );
			define( 'WMSC_PLUGIN_PRO_PATH', plugin_dir_url( '/', __FILE__ ) );
			define( 'WMSC_PRO_VERSION', $this->version );
			define( 'WMSC_PLUGIN_PRO_NAME', 'Multi-Step Checkout Pro for WooCommerce' );
			define( 'WMSC_PLUGIN_PRO_SERVER', 'https://www.silkypress.com' );
			define( 'WMSC_PLUGIN_PRO_AUTHOR', 'Diana Burduja' );

			if ( ! class_exists( 'woocommerce' ) ) {
				add_action( 'admin_notices', array( $this, 'install_woocommerce_admin_notice' ) );
				return false;
			}

			if ( is_admin() ) {
				include_once 'includes/admin-side.php';
			}

			// Replace the checkout template.
			add_filter( 'woocommerce_locate_template', array( $this, 'woocommerce_locate_template' ), 30, 3 );

			require_once 'includes/class-wmsc-pro-ajax.php';

			require_once 'includes/settings-array.php';
			$this->options    = get_option( 'wmsc_options', array() );
			$default_settings = get_wmsc_settings( 'wp-multi-step-checkout-pro' );
			foreach ( $default_settings as $_key => $_value ) {
				$default_settings[ $_key ] = $_value['value'];
			}
			$this->options = array_merge( $default_settings, $this->options );

			$this->adjust_hooks();

			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			add_action( 'init', array( $this, 'on_init' ) );

			include_once 'includes/class-wmsc-compatibilities-pro.php';
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
			add_action( 'wpmc_after_step_tabs', 'woocommerce_output_all_notices', 20 );
			remove_action( 'woocommerce_before_checkout_form_cart_notices', 'woocommerce_output_all_notices', 10 );

			// Add the content functions to the steps.
			add_action( 'wmsc_step_content_login', 'wmsc_step_content_login', 10 );
			add_action( 'wmsc_step_content_shipping', 'wmsc_step_content_shipping', 10 );
			add_action( 'wmsc_step_content_billing', 'wmsc_step_content_billing', 10 );

			// Add the "address review" in the Order section.
			if ( isset( $this->options['review_address'] ) && $this->options['review_address'] ) {
				add_action( 'woocommerce_checkout_before_order_review', array( $this, 'woocommerce_checkout_before_order_review' ), 10 );
			}
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
		 * Override the /templates/checkout/review-order.php template.
		 *   Used for adding the thumbnail images to the Order section.
		 *   Used only when the "" hook is used also by another plugin or the theme and the thumbnail images break the layout.
		 *
		 * @param string $template      Template name.
		 * @param string $template_name Template name.
		 * @param string $template_path Template path. (default: '').
		 * @return string
		 */
		public function woocommerce_locate_template_review_order( $template, $template_name, $template_path ) {
			if ( 'checkout/review-order.php' !== $template_name ) {
				return $template;
			}

			$wc_version = '5.2';
			if ( defined( 'WC_VERSION' ) ) {
				$wc_version = version_compare( WC_VERSION, '5.2.0', '<' ) ? '3.8' : $wc_version;
				$wc_version = version_compare( WC_VERSION, '3.8.0', '<' ) ? '3.7' : $wc_version;
				$wc_version = version_compare( WC_VERSION, '3.7.0', '<' ) ? '3.3' : $wc_version;
			}

			$template = plugin_dir_path( __FILE__ ) . 'includes/woo-templates/review-order-' . $wc_version . '.php';
			return $template;
		}

		/**
		 * Enqueue the JS and CSS assets
		 */
		public function wp_enqueue_scripts() {
			if ( ! is_checkout() ) {
				return;
			}

			$template      = ( isset( $this->options['template'] ) && ! empty( $this->options['template'] ) ) ? wp_strip_all_tags( $this->options['template'] ) : 'default';
			$color         = ( isset( $this->options['main_color'] ) && ! empty( $this->options['main_color'] ) ) ? wp_strip_all_tags( $this->options['main_color'] ) : '#1e85be';
			$visited_color = ( isset( $this->options['visited_color'] ) && ! empty( $this->options['visited_color'] ) ) ? wp_strip_all_tags( $this->options['visited_color'] ) : '#1ebe3a';
			$check_sign    = ( isset( $this->options['wpmc_check_sign'] ) && $this->options['wpmc_check_sign'] ) ? true : false;
			$url           = plugins_url( '/', __FILE__ ) . 'assets/';
			$prefix        = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// Localization variables.
			$vars = array(
				'keyboard_nav'        => ( isset( $this->options['keyboard_nav'] ) && $this->options['keyboard_nav'] ) ? true : false,
				'clickable_steps'     => ( isset( $this->options['clickable_steps'] ) && $this->options['clickable_steps'] ) ? true : false,
				'url_hash'            => ( isset( $this->options['url_hash'] ) && ! is_admin() && $this->options['url_hash'] ) ? true : false,
				'validation_per_step' => ( isset( $this->options['validation_per_step'] ) && $this->options['validation_per_step'] ) ? true : false,
				'validation_strings'  => array(
					/* translators: 1: Label of the required field */
					'required' => sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>{0}</strong>' ),
					/* translators: 1: User's email */
					'email'    => sprintf( __( '%s is not a valid email  address.', 'woocommerce' ), '<strong>{0}</strong>' ),
				),
				'translation_strings' => array(),
				'error_msg'           => ( isset( $this->options['t_error'] ) && $this->options['t_error'] ) ? __( wp_strip_all_tags( $this->options['t_error'] ) ) : '',
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'wmsc_check_errors' ),
			);
			if ( isset( $this->options['review_address'] ) && $this->options['review_address'] ) {
				$vars['translation_strings']['t_billing_review'] = $this->options['t_billing_review'];
				$vars['translation_strings']['t_shipping_review'] = $this->options['t_shipping_review'];
			}
			if ( isset( $this->options['t_wpml'] ) && $this->options['t_wpml'] ) {
				$defaults          = get_wmsc_settings( 'wp-multi-step-checkout-pro' );
				$vars['error_msg'] = $defaults['t_error']['value'];
			}

			// Load scripts.
			wp_register_script( 'wpmc', $url . 'js/script' . $prefix . '.js', array( 'jquery' ), $this->version, false );
			wp_localize_script( 'wpmc', 'WPMC', apply_filters( 'wmsc_js_variables', $vars ) );
			wp_register_style( 'wpmc', $url . 'css/style-' . $template . $prefix . '.css', array(), $this->version );

			wp_enqueue_script( 'wpmc' );
			wp_enqueue_style( 'wpmc' );

			// Load the inline styles.
			$style  = '.wpmc-tabs-wrapper .wpmc-tab-item.visited::before { border-bottom-color: ' . $visited_color . ' }';
			$style .= '.wpmc-tabs-wrapper .wpmc-tab-item.visited .wpmc-tab-number { border-color: ' . $visited_color . ' }';
			$style .= '.wpmc-tabs-wrapper-md .wpmc-tab-item.visited .wpmc-tab-number { background-color: ' . $visited_color . ' }';
			$style .= '.wpmc-tabs-wrapper-breadcrumb .wpmc-tab-item.visited .wpmc-tab-number {background-color: ' . $visited_color . '}';
			$style .= '.wpmc-tabs-wrapper .wpmc-tab-item.current::before {border-bottom-color: ' . $color . '}';
			$style .= '.wpmc-tabs-wrapper .wpmc-tab-item.current .wpmc-tab-number {border-color: ' . $color . '}';
			$style .= '.wpmc-tabs-wrapper-breadcrumb {border-color: ' . $color . ' !important;}';
			$style .= '.wpmc-tabs-wrapper-breadcrumb .wpmc-tabs-list .wpmc-tab-item:after {box-shadow: 3px -3px 0 1px' . $color . '}';
			$style .= '.wpmc-tabs-wrapper-breadcrumb .wpmc-tab-item.current .wpmc-tab-number {background-color: ' . $color . '}';
			$style .= '.wpmc-tabs-wrapper-md .wpmc-tab-item.current .wpmc-tab-number {background-color: ' . $color . '}';
			$style .= '@media screen and (max-width: 767px) { .wpmc-tabs-wrapper-breadcrumb .wpmc-tabs-list.wpmc-5-tabs .wpmc-tab-item.current {border-left: 3px solid ' . $color . '} }';
			$style .= '.woocommerce-checkout-review-order-table img.attachment-woocommerce_thumbnail { margin:0 auto; margin-bottom: 0; max-width:6em;height:auto}';
			$style .= '.wpmc-button, .wpmc-button:hover { background-color: ' . $color . ';color: white; border: none; cursor: pointer; }';
			$style .= '.wpmc-button::after, .wpmc-button::before, .wpmc-button:hover { content: none !important; border: none; color: white; }';
			$style .= '.woocommerce-checkout form.login .form-row { width: 100%; float: none; }';
			$style .= '.woocommerce-checkout .col2-set { width: 100%; }';

			if ( $check_sign ) {
				$style .= '.wpmc-tabs-wrapper .wpmc-tab-item .wpmc-tab-number::before {content: url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path style="fill:%23ffffff" d="M9.95 26.2c-0.1 0-0.15 0-0.25 0-0.45-0.050-0.9-0.35-1.1-0.8l-5.45-10.55c-0.4-0.75-0.1-1.65 0.65-2 0.75-0.4 1.65-0.1 2 0.65l4.5 8.75 16.1-15.95c0.6-0.6 1.55-0.6 2.1 0 0.6 0.6 0.6 1.55 0 2.1l-17.5 17.35c-0.25 0.3-0.65 0.45-1.050 0.45z"></path></svg>\') !important; width: 14px; height: 14px; position: absolute; opacity: 0; top: 3px; left: 6px;}';
				$style .= '.wpmc-tabs-wrapper-default .wpmc-tab-item .wpmc-tab-number::before {content: url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path style="fill:' . rawurlencode( $visited_color ) . '" d="M9.95 26.2c-0.1 0-0.15 0-0.25 0-0.45-0.050-0.9-0.35-1.1-0.8l-5.45-10.55c-0.4-0.75-0.1-1.65 0.65-2 0.75-0.4 1.65-0.1 2 0.65l4.5 8.75 16.1-15.95c0.6-0.6 1.55-0.6 2.1 0 0.6 0.6 0.6 1.55 0 2.1l-17.5 17.35c-0.25 0.3-0.65 0.45-1.050 0.45z"></path></svg>\') !important; left: 8px; top: 2px;}';
				$style .= '.wpmc-tabs-wrapper .wpmc-tab-item.visited .wpmc-tab-number { color: transparent; position: relative; }';
				$style .= '.wpmc-tabs-wrapper .wpmc-tab-item.current .wpmc-tab-number { color: white; }';
				$style .= '.wpmc-tabs-wrapper-default .wpmc-tab-item.current .wpmc-tab-number { color: ' . $color . ' }';
				$style .= '.wpmc-tabs-wrapper .wpmc-tab-item.visited .wpmc-tab-number::before { opacity: 1;}';
				$style .= '.wpmc-tabs-wrapper .wpmc-tab-item.current .wpmc-tab-number::before { opacity: 0;}';
			}

			if ( isset( $this->options['review_thumbnails'] ) && $this->options['review_thumbnails'] ) {
				$style .= '.woocommerce_cart_item_name {display: flex;width: 100%;flex-direction: row;align-items: center;}';
				$style .= '.woocommerce_cart_item_name_title {flex: auto; padding-left: 20px;}';
				$style .= '@media screen and (max-width: 600px){.woocommerce_cart_item_name {flex-direction: column;}.woocommerce_cart_item_name_title {padding-left:0}}';
				$style .= 'table.woocommerce-checkout-review-order-table .product-name { width: 60%; }';
				$style .= '.woocommerce-checkout-review-order-table .product-total { vertical-align: middle; }';
			}

			if ( is_rtl() ) {
				$style .= '.wpmc-tabs-list .wpmc-tab-item {float: right;}';
				$style .= '.wpmc-tabs-wrapper-md .wpmc-tab-item:first-child .wpmc-tab-bar-left, .wpmc-tabs-wrapper-md .wpmc-tab-item:last-child .wpmc-tab-bar-right {display: block;}';
				$style .= '.wpmc-tabs-wrapper-md .wpmc-tab-item:first-child .wpmc-tab-bar-right, .wpmc-tabs-wrapper-md .wpmc-tab-item:last-child .wpmc-tab-bar-left {display: none;}';
			}
			wp_add_inline_style( 'wpmc', $style );
		}


		/**
		 * Show product thumbnails in the Order Review section.
		 *
		 * @param string $product_title Product title.
		 * @param array  $cart_item     Cart item.
		 * @param string $cart_item_key Cart item key.
		 */
		public function cart_thumbnail( $product_title, $cart_item, $cart_item_key ) {

			if ( ! is_checkout() ) {
				return $product_title;
			}

			$_product  = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail', array( 'class' => 'attachment-woocommerce_thumbnail size-woocommerce_thumbnail wmsc-thumbnail' ) ), $cart_item, $cart_item_key );

			echo '<div class="woocommerce_cart_item_name"><div class="woocommerce_cart_item_name_thumbnail">';
			echo wp_kses_post( $thumbnail );
			echo '</div><div class="woocommerce_cart_item_name_title">';
			echo $product_title;
		}
		/**
		 * Show product thumbnails in the Order Review section.
		 *
		 * @param integer $quantity      Quantity.
		 * @param array   $cart_item     Cart item.
		 * @param string  $cart_item_key Cart item key.
		 */
		public function cart_thumbnail_end( $quantity, $cart_item, $cart_item_key ) {
			if ( ! is_checkout() ) {
				return;
			}
			echo $quantity . '</div></div>';
		}


		/**
		 * Show the address review in the Order section.
		 */
		public function woocommerce_checkout_before_order_review() {
			echo '<div id="address_review">' .
				'<div class="address_review_1"></div>'.
				'<div class="address_review_2"></div>'.
			'</div>';
		}


		/**
		 * Fire on the WP "init" hook.
		 */
		public function on_init() {

			// Add product thumbnail in the Order section.
			if ( isset( $this->options['review_thumbnails'] ) && $this->options['review_thumbnails'] && apply_filters( 'wmsc_review_thumbnails_enable', true ) ) {
				if ( apply_filters( 'wmsc_review_thumbnails_template', false ) ) {
					add_filter( 'woocommerce_locate_template', array( $this, 'woocommerce_locate_template_review_order' ), 30, 3 );
				} else {
					add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_thumbnail' ), 20, 3 );
					add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'cart_thumbnail_end' ), 20, 3 );
				}
			}
		}


		/**
		 * Admin notice that WooCommerce is not activated
		 */
		public function install_woocommerce_admin_notice() {
			?><div class="error">
			<p><?php _x( 'The <b>Multi-Step Checkout Pro for WooCommerce </b> plugin is enabled, but it requires WooCommerce in order to work.', 'Alert Message: WooCommerce require', 'wp-multi-step-checkout-pro' ); ?></p>
			</div>
			<?php
		}


		/**
		 * Load the textdomain
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wp-multi-step-checkout-pro', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

	}

endif;

/**
 * Returns the main instance of WPMultiStepCheckoutPro
 *
 * @return WPMultiStepCheckoutPro
 */
function WPMultiStepCheckoutPro() {
	return WPMultiStepCheckoutPro::instance();
}

WPMultiStepCheckoutPro();

/**
 * Add Settings link on the Plugins page.
 *
 * @param array $links Currently available links.
 */
function wpmc_pro_plugin_settings_link( $links ) {
	$action_links = array(
		'settings' => '<a href="' . admin_url( 'admin.php?page=wmsc-settings' ) . '" aria-label="' . esc_attr__( 'View plugin\'s settings', 'wp-multi-step-checkout-pro' ) . '">' . esc_html__( 'Settings', 'wp-multi-step-checkout-pro' ) . '</a>',
	);
	return array_merge( $action_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wpmc_pro_plugin_settings_link' );
