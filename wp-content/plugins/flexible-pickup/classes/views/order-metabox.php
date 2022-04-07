<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
foreach ( $flexible_pickup as $key => $pickup ) {
	if ( isset( $shipping_methods[$key] ) ) {
		woocommerce_wp_select( array(
			'id'                => 'pickup_point_' . $key,
			'label'             => sprintf( __( 'Pickup point for %s (%s)', 'flexible-pickup' ), $shipping_methods[$key]['name'], $key ),
			'options'           => $options,
			'value'             => $pickup['pickup_point'],
			'class'             => 'pickup-point',
			'custom_attributes' => array( 'data-key' => $key )
		) );
		$pickup_point = $this->get_point_data( $pickup['pickup_point'] );
		include ( 'pickup-point-details.php' );
		?>
		<hr/>
		<?php
	}
}
wp_nonce_field( 'flexible_pickup_ajax_nonce', 'flexible_pickup_ajax_nonce' );
?>
<input type="hidden" id="flexible_pickup_order_id" value="<?php echo $order->id; ?>"/>
<button class="button button-primary button-save-pickup-points"><?php _e( 'Save', 'flexible-pickup' ); ?></button>
<span class="flexible-pickup-spinner spinner"></span>
<span id="flexible_pickup_message" style="display:none"></span>