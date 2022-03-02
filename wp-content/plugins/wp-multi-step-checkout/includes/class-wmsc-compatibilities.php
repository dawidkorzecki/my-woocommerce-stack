<?php
/**
 * WMSC_Compatibilities. Compatibilities with other themes or plugins.
 *
 * @package WPMultiStepCheckout
 */

defined( 'ABSPATH' ) || exit;


/**
 * WMSC_Compatibilities class.
 */
class WMSC_Compatibilities {
	/**
	 * Initiate the class.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', 'WMSC_Compatibilities::wp_enqueue_scripts', 40 );
		add_action( 'wp_head', 'WMSC_Compatibilities::wp_head_js', 40 );
		add_action( 'after_setup_theme', 'WMSC_Compatibilities::after_setup_theme', 40 );
		add_action( 'wp', 'WMSC_Compatibilities::wp', 40 );
		add_filter( 'woocommerce_locate_template', 'WMSC_Compatibilities::woocommerce_locate_template', 30, 3 );
		add_filter( 'woocommerce_germanized_filter_template', 'WMSC_Compatibilities::woocommerce_germanized_filter_template', 30, 3 );
		add_action( 'elementor/init', 'WMSC_Compatibilities::elementor_pro_widget', 30 );

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
		}

		/*
		 * Divi theme.
		 */
		if ( strpos( $theme, 'divi' ) !== false ) {
			$style .= '#wpmc-back-to-cart:after, #wpmc-prev:after { display: none; }';
			$style .= '#wpmc-back-to-cart:before, #wpmc-prev:before{ position: absolute; left: 1em; margin-left: 0em; opacity: 0; font-family: "ETmodules"; font-size: 32px; line-height: 1em; content: "\34"; -webkit-transition: all 0.2s; -moz-transition: all 0.2s; transition: all 0.2s; }';
			$style .= '#wpmc-back-to-cart:hover, #wpmc-prev:hover { padding-right: 0.7em; padding-left: 2em; left: 0.15em; }';
			$style .= '#wpmc-back-to-cart:hover:before, #wpmc-prev:hover:before { left: 0.2em; opacity: 1;}';
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
			$style .= 'form.checkout.woocommerce-checkout.processing:after {content: "";}.woocommerce form.checkout.woocommerce-checkout.processing:before {display: none;}';
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
		 * fuelthemes on codecanyon.
		 */
		foreach ( array( 'peakshops', 'revolution', 'theissue', 'werkstatt', 'twofold', 'goodlife', 'voux', 'notio', 'north' ) as $_theme ) {
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
		 * Flatsome theme.
		 */
		add_filter( 'wmsc_js_variables', 'WMSC_Compatibilities::flatsome_scroll_top' );

		/*
		 * Astra theme.
		 */
		if ( strpos( $theme, 'astra' ) !== false ) {
			$style .= '.woocommerce.woocommerce-checkout form #order_review, .woocommerce.woocommerce-checkout form #order_review_heading, .woocommerce-page.woocommerce-checkout form #order_review, .woocommerce-page.woocommerce-checkout form #order_review_heading {width: auto; float:none}';
			$style .= '.woocommerce.woocommerce-checkout form #order_review_heading, .woocommerce-page.woocommerce-checkout form #order_review_heading { border:none; margin:0; padding:0; font-size:1.6rem; }';
			$style .= '.woocommerce.woocommerce-checkout form #order_review, .woocommerce-page.woocommerce-checkout form #order_review { border:none; padding:0; }';
		}

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
			$style .= '#order_review_heading {display: block !important;} h3#order_payment_heading { display: none !important; }';
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
			add_filter( 'astra_woo_shop_product_structure_override', '__return_true' );
			add_action( 'woocommerce_checkout_shipping', array( WC()->checkout(), 'checkout_form_shipping' ), 20 );

			add_action( 'woocommerce_before_shop_loop_item', 'astra_woo_shop_thumbnail_wrap_start', 6 );
			add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_show_product_loop_sale_flash', 9 );
			add_action( 'woocommerce_after_shop_loop_item', 'astra_woo_shop_thumbnail_wrap_end', 8 );
			add_action( 'woocommerce_shop_loop_item_title', 'astra_woo_shop_out_of_stock', 8 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			add_action( 'woocommerce_after_shop_loop_item', 'astra_woo_woocommerce_shop_product_content' );
		}

		/**
		 * Porto theme.
		 */
		if ( strpos( $theme, 'porto' ) !== false ) {
			add_filter( 'porto_filter_checkout_version', 'WMSC_Compatibilities::porto_checkout_version' );
		}

		/**
		 * Electro theme.
		 */
		if ( strpos( $theme, 'electro' ) !== false ) {
			remove_action( 'woocommerce_checkout_before_order_review', 'electro_wrap_order_review', 0 );
			remove_action( 'woocommerce_checkout_after_order_review', 'electro_wrap_order_review_close', 0 );
		}

		/**
		 * Neve theme.
		 */
		if ( strpos( $theme, 'neve' ) !== false ) {
			add_filter( 'woocommerce_queued_js', 'WMSC_Compatibilities::neve_remove_js' );
		}

		/**
		 * Woodmart theme.
		 */
		if ( strpos( $theme, 'woodmart' ) !== false ) {
			add_filter( 'wmsc_buttons_class', 'WMSC_Compatibilities::woodmart_buttons' );
		}

		/*
		 * Germanized for WooCommerce plugin.
		 */
		if ( class_exists( 'WooCommerce_Germanized' ) ) {
			remove_action( 'woocommerce_review_order_after_payment', 'woocommerce_gzd_template_render_checkout_checkboxes', 10 );
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
			add_filter( 'wc_gzd_checkout_params', 'WMSC_Compatibilities::wc_gzd_checkout_params' );
			add_filter( 'wp_loaded', 'WMSC_Compatibilities::woocommerce_review_order_after_payment' );
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
	 * Override the Elementor Pro Checkout widget in order to remove the additional <div>s from the checkout form.
	 */
	public static function elementor_pro_widget() {
		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			return;
		}
		require_once plugin_dir_path( WMSC_PLUGIN_FILE ) . '/includes/elementor-widget-skin.php';
		add_action(
			'elementor/widget/woocommerce-checkout-page/skins_init',
			function( $widget ) {
				$widget->add_skin( new WMSC_Multistep_Checkout_Skin( $widget ) );
			}
		);
	}
}

WMSC_Compatibilities::init();
