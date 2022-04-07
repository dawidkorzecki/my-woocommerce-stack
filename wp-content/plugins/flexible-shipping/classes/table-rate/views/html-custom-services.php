<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
		<?php echo $this->get_tooltip_html( $data ); ?>
	</th>
	<td class="forminp">
		<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
		<span class="<?php echo esc_attr( $data['class'] ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>">

			<table class="flexible_shipping_services wc_shipping widefat wp-list-table" cellspacing="0">
				<thead>
					<tr>
						<th class="sort">&nbsp;</th>
						<th class="service_code"><?php _e( 'Code', 'flexible-shipping' ); ?></th>
						<th class="service_name"><?php _e( 'Name', 'flexible-shipping' ); ?></th>
						<th class="select">
                            <?php _e( 'Enabled', 'flexible-shipping' ); ?><?php /* <input type="checkbox" class="tips checkbox-select-all-services" value="1" data-tip="<?php _e( 'Enable all', 'flexible-shipping' ); ?>" /> */ ?>
                        </th>
					</tr>
				</thead>
				<tbody>
                    <?php foreach ( $services as $service_code => $service ) : ?>
                        <tr>
                            <td class="sort"></td>
                            <td class="service_code"><?php echo $service_code; ?></td>
                            <td class="service_name">
                                <input name="<?php echo esc_attr( $field_key ); ?>[<?php echo $service_code ?>][name]" type="text" value="<?php echo esc_attr( $service['name'] ); ?>" class="woocommerce_flexible_shipping_service_name"/>
                            </td>
							<td width="1%" class="select" nowrap>
							    <input name="<?php echo esc_attr( $field_key ); ?>[<?php echo $service_code ?>][enabled]" type="checkbox" class="checkbox-select-service" value="<?php echo esc_attr( $service_code ); ?>" <?php echo $service['enabled'] ? 'checked' : ''; ;?> />
							</td>
                        </tr>
                    <?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4"><span class="description"><?php _e( 'Drag and drop the services to control their display order. Confirm by clicking Save changes button below.', 'flexible-shipping' ); ?></span></th>
					</tr>
				</tfoot>
			</table>
         </span>
		<?php echo $this->get_description_html( $data ); ?>
		</fieldset>
	</td>
</tr>
