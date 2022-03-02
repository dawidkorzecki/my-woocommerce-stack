<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );

/**
 * Add WooCommerce additional Checkbox checkout field
 */

add_action( 'woocommerce_review_order_before_submit', 'bt_add_checkout_checkbox', 10 );

function bt_add_checkout_checkbox() {
   
    woocommerce_form_field( 'checkout_checkbox', array( // CSS ID
       'type'          => 'checkbox',
       'class'         => array('form-row mycheckbox'), // CSS Class
       'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
       'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
       'required'      => true, // Mandatory or Optional
       'label'         => 'Zgadzam się i akceptuję <a href="/polityka-prywatnosci/" target="_blank" rel="noopener">Politykę prywatności</a>', // Label and Link
    ));

	woocommerce_form_field( 'checkout_checkbox_do', array( // CSS ID
		'type'          => 'checkbox',
		'class'         => array('form-row mycheckbox'), // CSS Class
		'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
		'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
		'required'      => true, // Mandatory or Optional
		'label'         => 'Wyrażam zgodę na przetwarzanie moich danych osobowych. Twoje dane osobowe będą przetwarzane przez naszą firmę i tylko my będziemy ich Administratorem.', // Label and Link
	 ));
}

/**
 * Alert if checkbox not checked
 */ 

add_action( 'woocommerce_checkout_process', 'bt_add_checkout_checkbox_warning' );

function bt_add_checkout_checkbox_warning() {
    if ( ! (int) isset( $_POST['checkout_checkbox'] ) ) {
        wc_add_notice( __( 'Musisz zaakceptować politykę prywatności' ), 'error' );
    }

	if ( ! (int) isset( $_POST['checkout_checkbox_do'] ) ) {
        wc_add_notice( __( 'Musisz zaakceptować zgodę na przetwarzanie moich danych osobowych' ), 'error' );
    }
}

/**
 * Add custom field as order meta with field value to database
 */

add_action( 'woocommerce_checkout_update_order_meta', 'bt_checkout_field_order_meta_db' );

function bt_checkout_field_order_meta_db( $order_id ) {
    if ( ! empty( $_POST['checkout_checkbox'] ) ) {
        update_post_meta( $order_id, 'checkout_checkbox', sanitize_text_field( $_POST['checkout_checkbox'] ) );
    }

	if ( ! empty( $_POST['checkout_checkbox_do'] ) ) {
        update_post_meta( $order_id, 'checkout_checkbox_do', sanitize_text_field( $_POST['checkout_checkbox_do'] ) );
    }
}

/**
 * Display field value on the backend WooCommerce order
 */

add_action( 'woocommerce_admin_order_data_after_billing_address', 'bt_checkout_field_display_admin_order_meta', 10, 1 );

function bt_checkout_field_display_admin_order_meta($order){
   

	$checkout_checkbox = get_post_meta( $order->get_id(), 'checkout_checkbox', true );
	if( $checkout_checkbox == true) {
		echo '<p><strong>'.__( 'Polityka prywatności' ).':</strong> ' . __( 'Tak' ) . '<p>';
	}

	$checkout_checkbox_do = get_post_meta( $order->get_id(), 'checkout_checkbox_do', true );
	if( $checkout_checkbox_do == true) {
		echo '<p><strong>'.__( 'Dane osobowe' ).':</strong> ' . __( 'Tak' ) . '<p>';
	}
}

if ( ! function_exists( 'wmsc_step_content_shipping_modified' ) ) {

	/**
	 * The content of the Shipping step.
	 */
	function wmsc_step_content_shipping_modified() {
        echo '<div class="checkout-columns">';
		do_action( 'woocommerce_checkout_shipping' );
		do_action( 'woocommerce_checkout_after_customer_details' );
        echo '</div>';
	}
}
add_action( 'wmsc_step_content_shipping_modified', 'wmsc_step_content_shipping_modified' );

if ( ! function_exists( 'wpmc_modify_shipping_step' ) ) {

    function wpmc_modify_shipping_step( $steps ) {
        $steps['billing']['sections'] = array( 'billing', 'shipping_modified' );
		//echo '<pre>'; var_dump($steps); echo '</pre>';
        return $steps;
    }
}
add_filter( 'wpmc_modify_steps', 'wpmc_modify_shipping_step' );

