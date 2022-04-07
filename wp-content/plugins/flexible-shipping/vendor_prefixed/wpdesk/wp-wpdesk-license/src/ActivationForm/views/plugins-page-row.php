<?php

namespace FSVendor;

/**
 * @var string $status
 * @var bool   $is_active
 * @var string $plugin_slug
 * @var string $plugin_file
 * @var WPDesk\License\ActivationForm\Renderer $form_content
 */
?><tr class="plugin-update-tr plugin-wpdesk-license-tr active" id="<?php 
echo \esc_attr($plugin_slug);
?>-wpdesk-license" data-slug="<?php 
echo \esc_attr($plugin_slug);
?>" data-plugin="<?php 
echo \esc_attr($plugin_file);
?>">
	<td colspan="10" class="plugin-update colspanchange">
		<div id="<?php 
echo \esc_attr($plugin_slug);
?>-wpdesk-license-activation-notice" class="notice inline notice-alt <?php 
echo \esc_html($is_active ? 'notice-success' : 'notice-warning');
?>">
			<?php 
$form_content->output_render();
?>
		</div>
		<script type="text/javascript">
			jQuery(function() {
				let plugin_slug = "<?php 
echo \esc_html($plugin_slug);
?>";
				let plugin_file = "<?php 
echo \esc_attr($plugin_file);
?>";
				let $plugin_tr = jQuery("tr[data-plugin='" + plugin_file + "']");
				if ( $plugin_tr.hasClass('update') ) {
					jQuery( "#" + plugin_slug + "-wpdesk-license" ).find( 'td' ).css( 'box-shadow', 'none' );
				}
				$plugin_tr.addClass('update');
				jQuery( "#" + plugin_slug + "-wpdesk-license-activation-notice" ).on( 'license-updated', function( event, data ){
					let $notice_div = jQuery( this );
					if ( data.is_active ) {
						$notice_div.addClass( 'notice-success' );
						$notice_div.removeClass( 'notice-warning' );
					} else {
						$notice_div.addClass( 'notice-warning' );
						$notice_div.removeClass( 'notice-success' );
					}
				});
			});
		</script>
	</td>
</tr>
<?php 
