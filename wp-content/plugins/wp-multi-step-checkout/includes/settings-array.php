<?php
/**
 * Functions
 *
 * @package WPMultiStepCheckout
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'get_wmsc_settings' ) ) {

	/**
	 * The settings array for the admin page.
	 */
	function get_wmsc_settings() {

		$account_url                      = admin_url( 'admin.php?page=wc-settings&tab=account' );
		$no_login_screenshot              = 'https://www.silkypress.com/wp-content/uploads/2018/09/multi-step-checkout-no-login.png';
		$registration_settings_screenshot = 'https://www.silkypress.com/wp-content/uploads/2019/09/registration-description.png';
		$wmsc_settings                    = array(
			/* General Settings */
			'label1'                   => array(
				'label'      => __( 'Which Steps to show', 'wp-multi-step-checkout' ),
				'input_form' => 'header',
				'value'      => '',
				'section'    => 'general',
			),
			'show_shipping_step'       => array(
				'label'      => __( 'Show the <code>Shipping</code> step', 'wp-multi-step-checkout' ),
				'input_form' => 'checkbox',
				'value'      => true,
				'section'    => 'general',
			),
			'show_login_step'          => array(
				'label'      => __( 'Show the <code>Login</code> step', 'wp-multi-step-checkout' ),
				'input_form' => 'text',
				/* translators: 1: Woocommerce Accounts URL 2: Screenshot URL. */
				'value'      => sprintf( __( ' For removing the login step you need to uncheck the "Allow customers to log into an existing account during checkout" option on the <a href="%1$s">WP Admin -> WooCommerce -> Settings -> Accounts</a> page. See <a href="%2$s" target="_blank">this screenshot</a>.', 'wp-multi-step-checkout' ), esc_url( $account_url ), esc_url( $no_login_screenshot ) ),
				'section'    => 'general',
			),
			'unite_billing_shipping'   => array(
				'label'      => __( 'Show the <code>Billing</code> and the <code>Shipping</code> steps together', 'wp-multi-step-checkout' ),
				'input_form' => 'checkbox',
				'value'      => false,
				'section'    => 'general',
			),
			'unite_order_payment'      => array(
				'label'      => __( 'Show the <code>Order</code> and the <code>Payment</code> steps together', 'wp-multi-step-checkout' ),
				'input_form' => 'checkbox',
				'value'      => false,
				'section'    => 'general',
			),

			'label2'                   => array(
				'label'      => __( 'Additional Elements', 'wp-multi-step-checkout' ),
				'input_form' => 'header',
				'value'      => '',
				'section'    => 'general',
			),
			'show_back_to_cart_button' => array(
				'label'      => __( 'Show the <code>Back to Cart</code> button', 'wp-multi-step-checkout' ),
				'input_form' => 'checkbox',
				'value'      => true,
				'section'    => 'general',
			),
			'registration_with_login'  => array(
				'label'       => __( 'Show registration form in the Login step', 'wp-multi-step-checkout' ),
				'input_form'  => 'checkbox',
				'value'       => true,
				'section'     => 'general',
				'description' => __( 'The registration form will be shown next to the login form, it will not replace it', 'wp-multi-step-checkout' ),
				'pro'         => true,
			),
			'registration_desc'        => array(
				'label'      => '',
				'input_form' => 'text',
				/* translators: 1: Woocommerce Accounts URL 2: Screenshot URL. */
				'value'      => sprintf( __( 'Use the "Account creation" options on the <a href="%1$s">WP Admin -> WooCommerce -> Settings -> Accounts & Privacy</a> page to modify the Registration form. See <a href="%2$s" target="_blank">this screenshot</a>.', 'wp-multi-step-checkout' ), esc_url( $account_url ), esc_url( $registration_settings_screenshot ) ),
				'section'    => 'general',
				'pro'        => true,
			),
			'review_thumbnails'        => array(
				'label'      => __( 'Add product thumbnails to the Order Review section', 'wp-multi-step-checkout' ),
				'input_form' => 'checkbox',
				'value'      => true,
				'section'    => 'general',
				'pro'        => true,
			),
			'review_address'           => array(
				'label'      => __( 'Add "Address Review" to the <code>Order</code> section', 'wp-multi-step-checkout' ),
				'input_form' => 'checkbox',
				'value'      => false,
				'section'    => 'general',
				'pro'        => true,
			),
			'label3'                   => array(
				'label'      => __( 'Functionality', 'wp-multi-step-checkout' ),
				'input_form' => 'header',
				'value'      => '',
				'section'    => 'general',
			),
			'validation_per_step'      => array(
				'label'       => __( 'Validate the fields during each step', 'wp-multi-step-checkout' ),
				'description' => __( 'The default WooCommerce validation is done when clicking the Place Order button. With this option the validation is performed when trying to move to the next step', 'wp-multi-step-checkout' ),
				'input_form'  => 'checkbox',
				'value'       => true,
				'section'     => 'general',
				'pro'         => true,
			),
			'clickable_steps'          => array(
				'label'       => __( 'Clickable Steps', 'wp-multi-step-checkout' ),
				'description' => __( 'The user can click on the steps in order to get to the next one.', 'wp-multi-step-checkout' ),
				'input_form'  => 'checkbox',
				'value'       => true,
				'section'     => 'general',
				'pro'         => true,
			),
			'keyboard_nav'             => array(
				'label'       => __( 'Enable the keyboard navigation', 'wp-multi-step-checkout' ),
				'description' => __( 'Use the keyboard\'s left and right keys to move between the checkout steps', 'wp-multi-step-checkout' ),
				'input_form'  => 'checkbox',
				'value'       => false,
				'section'     => 'general',
			),
			'url_hash'                 => array(
				'label'       => __( 'Change the URL for each step', 'wp-multi-step-checkout' ),
				'description' => __( 'Each step will have a hash added to the URL. For example &quot;#login&quot; or &quot;#billing&quot;. This option, together with some &quot;History Change Trigger&quot; settings in the Google Tag Manager, allows Google Analytics to track each step as different pages.', 'wp-multi-step-checkout' ),
				'input_form'  => 'checkbox',
				'value'       => false,
				'section'     => 'general',
				'pro'         => true,
			),

			/* Templates */
			'main_color'               => array(
				'label'      => __( 'Main Color', 'wp-multi-step-checkout' ),
				'input_form' => 'input_color',
				'value'      => '#1e85be',
				'section'    => 'design',
			),
			'template'                 => array(
				'label'      => __( 'Template', 'wp-multi-step-checkout' ),
				'input_form' => 'radio',
				'value'      => 'default',
				'values'     => array(
					'default'    => __( 'Default', 'wp-multi-step-checkout' ),
					'md'         => __( 'Material Design', 'wp-multi-step-checkout' ),
					'breadcrumb' => __( 'Breadcrumbs', 'wp-multi-step-checkout' ),
				),
				'section'    => 'design',
				'pro'        => true,
			),
			'wpmc_buttons'             => array(
				'label'       => __( 'Use the plugin\'s buttons', 'wp-multi-step-checkout' ),
				'input_form'  => 'checkbox',
				'value'       => false,
				'description' => __( 'By default the plugin tries to use the theme\'s design for the buttons. If this fails, enable this option in order to use the plugin\'s button style', 'wp-multi-step-checkout' ),
				'section'     => 'design',
				'pro'         => true,
			),
			'wpmc_check_sign'          => array(
				'label'      => __( 'Show a "check" sign for visited steps', 'wp-multi-step-checkout' ),
				'input_form' => 'checkbox',
				'value'      => false,
				'section'    => 'design',
				'pro'        => true,
			),
			'visited_color'            => array(
				'label'      => __( 'Visited steps color', 'wp-multi-step-checkout' ),
				'input_form' => 'input_color',
				'value'      => '#1EBE3A',
				'section'    => 'design',
				'pro'        => true,
			),

			/* Step Titles */
			't_login'                  => array(
				'label'      => __( 'Login', 'wp-multi-step-checkout' ),
				'input_form' => 'input_text',
				'value'      => __( 'Login', 'wp-multi-step-checkout' ),
				'section'    => 'titles',
			),
			't_billing'                => array(
				'label'      => __( 'Billing', 'wp-multi-step-checkout' ),
				'input_form' => 'input_text',
				'value'      => __( 'Billing', 'wp-multi-step-checkout' ),
				'section'    => 'titles',
			),
			't_shipping'               => array(
				'label'      => __( 'Shipping', 'wp-multi-step-checkout' ),
				'input_form' => 'input_text',
				'value'      => __( 'Shipping', 'wp-multi-step-checkout' ),
				'section'    => 'titles',
			),
			't_order'                  => array(
				'label'      => __( 'Order', 'wp-multi-step-checkout' ),
				'input_form' => 'input_text',
				'value'      => __( 'Order', 'wp-multi-step-checkout' ),
				'section'    => 'titles',
			),
			't_payment'                => array(
				'label'      => __( 'Payment', 'wp-multi-step-checkout' ),
				'input_form' => 'input_text',
				'value'      => __( 'Payment', 'wp-multi-step-checkout' ),
				'section'    => 'titles',
			),
			't_back_to_cart'           => array(
				'label'      => __( 'Back to cart', 'wp-multi-step-checkout' ),
				'input_form' => 'input_text',
				'value'      => _x( 'Back to cart', 'Frontend: button label', 'wp-multi-step-checkout' ),
				'section'    => 'titles',
			),
			't_skip_login'             => array(
				'label'      => __( 'Skip Login', 'wp-multi-step-checkout' ),
				'input_form' => 'input_text',
				'value'      => _x( 'Skip Login', 'Frontend: button label', 'wp-multi-step-checkout' ),
				'section'    => 'titles',
			),
			't_previous'               => array(
				'label'      => __( 'Previous', 'wp-multi-step-checkout' ),
				'input_form' => 'input_text',
				'value'      => _x( 'Previous', 'Frontend: button label', 'wp-multi-step-checkout' ),
				'section'    => 'titles',
			),
			't_next'                   => array(
				'label'      => __( 'Next', 'wp-multi-step-checkout' ),
				'input_form' => 'input_text',
				'value'      => _x( 'Next', 'Frontend: button label', 'wp-multi-step-checkout' ),
				'section'    => 'titles',
			),
			't_error'                  => array(
				'label'       => __( 'Please fix the errors on this step before moving to the next step', 'wp-multi-step-checkout' ),
				'input_form'  => 'input_text',
				'value'       => _x( 'Please fix the errors on this step before moving to the next step', 'Frontend: error message', 'wp-multi-step-checkout' ),
				'section'     => 'titles',
				'description' => __( 'This is an error message shown in the frontend', 'wp-multi-step-checkout' ),
				'pro'         => true,
			),
			'c_sign'                   => array(
				'label'       => __( 'AND sign', 'wp-multi-step-checkout' ),
				'input_form'  => 'input_text',
				'value'       => __( '&', 'wp-multi-step-checkout' ),
				'section'     => 'titles',
				'description' => __( 'The sign between two unified steps. For example "Billing & Shipping"' ),
			),
			't_wpml'                   => array(
				'label'       => __( 'Use WPML to translate the text on the Steps and Buttons', 'wp-multi-step-checkout' ),
				'input_form'  => 'checkbox',
				'value'       => false,
				'section'     => 'titles',
				'description' => __( 'For a multilingual website the translations from WPML will be used instead of the ones in this form', 'wp-multi-step-checkout' ),
			),

		);

		return $wmsc_settings;

	}
}

if ( ! function_exists( 'get_wmsc_steps' ) ) {

	/**
	 * The steps array.
	 * Note: The Login is always the first step and is not part of the get_wmsc_steps() array.
	 */
	function get_wmsc_steps() {

		$steps = array(
			'billing'  => array(
				'title'    => __( 'Billing', 'wp-multi-step-checkout' ),
				'position' => 10,
				'class'    => 'wpmc-step-billing',
				'sections' => array( 'billing' ),
			),
			'shipping' => array(
				'title'    => __( 'Shipping', 'wp-multi-step-checkout' ),
				'position' => 20,
				'class'    => 'wpmc-step-shipping',
				'sections' => array( 'shipping' ),
			),
			'review'   => array(
				'title'    => __( 'Order', 'wp-multi-step-checkout' ),
				'position' => 30,
				'class'    => 'wpmc-step-review',
				'sections' => array( 'review' ),
			),
			'payment'  => array(
				'title'    => __( 'Payment', 'wp-multi-step-checkout' ),
				'position' => 40,
				'class'    => 'wpmc-step-payment',
				'sections' => array( 'payment' ),
			),

		);

		return $steps;
	}
}

if ( ! function_exists( 'wmsc_step_content_login' ) ) {

	/**
	 * The content for the Login step.
	 *
	 * @param object $checkout The Checkout object from the WooCommerce plugin.
	 * @param bool   $stop_at_login If the user should be logged in in order to checkout.
	 */
	function wmsc_step_content_login( $checkout, $stop_at_login ) { ?> 
	<div class="wpmc-step-item wpmc-step-login">
			<div id="checkout_login" class="woocommerce_checkout_login wp-multi-step-checkout-step">
				<?php
				woocommerce_login_form(
					array(
						'message'  => apply_filters( 'woocommerce_checkout_logged_in_message', __( 'If you have shopped with us before, please enter your details in the boxes below. If you are a new customer, please proceed to the Billing &amp; Shipping section.', 'wp-multi-step-checkout' ) ),
						'redirect' => wc_get_page_permalink( 'checkout' ),
						'hidden'   => false,
					)
				);
				?>
			</div>
				<?php
				if ( $stop_at_login ) {
					echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
	</div>
	<?php }
}

if ( ! function_exists( 'wmsc_step_content_billing' ) ) {

	/**
	 * The content of the Billing step.
	 */
	function wmsc_step_content_billing() {
		do_action( 'woocommerce_checkout_before_customer_details' );
		do_action( 'woocommerce_checkout_billing' );
	}
}

if ( ! function_exists( 'wmsc_step_content_shipping' ) ) {

	/**
	 * The content of the Shipping step.
	 */
	function wmsc_step_content_shipping() {
		do_action( 'woocommerce_checkout_shipping' );
		do_action( 'woocommerce_checkout_after_customer_details' );
	}
}

if ( ! function_exists( 'wmsc_step_content_payment' ) ) {

	/**
	 * The content of the Order Payment step.
	 */
	function wmsc_step_content_payment() {
		echo '<h3 id="payment_heading">' . esc_html__( 'Payment', 'woocommerce' ) . '</h3>';
		do_action( 'wpmc-woocommerce_checkout_payment' );
		do_action( 'woocommerce_checkout_after_order_review' );
	}
}

if ( ! function_exists( 'wmsc_step_content_review' ) ) {

	/**
	 * The content of the Order Review step.
	 */
	function wmsc_step_content_review() {
		do_action( 'woocommerce_checkout_before_order_review' );
		echo '<h3 id="order_review_heading">' . esc_html__( 'Your order', 'woocommerce' ) . '</h3>';
		echo '<div id="order_review">';
		do_action( 'woocommerce_checkout_order_review' );
		do_action( 'wpmc-woocommerce_order_review' );
		echo '</div>';
	}
}

if ( ! function_exists( 'wmsc_step_content_payment_germanized' ) ) {

	/**
	 * The content of the Payment step for the "Germanized for WooCommerce" plugin.
	 */
	function wmsc_step_content_payment_germanized() {
		echo '<h3 id="payment_heading">' . esc_html__( 'Choose a Payment Gateway', 'woocommerce-germanized' ) . '</h3>';
		do_action( 'wpmc-woocommerce_checkout_payment' );
	}
}

if ( ! function_exists( 'wmsc_step_content_review_germanized' ) ) {

	/**
	 * The content of the Order Review step for the "Germanized for WooCommerce" plugin.
	 */
	function wmsc_step_content_review_germanized() {
		do_action( 'woocommerce_checkout_before_order_review' );
		echo '<h3 id="order_review_heading">' . esc_html__( 'Your order', 'woocommerce' ) . '</h3>';
		do_action( 'wpmc-woocommerce_order_review' );
		if ( function_exists( 'woocommerce_gzd_template_order_submit' ) ) {
			woocommerce_gzd_template_order_submit();
		}
	}
}

if ( ! function_exists( 'wmsc_step_content_review_german_market' ) ) {

	/**
	 * The content of the Order Review step for the "German Market" plugin.
	 */
	function wmsc_step_content_review_german_market() {
		do_action( 'woocommerce_checkout_before_order_review' );
		echo '<h3 id="order_review_heading">' . esc_html__( 'Your order', 'woocommerce' ) . '</h3>';
		do_action( 'wpmc-woocommerce_order_review' );
		do_action( 'woocommerce_checkout_order_review' );
	}
}

if ( ! function_exists( 'wpmc_sort_by_position' ) ) {

	/**
	 * Comparison function for sorting the steps.
	 *
	 * @param array $a First array.
	 * @param array $b Second array.
	 */
	function wpmc_sort_by_position( $a, $b ) {
		return $a['position'] - $b['position'];
	}
}
