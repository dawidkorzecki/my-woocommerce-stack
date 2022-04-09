<?php
/**
 * Admin settings class
 *
 * @package WPMultiStepCheckoutPro
 */

defined( 'ABSPATH' ) || exit;


class WPMultiStepCheckoutPro_Settings {

	public $messages = array();

	private $settings = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$free_version = 'wp-multi-step-checkout/wp-multi-step-checkout.php';
		if ( is_plugin_active( $free_version ) ) {
			deactivate_plugins( $free_version );
			$pro_version = 'wp-multi-step-checkout-pro/wp-multi-step-checkout-pro.php';
			activate_plugins( $pro_version );
			return false;
		}

		require_once 'settings-array.php';
		require_once 'frm/class-form-fields.php';
		require_once 'frm/warnings.php';

		$this->settings = get_wmsc_settings( 'wp-multi-step-checkout-pro' );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_wpmc_license_beta', array( $this, 'license_beta' ) );

		$this->edd_updater();
		$this->warnings();
	}

	/**
	 * Create the menu link
	 */
	function admin_menu() {
		add_submenu_page(
			'woocommerce',
			'Multi-Step Checkout Pro',
			'Multi-Step Checkout Pro',
			'manage_options',
			'wmsc-settings',
			array( $this, 'admin_settings_page' )
		);
	}

	/**
	 * Enqueue the scripts and styles
	 */
	function admin_enqueue_scripts() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_URL );
		if ( $page != 'wmsc-settings' ) {
			return false;
		}

		// Color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		$u = plugins_url( '/', WMSC_PLUGIN_PRO_FILE ) . 'assets/';     // assets url
		$f = plugins_url( '/', WMSC_PLUGIN_PRO_FILE ) . 'includes/frm/assets/';           // framework assets url
		$v = WMSC_PRO_VERSION;          // version
		$d = array( 'jquery' );       // dependency
		$w = true;                  // where? in the footer?

		// Load scripts
		wp_enqueue_script( 'wmsc-bootstrap', $f . 'bootstrap.min.js', $d, $v, $w );
		wp_enqueue_script( 'wmsc-admin-script', $u . 'js/admin-script.js', $d, $v, $w );

		// Load styles
		wp_enqueue_style( 'wmsc-bootstrap', $f . 'bootstrap.min.css', array(), $v );
		wp_enqueue_style( 'wmsc-admin-style', $u . 'css/admin-style.css', array(), $v );

		// Load preview template
		if ( filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_URL ) === 'design' ) {
			$options  = get_option( 'wmsc_options' );
			$template = ( isset( $options['template'] ) && ! empty( $options['template'] ) ) ? $options['template'] : 'default';
			$p        = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			wp_enqueue_style( 'wmsc-style', $u . 'css/style-' . $template . '.css', array(), $v );
			wp_register_script( 'wpmc', $u . 'js/script' . $p . '.js', $d, $v, $w );
			wp_localize_script(
				'wpmc',
				'WPMC',
				array(
					'keyboard_nav'        => ( isset( $options['keyboard_nav'] ) && $options['keyboard_nav'] ) ? true : false,
					'clickable_steps'     => ( isset( $options['clickable_steps'] ) && $options['clickable_steps'] ) ? true : false,
					'validation_per_step' => ( isset( $options['validation_per_step'] ) && $options['validation_per_step'] ) ? true : false,
				)
			);
			wp_enqueue_script( 'wpmc' );
		}
	}


	/**
	 * Output the admin page
	 *
	 * @access public
	 */
	public function admin_settings_page() {

		// Get the tabs.
		$tabs = array(
			'general' => __( 'General Settings', 'wp-multi-step-checkout-pro' ),
			'design'  => __( 'Design', 'wp-multi-step-checkout-pro' ),
			'titles'  => __( 'Text on Steps and Buttons', 'wp-multi-step-checkout-pro' ),
			'license' => __( 'License', 'wp-multi-step-checkout-pro' ),
			'license' => __( 'License', 'wp-multi-step-checkout-pro' ),
		);

		$tab_current = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general';

		if ( ! isset( $tabs[ $tab_current ] ) ) {
			$tab_current = 'general';
		}

		// Get the field settings.
		$settings_all   = get_wmsc_settings( 'wp-multi-step-checkout-pro' );
		$values_current = get_option( 'wmsc_options', array() );

		$form = new \SilkyPressFrm\Form_Fields( $settings_all );
		$form->add_setting( 'tooltip_img', plugins_url( '/', WMSC_PLUGIN_PRO_FILE ) . 'assets/images/question_mark.svg' );
		$form->add_setting( 'section', $tab_current );
		$form->add_setting( 'label_class', 'col-sm-5' );
		$form->add_setting( 'disable_pro', false );
		$form->set_current_values( $values_current );

		// The settings were saved.
		if ( ! empty( $_POST ) && 'license' !== $tab_current ) {
			check_admin_referer( 'wmsc_' . $tab_current );

			if ( current_user_can( 'manage_woocommerce' ) ) {

				$values_post_sanitized = $form->validate( $_POST );

				$form->set_current_values( $values_post_sanitized );

				if ( update_option( 'wmsc_options', $values_post_sanitized ) ) {
					$form->add_message( 'success', '<b>' . __( 'Your settings have been saved.' ) . '</b>' );
				}
			}
		}

		// Render the content.
		$messages = $form->render_messages();
		$content  = $form->render();
		$content .= $this->show_tabs_preview( $tab_current );
		$content .= $this->show_license_tab( $tab_current );

		include_once 'admin-template.php';
	}

	function show_tabs_preview( $tab ) {
		if ( $tab !== 'design' ) {
			return '';
		}
		$options       = get_option( 'wmsc_options' );
		$color         = isset( $options['main_color'] ) && ! empty( $options['main_color'] ) ? $options['main_color'] : '#1e85be';
		$visited_color = isset( $options['visited_color'] ) && ! empty( $options['visited_color'] ) ? $options['visited_color'] : '#1ebe3a';
		$template      = isset( $options['template'] ) && ! empty( $options['template'] ) ? $options['template'] : 'default';
		ob_start();
		?>
<style type="text/css" id="wpmc-preview-css">
	.wpmc-tabs-wrapper .wpmc-tab-item.current::before {border-bottom-color: <?php echo $color; ?>}
	.wpmc-tabs-wrapper .wpmc-tab-item.current .wpmc-tab-number {border-color: <?php echo $color; ?>}
	.wpmc-tabs-wrapper-breadcrumb {border-color: <?php echo $color; ?>} 
	.wpmc-tabs-wrapper-breadcrumb .wpmc-tabs-list .wpmc-tab-item:after {box-shadow: 3px -3px 0 1px <?php echo $color; ?>}
	.wpmc-tabs-wrapper-breadcrumb .wpmc-tab-item.current .wpmc-tab-number, .wpmc-tabs-wrapper-breadcrumb .wpmc-tab-item.visited .wpmc-tab-number {background-color: <?php echo $color; ?>}
	.wpmc-tabs-wrapper-md .wpmc-tab-item.current .wpmc-tab-number, .wpmc-tabs-wrapper-md .wpmc-tab-item.visited .wpmc-tab-number {background-color: <?php echo $color; ?>}
	@media screen and (max-width: 767px) { .wpmc-tabs-wrapper-breadcrumb .wpmc-tabs-list.wpmc-5-tabs .wpmc-tab-item.current {border-left: 3px solid <?php echo $color; ?>}}
</style>

<div class="wpmc-preview">
	<span class="wpmc-preview-title">Preview</span>
	<div class="wpmc-tabs-wrapper wpmc-tabs-wrapper-<?php echo $template; ?> wpmc-tabs-clickable">
  <ul class="wpmc-tabs-list wpmc-4-tabs">
		<li class="wpmc-tab-item wpmc-ripple visited wpmc-login">
			<div class="wpmc-tab-number">1</div>
			<div class="wpmc-tab-text">Login</div>
			<div class="wpmc-tab-bar-left"></div>
			<div class="wpmc-tab-bar-right"></div>
		</li>
		<li class="wpmc-tab-item wpmc-ripple current wpmc-billing">
			<div class="wpmc-tab-number">2</div>
			<div class="wpmc-tab-text">Billing</div>
			<div class="wpmc-tab-bar-left"></div>
			<div class="wpmc-tab-bar-right"></div>
		</li>
		<li class="wpmc-tab-item wpmc-ripple wpmc-order">
			<div class="wpmc-tab-number">3</div>
			<div class="wpmc-tab-text">Order</div>
			<div class="wpmc-tab-bar-left"></div>
			<div class="wpmc-tab-bar-right"></div>
		</li>
		<li class="wpmc-tab-item wpmc-ripple wpmc-payment">
			<div class="wpmc-tab-number">4</div>
			<div class="wpmc-tab-text">Payment</div>
			<div class="wpmc-tab-bar-left"></div>
			<div class="wpmc-tab-bar-right"></div>
		</li>
	</ul>
</div>
</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}


	function show_license_tab( $tab ) {
		if ( $tab !== 'license' ) {
			return;
		}

		require_once 'edd/edd-plugin.php';

		$license_data = array(
			'store_url'      => WMSC_PLUGIN_PRO_SERVER,
			'item_name'      => WMSC_PLUGIN_PRO_NAME,
			'author'         => WMSC_PLUGIN_PRO_AUTHOR,
			'version'        => WMSC_PRO_VERSION,
			'prefix'         => 'wpmc_',
			'license'        => 'wpmc_license',
			'license_key'    => 'wpmc_license_key',
			'license_status' => 'wpmc_license_status',
			'license_beta'   => 'wpmc_license_beta',
		);

		$edd = new WPMC_LicenseForm( $license_data );

		$admin_notice = false;
		if ( ! empty( $_POST ) ) {
			$admin_notice = $edd->activate_deactivate_license( $_POST );
		}
		ob_start();

		$edd->license_page( $admin_notice );

		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}


	/**
	 * Initiate the EDD_SL_Plugin_Updater class
	 */
	public function edd_updater() {
		if ( ! class_exists( 'EDD_SL_Plugin_Updater_WPMC' ) ) {
			include 'edd/EDD_SL_Plugin_Updater.php';
		}

		$license_key = trim( get_option( 'wpmc_license_key' ) );
		$beta        = ( trim( get_option( 'wpmc_license_beta' ) ) == '1' ) ? true : false;

		$edd_updater = new EDD_SL_Plugin_Updater_WPMC(
			WMSC_PLUGIN_PRO_SERVER,
			WMSC_PLUGIN_PRO_FILE,
			array(
				'version'   => WMSC_PRO_VERSION,
				'license'   => $license_key,
				'item_name' => WMSC_PLUGIN_PRO_NAME,
				'author'    => WMSC_PLUGIN_PRO_AUTHOR,
				'url'       => home_url(),
				'beta'      => $beta,
			)
		);
	}

	function license_beta() {

		check_ajax_referer( 'wpmc_license_nonce', 'nonce' );

		update_option( 'wpmc_license_beta', (int) $_POST['beta_enabled'] );

		echo (int) $_POST['beta_enabled'];

		wp_die();
	}


	/**
	 * Show admin warnings
	 */
	function warnings() {

		$allowed_actions = array(
			'wmsc_dismiss_free',
			'wmsc_dismiss_suki_theme',
			'wmsc_dismiss_german_market_hooks',
			'wmsc_dismiss_elementor_pro_widget',
		);

		$w = new SilkyPress_Warnings( $allowed_actions );

		if ( ! $w->is_url( 'plugins' ) && ! $w->is_url( 'wmsc-settings' ) ) {
			return;
		}

		// Check if the free version is installed
		if ( file_exists( WP_PLUGIN_DIR . '/wp-multi-step-checkout/wp-multi-step-checkout.php' ) ) {
			$message = __( 'You are free to uninstall the free version of the <b>WooCommerce Multi-Step Checkout</b> plugin, if you want.', 'wp-multi-step-checkout-pro' );
			$w->add_notice( 'wmsc_dismiss_free', $message );
		}

		// Warning about the Suki theme
		if ( strpos( strtolower( get_template() ), 'suki' ) !== false && $w->is_url( 'wmsc-settings' ) ) {
			$message = __( 'The Suki theme adds some HTML elements to the checkout page in order to create the two columns. This additional HTML messes up the steps from the multi-step checkout plugin. Unfortunately the multi-step checkout plugin isn\'t compatibile with the Suki theme.', 'wp-multi-step-checkout-pro' );
			$w->add_notice( 'wmsc_dismiss_suki_theme', $message );
		}

		// Warning if the hooks from the German Market plugin are turned on
		if ( class_exists( 'Woocommerce_German_Market' ) && get_option( 'gm_deactivate_checkout_hooks', 'off' ) != 'off' && $w->is_url( 'wmsc-settings' ) ) {
			$message = __( 'The "Deactivate German Market Hooks" option on the <b>WP Admin -> WooCommerce -> German Market -> Ordering</b> page will interfere with the proper working of the <b>Multi-Step Checkout Pro for WooCommerce</b> plugin. Please consider turning the option off.', 'wp-multi-step-checkout-pro' );
			$w->add_notice( 'wmsc_dismiss_german_market_hooks', $message );
		}

		// Warning about the Elementor Pro Checkout widget.
		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$message = __('If the Elementor Pro Checkout widget is used on the checkout page, make sure the "Skin" option is set to "Multi-Step Checkout" in the widget\'s "Content -> General" section.', 'wp-multi-step-checkout-pro');
			$w->add_notice( 'wmsc_dismiss_elementor_pro_widget', $message);
		}

		$w->show_warnings();
	}

}

new WPMultiStepCheckoutPro_Settings();
