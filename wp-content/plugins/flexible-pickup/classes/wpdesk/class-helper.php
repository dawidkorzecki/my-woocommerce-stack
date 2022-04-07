<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPDesk_Helper_Plugin' ) ) {

	class WPDesk_Helper_Plugin {

		protected $plugin_data;

		protected $text_domain;

		protected $ame_activated_key;

		protected $ame_activation_tab_key;

		function __construct( $plugin_data ) {
			global $wpdesk_helper_plugins;

			$this->plugin_data = $plugin_data;
   			if ( ! isset( $wpdesk_helper_plugins ) ) $wpdesk_helper_plugins = array();
   			$plugin_data['helper_plugin'] = $this;
			$wpdesk_helper_plugins[] = $plugin_data;

			$this->ame_activated_key = 'api_' . dirname($plugin_data['plugin']) . '_activated';
			$this->ame_activation_tab_key = 'api_' . dirname($plugin_data['plugin']) . '_dashboard';

		}

		public function inactive_notice() { ?>
			<?php if ( ! current_user_can( 'manage_options' ) ) return; ?>
			<?php if ( 1==1 && isset( $_GET['page'] ) && $this->ame_activation_tab_key == $_GET['page'] ) return; ?>
			<div class="update-nag">
				<?php printf( __( 'The %s%s%s License Key has not been activated, so the plugin is inactive! %sClick here%s to activate the license key and the plugin.', 'wpdesk-plugin' ), '<strong>', $this->plugin_data['product_id'], '</strong>', '<a href="' . esc_url( admin_url( 'admin.php?page='.$this->ame_activation_tab_key ) ) . '">', '</a>' ); ?>
			</div>
			<?php
		}


		function is_active( $add_notice = false ) {
			if ( get_option( $this->ame_activated_key, '0' ) != 'Activated' ) {
			    if ( $add_notice ) {
			  	    add_action( 'admin_notices', array( $this, 'inactive_notice' ) );
			  	}
			  	return false;
			}
			else {
				return true;
			}
		}

	}

}
