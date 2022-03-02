<?php
/**
 * Checkout Form
 *
 * This is an overridden copy of the woocommerce/templates/checkout/form-checkout.php file.
 *
 * @package WPMultiStepCheckout
 */

defined( 'ABSPATH' ) || exit;

// check the WooCommerce MultiStep Checkout options
$options = get_option('wmsc_options');
require_once 'settings-array.php';
if ( !is_array($options) || count($options) === 0 ) {
    $defaults = get_wmsc_settings();
    $options = array();
    foreach($defaults as $_key => $_value ) {
        $options[$_key] = $_value['value'];
    }
} 
$options = array_map('stripslashes', $options);

// Use the WPML values instead of the ones from the admin form
if ( isset($options['t_wpml']) && $options['t_wpml'] == 1 ) {
    $defaults = get_wmsc_settings();
    foreach($options as $_key => $_value ) {
        if( substr($_key, 0, 2) == 't_' && $_key != 't_sign') {
            $options[$_key] = $defaults[$_key]['value'];
        }
    }
}

if ( !isset($options['c_sign']) ) $options['c_sign'] = '&';

// Get the steps
$steps = get_wmsc_steps();

// Set the step titles
$steps['billing']['title']  = $options['t_billing'];
$steps['shipping']['title'] = $options['t_shipping'];
$steps['review']['title']   = $options['t_order'];
$steps['payment']['title']  = $options['t_payment'];


// check the WooCommerce options
$is_registration_enabled = version_compare('3.0', WC()->version, '<=') ? $checkout->is_registration_enabled() : get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) == 'yes'; 
$has_checkout_fields = version_compare('3.0', WC()->version, '<=') ? $checkout->get_checkout_fields() : (is_array($checkout->checkout_fields) && count($checkout->checkout_fields) > 0 );
$show_login_step = ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) ? false : true;
$stop_at_login = ( ! $is_registration_enabled && $checkout->is_registration_required() && ! is_user_logged_in() ) ? true : false;
$checkout_url = apply_filters( 'woocommerce_get_checkout_url', version_compare( '2.5', WC()->version, '<=' ) ? wc_get_checkout_url() : WC()->cart->get_checkout_url() );

// Both options disabled for "Guest" on the WP Admin -> WooCommerce -> Settings -> Accounts & Privacy page
if ( ! $is_registration_enabled && $checkout->is_registration_required() && ! is_user_logged_in() && ! $show_login_step) {
    echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
    return;
}

// Swap the Payment and Review steps for german shops
$swap_payment_review = ( class_exists('WooCommerce_Germanized') || class_exists('Woocommerce_German_Market')) ? true : false;
$swap_payment_review = apply_filters('wpmc_swap_payment_review', $swap_payment_review);
if ( $swap_payment_review ) {
    $tmp = $steps['payment']['position'];
    $steps['payment']['position'] = $steps['review']['position'];
    $steps['review']['position'] = $tmp;
} 

// Disabled "Show the Shipping step" option on Multi-Step Checkout -> General Settings page 
if ( !$options['show_shipping_step'] ) {
    unset($steps['shipping']);
    $options['unite_billing_shipping'] = false;
    $steps['billing']['sections'][] = 'woocommerce_checkout_after_customer_details';
}

// Enabled "Show the Order and the Payment steps together" option on Multi-Step Checkout -> General Settings page 
if ( $options['unite_order_payment']) {
    $steps['review']['title'] = $options['t_order'] . ' '.esc_html($options['c_sign']).' ' . $options['t_payment']; 
    $steps['review']['class'] = $steps['review']['class'] . ' ' . $steps['payment']['class'];
    $steps['review']['sections'] = array('review', 'payment');
    if ( $swap_payment_review ) {
        $steps['review']['sections'] = array('payment', 'review');
    }
    unset($steps['payment']);
}

// Enabled "Show the Order and the Payment steps together" option on Multi-Step Checkout -> General Settings page 
if ( $options['unite_billing_shipping'] && $options['show_shipping_step'] ) {
    $steps['billing']['title'] = $options['t_billing'] . ' '.esc_html($options['c_sign']).' ' . $options['t_shipping']; 
    $steps['billing']['class'] = $steps['billing']['class'] . ' ' . $steps['shipping']['class'];
    $steps['billing']['sections'] = array('billing', 'shipping');
    unset($steps['shipping']);
}

// No checkout fields within the $checkout object
if ( !$has_checkout_fields) {
    unset($steps['billing']);
    unset($steps['shipping']);
}

// Pass the steps through a filter
$steps = apply_filters('wpmc_modify_steps', $steps);

// Sort the steps
uasort($steps, 'wpmc_sort_by_position');

wc_print_notices();

// show the tabs
include dirname(__FILE__) . '/form-tabs.php';

?>

<div style="clear: both;"></div>

<div class="wpmc-steps-wrapper">

<div id="checkout_coupon" class="woocommerce_checkout_coupon" style="display: none;">
	<?php do_action( 'wpmc-woocommerce_checkout_coupon_form', $checkout ); ?>
</div>

<div id="woocommerce_before_checkout_form" class="woocommerce_before_checkout_form" data-step="<?php echo apply_filters('woocommerce_before_checkout_form_step', 'step-review'); ?>" style="display: none;">
    <?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
</div>

<!-- Step: Login -->
<?php 
    if ( $show_login_step ) {
        wmsc_step_content_login($checkout, $stop_at_login); 
    }

    if ( $stop_at_login ) { 
        echo '</div>'; // closes the "wpmc-steps-wrapper" div 
        return false; 
    } 

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $checkout_url ); ?>" enctype="multipart/form-data">

<?php foreach( $steps as $_id => $_step ) {
    echo '<!-- Step: '.$_step['title'].' -->'; 
	echo '<div class="wpmc-step-item '.$_step['class'].'">';
    if ( isset($_step['sections'] ) ) {
        foreach ( $_step['sections'] as $_section ) {
            if ( strpos($_section, 'woocommerce_') === 0 ) {
                do_action( $_section );
            } else {
                do_action('wmsc_step_content_' . $_section);
            }
        }
    } else {
        do_action('wmsc_step_content_' . $_id);
    }
    echo '</div>';
} ?>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>

<?php include dirname(__FILE__) . '/form-buttons.php'; ?>
