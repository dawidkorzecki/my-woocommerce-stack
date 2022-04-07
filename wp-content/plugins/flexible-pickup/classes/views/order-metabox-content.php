<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="flexible-pickup-package">
<?php

    $args = array(
	    'id'                => 'pickup_point',
	    'label'             => sprintf( __( 'Pickup point', 'flexible-pickup' ) ),
	    'options'           => $options,
	    'value'             => $pickup_point,
	    'class'             => 'pickup-point',
	    'custom_attributes' => array( 'data-key' => $id )
    );

    if ( $disabled ) {
	    $args['custom_attributes'] = array( 'disabled' => 'disabled' );
    }

	woocommerce_wp_select( $args );
	$pickup_point_data = Flexible_Pickup::get_point_data( $pickup_point );

	wp_nonce_field( 'flexible_pickup_ajax_nonce_' . $id, 'flexible_pickup_ajax_nonce_' . $id );

	include ( 'pickup-point-details.php' );

	do_action( 'flexible_pickup_order_metabox_content', $this );

	?>
    <p>
		<?php _e( 'Shipment ID:', 'flexible-pickup' ); ?> <strong><?php echo $id; ?></strong>
    </p>
    <p>
        <?php _e( 'Status:', 'flexible-pickup' ); ?> <strong><?php echo $display_status; ?></strong>
    </p>
    <p>
        <?php if ( $status == 'fs-new' ) : ?>
            <button data-id="<?php echo $id; ?>" class="button button-primary flexible-pickup-button flexible-pickup-button-create button-shipping"><?php _e( 'Create', 'flexible-pickup' ); ?></button>
        <?php endif; ?>
        <?php if ( $status == 'fs-new' ) : ?>
            <button data-id="<?php echo $id; ?>" class="button flexible-pickup-button flexible-pickup-button-save button-shipping"><?php _e( 'Save', 'flexible-pickup' ); ?></button>
        <?php endif; ?>
        <?php if ( $status == 'fs-confirmed' ) : ?>
            <a data-id="<?php echo $id; ?>" class="flexible-pickup-button flexible-pickup-button-delete-created button-shipping"><?php _e( 'Cancel', 'flexible-pickup' ); ?></a>
        <?php endif; ?>
	    <?php if ( $this->label_avaliable() ) : ?>
            <a target="_blank" href='<?php echo $label_url; ?>' data-id="<?php echo $id; ?>" class="button button-primary flexible-pickup-button"><?php _e( 'Get label', 'flexible-pickup' ); ?></a>
	    <?php endif; ?>
        <span class="spinner flexible-pickup-spinner shipping-spinner"></span>
    </p>
	<?php if ( $label_available ) : ?>
		<?php if ( apply_filters( 'flexible_printing', false ) ) : ?>
            <p>
				<?php echo apply_filters( 'flexible_printing_print_button', '', 'flexible_pickup',
					array(
						'content' => 'print',
						'id' => $id,
						'icon'  => true,
						'label' => __( 'Drukuj na: %s', 'flexible-pickup' ),
						'data' => array(
							'shippment_id'          => $id,
						),
					)
				); ?>
            </p>
		<?php endif; ?>
	<?php endif; ?>
</div>
<script type="text/javascript">
    flexible_pickup_init();
</script>