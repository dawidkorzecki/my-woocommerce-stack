<?php
/**
 * Order Items Table
 *
 * This template can be overridden by copying it to yourtheme/flexible-pickup/order-items-table.php
 *
 * @author 		WP Desk
 * @package 	Flexible Pickup/Templates
 * @version     1.0.0
 */
?>
<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php if ( count( $flexible_pickup) ) : ?>
	<?php if ( count( $flexible_pickup) > 1 ) : ?>
		<header>
			<h2><?php _e( 'Pickup points', 'flexible-pickup' ); ?></h2>
		</header>
	<?php else : ?>
		<header>
			<h2><?php _e( 'Pickup point', 'flexible-pickup' ); ?></h2>
		</header>
	<?php endif; ?>
	<?php foreach ( $flexible_pickup as $key => $val ) : ?>
        <?php $pickup_point = $val['pickup_point']; ?>
		<dl>
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
        </dl>
	<?php endforeach; ?>
<?php endif; ?>
