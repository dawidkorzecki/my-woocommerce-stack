<?php if ( !defined( 'ABSPATH' ) ) exit; ?>
<?php if ( $pickup_point_data ) : ?>
	<div class="fp-point-details" id="flexible_pickup_point_<?php echo $id; ?>_details">
		<div class="header">
			<?php _e( 'Selected point details:', 'flexible-pickup' ); ?>
		</div>
		<div class="title">
			<?php echo $pickup_point_data['title']; ?>
		</div>
		<div class="address">
			<?php echo $pickup_point_data['address']; ?>
		</div>
		<?php if ( !empty( $pickup_point_data['address_2'] ) ) : ?>
			<div class="address">
				<?php echo $pickup_point_data['address_2']; ?>
			</div>
		<?php endif; ?>
		<?php if ( !empty( $pickup_point_data['postal_code'] ) || !empty( $pickup_point_data['city'] ) ) : ?>
			<div class="address">
				<?php echo $pickup_point_data['postal_code']; ?> <?php echo $pickup_point_data['city']; ?>
			</div>
		<?php endif; ?>
		<?php if ( !empty( $pickup_point_data['description'] ) ) : ?>
			<div class="description">
				<?php echo $pickup_point_data['description']; ?>
			</div>
		<?php endif; ?>
	</div>
<?php else : ?>
	<div class="fp-point-details" id="flexible_pickup_point_<?php echo $id; ?>_details" style="display:none;">
	</div>
<?php endif; ?>
