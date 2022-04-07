<?php

namespace FSVendor;

/** @var string $message */
?>

<p id="woocommerce_flexible_shipping_compatibility_message"
   style="color: red;margin-bottom: 20px;"><?php 
echo \wp_kses_post($message);
?></p>

<input type="hidden" name="settings_saving_block" value="1"/>

<script type="text/javascript">
	document.addEventListener( "DOMContentLoaded", function ( event ) {
		document.querySelector( '#mainform .woocommerce-save-button' ).disabled = true;

		var table_container = jQuery( '#woocommerce_flexible_shipping_method_rules' );
		var message_container = jQuery( '#woocommerce_flexible_shipping_compatibility_message' );

		if ( table_container.length ) {
			message_container.insertBefore( table_container );
		}
	} );
</script><?php 
