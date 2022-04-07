<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( class_exists( 'WPDesk_Flexible_Shipping_Shipment' ) ) {

	class WPDesk_Flexible_Shipping_Shipment_flexible_pickup extends WPDesk_Flexible_Shipping_Shipment implements WPDesk_Flexible_Shipping_Shipment_Interface {

		public function __construct( $shipment, WC_Order $order = null ) {
			parent::__construct( $shipment, $order );
		}

		public function checkout( array $fs_method, $package ) {
			global $fs_package_id;
			$this->set_meta( '_pickup_point', sanitize_text_field( $_POST['flexible_pickup_point_' . strval( $fs_package_id ) ] ) );
			$this->update_status( 'fs-new' );
			do_action( 'flexible_pickup_checkout', $this, $fs_method, $package );
		}

		public function order_metabox_content() {

			global $thepostid;

			$thepostid = $this->get_id();

			$this->load_meta_data();

			if ( ! function_exists( 'woocommerce_form_field' ) ) {
				$wc_template_functions = trailingslashit( dirname( __FILE__) ) . '../../woocommerce/includes/wc-template-functions.php';
				if ( file_exists( $wc_template_functions ) ) {
					include_once( $wc_template_functions );
				}
			}

			$id = $this->get_id();

			$order = $this->get_order();
			$pickup_point = $this->get_meta( '_pickup_point', '' );
			$options = array( '' => __( 'Select pickup point', 'flexible-pickup' ) );
			$posts = get_posts( array(
				'post_type' => 'pickup_point',
				'posts_per_page' => -1,
			) );
			foreach ( $posts as $post ) {
				$options[$post->ID] = $post->post_title;
			}

			$disabled = false;

			$status = $this->get_status();

			if ( $status != 'fs-new' ) {
				$disabled = true;
			}

			$display_status = $this->get_status_for_shipping_column();

			$label_url = $this->get_label_url();

			$label_available = $this->label_avaliable();

			ob_start();

			$echo = false;

			include( 'views/order-metabox-content.php' );

			$content = ob_get_contents();

			ob_end_clean();

			return $content;
		}

		public function order_metabox() {
			echo $this->order_metabox_content();
		}

		public function get_order_metabox_title() {
			return __( 'Flexible Pickup', 'flexible-pickup' );
		}

		public function ajax_request( $action, $data ) {
			$ret = array();
			if ( $action == 'save' ) {
				$this->save_ajax_data( $data );
				$this->save();
				$ret['message'] = __( 'Shipment saved.', 'flexible-pickup' );
			}
			else if ( $action == 'send' ) {
				$this->save_ajax_data( $data );
				$this->api_create();
				$this->save();
				do_action( 'flexible_shipping_shipment_confirmed', $this );
				$ret['message'] = __( 'Shipment created.', 'flexible-pickup' );
			}
			else if ( $action == 'cancel' ) {
				$dpd_package_number = $this->get_meta( '_dpd_package_number' );
				$this->save_ajax_data( $data );
				$this->api_cancel();
				$this->save();
				$ret['message'] = __( 'Shipment canceled.', 'flexible-pickup' );
			}
			else if ( $action == 'refresh' ) {
				$this->api_refresh();
			}
			else {
				throw new Exception( sprintf( __( 'Unknown action: "%s"', 'flexible-pickup' ), $action ) );
			}
			$ret['content'] = $this->order_metabox_content();
			return $ret;
		}

		public function api_refresh() {
		}


		public function save_ajax_data( $data ) {
			if ( isset( $data['pickup_point'] ) ) {
				$this->set_meta( '_pickup_point', $data['pickup_point'] );
			}
			else {
				$this->delete_meta( '_pickup_point' );
			}
			do_action( 'flexible_pickup_save_ajax_data', $this, $data );
		}

		public function get_error_message() {
			return $this->get_meta( '_dpd_message' );
		}

		public function get_tracking_number() {
			return $this->get_id();
		}

		public function get_tracking_url() {
			return null;
		}

		public function api_create() {
			$this->update_status( 'fs-confirmed' );
		}

		public function api_cancel() {
			$this->update_status( 'fs-new' );
		}

		public function get_label() {
			if ( ! $this->label_avaliable() ) {
				throw new Exception( sprintf( __( 'Label not available for status %s.', 'flexible-pickup' ), $this->get_status() ) );
			}
			return $this->get_label_file();
		}

		public function label_avaliable() {
			if ( in_array( $this->get_status(), array( 'fs-confirmed', 'fs-manifest' ) ) ) {
				return true;
			}
			return false;
		}

		public function get_email_after_order_table() {
		}

		public function get_after_order_table() {
		}

		public function get_label_file() {
			$pdf = false;
			$settings = get_option( 'flexible_pickup_settings', array() );
			if ( isset( $settings['documents_format'] ) && $settings['documents_format'] == 'pdf' && function_exists( 'fpdf_create_pdf' ) ) {
				$pdf = true;
			}

			$label_data = array(
				'label_format' => 'html',
				'content' => null,
				'file_name' => 'flexible_pickup_' . $this->get_id() . '.html'
			);
			$flexible_pickup_plugin = flexible_pickup_plugin();
			$args = array(
				'pickup_point'  => $flexible_pickup_plugin->get_point_data( $this->get_meta( '_pickup_point') ),
				'order'         => $this->get_order(),
				'shipment'      => $this,
				'pdf'           => $pdf,
			);
			$label_data['content'] = $flexible_pickup_plugin->load_template( 'label', '', $args );
			if ( $pdf ) {
				$label_data['label_format'] = 'pdf';
				$label_data['file_name'] = 'flexible_pickup_' . $this->get_id() . '.pdf';
				$label_data['content'] = fpdf_create_pdf( $label_data['content'] );
			}
			return $label_data;
		}

		public function get_shipping_method() {
			$shipping_methods = WC()->shipping()->shipping_methods;
			if ( empty( $shipping_methods ) || !is_array( $shipping_methods ) || count( $shipping_methods ) == 0 ) {
				$shipping_methods = WC()->shipping()->load_shipping_methods();
			}
			return $shipping_methods['flexible-pickup'];
		}

		public function admin_add_shipment() {
			$order = $this->get_order();
			$weight = fs_calculate_order_weight( $order );
		}

		public function get_manifest_name() {
			$manifest_name = 'flexible_pickup_' . $this->get_meta( '_pickup_point', '0' );
			return $manifest_name;
		}

	}

}
