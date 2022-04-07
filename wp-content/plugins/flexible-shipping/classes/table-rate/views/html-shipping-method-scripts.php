<?php
/**
 * @package Flexible Shipping
 *
 * Shipping method scripts.
 */

?>
<script type="text/javascript">
	var url = document.location.href;
	url = fs_removeParam( 'action', url );
	url = fs_removeParam( 'methods_id', url );
	url = fs_removeParam( 'added', url );
	url = fs_trimChar( url, '?' );
	if ( url.includes( 'method_id=' ) ) {
		url = url + "&action=edit";
	}
	jQuery( '#mainform' ).attr( 'action', url );
</script>
<?php if ( isset( $_GET['action'] ) && isset( $_GET['instance_id'] ) ) : ?>
	<script type="text/javascript">
		<?php
		$zone                = WC_Shipping_Zones::get_zone_by( 'instance_id', sanitize_key( $_GET['instance_id'] ) );
		$shipping_method_woo = WC_Shipping_Zones::get_shipping_method( sanitize_key( $_GET['instance_id'] ) );
		$content = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping' ) . '">' . __( 'Shipping Zones', 'flexible-shipping' ) . '</a> &gt ';
		$content .= '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&zone_id=' . absint( $zone->get_id() ) ) . '">' . esc_html( $zone->get_zone_name() ) . '</a> &gt ';
		$content .= '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&instance_id=' . sanitize_key( $_GET['instance_id'] ) ) . '">' . esc_html( $shipping_method_woo->get_title() ) . '</a>';
		$content .= ' &gt <span class="flexible-shipping-method-title"></span>';
		?>
		jQuery('#mainform h2').first().replaceWith( '<h2>' + '<?php echo $content; // phpcs:ignore ?>' + '</h2>' );
		jQuery('.flexible-shipping-method-title').text(jQuery('#woocommerce_flexible_shipping_method_title').val());
		jQuery('#woocommerce_flexible_shipping_method_title').on('keyup',function(){
			jQuery('.flexible-shipping-method-title').text(jQuery('#woocommerce_flexible_shipping_method_title').val());
		});
	</script>
<?php endif; ?>
