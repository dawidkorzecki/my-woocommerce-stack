<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Base plugin class for WP Desk plugins
 *
 * @author Grzegorz
 *
 */	
 
 class Flexible_Pickup_Settings_Hooks {
 	
 	private $_plugin;

 	public function __construct( Flexible_Pickup_Plugin $plugin ) {
 		$this->_plugin = $plugin;
 		$this->hooks();
 	}
 	
 	public function hooks() {
 		$func = str_replace( '-', '_', $this->_plugin->get_namespace() );

 		// settings menu
 		add_filter( $func . '_menu', array( $this, 'settings_menu' ) );
 		
 		// settings tabs
 		add_filter( $func . '_settings_tabs', array( $this, 'settings_tabs' ) );
 		
 		// unsavable tabs
 		add_filter( $func . '_unsavable_tabs', array( $this, 'unsavable_tabs' ) );
 			
 		// settings sections
 		add_filter( $func . '_registered_settings_sections', array( $this, 'registered_settings_sections' ) );
 			
 		// settings
 		add_filter( $func . '_registered_settings', array( $this, 'registered_settings' ) );
 	}
 	
 	public function get_text_domain() {
 		return $this->_plugin->get_text_domain();
 	}

 	public function settings_menu( array $menu ) {
 		$menu['type']       = 'submenu';
 		$menu['parent']     = 'edit.php?post_type=pickup_point';
 		$menu['page_title'] = __( 'Flexible Pickup Settings', $this->get_text_domain() );
 		$menu['show_title'] = true;
 		$menu['menu_title'] = __( 'Settings', $this->get_text_domain() );
 		$menu['capability'] = 'manage_options';
 		$menu['icon']       = 'dashicons-media-default';
 		$menu['position']   = null;
 		return $menu;
 	}
 	
 	public function settings_tabs( $tabs ) {
 		$tabs = array(
 				'settings'  => __( 'Settings', 'flexible-pickup'  ),
			    'printing'  => __( 'Printing', 'flexible-pickup'  )
 		);
 		$tabs = apply_filters( 'flexible_pickup_settings_tabs_tabs', $tabs );
 		return $tabs;
 	}
 	
 	public function unsavable_tabs( $tabs ) {
 		$tabs = array(
 		);
 		return $tabs;
 	}
 	
 	public function registered_settings_sections( $sections ) {
 		$sections = array(
 				'settings' => array(
				    'settings'     => __( 'Settings', 'flexible-pickup' ),
 				),
			    'printing' => array(
				    'printing'     => __( 'Printing', 'flexible-pickup' ),
			    ),
 		);
	    $sections = apply_filters( 'flexible_pickup_settings_sections_sections', $sections );
 		return $sections;
 	}
 	
 	public function registered_settings( $settings ) {
	    $fields_display_options = array(
		    ''              => __( 'None', 'flexible-pickup' ),
		    'post_title'    => __( 'Title', 'flexible-pickup' ),
		    'city'          => __( 'City', 'flexible-pickup' ),
		    'postal_code'   => __( 'Postal code', 'flexible-pickup' ),
		    'address'       => __( 'Address', 'flexible-pickup' ),
		    'address_2'     => __( 'Address 2', 'flexible-pickup' ),
	    );

	    $fields_sort_options = array(
		    ''                  => __( 'None', 'flexible-pickup' ),
		    'post_title-ASC'    => __( 'Title', 'flexible-pickup' ),
		    'post_title-DESC'   => __( 'Title DESC', 'flexible-pickup' ),
		    'city-ASC'          => __( 'City', 'flexible-pickup' ),
		    'city-DESC'         => __( 'City DESC', 'flexible-pickup' ),
		    'postal_code-ASC'   => __( 'Postal code', 'flexible-pickup' ),
		    'postal_code-DESC'  => __( 'Postal code DESC', 'flexible-pickup' ),
		    'address-ASC'       => __( 'Address', 'flexible-pickup' ),
		    'address-DESC'      => __( 'Address DESC', 'flexible-pickup' ),
		    'address_2-ASC'     => __( 'Address 2', 'flexible-pickup' ),
		    'address_2-DESC'    => __( 'Address 2 DESC', 'flexible-pickup' ),
	    );

	    $flexible_printing = apply_filters( 'flexible_printing', false );
	    $auto_print_desc = __( 'Enable automatic printing.', 'flexible-pickup' );

	    if ( $flexible_printing ) {
		    $flexible_printing_integration_url = apply_filters( 'flexible_printing_integration_url', 'flexible_pickup' );
		    $auto_print_desc = sprintf( __( 'Enable automatic printing. To change printer settings click %shere%s', 'flexible-pickup' ), '<a target="_blank" href="' . $flexible_printing_integration_url . '">', '</a>' );
	    }
	    else {
		    if ( get_locale() === 'pl_PL' ) {
			    $flexible_printing_buy_url = 'https://www.wpdesk.pl/sklep/flexible-printing/';
		    }
		    else {
			    $flexible_printing_buy_url = 'https://www.wpdesk.net/products/flexible-printing/';
		    }
		    $auto_print_desc = sprintf( __( 'Print shipment labels directly to printers. %sBuy Flexible Printing plugin%s.', 'flexible-pickup' ), '<a target="_blank" href="' . $flexible_printing_buy_url . '">', '</a>' );
	    }

	    $plugin_settings = array(
            'settings' => array(
                'settings' => array(
	                array(
		                'id'        => 'checkout',
		                'name'      => __( 'Checkout', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'header',
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'checkout_description',
		                'name'      => '',
		                'desc'      => __( 'Where to display pickup point section on checkout page', 'flexible-pickup'  ),
		                'type'      => 'descriptive_text',
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'checkout_pickup_point_position',
		                'name'      => __( 'Pickup point position', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'select',
		                'options'   => array(
		                	'woocommerce_review_order_after_order_total' => __( 'After order total', 'flexible-pickup'  ),
			                'woocommerce_review_order_after_shipping' => __( 'After order shipping', 'flexible-pickup'  ),
		                ),
		                'std'       => 'woocommerce_review_order_after_order_total'
	                ),
                    array(
                        'id'        => 'display_fields',
                        'name'      => __( 'Display fields', 'flexible-pickup'  ),
                        'desc'      => '',
                        'type'      => 'header',
                        'std'       => ''
                    ),
	                array(
		                'id'        => 'display_fields_description',
		                'name'      => '',
		                'desc'      => __( 'Fields which will be displayed on pickup point', 'flexible-pickup'  ),
		                'type'      => 'descriptive_text',
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'display_field_1',
		                'name'      => __( 'Field 1', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'select',
		                'options'   => $fields_display_options,
		                'std'       => ''
	                ),
                    array(
                        'id'        => 'display_field_2',
                        'name'      => __( 'Field 2', 'flexible-pickup'  ),
                        'desc'      => '',
                        'type'      => 'select',
                        'options'   => $fields_display_options,
                        'std'       => ''
                    ),
	                array(
		                'id'        => 'display_field_3',
		                'name'      => __( 'Field 3', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'select',
		                'options'   => $fields_display_options,
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'display_field_4',
		                'name'      => __( 'Field 4', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'select',
		                'options'   => $fields_display_options,
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'display_field_5',
		                'name'      => __( 'Field 5', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'select',
		                'options'   => $fields_display_options,
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'sort_by',
		                'name'      => __( 'Sort by', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'header',
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'sort_by_description',
		                'name'      => '',
		                'desc'      => __( 'Fields used to pickup point sort', 'flexible-pickup'  ),
		                'type'      => 'descriptive_text',
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'sort_field_1',
		                'name'      => __( 'Field 1', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'select',
		                'options'   => $fields_sort_options,
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'sort_field_2',
		                'name'      => __( 'Field 2', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'select',
		                'options'   => $fields_sort_options,
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'sort_field_3',
		                'name'      => __( 'Field 3', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'select',
		                'options'   => $fields_sort_options,
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'sort_field_4',
		                'name'      => __( 'Field 4', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'select',
		                'options'   => $fields_sort_options,
		                'std'       => ''
	                ),
	                array(
		                'id'        => 'sort_field_5',
		                'name'      => __( 'Field 5', 'flexible-pickup'  ),
		                'desc'      => '',
		                'type'      => 'select',
		                'options'   => $fields_sort_options,
		                'std'       => ''
	                ),
                ),
            ),
		    'printing' => array(
		        'printing' => array(
		        )
		    ),
        );
	    $documents_format_options = array( 'html' => __( 'HTML', 'flexible-pickup' ) );
	    $documents_format_desc = '';
	    $documents_format_name = __( 'Labels format', 'flexible-pickup' );
	    if ( function_exists( 'fpdf_create_pdf' ) ) {
		    $documents_format_options['pdf'] = __( 'PDF', 'flexible-pickup' );
	    }
	    else {
		    $documents_format_desc = sprintf( __( 'To create documents in PDF format you must install %sFlexible PDF plugin%s.' ), '<a href="https://wordpress.org/plugins/flexible-pdf/">', '</a>' );
	    }
	    if ( function_exists( 'flexible_pickup_pro_plugin' ) ) {
		    $documents_format_name = __( 'Labels and manifests format', 'flexible-pickup' );
	    }
	    $plugin_settings['printing']['printing'][] = array(
		    'id'            => 'documents_format',
		    'name' 		    => $documents_format_name,
		    'desc'          => $documents_format_desc,
		    'type'          => 'select',
		    'options'       => $documents_format_options,
		    'std'           => ''
	    );
	    if ( $flexible_printing ) {
	    	$plugin_settings['printing']['printing'][] = array(
			    'id'            => 'auto_print',
			    'name' 		    => __( 'Auto print', 'flexible-pickup' ),
			    'type' 			=> 'checkbox',
			    'desc'          => $auto_print_desc,
		    );
	    }
	    else {
		    $plugin_settings['printing']['printing'][] = array(
			    'id'            => 'auto_print',
			    'name' 		    => __( 'Auto print', 'flexible-pickup' ),
			    'type' 			=> 'descriptive_text',
			    'desc'          => $auto_print_desc,
		    );
	    }

	    $plugin_settings = apply_filters( 'flexible_pickup_settings', $plugin_settings );

 	    return array_merge( $settings, $plugin_settings );
 	}
 	
}

