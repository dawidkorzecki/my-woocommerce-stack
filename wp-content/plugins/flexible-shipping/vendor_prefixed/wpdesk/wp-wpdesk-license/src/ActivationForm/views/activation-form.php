<?php

namespace FSVendor;

/**
 * @var string $plugin_slug
 * @var string $plugin_file
 * @var \WPDesk\License\ActivationForm\FormContentRenderer $form_content
 */
use FSVendor\WPDesk\License\ActivationForm\AjaxHandler;
?>
<div id="<?php 
echo \esc_attr($plugin_slug);
?>-activation-form" class="wpdesk-license-activation-form">
	<style>
		#<?php 
echo \esc_html($plugin_slug);
?>-activation-form {
			overflow: hidden;
		}
		#<?php 
echo \esc_html($plugin_slug);
?>-activation-form div {
			float: left;
			margin-right: 10px;
			margin-bottom: 3px;
		}
		#<?php 
echo \esc_html($plugin_slug);
?>-activation-form div input[type=text] {
			min-width: 350px;
			box-shadow: 0 0 0 transparent;
			border-radius: 4px;
			border: 1px solid #8c8f94;
			background-color: #fff;
			color: #2c3338;
		}
		#<?php 
echo \esc_html($plugin_slug);
?>-activation-form div input[type=text]:disabled {
			background: rgba(255,255,255,.5);
			border-color: rgba(220,220,222,.75);
			box-shadow: inset 0 1px 2px rgb(0 0 0 / 4%);
			color: rgba(44,51,56,.5);
		}
		#<?php 
echo \esc_html($plugin_slug);
?>-activation-form div.wpdesk-license-notice {
			float: none;
			margin-left: 0px;
			font-weight: bold;
			padding: 10px 10px 10px 20px;
			color: rgb(60, 67, 74);
		}
	</style>
	<div class="wpdesk-license-activation-form-content">
		<?php 
$form_content->output_render();
?>
	</div>
	<script type="text/javascript">
		jQuery(function(){
			let plugin_slug = "<?php 
echo \esc_html($plugin_slug);
?>";
			let admin_ajax_url = "<?php 
echo \esc_url(\admin_url('admin-ajax.php'));
?>";
			let plugin_file = "<?php 
echo \esc_attr($plugin_file);
?>";
			let nonce = "<?php 
echo \esc_attr(\wp_create_nonce(\FSVendor\WPDesk\License\ActivationForm\AjaxHandler::AJAX_ACTION));
?>";
			let alert_text = "<?php 
echo \esc_attr(\__('The action failed with an error: ', 'flexible-shipping'));
?>";
			let enter_license_key = "<?php 
echo \esc_attr(\__('Enter license key', 'flexible-shipping'));
?>";

			/**
			 * Sends AJAX request.
			 * @param {string} action
			 * @param {object} $button
			 */
			function send_ajax_request( action, $button ) {
				$button.prop( "disabled", true );
				$button.parent().find( "span.spinner" ).addClass( 'is-active' );
				let api_key = jQuery( "#" + plugin_slug + "-activation-form input[name='api_key']" ).val();
				let activation_email = ' ';
				jQuery.ajax({
					type : "post",
					dataType : "json",
					url : admin_ajax_url,
					data : {
						action: "wpdesk_license_activation_" + plugin_file,
						license_action: action,
						api_key : api_key,
						activation_email: activation_email,
						security: nonce,
					},
					success: function( response ) {
						if ( response.success ) {
							let $form_content = jQuery( "#" + plugin_slug + "-activation-form div.wpdesk-license-activation-form-content" );
							$form_content.html( response.data.activation_form_content );
							$form_content.parent().trigger( "license-updated", response.data );
						}
						jQuery( "#" + plugin_slug + "-update" ).remove();
					},
					error: function ( xhr, ajaxOptions, thrownError ) {
						alert( alert_text + thrownError );
						$button.prop( "disabled", false );
						$button.parent().find( "span.spinner" ).removeClass( "is-active" );
					}
				});
			}
			jQuery(document).on( "click", "#" + plugin_slug + "-activation-form button.deactivate", function (event) {
				event.preventDefault();
				send_ajax_request( 'deactivate', jQuery( this ) );
			});
			jQuery(document).on( "click", "#" + plugin_slug + "-activation-form button.activate", function (event) {
				event.preventDefault();
				let $api_key = jQuery( "#" + plugin_slug + "-activation-form input[name='api_key']" );
				let api_key_val = $api_key.val();
				if ( "" !== api_key_val ) {
					send_ajax_request( "activate", jQuery( this ) );
				} else {
					$api_key.focus();
					$api_key.attr( "placeholder", enter_license_key );
				}
			});
		});
	</script>
</div>
<?php 
