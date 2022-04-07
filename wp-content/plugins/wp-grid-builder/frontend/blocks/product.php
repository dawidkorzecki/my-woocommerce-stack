<?php
/**
 * Product blocks
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieve product object from custom loop
 *
 * @since 1.0.0
 *
 * @return object
 */
function wpgb_get_product() {

	return wpgb_get_object();

}

/**
 * Retrieve the product price
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_price() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->price ) ) {
		return;
	}

	return $post->product->price;

}

/**
 * Display the product price
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_price( $block = [], $action = [] ) {

	$price = wpgb_get_price();
	$type  = wpgb_get_product_type();
	$range = wpgb_get_variation_price();

	if ( empty( $price ) ) {
		return;
	}

	wpgb_block_start( $block, $action );

	if ( isset( $range['min'], $range['max'] ) ) {

		echo '<span>' . esc_html( $range['min'] ) . '</span>';
			echo '&nbsp;&ndash;&nbsp;';
		echo '<span>' . esc_html( $range['max'] ) . '</span>';

	} else {
		echo esc_html( $price );
	}

	wpgb_the_price_suffix();
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the product variation price
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_variation_price() {

	$post = wpgb_get_post();

	if ( empty( $post->product->variation_price ) ) {
		return;
	}

	return $post->product->variation_price;

}

/**
 * Retrieve the product regular price
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_regular_price() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->regular_price ) ) {
		return;
	}

	return $post->product->regular_price;

}

/**
 * Display the product regular price
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_regular_price( $block = [], $action = [] ) {

	$regular_price = wpgb_get_regular_price();

	if ( empty( $regular_price ) ) {
		return;
	}

	if ( ! wpgb_is_on_sale() || wpgb_get_variation_price() ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo '<del class="wpgb-block-price">' . esc_html( $regular_price ) . '</del>';
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the product sale price
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_sale_price() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->sale_price ) ) {
		return;
	}

	return $post->product->sale_price;

}

/**
 * Display the product sale price
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_sale_price( $block = [], $action = [] ) {

	if ( ! wpgb_is_on_sale() ) {
		return;
	}

	$sale_price = wpgb_get_sale_price();

	if ( empty( $sale_price ) || wpgb_get_variation_price() ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $sale_price );
	wpgb_block_end( $block, $action );

}

/**
 * Display the product full price
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_full_price( $block = [], $action = [] ) {

	$price = wpgb_get_price();

	if ( empty( $price ) ) {
		return;
	}

	if ( wpgb_get_variation_price() ) {

		wpgb_the_price( $block, $action );
		return;

	}

	$sale_price    = wpgb_get_sale_price();
	$regular_price = wpgb_get_regular_price();

	wpgb_block_start( $block, $action );

	if ( wpgb_is_on_sale() && $sale_price !== $regular_price ) {

		echo '<del class="wpgb-block-price"><span>' . esc_html( $regular_price ) . '</span></del>';
		echo '<ins class="wpgb-block-price"><span>' . esc_html( $sale_price ) . '</span></ins>';

	} else {
		echo esc_html( $price );
	}

	wpgb_the_price_suffix();
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the product price suffix
 *
 * @since 1.5.7
 *
 * @return string
 */
function wpgb_get_price_suffix() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->price_suffix ) ) {
		return '';
	}

	return $post->product->price_suffix;

}

/**
 * Display the product price suffix
 *
 * @since 1.5.7
 */
function wpgb_the_price_suffix() {

	$suffix = wpgb_get_price_suffix();

	if ( empty( $suffix ) ) {
		return;
	}

	echo ' <small>' . esc_html( $suffix ) . '</small>';

}

/**
 * Retrieve the product cart url
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_add_to_cart_url() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->add_to_cart_url ) ) {
		return;
	}

	return $post->product->add_to_cart_url;

}

/**
 * Retrieve the product cart text
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_add_to_cart_text() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->add_to_cart_text ) ) {
		return;
	}

	return $post->product->add_to_cart_text;

}

/**
 * Retrieve the product cart description
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_add_to_cart_description() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->add_to_cart_description ) ) {
		return;
	}

	return $post->product->add_to_cart_description;

}

/**
 * Retrieve the product stock quantity
 *
 * @since 1.0.0
 *
 * @return integer
 */
function wpgb_get_stock_quantity() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->stock_quantity ) ) {
		return;
	}

	return (int) $post->product->stock_quantity;

}

/**
 * Retrieve the product sku
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_sku() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->sku ) ) {
		return;
	}

	return $post->product->sku;

}

/**
 * Retrieve the product type
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_product_type() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->type ) ) {
		return;
	}

	return $post->product->type;

}

/**
 * Check if a product is purchasable
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function wpgb_is_purchasable() {

	$post = wpgb_get_post();

	return ! empty( $post->product->is_purchasable );

}

/**
 * Check if a product is in stock
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function wpgb_is_in_stock() {

	$post = wpgb_get_post();

	return ! empty( $post->product->in_stock );

}

/**
 * Check if a product support ajax cart
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_support_ajax_add_to_cart() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->ajax_add_to_cart ) ) {
		return;
	}

	return $post->product->ajax_add_to_cart;

}

/**
 * Display WooCommerce cart button
 *
 * @since 1.0.0
 *
 * @param array $block Holds block args.
 */
function wpgb_woocommerce_cart_button( $block = [] ) {

	$cart_url = wpgb_get_add_to_cart_url();

	if ( empty( $cart_url ) ) {
		return;
	}

	$class = wpgb_get_block_class( $block );

	if ( wpgb_is_purchasable() && wpgb_is_in_stock() ) {

		$class .= ' add_to_cart_button';
		$class .= wpgb_support_ajax_add_to_cart() ? ' ajax_add_to_cart' : '';

	}

	printf(
		'<a href="%s" data-quantity="1" class="%s product_type_%s" data-product_id="%s" data-product_sku="%s" aria-label="%s" rel="nofollow"><span>%s</span></a>',
		esc_url( $cart_url ),
		esc_attr( $class ),
		esc_attr( wpgb_get_product_type() ),
		esc_attr( wpgb_get_the_id() ),
		esc_attr( wpgb_get_sku() ),
		esc_attr( wpgb_get_add_to_cart_description() ),
		esc_html( wpgb_get_add_to_cart_text() )
	);

}

/**
 * Display Easy Digital Downloads cart button
 *
 * @since 1.0.0
 *
 * @param array $block Holds block args.
 */
function wpgb_edd_cart_button( $block = [] ) {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->add_to_cart_button ) ) {
		return;
	}

	$class = wpgb_get_block_class( $block );

	$cart_button = $post->product->add_to_cart_button;
	$cart_button = str_replace( 'wpgb-edd-cart', esc_attr( $class ), $cart_button );

	echo $cart_button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}

/**
 * Display the product cart button
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_cart_button( $block = [], $action = [] ) {

	$post = wpgb_get_post();

	if ( ! isset( $post->post_type ) ) {
		return;
	}

	if ( 'download' === $post->post_type ) {
		wpgb_edd_cart_button( $block );
	} else {
		wpgb_woocommerce_cart_button( $block );
	}

}

/**
 * Retrieve the product review number
 *
 * @since 1.0.0
 *
 * @return integer
 */
function wpgb_get_review_count() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->review_count ) ) {
		return;
	}

	return $post->product->review_count;

}

/**
 * Retrieve the product rating average
 *
 * @since 1.0.0
 *
 * @return int|float
 */
function wpgb_get_rating_average() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->average_rating ) ) {
		return;
	}

	return $post->product->average_rating;

}

/**
 * Display the product rating average stars
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_star_rating( $block = [], $action = [] ) {

	$review_count   = wpgb_get_review_count();
	$rating_average = wpgb_get_rating_average();

	if ( ! $review_count ) {
		return;
	}

	wpgb_block_start( $block, $action );
	wpgb_rating_stars_icon( $rating_average );
	wpgb_block_end( $block, $action );

}

/**
 * Display rating stars svg icon
 *
 * @since 1.0.0
 *
 * @param integer $average Rating average 5 based.
 */
function wpgb_rating_stars_icon( $average = 5 ) {

	$average  = min( 5, max( 0, $average ) );
	$percent  = $average / 5 * 100;
	$sr_label = sprintf(
		/* translators: %s: average rating */
		__( '%s out of 5', 'wp-grid-builder' ),
		(float) $average
	);

	?>
	<svg class="wpgb-rating-svg" viewBox="0 0 120 24" aria-hidden="true" focusable="false">
		<use xlink:href="#wpgb-rating-stars-svg" width="<?php echo (float) $percent; ?>%"></use>
		<use xlink:href="#wpgb-rating-stars-svg" width="100%"></use>
	</svg>
	<span class="wpgb-sr-only"><?php echo esc_html( $sr_label ); ?></span>
	<?php

}

/**
 * Display the product rating average text
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_text_rating( $block = [], $action = [] ) {

	$review_count   = wpgb_get_review_count();
	$rating_average = wpgb_get_rating_average();

	if ( ! $review_count ) {
		return;
	}

	$rating_average = number_format( $rating_average, 2 );

	wpgb_block_start( $block, $action );
		printf(
			/* translators: %s: average rating */
			esc_html__( '%s out of 5', 'wp-grid-builder' ),
			(float) $rating_average
		);
	wpgb_block_end( $block, $action );

}

/**
 * Check if a product is on sale
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function wpgb_is_on_sale() {

	$post = wpgb_get_post();

	return ! empty( $post->product->on_sale );

}

/**
 * Display the product on sale badge
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_on_sale_badge( $block = [], $action = [] ) {

	if ( ! wpgb_is_on_sale() ) {
		return;
	}

	$type  = isset( $block['badge_type'] ) && ! empty( $block['badge_type'] ) ? $block['badge_type'] : 'text';
	$label = isset( $block['badge_label'] ) && ! empty( $block['badge_label'] ) ? trim( $block['badge_label'] ) : '';
	$icon  = isset( $block['badge_icon'] ) && ! empty( $block['badge_icon'] ) ? $block['badge_icon'] : 'wpgb/business/discount-2';

	if ( empty( $label ) ) {
		$label = __( 'Sale!', 'wp-grid-builder' );
	}

	wpgb_block_start( $block, $action );

	if ( 'icon' === $type ) {
		wpgb_svg_icon( $icon );
	} else {
		echo esc_html( $label );
	}

	wpgb_block_end( $block, $action );

}

/**
 * Display the product in stock badge
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_in_stock_badge( $block = [], $action = [] ) {

	if ( ! wpgb_is_purchasable() || ! wpgb_is_in_stock() ) {
		return;
	}

	if ( isset( $block['badge_label'] ) && ! empty( $block['badge_label'] ) ) {
		$label = trim( $block['badge_label'] );
	}

	if ( empty( $label ) && function_exists( 'wc_get_product_stock_status_options' ) ) {

		$labels = wc_get_product_stock_status_options();
		$label  = $labels['instock'];

	}

	if ( empty( $label ) ) {
		$label = __( 'In stock', 'wp-grid-builder' );
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $label );
	wpgb_block_end( $block, $action );

}

/**
 * Display the product out of stock badge
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_out_of_stock_badge( $block = [], $action = [] ) {

	if ( ! wpgb_is_purchasable() || wpgb_is_in_stock() ) {
		return;
	}

	if ( isset( $block['badge_label'] ) && ! empty( $block['badge_label'] ) ) {
		$label = trim( $block['badge_label'] );
	}

	if ( empty( $label ) && function_exists( 'wc_get_product_stock_status_options' ) ) {

		$labels = wc_get_product_stock_status_options();
		$label  = $labels['outofstock'];

	}

	if ( empty( $label ) ) {
		$label = __( 'Out of stock', 'wp-grid-builder' );
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $label );
	wpgb_block_end( $block, $action );

}

/**
 * Display the product thumbnail and first gallery image on hover
 *
 * @since 1.1.5
 *
 * @param array $action Holds layer action properties.
 */
function wpgb_the_product_thumbnail( $action = [] ) {

	$can_hover = wpgb_get_grid_settings( 'product_image_hover' );
	$first_img = wpgb_get_product_first_gallery_image( 'thumbnail' );

	if ( ! $can_hover || empty( $first_img ) ) {

		wpgb_the_post_thumbnail( $action );
		return;

	}

	$thumb = wpgb_get_attachment_image_src( 'thumbnail' );
	$class = 'wpgb-card-media-thumbnail';

	if ( isset( $action['action_type'] ) && 'open_media' === $action['action_type'] ) {
		$class .= ' wpgb-card-media-button';
	}

	wpgb_get_thumbnail_ratio( $thumb );

	echo '<div class="' . esc_attr( $class ) . '">';
	echo '<ul class="wpgb-card-media-gallery wpgb-product-image-hover">';
		echo '<li class="wpgb-card-media-gallery-item" data-active>';
		wpgb_the_thumbnail();
		echo '</li>';
		echo '<li class="wpgb-card-media-gallery-item">';
		wpgb_the_thumbnail( $first_img );
		echo '</li>';
	echo '</ul>';
	wpgb_the_post_media_link( $action );
	echo '</div>';

}

/**
 * Retrieve the first gallery attachment of product
 *
 * @since 1.1.5
 *
 * @return array
 */
function wpgb_get_product_first_gallery_image() {

	$post = wpgb_get_post();

	if ( ! isset( $post->product->first_gallery_image ) ) {
		return;
	}

	return $post->product->first_gallery_image;

}
