<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<div class="panel woocommerce_options_panel">
	<div class="options_group">
		<h3><?php _e( 'Address', 'flexible-pickup' ); ?></h3>
		<?php
		woocommerce_wp_text_input( array(
			'id'                => '_company',
			'label'             => __( 'Company', 'flexible-pickup' ),
			'desc_tip'          => false,
			'type'              => 'text',
		));
		?>
		<?php
		woocommerce_wp_text_input( array(
			'id'                => '_address',
			'label'             => __( 'Address', 'flexible-pickup' ),
			'desc_tip'          => false,
			'type'              => 'text',
		));
		?>
		<?php
		woocommerce_wp_text_input( array(
			'id'                => '_address_2',
			'label'             => __( 'Address line 2', 'flexible-pickup' ),
			'desc_tip'          => false,
			'type'              => 'text',
		));
		?>
		<?php
		woocommerce_wp_text_input( array(
			'id'                => '_postal_code',
			'label'             => __( 'Postal code', 'flexible-pickup' ),
			'desc_tip'          => false,
			'type'              => 'text',
		));
		?>
		<?php
		woocommerce_wp_text_input( array(
			'id'                => '_city',
			'label'             => __( 'City', 'flexible-pickup' ),
			'desc_tip'          => false,
			'type'              => 'text',
		));
		?>
        <?php do_action( 'flexible_pickup_point_settings', $post ); ?>
	</div>
<?php /*
	<div class="options_group">
		<h3><?php _e( 'Costs', 'flexible-pickup' ); ?></h3>
		<?php
		woocommerce_wp_text_input( array(
			'id'                => '_cost',
			'label'             => __( 'Additional cost', 'flexible-pickup' ),
			'desc_tip'          => false,
			'type'              => 'number',
			'custom_attributes' => array(
			'step' => '0.01'
			),
			'data_type'         => 'number',
		));
		?>
	</div>
*/ ?>
</div>
<?php do_action( 'flexible_pickup_point_settings_scripts', $post ); ?>
