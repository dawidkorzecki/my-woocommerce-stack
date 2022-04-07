<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<tr valign="top">
	<td class="forminp" style="padding-left:0;padding-right:0;">
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>

			<table class="flexible_shipping_methods wc_shipping widefat wp-list-table" cellspacing="0">
				<thead>
					<tr>
						<th class="sort">&nbsp;</th>
						<th class="title"><?php _e( 'Title', 'flexible-shipping' ); ?></th>
						<th class="status"><?php _e( 'Enabled', 'flexible-shipping' ); ?></th>
						<th class="visibility"><?php _e( 'Visibility', 'flexible-shipping' ); ?></th>
						<th class="default"><?php _e( 'Default', 'flexible-shipping' ); ?></th>
						<th class="integration"><?php _e( 'Integration', 'flexible-shipping' ); ?></th>
						<th class="select"><input type="checkbox" class="tips checkbox-select-all" value="1" data-tip="<?php _e( 'Select all', 'flexible-shipping' ); ?>" /></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( isset( $shipping_method_order ) && is_array( $shipping_method_order ) ) : ?>
					<?php foreach ( $shipping_method_order as $shipping_method_id ) : $shipping_method = $shipping_methods[$shipping_method_id]; ?>
						<?php $tr_class = ''; ?>
						<?php if ( isset( $_GET['added'] ) && sanitize_key( $_GET['added'] ) == $shipping_method_id ) $tr_class = 'highlight'; ?>
						<?php if ( isset( $_GET['updated'] ) && sanitize_key( $_GET['updated'] ) == $shipping_method_id ) $tr_class = 'highlight'; ?>
						<tr id="method_<?php echo $shipping_method_id; ?>" class="<?php echo $tr_class; ?>">
							<td width="1%" class="sort">
								<input type="hidden" name="method_order[<?php echo esc_attr( $shipping_method['id'] ); ?>]" value="<?php echo esc_attr( $shipping_method['id'] ); ?>" />
							</td>
							<td class="title">
								<a data-qa-id="shipping-method-<?php echo esc_attr( $shipping_method['method_title'] ); ?>" href="<?php echo add_query_arg( 'method_id' , $shipping_method_id, add_query_arg( 'action', 'edit' ) ); ?>">
									<strong><?php echo esc_html( $shipping_method['method_title'] ); ?></strong>
								</a>
								<?php if ( isset( $shipping_method['method_description'] ) && $shipping_method['method_description'] != '' ) : ?>
									(<?php echo esc_html( $shipping_method['method_description'] ); ?>)
								<?php endif; ?>
							</td>
							<td width="1%" class="status">
								<?php if ( isset($shipping_method['method_enabled']) && 'yes' === $shipping_method['method_enabled'] ) : ?>
									<span class="status-enabled tips" data-tip="<?php _e( 'yes', 'flexible-shipping' ); ?>"><?php _e( 'yes', 'flexible-shipping' ); ?></span>
								<?php else : ?>
									<span class="na">-</span>
								<?php endif; ?>
							</td>
							<td width="1%" class="default visibility">
								<?php if ( isset( $shipping_method['method_visibility'] ) && 'yes' === $shipping_method['method_visibility'] ) : ?>
									<span class="status-enabled tips" data-tip="<?php _e( 'Show only for logged in users', 'flexible-shipping' ); ?>"><?php _e( 'yes', 'flexible-shipping' ); ?></span>
								<?php else : ?>
									<span class="na tips"  data-tip="<?php _e( 'Show for all users', 'flexible-shipping' ); ?>">-</span>
								<?php endif; ?>
							</td>
							<td width="1%" class="default">
								<?php if ( 'yes' === $shipping_method['method_default'] ) : ?>
									<span class="status-enabled tips" data-tip="<?php _e( 'yes', 'flexible-shipping' ); ?>"><?php _e( 'yes', 'flexible-shipping' ); ?></span>
								<?php else : ?>
									<span class="na">-</span>
								<?php endif; ?>
							</td>
							<?php echo apply_filters( 'flexible_shipping_method_integration_col', '<td width="1%" class="integration default">-</td>', $shipping_method );?>
							<td width="1%" class="select" nowrap>
							    <input type="checkbox" class="tips checkbox-select" value="<?php echo esc_attr( $shipping_method['id'] ); ?>" data-tip="<?php echo esc_html( $shipping_method['method_title'] ); ?>" />
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th>&nbsp;</th>
						<th colspan="8"><span class="description"><?php _e( 'Drag and drop the above shipment methods to control their display order. Confirm by clicking Save changes button below.', 'flexible-shipping' ); ?></span></th>
					</tr>
                    <tr>
                        <th>&nbsp;</th>
                        <th colspan="8">
                            <button id="flexible_shipping_remove_selected" class="button" disabled><?php _e( 'Remove selected', 'flexible-shipping' ); ?></button>
                            <div class="flexilble_shipping_export_import">
                                <input id="flexible_shipping_import_file" type="file" name="import_file" style="display:none;" accept=".csv,.json" />
                                <input id="flexible_shipping_import_action" type="hidden" name="import_action" value="0" />
                                <button id="flexible_shipping_import_cancel" class="button" style="display:none;"><?php _e( 'Cancel import', 'flexible-shipping' ); ?></button>
                                <input id="flexible_shipping_do_import" style="display:none;" class="button button-primary" data-instance-id="<?php echo isset( $_GET['instance_id'] ) ? sanitize_key( $_GET['instance_id'] ) : '1'; ?>" data-nonce="<?php echo wp_create_nonce( "flexible_shipping" ); ?>" type="submit" value="<?php _e( 'Import', 'flexible-shipping' ); ?>" />
                                <button id="flexible_shipping_import" class="button" data-instance-id="<?php isset( $_GET['instance_id'] ) ? sanitize_key( $_GET['instance_id'] ) : '1'; ?>" data-nonce="<?php echo wp_create_nonce( "flexible_shipping" ); ?>" ><?php _e( 'Import', 'flexible-shipping' ); ?></button>
                                <?php do_action( 'flexible_shipping_actions_row' ); ?>
                            </div>
                            <div style="clear:both;"></div>
                        </th>
                    </tr>
				</tfoot>
			</table>

			<?php echo $this->get_description_html( $data ); ?>
		</fieldset>
	</td>
</tr>
<script type="text/javascript">
	<?php
	if( version_compare( WC()->version, '2.6.0', ">=" ) ) :
	?>
        <?php
            $zone            = WC_Shipping_Zones::get_zone_by( 'instance_id', sanitize_key( $_GET['instance_id'] ) );
            $shipping_method_woo = WC_Shipping_Zones::get_shipping_method( sanitize_key( $_GET['instance_id'] ) );
            $content = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping' ) . '">' . __( 'Shipping Zones', 'woocommerce' ) . '</a> &gt ';
            $content .= '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&zone_id=' . absint( $zone->get_id() ) ) . '">' . esc_html( $zone->get_zone_name() ) . '</a> &gt';
            $content .= esc_html( $shipping_method_woo->get_title() );
        ?>
        jQuery('#mainform h2').first().replaceWith( '<h2>' + '<?php echo $content; ?>' + '</h2>' );

        <?php
            global $wp;
            $current_url = 'admin.php?page=wc-settings&tab=shipping&instance_id=' . sanitize_key( $_GET['instance_id'] );
        ?>

        jQuery('#mainform').attr('action', '<?php echo $current_url; ?>' );
    <?php
    endif;
    ?>

	jQuery("input.checkbox-select-all").click(function(){
	    if ( jQuery(this).is(':checked') ) {
            jQuery('input.checkbox-select').prop('checked', true);
        }
        else {
            jQuery('input.checkbox-select').prop('checked', false);
        }
    })

    /**
     * Enable Bulk Action Buttons when at least one Shipping Method is selected
     *
     */
    jQuery( '.flexible_shipping_methods input[type="checkbox"]' ).click( function() {
        jQuery( '#flexible_shipping_export_selected, #flexible_shipping_remove_selected' ).attr( 'disabled', ! jQuery( '.flexible_shipping_methods td input[type="checkbox"]' ).is( ':checked' ) );
    } );

    jQuery('#flexible_shipping_remove_selected').click(function(){
        var url = '<?php echo add_query_arg( 'methods_id' , '', add_query_arg( 'action', 'delete' ) ); ?>';
        var first = true;
        jQuery('input.checkbox-select').each(function(){
            if ( jQuery(this).is(':checked')) {
                if ( !first ) {
                    url = url + ',';
                }
                else {
                    url = url + '=';
                }
                url = url + jQuery(this).val();
                first = false;
            }
        })
        if ( first ) {
            alert( '<?php _e( 'Please select shipment methods to remove', 'flexible-shipping' ); ?>' );
            return false;
        }
        if ( url != '<?php echo add_query_arg( 'method_id' , '', add_query_arg( 'action', 'delete' ) ); ?>' ) {
            jQuery('#flexible_shipping_remove_selected').prop('disabled',true);
            jQuery('.woocommerce-save-button').prop('disabled',true);
            window.location.href = url;
        }
        return false;
    })

    jQuery('#flexible_shipping_import').click(function(){
        jQuery(this).hide();
        jQuery('#flexible_shipping_do_import').show();
        jQuery('#flexible_shipping_import_file').show();
        jQuery('#flexible_shipping_import_cancel').show();
        jQuery('input[name=save]').prop('disabled',true);
        return false;
    })

    jQuery('#flexible_shipping_import_cancel').click(function(){
        jQuery(this).hide();
        jQuery('#flexible_shipping_do_import').hide();
        jQuery('#flexible_shipping_import_file').hide();
        jQuery('#flexible_shipping_import_cancel').hide();
        jQuery('#flexible_shipping_import').show();
        jQuery('input[name=save]').prop('disabled',false);
        return false;
    })

    jQuery('#flexible_shipping_do_import').click(function(){
        if ( jQuery('#flexible_shipping_import_file').val() == '' ) {
            alert('<?php _e( 'Select file to import', 'flexible-shipping' ); ?>');
            return false;
        }
        jQuery('#flexible_shipping_import_action').val('1');
        jQuery('input[name=save]').prop('disabled',false);
        jQuery('.woocommerce-save-button').click();
        return false;
    })

    <?php
        if ( isset( $_POST['import_action'] ) && sanitize_key( $_POST['import_action'] ) == '1' ) {
            ?>
            jQuery('.updated.inline:lt(1)').hide();
            jQuery('.updated.inline:lt(2)').hide();
            <?php
        }
    ?>
</script>
<?php
