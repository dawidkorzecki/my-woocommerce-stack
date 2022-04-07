<?php
/**
 * Bulk actions.
 *
 * @package Flexible Shipping
 */

use FSVendor\WPDesk\Notice\Notice;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\Session\Session;
use FSVendor\WPDesk\Session\SessionFactory;

/**
 * Can handle bulk actions on shipments.
 */
class WPDesk_Flexible_Shipping_Bulk_Actions implements Hookable {

	/**
	 * .
	 *
	 * @var SessionFactory
	 */
	private $session_factory;

	/**
	 * WPDesk_Flexible_Shipping_Bulk_Actions constructor.
	 *
	 * @param SessionFactory $session_factory .
	 */
	public function __construct( SessionFactory $session_factory ) {
		$this->session_factory = $session_factory;
	}

	/**
	 * @return Session
	 */
	public function get_session() {
		return $this->session_factory->get_woocommerce_session_adapter();
	}

	/**
	 * Hooks.
	 */
	public function hooks() {

		add_filter( 'manage_edit-shop_order_columns', array( $this, 'manage_edit_shop_order_columns' ), 11 );
		add_action( 'manage_shop_order_posts_custom_column', array(
			$this,
			'manage_shop_order_posts_custom_column'
		), 11 );

		add_filter( 'bulk_actions-edit-shop_order', array( $this, 'bulk_actions_edit_shop_order' ) );
		add_filter( 'handle_bulk_actions-edit-shop_order', array(
			$this,
			'handle_bulk_actions_edit_shop_order'
		), 10, 3 );

		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ), 9999 );

		add_filter( 'posts_where', array( $this, 'posts_where' ), 999 );

		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		add_action( 'admin_init', array( $this, 'dispatch_labels_file_if_expected' ), 1 );

		add_filter( 'flexible_shipping_status', array( $this, 'flexible_shipping_status' ) );

	}

	/**
	 * .
	 *
	 * @param array $statuses .
	 *
	 * @return mixed
	 */
	public function flexible_shipping_status( $statuses ) {
		$statuses['new']       = __( 'New', 'flexible-shipping' );
		$statuses['created']   = __( 'Created', 'flexible-shipping' );
		$statuses['confirmed'] = __( 'Confirmed', 'flexible-shipping' );
		$statuses['manifest']  = __( 'Manifest', 'flexible-shipping' );
		$statuses['failed']    = __( 'Failed', 'flexible-shipping' );

		return $statuses;
	}

	/**
	 * @param string $where .
	 *
	 * @return string
	 */
	public function posts_where( $where = '' ) {
		global $pagenow;
		global $wp_query;
		global $wpdb;
		$query = $wp_query;
		if ( 'edit.php' === $pagenow && is_admin()
		     && isset( $query->query_vars['post_type'] )
		     && 'shop_order' === $query->query_vars['post_type']
		) {
			$integration = '';
			if ( isset( $_GET['flexible_shipping_integration_filter'] ) ) {
				$integration = sanitize_key( $_GET['flexible_shipping_integration_filter'] );
			}
			$status = '';
			if ( isset( $_GET['flexible_shipping_status_filter'] ) ) {
				$status = sanitize_key( $_GET['flexible_shipping_status_filter'] );
			}
			if ( '' !== $integration || '' !== $status ) {
				$add_where_meta_integration     = '';
				$add_where_meta_status          = '';
				$add_where_shipment_integration = '';
				$add_where_shipment_status      = '';
				$add_where                      = '';
				if ( '' !== $integration ) {
					$add_where_meta_integration     = " EXISTS ( SELECT 1 FROM {$wpdb->postmeta} fs_postmeta WHERE {$wpdb->posts}.ID = fs_postmeta.post_id AND fs_postmeta.meta_key = '_flexible_shipping_integration' AND  fs_postmeta.meta_value = '$integration' ) ";
					$add_where_shipment_integration = " EXISTS ( SELECT 1 FROM {$wpdb->posts} fs_posts, {$wpdb->postmeta} fs_postmeta WHERE {$wpdb->posts}.ID = fs_posts.post_parent AND fs_posts.ID = fs_postmeta.post_id AND fs_postmeta.meta_key = '_integration' AND  fs_postmeta.meta_value = '$integration' ) ";
				}
				if ( '' !== $status ) {
					$add_where_meta_status     = " EXISTS ( SELECT 1 FROM {$wpdb->postmeta} fs_postmeta WHERE {$wpdb->posts}.ID = fs_postmeta.post_id AND fs_postmeta.meta_key = '_flexible_shipping_status' AND  fs_postmeta.meta_value = '$status' ) ";
					$add_where_shipment_status = " EXISTS ( SELECT 1 FROM {$wpdb->posts} fs_posts WHERE {$wpdb->posts}.ID = fs_posts.post_parent AND fs_posts.post_status = 'fs-{$status}' ) ";
				}
				$add_where_meta = '';
				if ( '' !== $add_where_meta_integration ) {
					$add_where_meta .= $add_where_meta_integration;
				}
				if ( '' !== $add_where_meta_status ) {
					if ( '' !== $add_where_meta ) {
						$add_where_meta .= ' AND ';
					}
					$add_where_meta .= $add_where_meta_status;
				}
				$add_where_shipment = '';
				if ( '' !== $add_where_shipment_integration ) {
					$add_where_shipment .= $add_where_shipment_integration;
				}
				if ( '' !== $add_where_shipment_status ) {
					if ( '' !== $add_where_shipment ) {
						$add_where_shipment .= ' AND ';
					}
					$add_where_shipment .= $add_where_shipment_status;
				}
				$add_where_meta     = ' ( ' . $add_where_meta . ' ) ';
				$add_where_shipment = ' ( ' . $add_where_shipment . ' ) ';
				$add_where          = ' AND ( ' . $add_where_meta . ' OR ' . $add_where_shipment . ' ) ';
				$where              .= $add_where;
			}
		}

		return $where;
	}

	/**
	 * .
	 */
	public function restrict_manage_posts() {
		if ( apply_filters( 'flexible_shipping_disable_order_filters', false ) ) {
			return;
		}

		$integrations = apply_filters( 'flexible_shipping_integration_options', array() );
		if ( 0 === count( $integrations ) ) {
			return;
		}

		global $typenow;
		if ( 'shop_order' === $typenow ) {
			$integrations = apply_filters( 'flexible_shipping_integration_options', array() );
			$statuses     = apply_filters( 'flexible_shipping_status', array() );
			$integration  = '';
			if ( isset( $_GET['flexible_shipping_integration_filter'] ) ) {
				$integration = sanitize_key( $_GET['flexible_shipping_integration_filter'] );
			}
			$status = '';
			if ( isset( $_GET['flexible_shipping_status_filter'] ) ) {
				$status = sanitize_key( $_GET['flexible_shipping_status_filter'] );
			}
			include( 'views/html-orders-filter-form.php' );
		}
	}

	/**
	 * @param string $column .
	 */
	public function manage_shop_order_posts_custom_column( $column ) {
		global $post;
		if ( 'flexible_shipping' === $column ) {
			$classes   = array(
				'error'     => 'failed',
				'new'       => 'on-hold',
				'created'   => 'processing created',
				'confirmed' => 'processing confirmed',
				'manifest'  => 'processing manifest',
			);
			$statuses  = array(
				'error'     => __( 'Error', 'flexible-shipping' ),
				'new'       => __( 'New shipment', 'flexible-shipping' ),
				'created'   => __( 'Created', 'flexible-shipping' ),
				'confirmed' => __( 'Confirmed', 'flexible-shipping' ),
				'manifest'  => __( 'Manifest created', 'flexible-shipping' ),
			);
			$shippings = array();
			$shipments = fs_get_order_shipments( $post->ID );
			foreach ( $shipments as $shipment ) {
				/* @var $shipment WPDesk_Flexible_Shipping_Shipment|WPDesk_Flexible_Shipping_Shipment_Interface */
				$shipping                    = array();
				$shipping['order_id']        = $post->ID;
				$shipping['integration']     = $shipment->get_integration();
				$shipping['url']             = $shipment->get_order_metabox_url();
				$shipping['error']           = $shipment->get_error_message();
				$shipping['status']          = $shipment->get_status_for_shipping_column();
				$shipping['tracking_number'] = $shipment->get_tracking_number();
				$shipping['label_url']       = $shipment->get_label_url();
				$shipping['tracking_url']    = $shipment->get_tracking_url();
				$shipping['shipment']        = $shipment;
				$shippings[]                 = $shipping;
			}
			$shippings = apply_filters( 'flexible_shipping_shipping_data', $shippings );
			foreach ( $shippings as $shipping ) {
				if ( 'error' === $shipping['status'] ) {
					$statuses['error'] = $shipping['error'];
				} else {
					$statuses['error'] = __( 'Error', 'flexible-shipping' );
				}
				include( 'views/html-column-shipping-shipping.php' );
			}
			$messages = $this->get_session()->get( 'flexible_shipping_bulk_send', array() );
			if ( isset( $messages[ $post->ID ] ) ) {
				unset( $messages[ $post->ID ] );
			}
			$this->get_session()->set( 'flexible_shipping_bulk_send', $messages );
		}
	}

	/**
	 * @param array $columns .
	 *
	 * @return array
	 */
	public function manage_edit_shop_order_columns( $columns ) {
		$integrations = apply_filters( 'flexible_shipping_integration_options', array() );
		if ( count( $integrations ) == 0 ) {
			return $columns;
		}
		if ( isset( $columns['flexible_shipping'] ) ) {
			return $columns;
		}
		$ret = array();

		$col_added = false;

		foreach ( $columns as $key => $column ) {
			if ( ! $col_added && ( 'order_actions' === $key || 'wc_actions' === $key ) ) {
				$ret['flexible_shipping'] = __( 'Shipping', 'flexible-shipping' );
				$col_added                = true;
			}
			$ret[ $key ] = $column;
		}
		if ( ! $col_added ) {
			$ret['flexible_shipping'] = __( 'Shipping', 'flexible-shipping' );
		}

		return $ret;
	}

	/**
	 * @param array $bulk_actions .
	 *
	 * @return mixed
	 */
	public function bulk_actions_edit_shop_order( $bulk_actions ) {
		$integrations = apply_filters( 'flexible_shipping_integration_options', array() );
		if ( count( $integrations ) ) {
			$bulk_actions['flexible_shipping_send']   = __( 'Send shipment', 'flexible-shipping' );
			$bulk_actions['flexible_shipping_labels'] = __( 'Get labels', 'flexible-shipping' );
			if ( apply_filters( 'flexible_shipping_has_manifests', false ) ) {
				$bulk_actions['flexible_shipping_manifest'] = __( 'Create shipping manifest', 'flexible-shipping' );
			}
		}

		return $bulk_actions;
	}

	/**
	 * @param string $redirect_to .
	 * @param string $do_action   .
	 * @param array  $post_ids    .
	 *
	 * @return bool|string
	 */
	public function handle_bulk_actions_edit_shop_order( $redirect_to, $do_action, $post_ids ) {
		$redirect_to = remove_query_arg( 'bulk_flexible_shipping_send', $redirect_to );
		$redirect_to = remove_query_arg( 'bulk_flexible_shipping_labels', $redirect_to );
		$redirect_to = remove_query_arg( 'bulk_flexible_shipping_manifests', $redirect_to );
		if ( 'flexible_shipping_send' === $do_action ) {
			$messages = array();
			foreach ( $post_ids as $post_id ) {
				$shipments            = fs_get_order_shipments( $post_id );
				$messages[ $post_id ] = array();
				foreach ( $shipments as $shipment ) {
					/* @var $shipment WPDesk_Flexible_Shipping_Shipment|WPDesk_Flexible_Shipping_Shipment_Interface */
					try {
						$shipment->set_sent_via_bulk();
						$shipment->api_create();
						$messages[ $post_id ][ $shipment->get_id() ] = array(
							'status'  => 'created',
							'message' => __( 'Shipment created.', 'flexible-shipping' ),
						);
					} catch ( Exception $e ) {
						$messages[ $post_id ][ $shipment->get_id() ] = array(
							'status'  => 'error',
							'message' => $e->getMessage(),
						);
					}
				}
				$messages[ $post_id ][] = apply_filters(
					'flexible_shipping_bulk_send',
					array(
						'status'  => 'none',
						'message' => __( 'No action performed.', 'flexible-shipping' ),
					),
					$post_id
				);
			}
			$this->get_session()->set( 'flexible_shipping_bulk_send', $messages );
			$redirect_to = add_query_arg( 'bulk_flexible_shipping_send', count( $post_ids ), $redirect_to );

			return $redirect_to;
		}
		if ( 'flexible_shipping_labels' === $do_action ) {
			$labels_bulk_actions_handler = \FSVendor\WPDesk\FS\Shipment\Label\LabelsBulkActionHandler::get_labels_bulk_actions_handler();
			$labels_bulk_actions_handler->bulk_process_orders( $post_ids );

			try {
				$labels = $labels_bulk_actions_handler->get_labels_for_shipments();
				if ( 0 === count( $labels ) ) {
					$redirect_to = add_query_arg( 'bulk_flexible_shipping_labels', count( $post_ids ), $redirect_to );
					$redirect_to = add_query_arg( 'bulk_flexible_shipping_no_labels_created', 1, $redirect_to );

					return $redirect_to;
				}

				$labels_file_creator = new \FSVendor\WPDesk\FS\Shipment\Label\LabelsFileCreator( $labels );
				$labels_file_creator->create_labels_file();
				$labels['tmp_file']    = $labels_file_creator->get_tmp_file_name();
				$labels['client_file'] = $labels_file_creator->get_file_name();
				foreach ( $labels as $key => $label ) {
					if ( is_array( $labels[ $key ] ) && isset( $labels[ $key ]['content'] ) ) {
						unset( $labels[ $key ]['content'] );
					}
				}
			} catch ( WPDesk_Flexible_Shipping_Unable_To_Create_Tmp_Zip_File_Exception $zip_file_exception ) {
				$labels['error'] = __( 'Unable to create temporary zip archive for labels. Check temporary folder configuration on server.', 'flexible-shipping' );
			} catch ( WPDesk_Flexible_Shipping_Unable_To_Create_Tmp_File_Exception $tmp_file_exception ) {
				$labels['error'] = __( 'Unable to create temporary file for labels. Check temporary folder configuration on server.', 'flexible-shipping' );
			} catch ( Exception $e ) {
				$labels['error'] = $e->getMessage();
			}

			$this->get_session()->set( 'flexible_shipping_bulk_labels', $labels );

			$redirect_to = add_query_arg( 'bulk_flexible_shipping_labels', count( $post_ids ), $redirect_to );

			return $redirect_to;
		}
		if ( 'flexible_shipping_manifest' === $do_action ) {
			$manifests = array();
			foreach ( $post_ids as $post_id ) {
				$shipments = fs_get_order_shipments( $post_id );
				foreach ( $shipments as $shipment ) {
					/* @var $shipment WPDesk_Flexible_Shipping_Shipment|WPDesk_Flexible_Shipping_Shipment_Interface */
					if ( $shipment->get_status() != 'fs-confirmed' || $shipment->get_meta( '_manifest', '' ) != '' ) {
						continue;
					}
					try {
						$integration   = $shipment->get_integration();
						$manifest_name = $integration;
						if ( method_exists( $shipment, 'get_manifest_name' ) ) {
							$manifest_name = $shipment->get_manifest_name();
						}
						$manifest = null;
						if ( empty( $manifests[ $manifest_name ] ) ) {
							if ( fs_manifest_integration_exists( $integration ) ) {
								$manifest = fs_create_manifest( $integration );
							}
						} else {
							$manifest = $manifests[ $manifest_name ];
						}
						if ( null !== $manifest ) {
							$manifest->add_shipments( $shipment );
							$manifest->save();
							$shipment->update_status( 'fs-manifest' );
							$shipment->save();
							$manifests[ $manifest_name ] = $manifest;
						}
					} catch ( Exception $e ) { // phpcs:ignore
						// Do nothing.
					}
				}
			}
			$messages     = array();
			$integrations = apply_filters( 'flexible_shipping_integration_options', array() );
			foreach ( $manifests as $manifest ) {
				try {
					$manifest->generate();
					$manifest->save();
					$download_manifest_url = admin_url( 'edit.php?post_type=shipping_manifest&flexible_shipping_download_manifest=' . $manifest->get_id() . '&nonce=' . wp_create_nonce( 'flexible_shipping_download_manifest' ) );
					$messages[]            = array(
						'type'    => 'updated',
						'message' => sprintf(
						// Translators: manifests count and integration.
							__( 'Created manifest: %s (%s). If download not start automatically click %shere%s.', 'flexible-shipping' ), // phpcs:ignore
							$manifest->get_number(),
							$integrations[ $manifest->get_integration() ],
							'<a class="shipping_manifest_download" target="_blank" href="' . $download_manifest_url . '">',
							'</a>'
						),
					);
				} catch ( Exception $e ) {
					$messages[] = array(
						'type'    => 'error',
						'message' => sprintf(
							__( 'Manifest creation error: %s (%s).', 'flexible-shipping' ), // phpcs:ignore
							$e->getMessage(),
							$integrations[ $manifest->get_integration() ]
						),
					);
					fs_delete_manifest( $manifest );
				}
			}
			if ( count( $messages ) == 0 ) {
				$messages[] = array(
					'type'    => 'updated',
					'message' => __( 'No manifests created.', 'flexible-shipping' ),
				);
			}
			$this->get_session()->set( 'flexible_shipping_bulk_manifests', $messages );

			$redirect_to = add_query_arg( 'bulk_flexible_shipping_manifests', count( $post_ids ), $redirect_to );

			return $redirect_to;
		}

		return $redirect_to;
	}

	/**
	 * .
	 */
	public function admin_notices() {
		if ( ! empty( $_REQUEST['bulk_flexible_shipping_send'] ) ) {
			$bulk_flexible_shipping_send_count = intval( sanitize_text_field( wp_unslash( $_REQUEST['bulk_flexible_shipping_send'] ) ) );
			new Notice(
				sprintf( __( 'Bulk send shipment - processed orders: %d', 'flexible-shipping' ), $bulk_flexible_shipping_send_count ) // phpcs:ignore
			);
		}
		if ( ! empty( $_REQUEST['bulk_flexible_shipping_labels'] ) ) {
			$bulk_flexible_shipping_labels_count = intval( sanitize_text_field( wp_unslash( $_REQUEST['bulk_flexible_shipping_labels'] ) ) );
			if ( ! empty( $_REQUEST['bulk_flexible_shipping_no_labels_created'] ) ) {
				new Notice(
					sprintf( __( 'Bulk labels - processed orders: %d. No labels for processed orders.', 'flexible-shipping' ) ) // phpcs:ignore
				);
			} else {
				$labels = $this->get_session()->get( 'flexible_shipping_bulk_labels' );
				if ( is_array( $labels ) ) {
					if ( isset( $labels['error'] ) ) {
						new Notice( $labels['error'], Notice::NOTICE_TYPE_ERROR, true, 20 );
					} else {
						$nonce = wp_create_nonce( 'flexible_shipping_labels' );
						new Notice(
							sprintf(
								__( 'Bulk labels - processed orders: %d. If download not start automatically click %shere%s.', 'flexible-shipping' ), // phpcs:ignore
								$bulk_flexible_shipping_labels_count,
								'<a id="flexible_shipping_labels_url" target="_blank" href=' . admin_url( 'admin.php?flexible_shipping_labels=' . basename( $labels['client_file'] ) . '&tmp_file=' . basename( $labels['tmp_file'] ) . '&nonce=' . $nonce ) . '>',
								'</a>'
							)
						);
					}
				}
			}
		}
		if ( ! empty( $_REQUEST['bulk_flexible_shipping_manifests'] ) ) {
			$bulk_flexible_shipping_manifest_count = intval( sanitize_text_field( wp_unslash( $_REQUEST['bulk_flexible_shipping_manifests'] ) ) );
			new Notice(
				sprintf( __( 'Bulk shipping manifest - processed orders: %d', 'flexible-shipping' ), $bulk_flexible_shipping_manifest_count ) // phpcs:ignore
			);
			if ( $this->get_session()->get( 'flexible_shipping_bulk_manifests' ) ) {
				$messages = $this->get_session()->get( 'flexible_shipping_bulk_manifests' );
				foreach ( $messages as $message ) {
					new Notice(
						$message['message'],
						$message['type']
					);
				}
				$this->get_session()->set( 'flexible_shipping_bulk_manifests', null );
			}
		}
	}

	/**
	 * Dispatch labels file if requested.
	 */
	public function dispatch_labels_file_if_expected() {
		if ( isset( $_GET['flexible_shipping_labels'] ) && isset( $_GET['tmp_file'] ) && isset( $_GET['nonce'] ) ) {
			if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'flexible_shipping_labels' ) ) {
				$file     = trailingslashit( sys_get_temp_dir() ) . sanitize_text_field( wp_unslash( $_GET['flexible_shipping_labels'] ) );
				$tmp_file = trailingslashit( sys_get_temp_dir() ) . sanitize_text_field( wp_unslash( $_GET['tmp_file'] ) );

				if ( ! file_exists( $tmp_file ) ) {
					die( 'This file was already downloaded! Please retry bulk action!' );
				}

				$labels_file_dispatcher = new \FSVendor\WPDesk\FS\Shipment\Label\LabelsFileDispatcher();
				$labels_file_dispatcher->dispatch_and_delete_labels_file( $file, $tmp_file );
				die();
			}
		}
	}

}

