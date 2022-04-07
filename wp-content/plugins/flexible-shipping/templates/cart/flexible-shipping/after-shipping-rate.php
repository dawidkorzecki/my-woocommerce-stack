<?php
/**
 * Checkout before customer details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/flexible-shipping/after_shipping_rate.php
 *
 * @package WPDesk\FS\TableRate\ShippingMethod
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<p class="shipping-method-description">
	<?php echo wp_kses_post( $method_description ); ?>
</p>
