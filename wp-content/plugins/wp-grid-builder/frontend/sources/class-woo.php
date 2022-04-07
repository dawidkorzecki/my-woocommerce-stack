<?php
/**
 * WooCommerce
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Sources;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce class.
 *
 * @class WP_Grid_Builder\FrontEnd\Sources\Woo
 * @since 1.0.0
 */
class Woo {

	/**
	 * Holds post object
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var object
	 */
	public $post;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		global $product;

		$this->post = new \stdClass();

		$this->get_general_data( $product );
		$this->get_simple_prices( $product );
		$this->get_variable_prices( $product );
		$this->get_grouped_prices( $product );
		$this->get_sales( $product );
		$this->get_stock( $product );
		$this->get_downloads( $product );
		$this->get_review( $product );
		$this->get_gallery( $product );

	}

	/**
	 * Get General Info
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $product Post object of the product.
	 */
	public function get_general_data( $product ) {

		$this->post->type                    = $product->get_type();
		$this->post->featured                = $product->get_featured();
		$this->post->catalog_visibility      = $product->get_catalog_visibility();
		$this->post->sku                     = $product->get_sku();
		$this->post->is_purchasable          = $product->is_purchasable();
		$this->post->add_to_cart_url         = $product->add_to_cart_url();
		$this->post->add_to_cart_text        = $product->add_to_cart_text();
		$this->post->add_to_cart_description = $product->add_to_cart_description();
		$this->post->ajax_add_to_cart        = $product->supports( 'ajax_add_to_cart' );

	}

	/**
	 * Get simple prices
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $product Post object of the product.
	 */
	public function get_simple_prices( $product ) {

		$this->post->price         = $this->get_price( $product, $product->get_price() );
		$this->post->sale_price    = $this->get_price( $product, $product->get_sale_price() );
		$this->post->regular_price = $this->get_price( $product, $product->get_regular_price() );
		$this->post->price_suffix  = wp_strip_all_tags( $product->get_price_suffix() );

	}

	/**
	 * Get variable prices
	 *
	 * @since 1.2.3
	 * @access public
	 *
	 * @param object $product Post object of the product.
	 */
	public function get_variable_prices( $product ) {

		if ( 'variable' !== $this->post->type ) {
			return;
		}

		$prices = $product->get_variation_prices( true );

		if ( empty( $prices['price'] ) ) {
			return;
		}

		$min_active_price  = current( $prices['price'] );
		$max_active_price  = end( $prices['price'] );
		$max_regular_price = end( $prices['regular_price'] );

		if ( $min_active_price !== $max_active_price ) {

			$this->post->variation_price = [
				'min' => wp_strip_all_tags( wc_price( $min_active_price ) ),
				'max' => wp_strip_all_tags( wc_price( $max_active_price ) ),
			];

		} elseif ( $product->is_on_sale() ) {
			$this->post->regular_price = wp_strip_all_tags( wc_price( $max_regular_price ) );
		} else {
			$this->post->price = wp_strip_all_tags( wc_price( $min_active_price ) );
		}
	}

	/**
	 * Get grouped prices
	 *
	 * @since 1.2.3
	 * @access public
	 *
	 * @param object $product Post object of the product.
	 */
	public function get_grouped_prices( $product ) {

		if ( 'grouped' !== $this->post->type || ! function_exists( 'wc_products_array_filter_visible_grouped' ) ) {
			return;
		}

		$prices   = [];
		$children = array_filter(
			array_map( 'wc_get_product', $product->get_children() ),
			'wc_products_array_filter_visible_grouped'
		);

		foreach ( $children as $child ) {

			if ( '' !== $child->get_price() ) {
				$prices[] = $child->get_price();
			}
		}

		$min_price = ! empty( $prices ) ? min( $prices ) : '';
		$max_price = ! empty( $prices ) ? max( $prices ) : '';

		if ( $min_price !== $max_price ) {

			$this->post->variation_price = [
				'min' => wp_strip_all_tags( wc_price( $min_price ) ),
				'max' => wp_strip_all_tags( wc_price( $max_price ) ),
			];
		}

		$this->post->price = wp_strip_all_tags( wc_price( $min_price ) );
		$this->post->sale_price = wp_strip_all_tags( wc_price( $min_price ) );
		$this->post->regular_price = wp_strip_all_tags( wc_price( $max_price ) );

	}

	/**
	 * Get product price
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array   $product Post object of the product.
	 * @param integer $price Price to process.
	 */
	public function get_price( $product, $price = '' ) {

		$price = wc_get_price_to_display(
			$product,
			[
				'price' => $price,
			]
		);

		$price = wc_price( $price );
		$price = wp_strip_all_tags( $price );

		return $price;

	}

	/**
	 * Get product sales
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $product Post object of the product.
	 */
	public function get_sales( $product ) {

		$this->post->on_sale           = $product->is_on_sale();
		$this->post->total_sales       = $product->get_total_sales();
		$this->post->date_on_sale_from = $product->get_date_on_sale_from();
		$this->post->date_on_sale_to   = $product->get_date_on_sale_to();

	}

	/**
	 * Get product shipping & stock
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $product Post object of the product.
	 */
	public function get_stock( $product ) {

		$this->post->stock_quantity = $product->get_stock_quantity();
		$this->post->stock_status   = $product->get_stock_status();
		$this->post->in_stock       = $product->is_in_stock();

	}

	/**
	 * Get product downloads
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $product Post object of the product.
	 */
	public function get_downloads( $product ) {

		$this->post->downloads       = $product->get_downloads();
		$this->post->download_expiry = $product->get_download_expiry();
		$this->post->downloadable    = $product->get_downloadable();
		$this->post->download_limit  = $product->get_download_limit();

	}

	/**
	 * Get product reviews
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $product Post object of the product.
	 */
	public function get_review( $product ) {

		$this->post->rating_counts  = $product->get_rating_counts();
		$this->post->average_rating = $product->get_average_rating();
		$this->post->review_count   = $product->get_review_count();

	}

	/**
	 * Get product gallery images
	 *
	 * @since 1.1.5 Get first product gallery attachment.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $product Post object of the product.
	 */
	public function get_gallery( $product ) {

		$this->post->gallery_image_ids   = $product->get_gallery_image_ids();
		$this->post->first_gallery_image = ! empty( $this->post->gallery_image_ids ) ? $this->post->gallery_image_ids[0] : '';

	}
}
