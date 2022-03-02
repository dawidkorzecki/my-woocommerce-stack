<?php

defined( 'ABSPATH' ) || exit; 

class WPMultiStepCheckout_Settings {

    /**
     * Constructor
     */
    public function __construct() {

        require_once 'settings-array.php';
        require_once 'frm/class-form-fields.php';
        require_once 'frm/premium-tooltips.php';
        require_once 'frm/warnings.php';

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_scripts') );

        $this->warnings();
    }

    /**
     * Create the menu link
     */
    function admin_menu() {
        add_submenu_page(
            'woocommerce', 
            'Multi-Step Checkout', 
            'Multi-Step Checkout', 
            'manage_options', 
			'wmsc-settings',
            array($this, 'admin_settings_page')
        );
    }

    /**
     * Enqueue the scripts and styles 
     */
    function admin_enqueue_scripts() {
        $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_URL);
        if ( $page != 'wmsc-settings' ) return false;

        // Color picker
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script('wp-color-picker');

        $u = plugins_url('/', WMSC_PLUGIN_FILE) . 'assets/';     // assets url
        $f = plugins_url('/', WMSC_PLUGIN_FILE) . 'includes/frm/assets/';           // framework assets url
        $v = WMSC_VERSION;                          // version
        $d = array('jquery');                       // dependency
        $w = true;                                  // where? in the footer?

        // Load scripts
        wp_enqueue_script( 'wmsc-bootstrap', $f.'bootstrap.min.js', $d, $v, $w);
        wp_enqueue_script( 'wmsc-admin-script', $u.'js/admin-script.js', $d, $v, $w);

        // Load styles
        wp_enqueue_style ( 'wmsc-bootstrap',   $f.'bootstrap.min.css', array(), $v);
        wp_enqueue_style ( 'wmsc-admin-style', $u.'css/admin-style.css', array(), $v);
    }

    /**
     * Output the admin page
     * @access public
     */
	public function admin_settings_page() {

        // Get the tabs. 
        $tabs = array(
            'general'       => __('General Settings', 'wp-multi-step-checkout'),
            'design'        => __('Design', 'wp-multi-step-checkout'),
            'titles'        => __('Text on Steps and Buttons', 'wp-multi-step-checkout')
        );

        $tab_current = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';

        if ( ! isset( $tabs[ $tab_current ] ) ) $tab_current = 'general';

		// Get the field settings.
		$settings_all   = get_wmsc_settings();
		$values_current = get_option( 'wmsc_options', array() );

		$form = new \SilkyPressFrm\Form_Fields( $settings_all );
		$form->add_setting( 'tooltip_img', plugins_url('/', WMSC_PLUGIN_FILE) . 'assets/images/question_mark.svg' );
		$form->add_setting( 'section', $tab_current );
		$form->add_setting( 'label_class', 'col-sm-5' );
		$form->set_current_values( $values_current );

		// The settings were saved.
		if ( ! empty( $_POST ) ) {
            check_admin_referer( 'wmsc_' . $tab_current );

			if ( current_user_can( 'manage_woocommerce' ) ) {

				$values_post_sanitized = $form->validate( $_POST );

				$form->set_current_values( $values_post_sanitized );

				foreach ( $settings_all as $_key => $_setting ) {
					if ( isset( $_setting['pro'] ) && $_setting['pro'] && isset( $_setting['value'] ) ) {
						$values_post_sanitized[ $_key ] = $_setting['value'];
					}
				}

				if ( update_option( 'wmsc_options', $values_post_sanitized ) ) {
					$form->add_message( 'success', '<b>'. __('Your settings have been saved.') . '</b>' );
				}
			}
			
		}

        // Premium tooltips.
        $message = __('Only available in <a href="%1$s" target="_blank">PRO version</a>', 'wp-multi-step-checkout');
        $message = wp_kses( $message, array('a' => array('href' => array(), 'target'=> array())));
        $message = sprintf( $message, 'https://www.silkypress.com/woocommerce-multi-step-checkout-pro/?utm_source=wordpress&utm_campaign=wmsc_free&utm_medium=banner');
        new SilkyPress_PremiumTooltips($message); 

		// Render the content.
		$messages = $form->render_messages();
		$content  = $form->render();

		include_once 'admin-template.php'; 

        include_once 'right_columns.php';
	}

    /**
     * Show admin warnings
     */
    function warnings() {

        $allowed_actions = array(
			'wmsc_dismiss_suki_theme',
			'wmsc_dismiss_german_market_hooks',
			'wmsc_dismiss_elementor_pro_widget',
        );

        $w = new SilkyPress_Warnings($allowed_actions); 


        if ( !$w->is_url('plugins') && !$w->is_url('wmsc-settings') ) {
            return;
        }

        // Warning about the Suki theme
        if ( strpos( strtolower(get_template()), 'suki') !== false && $w->is_url('wmsc-settings') ) {
            $message = __('The Suki theme adds some HTML elements to the checkout page in order to create the two columns. This additional HTML messes up the steps from the multi-step checkout plugin. Unfortunately the multi-step checkout plugin isn\'t compatibile with the Suki theme.', 'wp-multi-step-checkout');
            $w->add_notice( 'wmsc_dismiss_suki_theme', $message);
        }


        // Warning if the hooks from the German Market plugin are turned on
        if ( class_exists('Woocommerce_German_Market') && get_option( 'gm_deactivate_checkout_hooks', 'off' ) != 'off' && $w->is_url('wmsc-settings') ) {
            $message = __('The "Deactivate German Market Hooks" option on the <b>WP Admin -> WooCommerce -> German Market -> Ordering</b> page will interfere with the proper working of the <b>Multi-Step Checkout for WooCommerce</b> plugin. Please consider turning the option off.', 'wp-multi-step-checkout');
            $w->add_notice( 'wmsc_dismiss_german_market_hooks', $message);
        }


		// Warning about the Elementor Pro Checkout widget.
		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$message = __('If the Elementor Pro Checkout widget is used on the checkout page, make sure the "Skin" option is set to "Multi-Step Checkout" in the widget\'s "Content -> General" section.', 'wp-multi-step-checkout');
			$w->add_notice( 'wmsc_dismiss_elementor_pro_widget', $message);
		}

        $w->show_warnings();
    }
}

new WPMultiStepCheckout_Settings();
