<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Flexible_Pickup_FS_Hooks' ) ) {

	class Flexible_Pickup_FS_Hooks {
		
		public function __construct() {
					
			add_filter( 'flexible_shipping_integration_options', array( $this, 'flexible_shipping_integration_options' ),  10 );
				
			add_filter( 'flexible_shipping_method_settings', array( $this, 'flexible_shipping_method_settings' ), 10, 2 );
				
			add_action( 'flexible_shipping_method_script', array( $this, 'flexible_shipping_method_script' ) );
		
			add_filter( 'flexible_shipping_process_admin_options', array( $this, 'flexible_shipping_process_admin_options' ), 10, 1 );
				
			add_filter( 'flexible_shipping_method_integration_col', array( $this, 'flexible_shipping_method_integration_col' ), 10, 2 );
				
			add_filter( 'flexible_shipping_method_rate_id', array( $this, 'flexible_shipping_method_rate_id' ), 10, 2 );

			add_filter( 'flexible_shipping_add_method', array( $this, 'flexible_shipping_add_method' ), 10, 3 );
									
		}
		
		function flexible_shipping_integration_options( $options ) {
			$options['flexible_pickup'] = __( 'Flexible Pickup', 'flexible-pickup' );
			return $options;
		}
		
		public function flexible_shipping_method_settings( $flexible_shipping_settings, $shipping_method ) {
			$pl = get_locale() === 'pl_PL';
			$domain = 'net';
			if ( $pl ) {
				$domain = 'pl';
			}
		    $pro_url = 'https://www.wpdesk.net/product/flexible-pickup';
			if ( $domain == 'pl' ) {
				$pro_url = 'https://www.wpdesk.net/shop/flexible-pickup';
            }
			$points_group = array( '' => __( 'All pickup points', 'flexible-pickup' ) );
			$settings = array(
                'fp_points_group'   => array(
                    'title'     => __( 'Pickup points group', 'flexible-pickup' ),
                    'type'      => 'select',
                    'default'   => isset( $shipping_method['fp_points_group'] ) ? $shipping_method['fp_points_group'] : '',
                    'options'   => $points_group,
                    'description' => sprintf( __( 'More options available in %sPRO version. →%s', 'flexible-pickup' ), '<a target="_blank" href="' . $pro_url . '">', '</a>' ),
                ),
                'fp_points_field'   => array(
                    'title'     => __( 'Display pickup points as', 'flexible-pickup' ),
                    'type'      => 'select',
                    'default'   => isset( $shipping_method['fp_points_field'] ) ? $shipping_method['fp_points_field'] : '',
                    'options'   => array(
                        'radio'     => __( 'Radio', 'flexible-pickup' ),
                    ),
                    'description' => sprintf( __( 'More options available in %sPRO version. →%s', 'flexible-pickup' ), '<a target="_blank" href="' . $pro_url . '">', '</a>' ),
                ),
                'fp_map_selector'   => array(
                    'title'     => __( 'Show map selector', 'flexible-pickup' ),
                    'type'      => 'select',
                    'default'   => isset( $shipping_method['fp_map_selector'] ) ? $shipping_method['fp_map_selector'] : '',
                    'options'   => array(
                        '0' => __( 'No', 'flexible-pickup' ),
                        '1' => __( 'Yes', 'flexible-pickup' ),
                    )
                ),
                'fp_point_details'   => array(
                    'title'     => __( 'Show points details', 'flexible-pickup' ),
                    'type'      => 'select',
                    'default'   => isset( $shipping_method['fp_point_details'] ) ? $shipping_method['fp_point_details'] : '',
                    'options'   => array(
                        '0' => __( 'No', 'flexible-pickup' ),
                    ),
                    'description' => sprintf( __( 'More options available in %sPRO version. →%s', 'flexible-pickup' ), '<a target="_blank" href="' . $pro_url . '">', '</a>' ),
                ),
                'fp_cod'   => array(
	                'title'     => __( 'COD', 'flexible-pickup' ),
	                'type'      => 'select',
	                'default'   => isset( $shipping_method['fp_cod'] ) ? $shipping_method['fp_cod'] : '',
	                'options'   => array(
		                '0' => __( 'No', 'flexible-pickup' ),
	                ),
	                'description' => sprintf( __( 'More options available in %sPRO version. →%s', 'flexible-pickup' ), '<a target="_blank" href="' . $pro_url . '">', '</a>' ),
                ),
            );
            $settings = apply_filters( 'flexible_pickup_fs_settings', $settings );
            return array_merge( $flexible_shipping_settings, $settings );
		}
		
		public function flexible_shipping_method_script() {
			?>
				<script type="text/javascript">
					jQuery(document).ready(function() {
						function fpOptions() {
							if ( jQuery('#woocommerce_flexible_shipping_method_integration').val() == 'flexible_pickup' ) {
								jQuery('#woocommerce_flexible_shipping_fp_points_group').closest('tr').css('display','table-row');
                                jQuery('#woocommerce_flexible_shipping_fp_points_field').closest('tr').css('display','table-row');
                                jQuery('#woocommerce_flexible_shipping_fp_point_details').closest('tr').css('display','table-row');
                                if ( jQuery('#woocommerce_flexible_shipping_fp_points_field').val() == 'select'
                                    || jQuery('#woocommerce_flexible_shipping_fp_points_field').val() == 'select2'
                                ) {
                                    jQuery('#woocommerce_flexible_shipping_fp_map_selector').closest('tr').css('display','table-row');
                                }
                                else {
                                    jQuery('#woocommerce_flexible_shipping_fp_map_selector').closest('tr').css('display','none');
                                }
                                jQuery('#woocommerce_flexible_shipping_fp_cod').closest('tr').css('display','table-row');
							}
							else {
								jQuery('#woocommerce_flexible_shipping_fp_points_group').closest('tr').css('display','none');
                                jQuery('#woocommerce_flexible_shipping_fp_points_field').closest('tr').css('display','none');
                                jQuery('#woocommerce_flexible_shipping_fp_point_details').closest('tr').css('display','none');
                                jQuery('#woocommerce_flexible_shipping_fp_map_selector').closest('tr').css('display','none');
                                jQuery('#woocommerce_flexible_shipping_fp_cod').closest('tr').css('display','none');
							}
						}
						jQuery('#woocommerce_flexible_shipping_method_integration,#woocommerce_flexible_shipping_fp_points_field').change(function() {
							fpOptions();
						});
						fpOptions();
					});
				</script>
			<?php
		}
		
		public function flexible_shipping_process_admin_options( $shipping_method )	{
			$shipping_method['fp_points_group'] = sanitize_text_field( $_POST['woocommerce_flexible_shipping_fp_points_group'] );
			$shipping_method['fp_points_field'] = sanitize_text_field( $_POST['woocommerce_flexible_shipping_fp_points_field'] );
			$shipping_method['fp_map_selector'] = 0;
			if ( isset( $_POST['woocommerce_flexible_shipping_fp_map_selector'] ) ) {
				$shipping_method['fp_map_selector'] = sanitize_text_field( $_POST['woocommerce_flexible_shipping_fp_map_selector'] );
            }
			$shipping_method['fp_point_details'] = 0;
			if ( isset( $_POST['woocommerce_flexible_shipping_fp_point_details'] ) ) {
				$shipping_method['fp_point_details'] = sanitize_text_field( $_POST['woocommerce_flexible_shipping_fp_point_details'] );
			}
			$shipping_method['fp_cod'] = 0;
			if ( isset( $_POST['woocommerce_flexible_shipping_fp_cod'] ) ) {
				$shipping_method['fp_cod'] = sanitize_text_field( $_POST['woocommerce_flexible_shipping_fp_cod'] );
			}
			return $shipping_method;
		}
		
		public function flexible_shipping_method_integration_col( $col, $shipping_method ) {
			$shipping_methods = WC()->shipping->get_shipping_methods();
            if ( isset( $shipping_method['method_integration'] ) && 'flexible_pickup' === $shipping_method['method_integration'] ) {
                ob_start();
                $tip = __( 'None', 'flexible-pickup' );
                $tip = "";
                ?>
                <td width="1%" class="integration default">
                    <span class="tips" data-tip="<?php echo $tip; ?>">
                        <?php echo $shipping_methods['flexible_pickup']->title; ?>
                    </span>
                </td>
                <?php
                $col = ob_get_contents();
                ob_end_clean();
            }
			return $col;
		}
				
		public function flexible_shipping_method_rate_id( $rate_id, $shipping_method ) {
			if ( isset( $shipping_method['method_integration'] ) && 'flexible_pickup' === $shipping_method['method_integration'] ) {
				$rate_id = $rate_id . '_flexible_pickup_' . sanitize_title( $shipping_method['fp_points_group'] );
			}
			return $rate_id;
		}
		
		public function flexible_shipping_add_method( $add_method, $shipping_method, $package )	{
			if ( isset( $shipping_method['method_integration'] ) && 'flexible_pickup' === $shipping_method['method_integration']
			) {
				/*
				Check additional conditions ie. package contents and return false if this method is not avaliable			
				*/
				/*
				return false; 
				*/
			}
			return $add_method;
		}
	
	}

}

