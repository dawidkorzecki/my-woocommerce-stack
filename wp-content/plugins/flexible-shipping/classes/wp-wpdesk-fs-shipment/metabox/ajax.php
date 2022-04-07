<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPDesk_Flexible_Shipping_Shipment_Ajax' ) ) {

    class WPDesk_Flexible_Shipping_Shipment_Ajax {

        private $plugin = null;

        public function __construct( Flexible_Shipping_Plugin $plugin ) {
            $this->plugin = $plugin;
            $this->hooks();
        }

        public function hooks() {
            add_action( 'wp_ajax_flexible_shipping', array( $this, 'wp_ajax_flexible_shipping' ) );
        }

        public function wp_ajax_flexible_shipping() {
            $json = array('status' => 'fail');
            $json['message'] = __( 'Unknown error!', 'flexible-shipping' );
            if ( empty( $_REQUEST['nonce'] ) || !wp_verify_nonce( sanitize_text_field( $_REQUEST['nonce'] ), 'flexible_shipping_shipment_nonce' ) ) {
                $json['status'] = 'fail';
                $json['message'] = __( 'Nonce verification error! Invalid request.', 'flexible-shipping' );
            }
            else if ( empty( $_REQUEST['shipment_id'] ) ) {
                $json['status'] = 'fail';
                $json['message'] = __( 'No shipment id!', 'flexible-shipping' );
            }
            else if ( empty( $_REQUEST['data'] ) || !is_array( $_REQUEST['data'] ) ) {
                $json['status'] = 'fail';
                $json['message'] = __( 'No data!', 'flexible-shipping' );
            }
            else {
                $shipment = fs_get_shipment( intval( $_REQUEST['shipment_id'] ) );
                $action = sanitize_key( $_REQUEST['fs_action'] );
                $data = $_REQUEST['data'];
                try {
                    $ajax_request = $shipment->ajax_request( $action, $data );
                    if ( is_array( $ajax_request ) ) {
                        $json['content'] = $ajax_request['content'];
                        $json['message'] = '';
                        if ( isset( $ajax_request['message'] ) ) {
                            $json['message'] = $ajax_request['message'];
                        }
                    }
                    else {
                        $json['content'] = $ajax_request;
	                    $json['message'] = '';
                        if ( $action == 'save' ) {
	                        $json['message'] = __( 'Saved', 'flexible-shipping' );
                        }
	                    if ( $action == 'send' ) {
		                    $json['message'] = __( 'Created', 'flexible-shipping' );
	                    }
                    }
	                $json['status'] = 'success';
                    if ( ! empty( $ajax_request['status'] ) ) {
	                    $json['status'] = $ajax_request['status'];
                    }
                }
                catch ( Exception $e ) {
                    $json['status'] = 'fail';
                    $json['message'] = $e->getMessage();
                }
            }
            echo json_encode($json);
            die;
        }

    }

}
