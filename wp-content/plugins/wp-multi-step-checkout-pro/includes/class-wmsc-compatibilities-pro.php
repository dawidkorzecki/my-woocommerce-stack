<?php
/**
 * WMSC_Compatibilities_Pro. Compatibilities with other themes or plugins.
 *
 * @package WPMultiStepCheckout_Pro
 */

defined( 'ABSPATH' ) || exit;

/**
 * WMSC_Compatibilities_Pro class.
 */
class WMSC_Compatibilities_Pro {
	/**
	 * Initiate the class.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', 'WMSC_Compatibilities_Pro::wp_enqueue_scripts', 40 );
		add_action( 'wp_head', 'WMSC_Compatibilities_Pro::wp_head_js', 40 );
		add_action( 'after_setup_theme', 'WMSC_Compatibilities_Pro::after_setup_theme', 40 );
		add_action( 'wp', 'WMSC_Compatibilities_Pro::wp', 40 );
		add_action( 'wp', 'WMSC_Compatibilities_Pro::logout_user_checkout_page', 10 );
		add_filter( 'wmsc_custom_validation', 'WMSC_Compatibilities_Pro::brazilian_market', 10 );
		add_filter( 'wmsc_custom_validation', 'WMSC_Compatibilities_Pro::local_pickup_plus', 20, 2 );
		add_filter( 'woocommerce_locate_template', 'WMSC_Compatibilities_Pro::woocommerce_locate_template', 30, 3 );
		add_filter( 'woocommerce_before_checkout_form_step', 'WMSC_Compatibilities_Pro::woocommerce_before_checkout_form_step', 10 );
		add_action( 'woocommerce_checkout_fields', 'WMSC_Compatibilities_Pro::woocommerce_checkout_fields', 40 );
		add_filter( 'woocommerce_germanized_filter_template', 'WMSC_Compatibilities_Pro::woocommerce_germanized_filter_template', 30, 3 );
		add_filter( 'woocommerce_checkout_posted_data', 'WMSC_Compatibilities_Pro::woocommerce_checkout_posted_data', 10 );
		add_action( 'elementor/init', 'WMSC_Compatibilities_Pro::elementor_pro_widget', 30 );

		self::adjust_hooks();
	}


	/**
	 * CSS adjustments to themes and plugins.
	 */
	public static function wp_enqueue_scripts() {
		if ( ! is_checkout() ) {
			return;
		}

		$theme = strtolower( get_template() );

		$style = '';

		/*
		 * Avada theme.
		 */
		if ( strpos( $theme, 'avada' ) !== false ) {
			$style .= '.wpmc-nav-wrapper { float: left; margin-top: 10px; }';
			$style .= '.woocommerce-checkout a.continue-checkout{display: none;}';
			$style .= '.woocommerce-error,.woocommerce-info,.woocommerce-message{padding:1em 2em 1em 3.5em;margin:0 0 2em;position:relative;background-color:#f7f6f7;color:#515151;border-top:3px solid #a46497;list-style:none outside;width:auto;word-wrap:break-word}.woocommerce-error::after,.woocommerce-error::before,.woocommerce-info::after,.woocommerce-info::before,.woocommerce-message::after,.woocommerce-message::before{content:" ";display:table}.woocommerce-error::after,.woocommerce-info::after,.woocommerce-message::after{clear:both}.woocommerce-error .button,.woocommerce-info .button,.woocommerce-message .button{float:right}.woocommerce-error li,.woocommerce-info li,.woocommerce-message li{list-style:none outside!important;padding-left:0!important;margin-left:0!important}.rtl.woocommerce .price_label,.rtl.woocommerce .price_label span{direction:ltr;unicode-bidi:embed}.woocommerce-message{border-top-color:#8fae1b}.woocommerce-info{border-top-color:#1e85be}.woocommerce-info::before{color:#1e85be}.woocommerce-error{border-top-color:#b81c23}.woocommerce-checkout .shop_table td, .woocommerce-checkout .shop_table th {padding: 10px}.woocommerce .single_add_to_cart_button, .woocommerce button.button {margin-top: 10px}';
			$style .= '.woocommerce .woocommerce-form-coupon-toggle { display: none; }';
			$style .= '.woocommerce form.checkout #order_review, .woocommerce form.checkout #order_review_heading, .woocommerce form.checkout .col-2 {display:block!important;}';
			$style .= '.woocommerce .checkout_coupon { display: flex !important; }';
			$style .= '.woocommerce #wpmc-next { margin-left: 4px; }';
			$style .= '.wpmc-nav-wrapper { width: 100% !important; }';
		}

		/*
		 * The Retailer theme.
		 */
		if ( strpos( $theme, 'theretailer' ) !== false ) {
			$style .= '.woocommerce .wpmc-nav-buttons button.button { display: none !important; }';
			$style .= '.woocommerce .wpmc-nav-buttons button.button.current { display: inline-block !important; }';
			$style .= '.wpmc-step-login .gbtr_login_register_wrapper { border: none; }';
		}

		/*
		 * Divi theme.
		 */
		if ( strpos( $theme, 'divi' ) !== false ) {
			$style .= '#wpmc-back-to-cart:after, #wpmc-prev:after { display: none; } ';
			$style .= '#wpmc-back-to-cart:before, #wpmc-prev:before{ position: absolute; left: 1em; margin-left: 0em; opacity: 0; font-family: "ETmodules"; font-size: 32px; line-height: 1em; content: "\34"; -webkit-transition: all 0.2s; -moz-transition: all 0.2s; transition: all 0.2s; }';
			$style .= '#wpmc-back-to-cart:hover, #wpmc-prev:hover { padding-right: 0.7em; padding-left: 2em; left: 0.15em; }';
			$style .= '#wpmc-back-to-cart:hover:before, #wpmc-prev:hover:before { left: 0.2em; opacity: 1;}';
			$style .= '.woocommerce .woocommerce-notices-wrapper { clear: left; }';
		}

		/*
		 * Enfold theme.
		 */
		if ( strpos( $theme, 'enfold' ) !== false ) {
			$style .= '.wpmc-footer-right { width: auto; }';
		}

		/*
		 * Flatsome theme.
		 */
		if ( strpos( $theme, 'flatsome' ) !== false ) {
			$style .= '.processing::before, .loading-spin { content: none !important; }';
			$style .= '.wpmc-footer-right button.button { margin-right: 0; }';
		}

		/*
		 * Bridge theme.
		 */
		if ( strpos( $theme, 'bridge' ) !== false ) {
			$style .= '.woocommerce input[type="text"]:not(.qode_search_field), .woocommerce input[type="password"], .woocommerce input[type="email"], .woocommerce textarea, .woocommerce-page input[type="tel"], .woocommerce-page input[type="text"]:not(.qode_search_field), .woocommerce-page input[type="password"], .woocommerce-page input[type="email"], .woocommerce-page textarea, .woocommerce-page select { width: 100%; }';
			$style .= '.woocommerce-checkout table.shop_table { width: 100% !important; }';
		}

		/*
		 * Zass theme.
		 */
		if ( strpos( $theme, 'zass' ) !== false ) {
			$style .= '.woocommerce form.checkout.woocommerce-checkout.processing:after {content: "";}.woocommerce .wpmc-steps-wrapper form.checkout.woocommerce-checkout.processing:before {display: none;}';
			$style .= '.wpmc-step-item h3#order_review_heading{ display: block;}';
			$style .= '.wpmc-steps-wrapper form.checkout.woocommerce-checkout #order_review{width:100%;}';
		}

		/*
		 * Astra theme.
		 */
		if ( strpos( $theme, 'astra' ) !== false ) {
			$style .= '.woocommerce.woocommerce-checkout form #order_review, .woocommerce.woocommerce-checkout form #order_review_heading, .woocommerce-page.woocommerce-checkout form #order_review, .woocommerce-page.woocommerce-checkout form #order_review_heading {width: auto; float:none}';
			$style .= '.woocommerce.woocommerce-checkout form #order_review_heading, .woocommerce-page.woocommerce-checkout form #order_review_heading { border:none; margin:0; padding:0; font-size:1.6rem; }';
			$style .= '.woocommerce.woocommerce-checkout form #order_review, .woocommerce-page.woocommerce-checkout form #order_review { border:none; padding:0; }';
		}

		/*
		 * OceanWP theme.
		 */
		if ( strpos( $theme, 'oceanwp' ) !== false ) {
			$style .= '.woocommerce .woocommerce-checkout .wpmc-step-item h3#order_review_heading {font-size: 18px;position: relative;float: left; padding-bottom: 0px;border: none;text-transform: none;letter-spacing: normal;}';
			$style .= '.woocommerce-checkout h3#order_review_heading, .woocommerce-checkout #order_review {float: none !important;width: 100% !important;}';
			$style .= '#owp-checkout-timeline {display: none}';
		}

		/*
		 * Hestia Pro theme.
		 */
		if ( strpos( $theme, 'hestia-pro' ) !== false ) {
			$style .= '.woocommerce .wpmc-step-item .col2-set, .woocommerce-page .wpmc-step-item .col2-set, .woocommerce-checkout .wpmc-step-item .col2-set{width: 100%;}';
		}

		/*
		 * Puca theme.
		 */
		if ( strpos( $theme, 'puca' ) !== false ) {
			$style .= '.woocommerce-checkout .wpmc-steps-wrapper form.checkout { padding: 0; border: none; }';
		}

		/*
		 * fuelthemes on codecanyon.
		 */
		$fullthemes = array( 'peakshops', 'revolution', 'theissue', 'werkstatt', 'twofold', 'goodlife', 'voux', 'notio', 'north' );
		foreach ( $fullthemes as $_theme ) {
			if ( strpos( $theme, $_theme ) === false ) {
				continue;
			}
			$style .= '.woocommerce-checkout-payment .place-order {float: none !important;} .woocommerce-billing-fields, .woocommerce-shipping-fields, .woocommerce-additional-fields { padding-right: 0 !important; } .woocommerce .woocommerce-form-login .lost_password { top: 0 !important; } h3#ship-to-different-address label { font-size: 22px; font-weight: 500; }';
		}

		/*
		 * Neve theme.
		 */
		if ( strpos( $theme, 'neve' ) !== false ) {
			$style .= '.woocommerce-checkout form.checkout { display: block !important; } .woocommerce-checkout .col2-set .col-1, .woocommerce-checkout .col2-set .col-2 { float: left !important; } .woocommerce-checkout .nv-content-wrap .wp-block-columns { display: block !important; }';
		}

		/*
		 * Set the Login and Register sections in two columns
		 */
		$style .= '@media screen and (min-width: 679px) { .wp-multi-step-checkout-step .col2-set#customer_login .col-1 { width: 48%; margin-right: 4%; margin-left: 0%; flex-basis: auto; }';
		$style .= '.wp-multi-step-checkout-step .col2-set#customer_login .col-2 { width: 48%; margin-right: 0%; margin-left: 0%; flex-basis: auto; } }';

		/*
		 * WPBakery (former Visual Composer) plugin.
		 */
		if ( defined( 'WPB_VC_VERSION' ) ) {
			$style .= '.woocommerce-checkout .wpb_column .vc_column-inner::after{clear:none !important; content: none !important;}';
			$style .= '.woocommerce-checkout .wpb_column .vc_column-inner::before{content: none !important;}';
		}

		/*
		 * Germanized for WooCommerce plugin.
		 */
		if ( class_exists( 'WooCommerce_Germanized' ) ) {
			$style .= '#order_review_heading {display: block !important;} h3#order_payment_heading { display: none !important; } .wc-gzd-product-name-left { display: none; }';
		}

		/*
		 * WooCommerce Amazon Pay plugin.
		 */
		if ( defined( 'WC_AMAZON_PAY_VERSION' ) ) {
			$style .= '#amazon_customer_details.wc-amazon-payments-advanced-populated + .woocommerce-account-fields { display: none; }';
		}

		/*
		 * The "Enhanced Select (Select2)" input fields added with the Checkout Field Editor for WooCommerce plugin.
		 */
		if ( defined( 'THWCFE_VERSION' ) ) {
			$style .= '.woocommerce-checkout .select2-container { display: block !important; }';
		}

		/*
		 * The Elementor Pro widget.
		 */
		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			$style .= '.woocommerce-form-coupon-toggle { display: none !important; } form.woocommerce-form-coupon[style] { display: block !important; }';
		}

		wp_add_inline_style( 'wpmc', $style );
	}


	/**
	 * Add JavaScript to the header.
	 */
	public static function wp_head_js() {
		if ( ! is_checkout() ) {
			return;
		}

		$theme = strtolower( get_template() );

		$js = '';

		/**
		 * Email Verification / SMS verification / Mobile Verification plugin by miniOrange.
		 */
		if ( defined( 'MOV_PLUGIN_NAME' ) ) {
			ob_start();
			?>
			jQuery(document).ready(function( $ ){
				setTimeout(function() {
					$(".woocommerce-checkout-review-order").click();
				}, 1500);
			});
			<?php
			$js .= ob_get_contents();
			ob_end_clean();
		}

		/**
		 * Invert the Breadcrumb steps for the right-to-left languages.
		 */
		if ( is_rtl() ) {
			ob_start();
			?>
			jQuery(document).ready(function( $ ){
				let steps = $('body.rtl .wpmc-tabs-wrapper-breadcrumb .wpmc-tabs-list');
				steps.children().each(function(i,li) {
					steps.prepend(li);
				});
			});
			<?php

			$js .= ob_get_contents();
			ob_end_clean();
		}

		/**
		 * Avada
		 */
		if ( strpos( $theme, 'avada' ) !== false ) {
			ob_start();
			?>
			jQuery(document).ready(function( $ ){
				$(".wpmc-nav-wrapper").addClass('woocommerce');
			});
			<?php
			$js .= ob_get_contents();
			ob_end_clean();
		}

		/**
		 * Flexible Checkout Fields Pro plugin.
		 */
		if ( defined( 'FLEXIBLE_CHECKOUT_FIELDS_PRO_VERSION' ) ) {
			ob_start();
			?>
			jQuery(document).ready(function( $ ){

				if ( typeof fcf_conditions != 'undefined' ) {
					$.each( fcf_conditions, function( index, value ) {
						$( '#' + index + '_field' ).on('change', function() {
							$(this).find('input, select').attr('disabled', false);
						} );
					} );
				}

				if ( typeof fcf_shipping_conditions != 'undefined' ) {
					$.each( fcf_shipping_conditions, function( index, value ) {
						$( '#' + index + '_field' ).on('change', function() {
							$(this).find('input, select').attr('disabled', false);
						} );
					} );
				}

			});
			<?php
			$js .= ob_get_contents();
			ob_end_clean();
		}

		/**
		 * WooCommerce Local Pickup Plus by SkyVerge.
		 */
		if ( class_exists( 'WC_Local_Pickup_Plus_Loader' ) ) {
			ob_start();
			?>
			jQuery(document).ready(function( $ ){
				$( '.woocommerce-checkout' ).on( 'wpmc_after_switching_tab', function() {
					if ( $('ul.wpmc-tabs-list').data('current-title') === 'review') {
						$(document.body).trigger("update_checkout");
					}
				});
			});
			<?php
			$js .= ob_get_contents();
			ob_end_clean();

		}

		if ( ! empty( $js ) ) {
			$type = current_theme_supports( 'html5', 'script' ) ? '' : ' type="text/javascript"';
			echo '<script' . $type . '>' . $js . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Hook adjustments to themes and plugins.
	 */
	public static function after_setup_theme() {

		$theme = strtolower( get_template() );

		/*
		 * Avada theme.
		 */
		if ( strpos( $theme, 'avada' ) !== false ) {
			if ( function_exists( 'avada_woocommerce_before_checkout_form' ) ) {
				remove_action( 'woocommerce_before_checkout_form', 'avada_woocommerce_before_checkout_form' );
			}

			if ( function_exists( 'avada_woocommerce_checkout_after_customer_details' ) ) {
				remove_action( 'woocommerce_checkout_after_customer_details', 'avada_woocommerce_checkout_after_customer_details' );
			}

			if ( function_exists( 'avada_woocommerce_checkout_before_customer_details' ) ) {
				remove_action( 'woocommerce_checkout_before_customer_details', 'avada_woocommerce_checkout_before_customer_details' );
			}
			global $avada_woocommerce;

			if ( ! empty( $avada_woocommerce ) ) {
				remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'avada_top_user_container' ), 1 );
				remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'checkout_coupon_form' ), 10 );
				remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'before_checkout_form' ) );
				remove_action( 'woocommerce_after_checkout_form', array( $avada_woocommerce, 'after_checkout_form' ) );
				remove_action( 'woocommerce_checkout_before_customer_details', array( $avada_woocommerce, 'checkout_before_customer_details' ) );
				remove_action( 'woocommerce_checkout_after_customer_details', array( $avada_woocommerce, 'checkout_after_customer_details' ) );
			}

			add_filter( 'wmsc_review_thumbnails_enable', '__return_false' );
		}

		/*
		 * Hestia Pro theme.
		 */
		if ( strpos( $theme, 'hestia-pro' ) !== false ) {
			remove_action( 'woocommerce_before_checkout_form', 'hestia_coupon_after_order_table_js' );
		}

		/*
		 * Astra theme.
		 */
		if ( strpos( $theme, 'astra' ) !== false ) {
			if ( ! defined( 'WC_AMAZON_PAY_VERSION' ) ) {
				add_filter( 'astra_woo_shop_product_structure_override', '__return_true' );
				add_action( 'woocommerce_checkout_shipping', array( WC()->checkout(), 'checkout_form_shipping' ), 20 );

				add_action( 'woocommerce_before_shop_loop_item', 'astra_woo_shop_thumbnail_wrap_start', 6 );
				add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_show_product_loop_sale_flash', 9 );
				add_action( 'woocommerce_after_shop_loop_item', 'astra_woo_shop_thumbnail_wrap_end', 8 );
				add_action( 'woocommerce_shop_loop_item_title', 'astra_woo_shop_out_of_stock', 8 );
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
				add_action( 'woocommerce_after_shop_loop_item', 'astra_woo_woocommerce_shop_product_content' );
			}
		}

		/**
		 * Porto theme.
		 */
		if ( strpos( $theme, 'porto' ) !== false ) {
			add_filter( 'porto_filter_checkout_version', 'WMSC_Compatibilities_Pro::porto_checkout_version' );
		}

		/**
		 * Electro theme.
		 */
		if ( strpos( $theme, 'electro' ) !== false ) {
			remove_action( 'woocommerce_checkout_before_order_review', 'electro_wrap_order_review', 0 );
			remove_action( 'woocommerce_checkout_after_order_review', 'electro_wrap_order_review_close', 0 );
		}

		/*
		 * tet30 theme.
		 */
		if ( strpos( $theme, 'tet30' ) !== false ) {
			remove_action( 'woocommerce_before_checkout_form', 'c4d_kit_open_main_width' );
			remove_action( 'woocommerce_after_checkout_form', 'c4d_kit_close_main_width' );
		}

		/*
		 * Germanized for WooCommerce plugin.
		 */
		if ( class_exists( 'WooCommerce_Germanized' ) ) {
			remove_action( 'woocommerce_review_order_after_payment', 'woocommerce_gzd_template_render_checkout_checkboxes', 10 );
		}

		/*
		 * Shopper theme.
		 */
		add_filter( 'shopper_sticky_order_review', '__return_false' );

		/*
		 * Flatsome theme.
		 */
		add_filter( 'wmsc_js_variables', 'WMSC_Compatibilities_Pro::flatsome_scroll_top' );

		/**
		 * Urna theme.
		 */
		if ( strpos( $theme, 'urna' ) !== false ) {
			remove_action( 'wpmc-woocommerce_checkout_payment', 'woocommerce_checkout_payment', 10 );
		}

		/**
		 * Neve theme.
		 */
		if ( strpos( $theme, 'neve' ) !== false ) {
			add_filter( 'woocommerce_queued_js', 'WMSC_Compatibilities_Pro::neve_remove_js' );
		}

		/**
		 * Woodmart theme.
		 */
		if ( strpos( $theme, 'woodmart' ) !== false ) {
			add_filter( 'wmsc_buttons_class', 'WMSC_Compatibilities_Pro::woodmart_buttons' );
		}

		/**
		 * Fuelthemes on codecanyon.
		 */
		$fuelthemes = array( 'peakshops', 'revolution', 'theissue', 'werkstatt', 'twofold', 'goodlife', 'voux', 'notio', 'north' );
		foreach ( $fuelthemes as $_theme ) {
			if ( strpos( $theme, $_theme ) === false ) {
				continue;
			}
			add_filter( 'wmsc_buttons_class', 'WMSC_Compatibilities_Pro::fuelthemes_buttons' );
		}

		/*
		 * Sg Optimizer plugin.
		 */
		add_filter( 'sgo_lazy_load_exclude_classes', 'WMSC_Compatibilities_Pro::sgo_lazy_load_exclude_classes' );


		/*
		 * WPC Fly Cart for WooCommerce plugin.
		 */
		if ( defined( 'WOOFC_VERSION' ) ) {
			add_filter( 'wmsc_review_thumbnails_template', '__return_true' ); 
		}
	}


	/**
	 * Hook adjustments for themes and plugins.
	 */
	public static function wp() {

		$theme = strtolower( get_template() );

		/*
		 * Avada theme.
		 */
		if ( strpos( $theme, 'avada' ) !== false ) {
			$filters_to_remove = array(
				'woocommerce_checkout_after_customer_details',
				'woocommerce_checkout_before_customer_details',
				'woocommerce_before_checkout_form',
			);
			self::remove_filters( $filters_to_remove, 'Avada_WooCommerce' );

			global $avada_woocommerce;
			add_action( 'wpmc_before_tabs', array( $avada_woocommerce, 'avada_top_user_container' ), 1 );
		}

		/*
		 * fuelthemes on codecanyon.
		 */
		foreach ( array( 'peakshops', 'revolution', 'theissue', 'werkstatt', 'twofold', 'goodlife', 'voux', 'notio', 'north' ) as $_theme ) {
			if ( strpos( $theme, $_theme ) === false ) {
				continue;
			}
			$filters_to_remove = array(
				'woocommerce_checkout_before_customer_details',
				'woocommerce_checkout_after_customer_details',
				'woocommerce_checkout_after_order_review',
			);
			self::remove_filters( $filters_to_remove, 'Closure' );
		}

		/*
		 * The Neve theme.
		 */
		if ( strpos( $theme, 'neve' ) !== false ) {
			$filters_to_remove = array(
				'woocommerce_checkout_before_customer_details',
				'woocommerce_checkout_after_customer_details',
			);
			self::remove_filters( $filters_to_remove, 'Closure' );
		}
	}


	/**
	 * Remove filters calls, which were defined as closures or binded to a class.
	 *
	 * @param array  $filters The hook names that are to be removed.
	 * @param string $class The class name of the filter callback function.
	 */
	public static function remove_filters( $filters, $class = 'Closure' ) {
		global $wp_filter;

		if ( ! is_array( $wp_filter ) || count( $wp_filter ) === 0 ) {
			return;
		}

		foreach ( $filters as $_filter ) {
			if ( ! isset( $wp_filter[ $_filter ] ) ) {
				continue;
			}
			foreach ( $wp_filter[ $_filter ]->callbacks as $_p_key => $_priority ) {
				foreach ( $wp_filter[ $_filter ]->callbacks[ $_p_key ] as $_key => $_function ) {
					if ( ! isset( $_function['function'] ) ) {
						continue;
					}
					if ( is_array( $_function['function'] ) ) {
						if ( ! $_function['function'][0] instanceof $class ) {
							continue;
						}
					} else {
						if ( ! $_function['function'] instanceof $class ) {
							continue;
						}
					}
					unset( $wp_filter[ $_filter ]->callbacks[ $_p_key ][ $_key ] );
				}
			}
		}
	}


	/**
	 * The Login section is misplaced in the Neve theme.
	 *
	 * @param string $js JavaScript string.
	 * @return string.
	 */
	public static function neve_remove_js( $js ) {
		$js = str_replace( '$( $( ".woocommerce-checkout div.woocommerce-info, .checkout_coupon, .woocommerce-form-login" ).detach() ).appendTo( "#neve-checkout-coupon" );', '', $js );
		return $js;
	}


	/**
	 * Woodmart buttons.
	 *
	 * @param string $btn The buttons' class.
	 * @return string.
	 */
	public static function woodmart_buttons( $btn ) {
		return $btn . ' btn-color-primary';
	}


	/**
	 * Fuelthemes on codecanyon buttons.
	 *
	 * @param string $btn The buttons' class.
	 * @return string.
	 */
	public static function fuelthemes_buttons( $btn ) {
		return $btn . ' button';
	}


	/**
	 * Use the default WooCommerce template, if necessary.
	 *
	 * @param string $template      Template file name.
	 * @param string $template_name Template name.
	 * @param string $template_path Template path.
	 *
	 * @return string
	 */
	public static function woocommerce_locate_template( $template, $template_name, $template_path ) {

		if ( ! is_checkout() ) {
			return $template;
		}

		$theme        = strtolower( get_template() );
		$wc_templates = plugin_dir_path( WC_PLUGIN_FILE ) . 'templates/';

		$themes = array(
			'puca'   => array(
				'myaccount/form-login.php',
			),
			'motors' => array(
				'checkout/review-order.php',
				'checkout/payment.php',
			),
		);

		$this_theme_files = apply_filters( 'wmsc_woocommerce_default_templates', array() );
		/**
		 * Example of using the "wmsc_woocommerce_default_templates" filter:
		 *
		 * Add_filter( 'wmsc_woocommerce_default_templates', function( $files ) {
		 *   return array('checkout/review-order.php', 'checkout/payment.php', 'myaccount/form-login.php');
		 * } );
		 */

		if ( count( $this_theme_files ) > 0 ) {
			$themes[ $theme ] = $this_theme_files;
		}

		foreach ( $themes as $_theme => $_files ) {
			if ( strpos( $theme, $_theme ) !== false && in_array( $template_name, $_files, true ) ) {
				return $wc_templates . $template_name;
			}
		}
		return $template;
	}


	/**
	 * Exclude the review thumbnail from the SG Optimizer lazy load functionality. Otherwise there are two thumbnails shown instead of one.
	 *
	 * @param string $classes Excluded classes.
	 *
	 * @return string
	 */
	public static function sgo_lazy_load_exclude_classes( $classes ) {
		$classes[] = 'wmsc-thumbnail';
		return $classes;
	}

	/**
	 * Add the content functions to the Payment and Order Review steps.
	 */
	public static function adjust_hooks() {

		if ( class_exists( 'WooCommerce_Germanized' ) ) {
			/*
			 * Germanized for WooCommerce plugin.
			 */
			add_action( 'wmsc_step_content_review', 'wmsc_step_content_review_germanized', 10 );
			add_action( 'wmsc_step_content_payment', 'wmsc_step_content_payment_germanized', 10 );
			add_action( 'wpmc-woocommerce_order_review', 'woocommerce_gzd_template_render_checkout_checkboxes', 10 );
			add_filter( 'wc_gzd_checkout_params', 'WMSC_Compatibilities_Pro::wc_gzd_checkout_params' );
			add_filter( 'wp_loaded', 'WMSC_Compatibilities_Pro::woocommerce_review_order_after_payment' );
		} elseif ( class_exists( 'Woocommerce_German_Market' ) ) {
			/*
			 * WooCommerce German Market plugin.
			 */
			add_action( 'wmsc_step_content_review', 'wmsc_step_content_review_german_market', 10 );
			add_action( 'wmsc_step_content_payment', 'wmsc_step_content_payment', 10 );

		} else {
			/*
			 * default.
			*/
			add_action( 'wmsc_step_content_review', 'wmsc_step_content_review', 10 );
			add_action( 'wmsc_step_content_payment', 'wmsc_step_content_payment', 10 );
		}
	}

	/**
	 * Override parameters for the Germanized for WooCommerce plugin.
	 *
	 * @param array $params The parameters to be overriden.
	 */
	public static function wc_gzd_checkout_params( $params ) {
		$params['adjust_heading'] = false;
		return $params;
	}

	/**
	 * Remove Terms and Conditions checkboxes from the Payment step for the Germanized for WooCommerce plugin.
	 */
	public static function woocommerce_review_order_after_payment() {
		remove_action( 'woocommerce_review_order_after_payment', 'woocommerce_gzd_template_render_checkout_checkboxes', 10 );
	}

	/**
	 * Scroll to top on Flatsome theme with sticky header.
	 *
	 * @param array $vars Options array.
	 */
	public static function flatsome_scroll_top( $vars ) {
		$vars['scroll_top'] = 120;
		return $vars;
	}


	/**
	 * Choose "Version 1" option for the checkout version in Porto theme.
	 *
	 * @param string $version Version.
	 */
	public static function porto_checkout_version( $version ) {
		return 'v1';
	}


	/**
	 * Filter "woocommerce_before_checkout_form_step"
	 *
	 * @param string $step The step.
	 *
	 * @return string.
	 */
	public static function woocommerce_before_checkout_form_step( $step ) {
		$step = defined( 'WC_AMAZON_PAY_VERSION' ) ? 'billing' : $step;
		return $step;
	}


	/**
	 * "Double Opt-In Customer Registration" option in the German Market plugin.
	 *
	 * @return void.
	 */
	public static function logout_user_checkout_page() {
		if ( ! class_exists( 'Woocommerce_German_Market' ) ) {
			return;
		}

		if ( is_user_logged_in() && is_checkout() ) {

			$user              = wp_get_current_user();
			$activation_status = get_user_meta( $user->ID, '_wgm_double_opt_in_activation_status', true );

			if ( 'waiting' === $activation_status ) {

				if ( apply_filters( 'german_market_double_opt_in_remove_all_actions', true ) ) {
					remove_all_actions( 'wp_logout' );
				}

				wp_logout();
				do_action( 'gm_double_opt_in_before_logout_user_checkout_page_redirect', $user->ID );

				$checkout_url = untrailingslashit( get_permalink( get_option( 'woocommerce_checkout_page_id' ) ) );
				$url_parse    = wp_parse_url( $checkout_url );
				if ( ! isset( $url_parse['query'] ) ) {
					$url_parse['query'] = '';
				}
				$redirect_link_args = empty( $url_parse['query'] ) ? '?gm_double_opt_in_message=true' : '&gm_double_opt_in_message=true';

				wp_safe_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) . $redirect_link_args );
				exit();

			}
		}
	}


	/**
	 * Validate the checkout fields added by the Brazilian Market for WooCommerce plugin.
	 *
	 * @param array $errors The errors array.
	 * @return array The modified errors array.
	 */
	public static function brazilian_market( $errors ) {
		if ( ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
			return $errors;
		}

		if ( apply_filters( 'wcbcf_disable_checkout_validation', false ) ) {
			return $errors;
		}

		// Get plugin settings.
		$settings           = get_option( 'wcbcf_settings' );
		$person_type        = intval( $settings['person_type'] );
		$only_brazil        = isset( $settings['only_brazil'] ) ? true : false;
		$billing_persontype = isset( $_POST['billing_persontype'] ) ? intval( wp_unslash( $_POST['billing_persontype'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification

		if ( $only_brazil && isset( $_POST['billing_country'] ) && 'BR' !== wp_unslash( $_POST['billing_country'] ) || 0 === $person_type ) { // phpcs:ignore
			return $errors;
		}

		if ( 0 === $billing_persontype && 1 === $person_type ) {
			$errors['billing_persontype'] = sprintf( '<strong>%s</strong> %s.', __( 'Person type', 'woocommerce-extra-checkout-fields-for-brazil' ), __( 'is a required field', 'woocommerce-extra-checkout-fields-for-brazil' ) );
		} else {

			// Check CPF.
			if ( ( 1 === $person_type && 1 === $billing_persontype ) || 2 === $person_type ) {
				if ( empty( $_POST['billing_cpf'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$errors['billing_cpf'] = sprintf( '<strong>%s</strong> %s.', __( 'CPF', 'woocommerce-extra-checkout-fields-for-brazil' ), __( 'is a required field', 'woocommerce-extra-checkout-fields-for-brazil' ) );
				}

				if ( isset( $settings['validate_cpf'] ) && ! empty( $_POST['billing_cpf'] ) && ! Extra_Checkout_Fields_For_Brazil_Formatting::is_cpf( $_POST['billing_cpf'] ) ) { // phpcs:ignore
					$errors['billing_cpf'] = sprintf( '<strong>%s</strong> %s.', __( 'CPF', 'woocommerce-extra-checkout-fields-for-brazil' ), __( 'is not valid', 'woocommerce-extra-checkout-fields-for-brazil' ) );
				}

				if ( isset( $settings['rg'] ) && empty( $_POST['billing_rg'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$errors['billing_rg'] = sprintf( '<strong>%s</strong> %s.', __( 'RG', 'woocommerce-extra-checkout-fields-for-brazil' ), __( 'is a required field', 'woocommerce-extra-checkout-fields-for-brazil' ) );
				}
			}

			// Check Company and CPNJ.
			if ( ( 1 === $person_type && 2 === $billing_persontype ) || 3 === $person_type ) {
				if ( empty( $_POST['billing_company'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$errors['billing_company'] = sprintf( '<strong>%s</strong> %s.', __( 'Company', 'woocommerce-extra-checkout-fields-for-brazil' ), __( 'is a required field', 'woocommerce-extra-checkout-fields-for-brazil' ) );
				}

				if ( empty( $_POST['billing_cnpj'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$errors['billing_cnpj'] = sprintf( '<strong>%s</strong> %s.', __( 'CNPJ', 'woocommerce-extra-checkout-fields-for-brazil' ), __( 'is a required field', 'woocommerce-extra-checkout-fields-for-brazil' ) );
				}

				if ( isset( $settings['validate_cnpj'] ) && ! empty( $_POST['billing_cnpj'] ) && ! Extra_Checkout_Fields_For_Brazil_Formatting::is_cnpj( wp_unslash( $_POST['billing_cnpj'] ) ) ) { // phpcs:ignore
					$errors['billing_cnpj'] = sprintf( '<strong>%s</strong> %s.', __( 'CNPJ', 'woocommerce-extra-checkout-fields-for-brazil' ), __( 'is not valid', 'woocommerce-extra-checkout-fields-for-brazil' ) );
				}

				if ( isset( $settings['ie'] ) && empty( $_POST['billing_ie'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
					$errors['billing_ie'] = sprintf( '<strong>%s</strong> %s.', __( 'State Registration', 'woocommerce-extra-checkout-fields-for-brazil' ), __( 'is a required field', 'woocommerce-extra-checkout-fields-for-brazil' ) );
				}
			}
		}

		return $errors;
	}


	/**
	 * Validate the checkout fields added by the Local Pickup Plus plugin by SkyVerge.
	 *
	 * @param array $errors The errors array.
	 * @param array $posted_data The posted data array.
	 * @return array The modified errors array.
	 */
	public static function local_pickup_plus( $errors, $posted_data ) {
		if ( ! class_exists( 'WC_Local_Pickup_Plus_Loader' ) ) {
			return $errors;
		}

		$local_pickup_method = wc_local_pickup_plus_shipping_method();
		$shipping_methods    = isset( $posted_data['shipping_method'] ) ? (array) $posted_data['shipping_method'] : array();
		$exception_message   = '';

		require_once WP_PLUGIN_DIR . '/woocommerce-shipping-local-pickup-plus/src/frontend/Checkout.php';

		$local_pickup_errors = SkyVerge\WooCommerce\Local_Pickup_Plus\Checkout::validate_checkout( $posted_data );

		if ( is_array( $local_pickup_errors ) ) {
			$errors = array_merge( $errors, $local_pickup_errors );
		}

		return $errors;
	}


	/**
	 * Filter the checkout fields.
	 *
	 * @param array $fields Array with the checkout fields.
	 *
	 * @return Array with the checkout fields.
	 */
	public static function woocommerce_checkout_fields( $fields ) {
		if ( defined( 'WOOCCM_PLUGIN_VERSION' ) ) {
			$shipping_options = get_option( 'wooccm_shipping', false );
			if ( false !== $shipping_options ) {
				foreach ( $shipping_options as $wooccm_field ) {
					if ( false === $wooccm_field['required'] && isset( $fields['shipping'][ $wooccm_field['key'] ] ) ) {
						$fields['shipping'][ $wooccm_field['key'] ]['required'] = false;
					}
				}
			}
		}

		return $fields;
	}


	/**
	 * The Germanized for WooCommerce plugin loads the WooCommerce form-checkout.php template,
	 * if the Fallback Mode option is enabled on the "WP Admin -> WooCommerce -> Settings -> Germanized -> Button Solution" page.
	 *
	 * This function will load the form-checkout.php template from the multi-step checkout plugin instead of from the WooCommerce plugin.
	 *
	 * @param string $template      Template file name.
	 * @param string $template_name Template name.
	 * @param string $template_path Template path.
	 *
	 * @return string
	 */
	public static function woocommerce_germanized_filter_template( $template, $template_name, $template_path ) {

		if ( ! class_exists( 'WooCommerce_Germanized' ) || get_option( 'woocommerce_gzd_display_checkout_fallback' ) !== 'yes' ) {
			return $template;
		}

		if ( strstr( $template_name, 'form-checkout.php' ) ) {
			$template = plugin_dir_path( WMSC_PLUGIN_FILE ) . 'includes/form-checkout.php';
		}

		return $template;
	}


	/**
	 * Filter the checkout posted data.
	 *
	 * @param array $data Array with the posted data.
	 *
	 * @return Array with the posted data.
	 */
	public static function woocommerce_checkout_posted_data( $data ) {

		/**
		 * The "Order Delivery Date Pro for WooCommerce" plugin by Tyche Softwares doesn't register the "orddd_locations" post data.
		 */
		if ( class_exists( 'order_delivery_date' ) ) {
			$data['orddd_locations'] = isset( $_POST['orddd_locations'] ) ? wc_clean( wp_unslash( $_POST['orddd_locations'] ) ) : ''; // phpcs:ignore
		}
		return $data;
	}


	/**
	 * Override the Elementor Pro Checkout widget in order to remove the additional <div>s from the checkout form.
	 */
	public static function elementor_pro_widget() {
		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			return;
		}
		require_once plugin_dir_path( WMSC_PLUGIN_PRO_FILE ) . '/includes/elementor-widget-skin.php';
		add_action(
			'elementor/widget/woocommerce-checkout-page/skins_init',
			function( $widget ) {
				$widget->add_skin( new WMSC_Multistep_Checkout_Skin( $widget ) );
			}
		);
	}
}

WMSC_Compatibilities_Pro::init();
