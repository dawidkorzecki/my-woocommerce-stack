<?php ?>
<div class="alignleft actions">
	<select name="flexible_shipping_integration_filter">
		<option value=""><?php _e( 'All shippings', 'flexible-shipping' ); ?></option>
		<optgroup label="<?php _e( 'Integration', 'flexible-shipping' ); ?>">
			<?php foreach ( $integrations as $key => $val ) : ?>
				<option value="<?php echo $key; ?>" <?php echo ($key == $integration ? 'selected' : '' );  ?>><?php echo $val; ?></option>
			<?php endforeach; ?>
		</optgroup>
	</select>
	<select name="flexible_shipping_status_filter">
		<option value=""><?php _e( 'All shippings', 'flexible-shipping' ); ?></option>
		<optgroup label="<?php _e( 'Shipment status', 'flexible-shipping' ); ?>">
			<?php foreach ( $statuses as $key => $val ) : ?>
				<option value="<?php echo $key; ?>" <?php echo ($key == $status ? 'selected' : '' );  ?>><?php echo $val; ?></option>
			<?php endforeach; ?>
		</optgroup>
	</select>
</div>
