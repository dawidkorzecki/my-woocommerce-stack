<?php
/**
 * The right column on the admin side 
 *
 * @package WPMultiStepCheckout
 */

defined( 'ABSPATH' ) || exit;

$now = time();

$wmsc_activation_time = get_option( 'wmsc_activation_time', '' );
$wmsc_version = get_option( 'wmsc_version', '' );

if ( empty( $wmsc_activation_time ) || version_compare( $wmsc_version, WMSC_VERSION, '<' ) ) {
    $wmsc_activation_time = $now; 
    update_option( 'wmsc_activation_time', $now );
    update_option( 'wmsc_version', WMSC_VERSION);
}


$show_discount = false;
if ( $now - 3*86400 < $wmsc_activation_time ) {
    $show_discount = true;
}

$start_date = date('j M', $wmsc_activation_time - 3*86400 );
$end_date = date('j M', $wmsc_activation_time + 2*86400 );



function iz_convert_numbers_letters( $text, $from = 'numbers' ) {
    $alphabet = str_split('abcdefghij');
    $numbers = str_split('0123456789');

    if ( $from == 'numbers' ) {
        return str_replace( $numbers, $alphabet, $text );
    } else {
        return str_replace( $alphabet, $numbers, $text );
    }
}

$offer_link = 'https://www.silkypress.com/woocommerce-multi-step-checkout-pro/?a=' . iz_convert_numbers_letters( $wmsc_activation_time ) . '&utm_source=wordpress&utm_campaign=iz_offer&utm_medium=banner';

$images_url = site_url().'/wp-content/plugins/wp-multi-step-checkout/assets/images/';
?>

<style type="text/css">
#right_column_metaboxes a.button {
    color: #fff !important;
    border: none;
    box-shadow: none;
    vertical-align: middle;
    font-size: 14px;
    height: 32px;
    line-height: 32px;
    padding: 0 18px 1px;
    background: #bc1117 !important;
    display: block-inline;
    text-align: center;
    margin: 10px auto;
}
#wpbody-content .metabox-holder.rating {
    background: url(<?php echo $images_url; ?>rating.png) 100% 80% no-repeat;
    background-size: auto auto;
    background-size: 50%;
}
#wpbody-content .metabox-holder.discount {
    background: url(<?php echo $images_url; ?>discount.png) 102% 102% no-repeat;
    background-size: auto auto;
    background-size: 50%;
}
</style>

<div id="right_column_metaboxes">

    <?php if ( $show_discount ) : ?>
    <div class="panel main_container">
    <div class="container_title">
    <h3><img src="<?php echo $images_url; ?>checkout-cart.svg" width="24" /> <?php _e('WooCommerce Multi-Step Checkout Pro', 'wp-multi-step-checkout'); ?></h3>
    </div>
        <div class="metabox-holder discount" style="text-align: center;"> 
                
        <p>Shhh... Can you keep a secret?</p>

        <p>
        <span style="color: #bc1117; font-size: 24px;">30% OFF</span><br />
        only between <span style="color: #bc1117;"><?php echo $start_date; ?> - <?php echo $end_date; ?></span>. 

        </p>
        <p>Don't tell anyone.</p>
        <p style="text-align: center;">
            <a href="<?php echo $offer_link; ?>" target="_blank" class="button" rel="noreferrer"><?php _e('Upgrade to PRO', 'wp-multi-step-checkout'); ?></a>
        </p>
        </div> 
    </div>   
    <?php endif; ?>
    
    <div class="panel main_container">
    <div class="container_title">
        <h3><?php _e('Like this Plugin?', 'wp-multi-step-checkout'); ?></h3>
    </div>
        <div class="metabox-holder rating" style="text-align: center;"> 
        <p><?php _e('Share your opinion with the world on the WordPress.org Plugin Repository.', 'wp-image-zoooom'); ?></p>
        <p><a href="https://wordpress.org/plugins/wp-multi-step-checkout/" target="_blank" class="button"><?php _e('Rate it on WordPress.org', 'wp-image-zoooom'); ?></a></p>
        </div> 
    </div>   
</div>

<div style="clear: both"></div>

