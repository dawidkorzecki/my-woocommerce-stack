<?php
/**
 * Pickup Point Details
 *
 * This template can be overridden by copying it to yourtheme/flexible-pickup/pickup-point-details.php
 *
 * @author 		WP Desk
 * @package 	Flexible Pickup/Templates
 * @version     1.0.0
 */
?>
<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php if ( $pickup_point ) : ?>
<div class="fp-point-details" id="flexible_pickup_point_<?php echo $id; ?>_details">
    <div class="header">
	    <?php _e( 'Selected point details:', 'flexible-pickup' ); ?>
    </div>
    <div class="title">
		<?php echo $pickup_point['title']; ?>
    </div>
    <div class="address">
        <?php echo $pickup_point['address']; ?>
    </div>
    <?php if ( !empty( $pickup_point['address_2'] ) ) : ?>
        <div class="address">
            <?php echo $pickup_point['address_2']; ?>
        </div>
    <?php endif; ?>
	<?php if ( !empty( $pickup_point['postal_code'] ) || !empty( $pickup_point['city'] ) ) : ?>
        <div class="address">
            <?php echo $pickup_point['postal_code']; ?> <?php echo $pickup_point['city']; ?>
        </div>
	<?php endif; ?>
	<?php if ( !empty( $pickup_point['description'] ) ) : ?>
        <div class="description">
            <?php echo $pickup_point['description']; ?>
        </div>
	<?php endif; ?>
</div>
<?php else : ?>
    <div class="fp-point-details" id="flexible_pickup_point_<?php echo $id; ?>_details" style="display:none;">
    </div>
<?php endif; ?>