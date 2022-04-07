<?php
/**
 * Checkout field: ie. access point.
 *
 * This template can be overridden by copying it to yourtheme/flexible-shipping/email/after_order_table_checkout_field.php
 *
 * @author  WP Desk
 * @version 1.0.0
 * @package Flexible Shipping.
 *
 * @var string $field_label
 * @var string $field_value
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>
<h2><?php echo esc_html( $field_label ); ?></h2>
<p>
	<?php echo esc_html( $field_value ); ?>
</p>
