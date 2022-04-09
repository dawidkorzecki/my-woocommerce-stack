<?php

defined( 'ABSPATH' ) || exit;

use ElementorPro\Plugin;
use ElementorPro\Modules\Woocommerce\Widgets\Checkout;
use Elementor\Widget_Base;
use Elementor\Skin_Base;
use Elementor\Controls_Manager;


class WMSC_Multistep_Checkout_Skin extends Skin_Base {

	public function __construct( Widget_Base $parent ) {
		$this->parent = $parent;
	}

	public function get_id() {
		return 'multistep-checkout';
	}

	public function get_title() {
		return __( 'Multi-Step Checkout', 'wp-multi-step-checkout' );
	}

	public function render() {
		$is_editor = Plugin::elementor()->editor->is_edit_mode();

		if ( $is_editor ) {
			$store_current_user = wp_get_current_user()->ID;
			wp_set_current_user( 0 );
		}

		$this->add_render_hooks_multistep();

		echo do_shortcode( '[woocommerce_checkout]' );

		$this->parent->remove_render_hooks();

		if ( $is_editor ) {
			wp_set_current_user( $store_current_user );
		}
	}

	public function add_render_hooks_multistep() {
		add_filter( 'woocommerce_form_field_args', array( $this->parent, 'modify_form_field' ), 70, 3 );
		add_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', array( $this->parent, 'woocommerce_terms_and_conditions_checkbox_text' ), 10, 1 );

		add_filter( 'gettext', array( $this->parent, 'filter_gettext' ), 20, 3 );

		add_action( 'woocommerce_checkout_before_order_review_heading', array( $this->parent, 'woocommerce_checkout_before_order_review_heading_1' ), 5 );
		add_action( 'woocommerce_checkout_before_order_review_heading', array( $this->parent, 'woocommerce_checkout_before_order_review_heading_2' ), 95 );

		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form' );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form' );
	}
}
