<?php
/**
 * Shipments functions.
 *
 * @package Flexible Shipping
 */

/**
 * @param string $integration .
 *
 * @return string
 */
function fs_shipment_integration_class( $integration ) {
	return apply_filters( 'flexible_shipping_shipment_class', 'WPDesk_Flexible_Shipping_Shipment_' . $integration, $integration );
}

/**
 * @param string $integration .
 *
 * @return bool
 */
function fs_shipment_integration_exists( $integration ) {
	return class_exists( fs_shipment_integration_class( $integration ) );
}

/**
 * @param string $integration .
 * @param string $order_type .
 *
 * @return false
 */
function fs_integration_supports_order_type( $integration, $order_type ) {
	$supports = true;
	if ( 'shop_subscription' === $order_type ) {
		$supports   = false;
		$class_name = fs_shipment_integration_class( $integration );
		if ( class_exists( $class_name ) ) {
			$supports = $class_name::is_subscriptions_supported();
		}
	}

	return $supports;
}

/**
 * @param WC_Abstract_Order $order Order.
 * @param array             $fs_method Flexible shipping method.
 *
 * @return WPDesk_Flexible_Shipping_Shipment
 */
function fs_create_shipment( $order, $fs_method ) {
	if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
		$order_id = $order->id;
	} else {
		$order_id = $order->get_id();
	}
	$integration   = $fs_method['method_integration'];
	// Translators: order id and integration.
	$post_title    = sprintf( __( 'Shipment for order %s, %s', 'flexible-shipping' ), $order_id, $integration ); // phpcs:ignore.
	$post_title    = apply_filters( 'flexible_shipping_shipment_post_title_' . $integration, $post_title, $fs_method );
	$shipment_post = array(
		'post_title'  => $post_title,
		'post_type'   => 'shipment',
		'post_status' => 'fs-new',
		'post_parent' => $order_id,
	);
	$shipment_id   = wp_insert_post( $shipment_post );
	update_post_meta( $shipment_id, '_integration', $integration );

	return fs_get_shipment( $shipment_id, $order );
}

/**
 * Returns shipments for order.
 * Shipments are ordered from oldest to newest.
 *
 * @param int         $order_id .
 * @param string|null $integration .
 *
 * @return WPDesk_Flexible_Shipping_Shipment[]
 */
function fs_get_order_shipments( $order_id, $integration = null ) {
	$shipments_posts_query = array(
		'nopaging'    => true,
		'post_parent' => $order_id,
		'post_type'   => 'shipment',
		'post_status' => 'any',
		'orderby'     => 'ID',
		'order'       => 'ASC',
	);
	if ( ! empty( $integration ) ) {
		$shipments_posts_query['meta_key']   = '_integration';
		$shipments_posts_query['meta_value'] = $integration;
	}
	$shipments_posts = get_posts( $shipments_posts_query );
	$shipments       = array();
	if ( count( $shipments_posts ) ) {
		$order = wc_get_order( $order_id );
		foreach ( $shipments_posts as $shipment_post ) {
			$integration = get_post_meta( $shipment_post->ID, '_integration', true );
			if ( fs_shipment_integration_exists( $integration ) ) {
				$shipments[] = fs_get_shipment( $shipment_post->ID, $order );
			}
		}
	}

	return $shipments;
}

/**
 * Get shipment.
 *
 * @param int           $shipment_id Shipment id.
 * @param WC_Order|null $order Order.
 *
 * @return WPDesk_Flexible_Shipping_Shipment
 */
function fs_get_shipment( $shipment_id, $order = null ) {
	$integration            = get_post_meta( $shipment_id, '_integration', true );
	$class_name             = 'WPDesk_Flexible_Shipping_Shipment';
	$integration_class_name = fs_shipment_integration_class( $integration );
	if ( class_exists( $integration_class_name ) ) {
		$class_name = $integration_class_name;
	}

	return new $class_name( $shipment_id, $order );
}

/**
 * @param WC_Abstract_Order $order Order.
 *
 * @return float
 */
function fs_calculate_order_weight( $order ) {
	$weight = 0;
	if ( count( $order->get_items() ) > 0 ) {
		foreach ( $order->get_items() as $item ) {
			if ( $item['product_id'] > 0 ) {
				$product = $item->get_product();
				$product_weight = $product->get_weight();
				if ( ! $product->is_virtual() && is_numeric( $product_weight ) ) {
					$weight += floatval( $product->get_weight() ) * floatval( $item['qty'] );
				}
			}
		}
	}

	return $weight;
}

/**
 * @param array $package .
 *
 * @return float|int
 */
function fs_calculate_package_weight( $package ) {
	$weight = 0;
	foreach ( $package['contents'] as $item ) {
		$weight += floatval( $item['data']->get_weight() ) * floatval( $item['quantity'] );
	}

	return $weight;
}
