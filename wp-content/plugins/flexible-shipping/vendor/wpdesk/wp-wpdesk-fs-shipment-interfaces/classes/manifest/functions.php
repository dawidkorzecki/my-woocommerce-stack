<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function fs_manifest_integration_exists( $integration ) {
    $class_name = 'WPDesk_Flexible_Shipping_Manifest'  . '_' . $integration ;
    if ( class_exists( $class_name ) ) {
        return true;
    }
    return false;
}


function fs_create_manifest( $integration ) {
    $post_title = sprintf( __( 'Shipping manifest %s, %s', 'flexible-shipping' ), $integration, date_i18n( get_option( 'date_format' ) ) );
    $post_title = apply_filters( 'flexible_shipping_manifest_post_title_'. $integration, $post_title );
    $manifest_post = array(
        'post_title'    => $post_title,
        'post_type'     => 'shipping_manifest',
        'post_status'   => 'publish',
    );
    $manifest_id = wp_insert_post( $manifest_post );
    update_post_meta( $manifest_id, '_integration', $integration );
    return fs_get_manifest( $manifest_id );
}

/**
 * @param $manifest_id
 * @return WPDesk_Flexible_Shipping_Manifest
 */
function fs_get_manifest( $manifest_id ) {
    $integration = get_post_meta( $manifest_id, '_integration', true );
    $class_name = 'WPDesk_Flexible_Shipping_Manifest';
    if ( class_exists( $class_name . '_' . $integration ) ) {
        $class_name = $class_name . '_' . $integration;
    }
    else {
    	$class_name = 'WPDesk_Flexible_Shipping_Manifest_FS';
    }
    return new $class_name( $manifest_id );
}

function fs_delete_manifest( $manifest ) {
    $shipments_posts = get_posts( array(
        'posts_per_page'    => -1,
        'post_type'         => 'shipment',
        'post_status'       => 'any',
        'meta_key'          => '_manifest',
        'meta_value'        => $manifest->get_id()
    ) );
    foreach ( $shipments_posts as $shipment_post ) {
        $shipment = fs_get_shipment( $shipment_post->ID );
        $shipment->delete_meta( '_manifest' );
        $shipment->update_status('fs-confirmed' );
        $shipment->save();
    }
    $manifest->set_meta( '_shipments', array() );
    $manifest->update_status( 'trash' );
    $manifest->save();
}

