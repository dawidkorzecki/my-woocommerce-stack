<?php
/**
 * Select Pickup Point
 *
 * This template can be overridden by copying it to yourtheme/flexible-pickup/select-pickup-point.php
 *
 * @author 		WP Desk
 * @package 	Flexible Pickup/Templates
 * @version     1.0.0
 */
?>
<?php
    if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<tr class="flexible-pickup-point">
    <th><?php echo $label; ?></th>
    <td>
        <?php add_filter( 'woocommerce_form_field_radio', array( $this, 'woocommerce_form_field_radio' ), 10, 4 ); ?>
        <?php woocommerce_form_field( $key, $args, $value ); ?>
        <?php remove_filter( 'woocommerce_form_field_radio', array( $this, 'woocommerce_form_field_radio' ), 10 ); ?>
        <?php if ( $map_selector == '1' ) : ?>
            <p class="fp-map-selector">
                <a data-id="<?php echo $key; ?>" data-select_field="<?php echo $key; ?>" class="fp-map-selector" href="#"><?php _e( 'Select point on map', 'flexible-pickup' ); ?></a>
            </p>
        <?php endif; ?>
        <?php echo $pickup_point_details; ?>
                   <script type="text/javascript">
            flexible_pickup_checkout.points_group_<?php echo $key; ?> = '<?php echo $points_group; ?>';
		    <?php if ( $field_type == 'select2' ) : ?>
            jQuery(document).ready(function() {
                jQuery('.select2-flexible-pickup').select2();
            })
		    <?php endif; ?>
        </script>
    </td>
</tr>