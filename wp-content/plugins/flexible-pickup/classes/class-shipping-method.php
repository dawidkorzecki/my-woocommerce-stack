<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Flexible_Pickup_Shipping_Method' ) ) {
    class Flexible_Pickup_Shipping_Method extends WC_Shipping_Method {

        public function __construct( $instance_id = 0 ) {

            $this->instance_id 			     	= absint( $instance_id );
            $this->id                 			= 'flexible_pickup';

            $this->supports = array();

	        $pl = get_locale() === 'pl_PL';
	        $domain = 'net';
	        if ( $pl ) {
		        $domain = 'pl';
	        }

            $this->method_title       			= __( 'Flexible Pickup', 'flexible-pickup' );
            $this->method_description 			= sprintf( __( 'Flexible Pickup with Flexible Shipping. %sRead documentation &rarr;%s', 'flexible-pickup' ), '<a href="https://www.wpdesk.' . $domain . '/docs/flexible-pickup-docs/" target="_blank">', '</a>' );

            $this->enabled		    = $this->get_option( 'enabled' );
            $this->title            = __( 'Flexible Pickup', 'flexible-pickup' );

            $this->init();

            add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

        }

        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init() {
            // Load the settings API
            $this->init_form_fields();
            $this->init_settings();
        }

	    /**
	     * Initialise Settings Form Fields
	     */
	    public function init_form_fields() {
	    }

	    public function generate_settings_html( $form_fields = array(), $echo = true ) {
	    	$ret = parent::generate_settings_html( $form_fields, $echo );
	    	ob_start();
	    	return $ret;
	    }

	    public function calculate_shipping( $package = array() ) {
	    }

    }
}
