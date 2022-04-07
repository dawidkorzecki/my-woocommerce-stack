<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="shipping">
	<div class="shipping-status">
		<a class="icon-status icon-status-<?php echo $shipping['status']; ?> tips" href="<?php echo $shipping['url']; ?>" data-tip="<?php echo esc_html( $statuses[$shipping['status']] ); ?>">
			<?php echo esc_html( $statuses[$shipping['status']] ); ?>
		</a>
		<?php do_action( 'flexible_shipping_shipping_status_html', $shipping ); ?>
	</div>
	<div class="shipping-actions order_actions">
		<?php if ( !empty( $shipping['label_url'] ) ) : ?>
			<a class="button tips get-label" target="_blank" href="<?php echo $shipping['label_url']; ?>" data-tip="<?php _e( 'Get label for: ', 'flexible-shipping' ); ?><?php echo $shipping['tracking_number']; ?>"><?php _e( 'Get label for: ', 'flexible-shipping' ); ?><?php echo $shipping['tracking_number']; ?></a>
		<?php endif; ?>
		<?php if ( !empty( $shipping['tracking_url'] ) ) : ?>
			<a class="button tips track" target="_blank" href="<?php echo $shipping['tracking_url']; ?>" data-tip="<?php _e( 'Track shipment for: ', 'flexible-shipping' ); ?><?php echo $shipping['tracking_number']; ?>"><?php _e( 'Track shipment for: ', 'flexible-shipping' ); ?><?php echo $shipping['tracking_number']; ?></a>
		<?php endif; ?>
		<?php do_action( 'flexible_shipping_shipping_actions_html', $shipping ); ?>
	</div>
	<div style="clear: both;"></div>
	<?php do_action( 'flexible_shipping_shipping_html', $shipping ); ?>
</div>
