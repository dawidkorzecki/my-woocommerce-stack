<?php
/**
 * Easy Digital Downloads
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
 * EDD class.
 *
 * @class WP_Grid_Builder\FrontEnd\Sources\EDD
 * @since 1.0.0
 */
class EDD {

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
	 *
	 * @param integer $post_id Post ID.
	 */
	public function __construct( $post_id ) {

		$this->post = new \stdClass();

		$this->get_general_data( $post_id );
		$this->get_prices( $post_id );
		$this->get_sales( $post_id );
		$this->get_stock( $post_id );
		$this->get_downloads( $post_id );
		$this->get_review( $post_id );
		$this->get_gallery( $post_id );

		return $this->post;

	}

	/**
	 * Get General Info
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id Post ID.
	 */
	public function get_general_data( $post_id ) {

		$this->post->is_purchasable = true;

		$this->post->add_to_cart_button = edd_get_purchase_link(
			[
				'download_id' => $post_id,
				'class'       => 'wpgb-edd-cart',
				'price'       => false,
				'style'       => '',
				'color'       => '',
			]
		);

	}

	/**
	 * Get Prices
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id Post ID.
	 */
	public function get_prices( $post_id ) {

		$price = edd_get_download_price( $post_id );
		$price = edd_sanitize_amount( $price );
		$price = apply_filters( 'edd_download_price', $price, $post_id, false );

		$this->post->type          = 'standard';
		$this->post->price         = $price;
		$this->post->regular_price = $price;

		if ( edd_is_free_download( $post_id ) ) {

			$this->post->price         = __( 'Free', 'wp-grid-builder' );
			$this->post->regular_price = __( 'Free', 'wp-grid-builder' );
			return;

		}

		if ( ! edd_has_variable_prices( $post_id ) ) {
			return;
		}

		$this->post->type = 'variable';

		$min_price = edd_get_lowest_price_option( $post_id );
		$min_price = edd_format_amount( $min_price );
		$max_price = edd_get_highest_price_option( $post_id );
		$max_price = edd_format_amount( $max_price );

		if ( $min_price !== $max_price ) {

			$this->post->variation_price = [
				'min' => edd_currency_filter( $min_price ),
				'max' => edd_currency_filter( $max_price ),
			];
		}

	}

	/**
	 * Get product sales
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id Post ID.
	 */
	public function get_sales( $post_id ) {}

	/**
	 * Get product shipping & stock
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id Post ID.
	 */
	public function get_stock( $post_id ) {

		$this->post->in_stock = true;

		if ( ! function_exists( 'edd_pl_get_file_purchase_limit' ) ) {
			return;
		}

		$scope          = edd_get_option( 'edd_purchase_limit_scope' ) ?: 'site-wide';
		$sold_out_label = edd_get_option( 'edd_purchase_limit_sold_out_label' );
		$max_purchases  = (int) edd_pl_get_file_purchase_limit( $post_id );

		if ( 'site-wide' === $scope && $max_purchases ) {
			$purchases = (int) edd_get_download_sales_stats( $post_id );
		} elseif ( 'per-user' === $scope && $max_purchases ) {
			$purchases = (int) edd_pl_get_user_purchase_count( get_current_user_id(), $post_id );
		}

		if ( ! isset( $purchases ) ) {
			return;
		}

		$purchases_left = $max_purchases - $purchases;

		$this->post->stock_quantity = $purchases_left;
		$this->post->stock_status   = $purchases_left > 0;
		$this->post->in_stock       = $this->post->stock_status;

	}

	/**
	 * Get product downloads
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id Post ID.
	 */
	public function get_downloads( $post_id ) {}

	/**
	 * Get product reviews
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id Post ID.
	 */
	public function get_review( $post_id ) {

		if ( ! function_exists( 'edd_reviews' ) ) {
			return;
		}

		$this->post->rating_counts  = null;
		$this->post->average_rating = trim( get_post_meta( $post_id, 'edd_reviews_average_rating', true ) );
		$this->post->review_count   = '' !== $this->post->average_rating ? 1 : 0;

	}

	/**
	 * Get product gallery images
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id Post ID.
	 */
	public function get_gallery( $post_id ) {}
}
