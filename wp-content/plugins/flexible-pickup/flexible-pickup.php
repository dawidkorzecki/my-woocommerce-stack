<?php
/*
	Plugin Name: Flexible Pickup
	Plugin URI: https://www.wpdesk.net/products/flexible-pickup/
	Description: Pickup for Flexible Shipping
	Product: Flexible Pickup
	Version: 1.0.3
	Author: WP Desk
	Author URI: https://www.wpdesk.net/
	Text Domain: flexible-pickup
	Domain Path: /languages/

	Copyright 2016 WP Desk Ltd.

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$flexible_pickup_plugin_data = array();

require_once( plugin_basename( 'classes/wpdesk/class-plugin.php' ) );

class Flexible_Pickup_Plugin extends WPDesk_Plugin_1_6 {

	public      $scripts_version = '4';
	private     $flexible_pickup = null;
	private     $post_type = null;
	private     $flexible_printing_integration = null;

	public function __construct( $plugin_data ) {

		$this->plugin_namespace = 'flexible-pickup';
		$this->plugin_text_domain = 'flexible-pickup';
		$this->plugin_has_settings = true;
		$this->default_settings_tab = 'settings';

		parent::__construct( $plugin_data );

		if ( $this->plugin_is_active() ) {
			$this->init();
			$this->hooks();
		}
	}

	public function init() {
		require_once( 'classes/class-settings-hooks.php' );
		new Flexible_Pickup_Settings_Hooks( $this );
        include_once( 'classes/class-flexible-shipping-hooks.php' );
        new Flexible_Pickup_FS_Hooks();
		include_once( 'classes/class-flexible-pickup.php' );
		$this->flexible_pickup = new Flexible_Pickup( $this );
        require_once( 'classes/class-cpt.php' );
        $this->post_type = new Flexible_Pickup_Post_Type( $this );
		include_once( 'classes/class-map-selector.php' );
		new Flexible_Pickup_Map_Selector( $this );
		do_action( 'flexible_pickup_init', $this );
	}

	public function hooks() {
		parent::hooks();
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 20 );
		add_filter( 'flexible_printing_integrations', array( $this, 'flexible_printing_integrations' ) );
    }

    public function plugins_loaded() {
	    require_once( 'classes/class-flexible-shipping-shipment.php' );
    }

	public function flexible_printing_integrations( array $integrations ) {
		include_once( 'classes/class-flexible-printing-integration.php' );
		$this->flexible_printing_integration                      = new Flexible_Pickup_Flexible_Printing_Integration( $this );
		$integrations[ $this->flexible_printing_integration->id ] = $this->flexible_printing_integration;

		return $integrations;
	}

	public function wp_enqueue_scripts() {
	    if ( is_checkout() || is_cart() ) {
		    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_script(
                'flexible-pickup-checkout',
                trailingslashit( $this->get_plugin_assets_url() ) . 'js/checkout' . $suffix . '.js',
                array( 'jquery' ),
                $this->scripts_version
            );
		    wp_localize_script( 'flexible-pickup-checkout', 'flexible_pickup_checkout', array(
			    'map_url' => site_url( '?flexible_pickup_map=1' ),
			    'ajax_url' => admin_url( 'admin-ajax.php' ),
		    ));
            wp_enqueue_style(
                'flexible-pickup',
                trailingslashit( $this->get_plugin_assets_url() ) . 'css/style' . $suffix . '.css',
                array(),
                $this->scripts_version
            );
        }
    }

	public function admin_enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		if ( isset( $screen ) && ( $screen->id == 'edit-pickup_point' || $screen->id == 'pickup_point' || $screen->id == 'shop_order' ) ) {
			wp_enqueue_script( 'fpickup_admin', trailingslashit( $this->get_plugin_assets_url() ) . 'js/admin' . $suffix . '.js', array(), $this->scripts_version );
			wp_localize_script( 'fpickup_admin', 'flexible_pickup', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			));

			wp_register_style( 'fpickup_admin', trailingslashit( $this->get_plugin_assets_url() ) . 'css/admin' . $suffix . '.css', array(), $this->scripts_version );
			wp_enqueue_style( 'fpickup_admin' );
		}
	}

	public function links_filter( $links ) {
		$pl = get_locale() === 'pl_PL';
		$domain = 'net';
		if ( $pl ) {
			$domain = 'pl';
		}
		$plugin_links = array(
			'<a href="' . admin_url( 'edit.php?post_type=pickup_point&page=flexible-pickup-settings') . '">' . __( 'Settings', 'flexible-pickup' ) . '</a>',
			'<a href="https://www.wpdesk.' . $domain . '/docs/flexible-pickup-docs/">' . __( 'Documentation', 'flexible-pickup' ) . '</a>',
			'<a href="https://www.wpdesk.' . $domain . '/support/">' . __( 'Support', 'flexible-pickup' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}

	public function woocommerce_form_field_radio( $field, $key, $args, $value ) {

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'flexible-pickup'  ) . '">*</abbr>';
		} else {
			$required = '';
		}

		// Custom attribute handling
		$custom_attributes = array();

		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		if ( ! empty( $args['validate'] ) ) {
			foreach( $args['validate'] as $validate ) {
				$args['class'][] = 'validate-' . $validate;
			}
		}

		$field = '';

		$field_container = '<fieldset class="form-row %1$s" id="%2$s">%3$s</fieldset>';

		$label_id = current( array_keys( $args['options'] ) );
		if ( ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $option_key => $option_text ) {
				$data_id = '0';
				if ( !empty( $args['custom_attributes']['data-id'] ) ) {
					$data_id = $args['custom_attributes']['data-id'];
				}
				$input = '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . 'data-id=' . $data_id . ' />';
				$field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) .'">' . $input . ' ' . $option_text . '</label>';
			}
		}

		if ( ! empty( $field ) ) {
			$field_html = '';

			if ( $args['label'] && 'checkbox' != $args['type'] ) {
				$field_html .= '<legend for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label'] . $required . '</legend>';
			}

			$field_html .= $field;

			if ( $args['description'] ) {
				$field_html .= '<span class="description">' . esc_html( $args['description'] ) . '</span>';
			}

			$container_class = esc_attr( implode( ' ', $args['class'] ) );
			$container_id = esc_attr( $args['id'] ) . '_field';

			$after = ! empty( $args['clear'] ) ? '<div class="clear"></div>' : '';

			$field = sprintf( $field_container, $container_class, $container_id, $field_html ) . $after;
		}

		return $field;
	}

	public function get_point_data( $post_id ) {
		return Flexible_Pickup::get_point_data( $post_id );
	}

}

if ( !function_exists( 'wpdesk_is_plugin_active' ) ) {
	function wpdesk_is_plugin_active( $plugin_file ) {

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( $plugin_file, $active_plugins ) || array_key_exists( $plugin_file, $active_plugins );
	}
}

require_once dirname( __FILE__ ) . '/classes/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'flexible_pickup_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function flexible_pickup_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		array(
			'name'               => __( 'WooCommerce', 'flexible-pickup' ), // The plugin name.
			'slug'               => 'woocommerce', // The plugin slug (typically the folder name).
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '2.6', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),

		array(
			'name'               => __( 'Flexible Shipping Plugin', 'flexible-pickup' ), // The plugin name.
			'slug'               => 'flexible-shipping', // The plugin slug (typically the folder name).
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '1.9.11', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),

		array(
			'name'               => __( 'Flexible PDF Plugin', 'flexible-pickup' ), // The plugin name.
			'slug'               => 'flexible-pdf', // The plugin slug (typically the folder name).
			'required'           => false, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '1.0.1', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),

	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'flexible-pickup',        // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                       // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins',  // Menu slug.
		'parent_slug'  => 'plugins.php',            // Parent menu slug.
		'capability'   => 'manage_options',         // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                     // Show admin notices or not.
		'dismissable'  => true,                     // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                       // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                    // Automatically activate plugins after installation or not.
		'message'      => '',                       // Message to output right before the plugins table.

		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'flexible-pickup' ),
			'menu_title'                      => __( 'Install Plugins', 'flexible-pickup' ),
			/* translators: %s: plugin name. */
			'installing'                      => __( 'Installing Plugin: %s', 'flexible-pickup' ),
			/* translators: %s: plugin name. */
			'updating'                        => __( 'Updating Plugin: %s', 'flexible-pickup' ),
			'oops'                            => __( 'Something went wrong with the plugin API.', 'flexible-pickup' ),
			'notice_can_install_required'     => _n_noop(
				/* translators: 1: plugin name(s). */
				'This theme requires the following plugin: %1$s.',
				'This theme requires the following plugins: %1$s.',
				'flexible-pickup'
			),
			'notice_can_install_recommended'  => _n_noop(
				/* translators: 1: plugin name(s). */
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				'flexible-pickup'
			),
			'notice_ask_to_update'            => _n_noop(
				/* translators: 1: plugin name(s). */
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				'flexible-pickup'
			),
			'notice_ask_to_update_maybe'      => _n_noop(
				/* translators: 1: plugin name(s). */
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'flexible-pickup'
			),
			'notice_can_activate_required'    => _n_noop(
				/* translators: 1: plugin name(s). */
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'flexible-pickup'
			),
			'notice_can_activate_recommended' => _n_noop(
				/* translators: 1: plugin name(s). */
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'flexible-pickup'
			),
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'flexible-pickup'
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'flexible-pickup'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'flexible-pickup'
			),
			'return'                          => __( 'Return to Required Plugins Installer', 'flexible-pickup' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'flexible-pickup' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'flexible-pickup' ),
			/* translators: 1: plugin name. */
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'flexible-pickup' ),
			/* translators: 1: plugin name. */
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'flexible-pickup' ),
			/* translators: 1: dashboard link. */
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'flexible-pickup' ),
			'dismiss'                         => __( 'Dismiss this notice', 'flexible-pickup' ),
			'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'flexible-pickup' ),
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'flexible-pickup' ),
			'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
		),

	);
	tgmpa( $plugins, $config );
}

function flexible_pickup_plugin() {
	$settings = get_option( 'flexible_pickup_settings', false );
	if ( $settings === false ) {
		$settings = array(
			'display_field_1'   => 'post_title',
			'display_field_2'   => 'city',
			'display_field_3'   => 'postal_code',
			'display_field_4'   => 'address',
			'display_field_5'   => 'address_2',
			'sort_field_1'      => 'city-ASC',
			'sort_field_2'      => 'address-ASC',
			'sort_field_3'      => 'post_title-ASC',
			'auto_create'       => 'manual',
			'order_status'      => 'wc-pending',
			'complete_order'    => '0',
		);
		update_option( 'flexible_pickup_settings', $settings );
	}
	global $flexible_pickup_plugin;
	if ( empty( $flexible_pickup_plugin ) ) {
		global $flexible_pickup_plugin_data;
		$flexible_pickup_plugin = new Flexible_Pickup_Plugin( $flexible_pickup_plugin_data );
	}
	return $flexible_pickup_plugin;;
}
flexible_pickup_plugin();

