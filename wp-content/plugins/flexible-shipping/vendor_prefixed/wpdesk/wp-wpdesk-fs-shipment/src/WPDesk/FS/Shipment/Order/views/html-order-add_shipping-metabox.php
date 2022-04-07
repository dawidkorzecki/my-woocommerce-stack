<?php

namespace FSVendor;

if (!\defined('ABSPATH')) {
    exit;
}
$params = array(
    'type' => 'select',
    'options' => $select_options,
    'class' => array('first', 'paczkomaty'),
    //'label' => __( 'Integration ', 'flexible-shipping' ),
    'id' => 'fs_add_shipping',
);
\woocommerce_form_field('fs_add_shipping', $params);
?>
<button id="fs_add_shipping_button" class="button button-primary" href="" disabled="disabled"><?php 
\_e('Add', 'flexible-shipping');
?></button>
<script type="text/javascript">
	jQuery('#fs_add_shipping').change(function(){
		if ( jQuery(this).val() != '' ) {
		    jQuery('#fs_add_shipping_button').attr( 'disabled', false );
		}
		else {
            jQuery('#fs_add_shipping_button').attr( 'disabled', true );
		}
	})
    jQuery('#fs_add_shipping_button').click(function(e){
        e.preventDefault();
        window.location.href = '<?php 
echo $add_shipping_url;
?>' + '&fs_add_shipping=' + jQuery('#fs_add_shipping').val();
    });
    if ( typeof window.history.pushState == 'function' ) {
        var url = document.location.href;
        var url2 = document.location.href;
        url = fs_shipment_removeParam('_wpnonce', url);
        url = fs_shipment_removeParam('fs_add_shipping', url);
        url = fs_shipment_trimChar(url,'?');
        if ( url != url2 ) {
            window.history.pushState({}, "", url);
        }
    }
</script>

<?php 
