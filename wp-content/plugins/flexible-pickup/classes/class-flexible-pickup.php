<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Flexible_Pickup' ) ) {
	class Flexible_Pickup {

	    private $plugin;

		public function __construct( Flexible_Pickup_Plugin $plugin ) {
		    $this->plugin = $plugin;
			$this->hooks();
		}

		public function hooks() {

			$checkout_pickup_point_position = $this->plugin->get_option( 'checkout_pickup_point_position', 'woocommerce_review_order_after_order_total' );

		    add_action( 'admin_menu', array( $this, 'admin_menu' ), 98 );

			add_filter( 'woocommerce_screen_ids', array ( $this, 'woocommerce_screen_ids' ) );

			add_filter( 'woocommerce_shipping_methods', array( $this, 'woocommerce_shipping_methods' ), 20, 1 );

			if ( $checkout_pickup_point_position == 'woocommerce_review_order_after_order_total' ) {
				add_action( 'woocommerce_review_order_after_order_total', array(
					$this,
					'woocommerce_review_order_after_order_total'
				) );
			}
			else {
				add_action( 'woocommerce_review_order_after_shipping', array(
					$this,
					'woocommerce_review_order_after_shipping'
				) );
			}

			add_action( 'woocommerce_checkout_process', array( $this, 'woocommerce_checkout_process') );

			add_action( 'woocommerce_checkout_update_order_review', array( $this, 'woocommerce_checkout_update_order_review' ) );

//			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'woocommerce_checkout_update_order_meta' ) );

//			add_action( 'woocommerce_order_details_after_order_table', array( $this, 'woocommerce_order_details_after_order_table' ) );

//			add_action( 'woocommerce_email_order_meta', array( $this, 'woocommerce_order_details_after_order_table' ), 195 );

			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

			add_action( 'wp_ajax_flexible_pickup', array( $this, 'wp_ajax_flexible_pickup' ) );

			add_action( 'wp_ajax_flexible_pickup_point', array( $this, 'wp_ajax_flexible_pickup_point' ) );
			add_action( 'wp_ajax_nopriv_flexible_pickup_point', array( $this, 'wp_ajax_flexible_pickup_point' ) );

			add_action( 'wp_ajax_flexible_pickup_order_point', array( $this, 'wp_ajax_flexible_pickup_order_point' ) );

			add_action( 'flexible_shipping_add_shipping_options', array( $this, 'flexible_shipping_add_shipping_options' ) );

			add_action( 'admin_footer', array( $this, 'admin_footer' ) );

			add_action( 'admin_notices', array( $this, 'admin_notices' ), 1 );

		}

		public function admin_notices() {
			if ( isset( $_GET['page'] ) && isset( $_GET['post_type'] ) && $_GET['page'] == 'flexible-pickup-settings' && $_GET['post_type'] == 'pickup_point' ) {
				delete_user_meta( get_current_user_id(), 'tgmpa_dismissed_notice_flexible-pickup' );
			}
		}

		public function admin_footer() {
			if ( isset( $_GET['page'] ) && isset( $_GET['tab'] ) && $_GET['page'] == 'wc-settings' && $_GET['tab'] == 'shipping' ) {
				include( 'views/admin-footer.php' );
			}
		}

		public function admin_menu() {
        }

        public function wp_ajax_flexible_pickup_order_point() {
	        check_ajax_referer( 'flexible_pickup_ajax_nonce_' . $_POST['shipping_id'], 'security' );
	        $ret = array();
	        $ret['post'] = $_POST;
	        $ret['get'] = $_GET;

	        $pickup_point_id = $_REQUEST['pickup_point'];
	        $shipping_id = $_REQUEST['shipping_id'];

	        $pickup_point_post = get_post( $pickup_point_id );

	        if ( $pickup_point_post ) {
	            $pickup_point_data = $this->get_point_data( $pickup_point_post );
	            $key = $shipping_id;
	            $id = $shipping_id;
	            ob_start();
	            include ( 'views/pickup-point-details.php' );
	            $out = ob_get_clean();
	            $ret['id'] = $shipping_id;
	            $ret['content'] = $out;
                $ret['status'] = 'ok';
            }
            else {
	            $ret['status'] = 'error';
	            $ret['message'] = __( 'Pickup point not found!', 'flexible-pickup' );
            }

	        echo json_encode( $ret );
	        wp_die();
        }

        public function wp_ajax_flexible_pickup_point() {
	        check_ajax_referer( 'flexible_pickup_ajax_nonce_' . $_REQUEST['id'], 'security' );

	        $ret = array();
	        $ret['post'] = $_POST;
	        $ret['get'] = $_GET;

	        $post_id = $_REQUEST['post_id'];

	        $id = $_REQUEST['id'];

	        $post = get_post( $post_id );

	        $point = false;

	        if ( $post ) {
		        $point = $this->get_point_data( $post->ID );
                $ret['content'] = $this->plugin->load_template(
	                'pickup-point-details',
	                '',
	                array(
		                'id'                => $id,
		                'pickup_point'      => $point
	                )
                );
                $ret['id'] = $id;
		        $ret['status'] = 'ok';
		        $ret['pickup_point'] = $point;
            }
            else {
	            $ret['status'] = 'error';
	            $ret['message'] = __( 'Pickup point not found!', 'flexible-pickup' );

            }

	        echo json_encode( $ret );
	        wp_die();
        }

		public function wp_ajax_flexible_pickup() {

			check_ajax_referer( 'flexible_pickup_ajax_nonce', 'security' );

			$ret = array();
			$ret['post'] = $_POST;
			$ret['get'] = $_GET;

			$order_id = $_REQUEST[ 'order_id' ];

			$order = wc_get_order( $order_id );

			if ( $order ) {
				$flexible_pickup = array();
				$data = $_REQUEST['data'];
				foreach ( $data as $key => $pickup_point ) {
					$flexible_pickup[ $key ] = array( 'pickup_point' => $pickup_point );
				}
				update_post_meta( $order_id, '_flexible_pickup', $flexible_pickup );
				$ret['status'] = 'ok';
				$ret['message'] = __( 'Saved', 'flexible-pickup' );
			}
			else {
				$ret['status'] = 'error';
				$ret['message'] = __( 'Order not found!', 'flexible-pickup' );
			}

			echo json_encode( $ret );
			wp_die();
		}

		public function add_meta_boxes() {
			global $post_id;
			$post = get_post($post_id);
			$show_metabox = false;
			if ( $post && $post->post_type == 'shop_order') {
				$order = new WC_Order( $post_id );
				$shipping_methods = $order->get_shipping_methods();
				foreach ( $shipping_methods as $shipping_method ) {
					if ( isset( $shipping_method['item_meta'] )
					     && isset( $shipping_method['item_meta']['method_id'] )
					     && isset( $shipping_method['item_meta']['method_id'][0] )
					     && $shipping_method['item_meta']['method_id'][0] == 'flexible_pickup'
					) {
						$show_metabox = true;
					}
					if ( isset( $shipping_method['item_meta'] )
					     && isset( $shipping_method['item_meta']['_fs_method'] )
					     && isset( $shipping_method['item_meta']['_fs_method'][0] )
					) {
						$fs_method = unserialize( $shipping_method['item_meta']['_fs_method'][0] );
						if ( isset( $fs_method['method_integration'] )
							&& $fs_method['method_integration'] == 'flexible_pickup'
						) {
							$show_metabox = true;
						}
					}
				}
			}
			if ( $show_metabox ) {
				add_meta_box(
					'flexible-pickup-box',
					__( 'Flexible Pickup', 'flexible-pickup' ),
					array( $this, 'admin_box_flexible_pickup' ),
					'shop_order',
					'side',
					'default'
				);
			}
		}

		public function admin_box_flexible_pickup() {
			global $post_id;
			$order = new WC_Order( $post_id );
			$flexible_pickup = get_post_meta( $post_id, '_flexible_pickup', true );
			if ( $flexible_pickup == '' ) {
				$flexible_pickup = array();
			}
			$options = array( '' => __( 'Select pickup point', 'flexible-pickup' ) );
			$posts = get_posts( array(
				'post_type' => 'pickup_point',
				'posts_per_page' => -1,
			) );
			foreach ( $posts as $post ) {
				$options[$post->ID] = $post->post_title;
			}
			$shipping_methods = $order->get_shipping_methods();
			foreach ( $shipping_methods as $key => $shipping_method ) {
				if ( isset( $shipping_method['item_meta'] )
				     && isset( $shipping_method['item_meta']['method_id'] )
				     && isset( $shipping_method['item_meta']['method_id'][0] )
				     && $shipping_method['item_meta']['method_id'][0] == 'flexible_pickup'
				) {
					if ( !isset( $flexible_pickup[$key] ) ) {
						$flexible_pickup[$key] = array( 'pickup_point' => '' );
					}
				}
				if ( isset( $shipping_method['item_meta'] )
				     && isset( $shipping_method['item_meta']['_fs_method'] )
				     && isset( $shipping_method['item_meta']['_fs_method'][0] )
				) {
					$fs_method = unserialize( $shipping_method['item_meta']['_fs_method'][0] );
					if ( isset( $fs_method['method_integration'] )
					     && $fs_method['method_integration'] == 'flexible_pickup'
					) {
						if ( !isset( $flexible_pickup[$key] ) ) {
							$flexible_pickup[$key] = array( 'pickup_point' => '' );
						}
					}
				}
			}
			include ( 'views/order-metabox.php' );
		}

		function woocommerce_screen_ids( $screen_ids ) {
			$screen_ids[] = 'edit-pickup_point';
			$screen_ids[] = 'pickup_point';
			return $screen_ids;
		}

		public function woocommerce_shipping_methods( $methods ) {
			include_once( 'class-shipping-method.php' );
			$methods['flexible_pickup'] = 'Flexible_Pickup_Shipping_Method';
			return $methods;
		}

		public function format_pickup_point( $post, $post_meta ) {
			$all_shipping_methods = WC()->shipping()->get_shipping_methods();
			$flexible_pickup_shipping_method = $all_shipping_methods['flexible_pickup'];
			$first = true;
			$ret = '';
			for ( $i = 1; $i < 6; $i++ ) {
				$display_field = $this->plugin->get_option( 'display_field_' . strval( $i ), '' );
				if ( $display_field != '' ) {
					if ( $display_field == 'post_title' ) {
						if ( ! $first ) {
							$ret .= ', ';
						}
						$ret .= $post->post_title;
						$first = false;
					}
					else {
						if ( isset( $post_meta['_' . $display_field] )
						     && isset( $post_meta['_' . $display_field][0] )
						     && $post_meta['_' . $display_field][0] != ''
						) {
							if ( ! $first ) {
								$ret .= ', ';
							}
							$ret .= $post_meta[ '_' . $display_field ][0];
							$first = false;
						}
					}
				}
			}
			return $ret;
        }

        private function prepare_pickup_point_data_for_template( $shipping_methods, $package, $key, $meta_data ) {
	        $pickup_point_data_for_template = array();
	        $field_type = 'radio';
	        if ( isset( $meta_data['_fs_method']['fp_points_field'] ) && $meta_data['_fs_method']['fp_points_field'] != '' ) {
		        $field_type = $meta_data['_fs_method']['fp_points_field'];
	        }
	        $field_type = apply_filters( 'flexible_pickup_field_type', 'radio', $field_type );
	        if ( $field_type != 'radio' ) {
		        $options = array( '' => __( 'Select pickup point', 'flexible-pickup' ) );
	        }
	        $map_selector = '0';
	        if ( $field_type == 'select' ||  $field_type == 'select2' ) {
		        if ( isset( $meta_data['_fs_method']['fp_map_selector'] ) && $meta_data['_fs_method']['fp_map_selector'] != '' ) {
			        $map_selector = $meta_data['_fs_method']['fp_map_selector'];
		        }
	        }
	        $query_args = array(
		        'post_type'         => 'pickup_point',
		        'posts_per_page'    => -1,
		        'meta_query'        => array(),
		        'orderby'           => array(),
	        );
	        if ( isset( $meta_data['_fs_method']['fp_cod'] ) && $meta_data['_fs_method']['fp_cod'] == '1' ) {
		        $query_args['meta_query'][] = array(
			        'key'      => '_cod',
			        'value'    => 'yes'
		        );
	        }
	        $points_group = '';
	        if ( isset( $meta_data['_fs_method']['fp_points_group'] ) && $meta_data['_fs_method']['fp_points_group'] != '' ) {
		        $points_group = $meta_data['_fs_method']['fp_points_group'];
		        $query_args['tax_query'] = array(
			        array(
				        'taxonomy'          => 'fp_points_group',
				        'include_children'  => true,
				        'field'             => 'id',
				        'terms'             => $meta_data['_fs_method']['fp_points_group'],
			        )
		        );
	        }
	        $point_details = '0';
	        if ( isset( $meta_data['_fs_method']['fp_point_details'] ) && $meta_data['_fs_method']['fp_point_details'] != '' ) {
		        $point_details = $meta_data['_fs_method']['fp_point_details'];
	        }
	        $all_shipping_methods = WC()->shipping()->get_shipping_methods();
	        $flexible_pickup_shipping_method = $all_shipping_methods['flexible_pickup'];
	        for ( $i = 1; $i < 6; $i++ ) {
		        //$sort_field_option = $flexible_pickup_shipping_method->get_option( 'sort_field_' . strval( $i ), '' );
		        $sort_field_option = $this->plugin->get_option( 'sort_field_' . strval( $i ), '' );
		        if ( $sort_field_option != '' ) {
			        list( $sort_field, $sort_field_order ) = explode( '-', $sort_field_option );
			        if ( $sort_field == 'post_title' ) {
				        $query_args['orderby'][$sort_field ] = $sort_field_order;
			        }
			        else {
				        $query_args['orderby'][ '_' . $sort_field ] = $sort_field_order;
				        $query_args['meta_query'][] = array( 'key' => '_' . $sort_field );
			        }
		        }
	        }
	        $posts = get_posts( $query_args );
	        foreach ( $posts as $post ) {
		        $post_meta = get_post_meta( $post->ID );
		        $first = true;
		        $options[$post->ID] = $this->format_pickup_point( $post, $post_meta );
	        }
	        $package_name = apply_filters( 'woocommerce_shipping_package_name', sprintf(_n('Shipping', 'Shipping %d', ($key + 1), 'flexible-pickup'), ($key + 1)), $key, $package);
	        $field_type_item = 'select';
	        if ( $field_type == 'radio' ) {
		        $field_type_item = 'radio';
	        }
	        $args = array(
		        'type'              => $field_type_item,
		        'label'             => '',
		        'class'             => array( 'flexible-pickup-point' ),
		        'options'           => $options,
		        'custom_attributes' => array( 'data-id' => strval($key) ),
	        );
	        if ( $field_type == 'select2' ) {
		        $args['input_class'] = array( 'select2-flexible-pickup' );
	        }
	        $id = 'flexible_pickup_point_' . $key;
	        wp_nonce_field( 'flexible_pickup_ajax_nonce_' . $key, 'flexible_pickup_ajax_nonce_' . $key );
	        $value = WC()->session->get( 'flexible_pickup_point_' . $key, '' );
	        $pickup_point_details = '';
	        if ( $point_details == '1' ) {
		        $pickup_point = false;
		        $pickup_point_post = false;
		        if ( $value ) {
			        $pickup_point_post = get_post( $value );
		        }
		        if ( $pickup_point_post ) {
			        $pickup_point = $this->plugin->get_point_data( $pickup_point_post->ID );
		        }
		        $pickup_point_details = $this->plugin->load_template(
			        'pickup-point-details',
			        '',
			        array(
				        'id'                => $key,
				        'key'               => $id,
				        'pickup_point'      => $pickup_point
			        )
		        );
	        }
	        $pickup_point_data_for_template = array(
			        'id'                    => $key,
			        'label'                 => sprintf( __('Select pickup point (%s)', 'flexible-pickup' ), $package_name ),
			        'package'               => $package,
			        'shipping_method'       => $shipping_methods[$key],
			        'package_name'          => $package_name,
			        'args'                  => $args,
			        'key'                   => $id,
			        'value'                 => $value,
			        'field_type'            => $field_type,
			        'map_selector'          => $map_selector,
			        'points_group'          => $points_group,
			        'pickup_point_details'  => $pickup_point_details,
	        );
	        return $pickup_point_data_for_template;
        }

		public function woocommerce_review_order_after_shipping() {
			$packages = WC()->shipping()->get_packages();
			$shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
			if ( is_array( $packages ) ) {
				foreach ( $packages as $key => $package ) {
					$rate               = false;
					$is_flexible_pickup = false;
					if ( isset( $package['rates'][ $shipping_methods[ $key ] ] ) ) {
						$rate = $package['rates'][ $shipping_methods[ $key ] ];
						if ( $rate->method_id == 'flexible_shipping' ) {
							$meta_data = $rate->get_meta_data();
							if ( isset( $meta_data['_fs_method'] ) && $meta_data['_fs_method']['method_integration'] == 'flexible_pickup' ) {
								$is_flexible_pickup = true;
							}
						}
					}
					if ( $is_flexible_pickup ) {
						$pickup_point_data_for_template = $this->prepare_pickup_point_data_for_template( $shipping_methods, $package, $key, $meta_data );
						echo $this->plugin->load_template(
							'select-pickup-point-review_order_after_shipping',
							'',
							$pickup_point_data_for_template
						);
					}
				}
			}
		}

		public function woocommerce_review_order_after_order_total() {
			$packages = WC()->shipping()->get_packages();
			$shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
			if ( is_array( $packages ) ) {
				foreach ( $packages as $key => $package ) {
					$rate = false;
					$is_flexible_pickup = false;
					if ( isset( $package['rates'][$shipping_methods[$key]] ) ) {
						$rate = $package['rates'][$shipping_methods[$key]];
						if ( $rate->method_id == 'flexible_shipping' ) {
							$meta_data = $rate->get_meta_data();
							if ( isset( $meta_data['_fs_method'] ) && $meta_data['_fs_method']['method_integration'] == 'flexible_pickup' ) {
								$is_flexible_pickup = true;
							}
						}
					}
					if ( $is_flexible_pickup ) {
						$pickup_point_data_for_template = $this->prepare_pickup_point_data_for_template( $shipping_methods, $package, $key, $meta_data );
						echo $this->plugin->load_template(
							'select-pickup-point-review_order_after_order_total',
							'',
							$pickup_point_data_for_template
						);
					}
				}
			}
		}

		public function woocommerce_checkout_update_order_review( $post_data ) {
			parse_str( $post_data, $data );
			if ( WC()->cart->needs_shipping() ) {
				WC()->cart->calculate_totals();
				$packages = WC()->cart->get_shipping_packages();
				foreach ( $packages as $key => $package ) {
					if ( isset( $data[ 'flexible_pickup_point_' . $key ] ) ) {
						WC()->session->set( 'flexible_pickup_point_' . $key, $data[ 'flexible_pickup_point_' . $key ] );
					}
				}
			}
		}

		public function woocommerce_checkout_process() {
			$session_shipping_methods = WC()->session->get('chosen_shipping_methods');
			WC()->cart->calculate_totals();
			$packages = WC()->cart->get_shipping_packages();
			$all_shipping_methods = WC()->shipping()->get_shipping_methods();
			if ( empty( $all_shipping_methods ) ) {
				$all_shipping_methods = WC()->shipping()->load_shipping_methods();
			}
			$flexible_shipping = $all_shipping_methods['flexible_shipping'];
			$flexible_shipping_rates = $flexible_shipping->get_all_rates();
			foreach ( $packages as $key => $package ) {
				$session_shipping_method = $session_shipping_methods[$key];
				if ( isset( $flexible_shipping_rates[$session_shipping_method] ) ) {
					$shipping_method = $flexible_shipping_rates[$session_shipping_method];
					if ( $shipping_method['method_integration'] == 'flexible_pickup' ) {
						if ( empty( $_POST['flexible_pickup_point_' . $key] ) ) {
							$package_name = apply_filters('woocommerce_shipping_package_name', sprintf( _n( 'Shipping', 'Shipping %d', ( $key + 1) , 'flexible-pickup'), ( $key + 1 ) ), $key, $package);
							wc_add_notice( sprintf( __( 'Please select pickup point (%s)', 'flexible-pickup' ), $package_name ), 'error' );
						}
					}
				}
			}
		}

		public function woocommerce_checkout_update_order_meta( $order_id ) {
			$order = wc_get_order( $order_id );
			$flexible_pickup_data = array();
			$shipping_methods = $order->get_shipping_methods();
			$count = 0;
			foreach ( $shipping_methods as $key => $shipping_method ) {
				if ( isset( $shipping_method['item_meta'] )
				     && isset( $shipping_method['item_meta']['_fs_method'] )
				     && isset( $shipping_method['item_meta']['_fs_method'][0] )
				) {
					$fs_method = unserialize( $shipping_method['item_meta']['_fs_method'][0] );
					if ( $fs_method['method_integration'] == 'flexible_pickup' ) {
						$flexible_pickup_data[$key] = array( 'pickup_point' => sanitize_text_field( $_POST['flexible_pickup_point_' . strval( $count ) ] ) );
						update_post_meta( $order_id, '_flexible_pickup', $flexible_pickup_data );
					}
				}
				$count++;
			}
		}

		public function woocommerce_order_details_after_order_table( $order ) {
			$flexible_pickup = get_post_meta( $order->id, '_flexible_pickup', true );
			if ( $flexible_pickup == '' ) {
				$flexible_pickup = array();
			}
			$shipping_methods = $order->get_shipping_methods();
			foreach ( $shipping_methods as $key => $shipping_method ) {
				if ( isset( $shipping_method['item_meta'] )
				     && isset( $shipping_method['item_meta']['method_id'] )
				     && isset( $shipping_method['item_meta']['method_id'][0] )
				     && $shipping_method['item_meta']['method_id'][0] == 'flexible_pickup'
				) {
					if ( !isset( $flexible_pickup[$key] ) ) {
						$flexible_pickup[$key] = array( 'pickup_point' => '' );
					}
				}
				if ( isset( $shipping_method['item_meta'] )
				     && isset( $shipping_method['item_meta']['_fs_method'] )
				     && isset( $shipping_method['item_meta']['_fs_method'][0] )
				) {
					$fs_method = unserialize( $shipping_method['item_meta']['_fs_method'][0] );
					if ( isset( $fs_method['method_integration'] )
					     && $fs_method['method_integration'] == 'flexible_pickup'
					) {
						if ( !isset( $flexible_pickup[$key] ) ) {
							$flexible_pickup[$key] = array( 'pickup_point' => '' );
						}
					}
				}
			}
			foreach ( $flexible_pickup as $key => $val ) {
			    if ( empty( $val['pickup_point'] ) || $val['pickup_point'] === '' ) {
			        unset( $flexible_pickup[$key] );
                }
                else {
			        $post = get_post( $val['pickup_point'] );
			        $post_meta = get_post_meta( $post->ID );
			        $flexible_pickup[$key]['pickup_point_formatted'] = $this->format_pickup_point( $post, $post_meta );
	                $flexible_pickup[$key]['pickup_point'] = $this->get_point_data( $post->ID );
                }
            }
			$args = array(
				'flexible_pickup' => $flexible_pickup,
            );
			echo $this->plugin->load_template( 'order-items-table', '' , $args );
		}

	    public static function get_point_data( $post_id ) {

		    $post = get_post( $post_id );

		    $point = array();

		    if ( $post ) {
			    $point        = array(
				    'id'    => $post->ID,
				    'title' => $post->post_title,
			    );
			    $post_meta = get_post_meta( $post->ID );
			    $point['description'] = $post->post_content;
			    $point['company'] = '';
			    if ( isset( $post_meta['_company'] ) && isset( $post_meta['_company'][0] ) ) {
				    $point['company'] = $post_meta['_company'][0];
			    }
			    $point['address'] = '';
			    if ( isset( $post_meta['_address'] ) && isset( $post_meta['_address'][0] ) ) {
				    $point['address'] = $post_meta['_address'][0];
			    }
			    $point['address_2'] = '';
			    if ( isset( $post_meta['_address_2'] ) && isset( $post_meta['_address_2'][0] ) ) {
				    $point['address_2'] = $post_meta['_address_2'][0];
			    }
			    $point['city'] = '';
			    if ( isset( $post_meta['_city'] ) && isset( $post_meta['_city'][0] ) ) {
				    $point['city'] = $post_meta['_city'][0];
			    }
			    $point['postal_code'] = '';
			    if ( isset( $post_meta['_postal_code'] ) && isset( $post_meta['_postal_code'][0] ) ) {
				    $point['postal_code'] = $post_meta['_postal_code'][0];
			    }
			    if ( isset( $post_meta['_lat'] ) && isset( $post_meta['_lat'][0] ) ) {
				    $point['lat'] = $post_meta['_lat'][0];
			    }
			    if ( isset( $post_meta['_lng'] ) && isset( $post_meta['_lng'][0] ) ) {
				    $point['lng'] = $post_meta['_lng'][0];
			    }
			    $point['all'] = $point['title'] . ' ' . $point['description'] . ' ' . $point['company'] . ' ' . $point['address'] . ' ' . $point['address_2'] . ' ' . $point['city'] . ' ' . $point['postal_code'];

			    $point = apply_filters( 'flexible_pickup_point_point_data', $point, $post, $post_meta );

		    }

		    return $point;
        }

		public function flexible_shipping_add_shipping_options( $options ) {
			$options['flexible_pickup'] = 'Flexible Pickup';
			return $options;
		}


	}
}
