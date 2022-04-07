<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Flexible_Pickup_Flexible_Printing_Integration' ) ) {
	class Flexible_Pickup_Flexible_Printing_Integration extends Flexible_Printing_Integration {

		private $plugin = null;

		public function __construct( Flexible_Pickup_Plugin $plugin ) {
			$this->plugin = $plugin;
			$this->id = 'flexible_pickup';
			$this->title = 'Flexible Pickup';

			add_action( 'flexible_shipping_shipping_actions_html', array( $this, 'flexible_shipping_shipping_actions_html' ) );
			add_action( 'flexible_shipping_shipment_status_updated', array( $this, 'flexible_shipping_shipment_status_updated' ), 10, 3 );
		}

		/**
		 * @param $old_status string
		 * @param $new_status string
		 * @param $shipment WPDesk_Flexible_Shipping_Shipment|WPDesk_Flexible_Shipping_Shipment_Interface
		 */
		public function flexible_shipping_shipment_status_updated( $old_status, $new_status, $shipment ) {
			if ( $new_status != $old_status && $new_status == 'fs-confirmed' && $shipment->get_integration() == 'flexible_pickup' ) {
				$settings = get_option( 'flexible_pickup_settings', array() );
				if ( isset( $settings['auto_print'] ) && $settings['auto_print'] == '1'	) {
					$label_data = $shipment->get_label();
					try {
						$content_type = 'text/html';
						$this->do_print(
							$label_data['file_name'],
							$label_data['content'],
							$content_type,
							false
						);
					}
					catch ( Exception $e ) {
						error_log( sprintf( __( 'Printing error: %s', 'flexible-pickup' ), $e->getMessage() ) );
					}
				}
			}
		}


		public function options() {
			$options = array();
			/*
						$options[] = array(
							'id' => 'auto_print',
							'name' => __('Automatyczne drukowanie', 'woocommerce-dpd'),
							'desc' => __('Włącz (po utworzeniu etykieta zostanie automatycznie wydrukowana)', 'woocommerce-dpd'),
							'type' => 'checkbox',
							'std' => '',
						);
			*/
			return $options;
		}

		public function do_print_action( $args ) {
			$shipment = fs_get_shipment( $args['data']['shippment_id'] );
			/* @var $shipment WPDesk_Flexible_Shipping_Shipment|WPDesk_Flexible_Shipping_Shipment_Interface */
			$label_data = $shipment->get_label();
			$args = array(
				'title' => $label_data['file_name'],
				'content' => $label_data['content'],
				'content_type' => 'text/' . $label_data['label_format'],
				'silent' => false
			);
			do_action( 'flexible_printing_print', 'flexible_pickup', $args );
		}

		public function flexible_shipping_shipping_actions_html( $shipping ) {
			if ( !empty( $shipping['shipment'] ) ) {
				$shipment = $shipping['shipment'];
				/* @var $shipment WPDesk_Flexible_Shipping_Shipment|WPDesk_Flexible_Shipping_Shipment_Interface */
				if ( $shipment->get_meta( '_integration', '' ) == 'flexible_pickup' ) {
					if ( $shipment->get_label_url() != null ) {
						echo apply_filters( 'flexible_printing_print_button', '', 'flexible_pickup',
							array(
								'content' => 'print',
								'icon'    => true,
								'id'      => str_replace( ', ', '-', $shipment->get_id() ),
								'tip'   => __( 'Print on: %s', 'flexible-pickup' ),
								'data'    => array(
									'shippment_id'          => $shipment->get_id(),
								),
							)
						);
					}
				}
			}
		}

	}
}