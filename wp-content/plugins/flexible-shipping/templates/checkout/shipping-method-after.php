<?php
/**
 * Display collection point select.
 *
 * This template can be overridden by copying it to yourtheme/flexible-shipping/checkout/shipping-method-after.php
 *
 * @package Flexible Shipping.
 *
 * @var $collection_point_label string
 * @var $select_options array
 * @var $selected_collection_point string
 * @var $collection_point_field_name string
 * @var $collection_point_description string
 * @var $collection_point_map_selector_label string
 * @car $collection_point_service_id string
 */

?><tr class="shipping flexible-shipping-collection-point">
	<td colspan="2">
		<h4><?php echo esc_html( $collection_point_label ); ?></h4>
		<?php

		$field_args = array(
			'type'        => 'select',
			'options'     => $select_options,
			'description' => $collection_point_description,
			'class'       => array( 'flexible-shipping-collection-point-select' ),
		);
		woocommerce_form_field( $collection_point_field_name, $field_args, $selected_collection_point );
		?>
		<a
			class="flexible-shipping-collection-point-map-selector"
			data-select-id="<?php echo esc_attr( $collection_point_field_name ); ?>" href="#<?php echo esc_attr( $collection_point_field_name ); ?>"
			data-service-id="<?php echo esc_attr( $collection_point_service_id ); ?>" href="#<?php echo esc_attr( $collection_point_field_name ); ?>"
		><?php echo esc_html( $collection_point_map_selector_label ); ?></a>
		<script type="text/javascript">
			if (jQuery().select2) {
				jQuery('#<?php echo esc_attr( $collection_point_field_name ); ?>').select2();
			}
		</script>
	</td>
</tr>
