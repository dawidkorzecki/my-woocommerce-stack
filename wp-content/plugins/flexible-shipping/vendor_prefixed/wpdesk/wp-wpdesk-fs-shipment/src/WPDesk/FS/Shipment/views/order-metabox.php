<?php

namespace FSVendor;

/**
 *
 */
?><div class="flexible_shipping_shipment" id="flexible_shipping_shipment_<?php 
echo $shipment->get_id();
?>" data-id="<?php 
echo $shipment_id;
?>">
    <?php 
\wp_nonce_field('flexible_shipping_shipment_nonce', 'flexible_shipping_shipment_nonce_' . $shipment_id, \false);
?>
    <div class="flexible_shipping_shipment_content">
        <?php 
$shipment->order_metabox();
?>
    </div>
    <div class="flexible_shipping_shipment_message flexible_shipping_shipment_message_error" style="<?php 
echo $message_css_style;
?>">
	    <?php 
echo $message;
?>
    </div>
</div>
<?php 
