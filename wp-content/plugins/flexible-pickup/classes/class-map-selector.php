<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Flexible_Pickup_Map_Selector {

	private $plugin = null;

	public function __construct( Flexible_Pickup_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	public function hooks() {
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
	}

	public function template_redirect() {
		if ( isset( $_GET['flexible_pickup_map'] ) && $_GET['flexible_pickup_map'] == '1' ) {
			$options = get_option( 'flexible_pickup_settings', array() );
			$pickup_points = array();
			$args = array(
				'points_group' => '',
				'gmap_api_key' => '',
			);
			if ( isset( $options['google_maps_api_key'] ) ) {
				$args['gmap_api_key'] = $options['google_maps_api_key'];
			}
			$query_args = array(
				'post_type'         => 'pickup_point',
				'posts_per_page'    => -1,
				'meta_query'        => array(),
				'orderby'           => array(),
			);
			if ( isset( $_GET['points_group'] ) && $_GET['points_group'] != '' ) {
				$args['points_group'] = $_GET['points_group'];
				$query_args['tax_query'] = array(
					array(
						'taxonomy'          => 'fp_points_group',
						'include_children'  => true,
						'field'             => 'id',
						'terms'             => $_GET['points_group'],
					)
				);
			}
			$posts = get_posts( $query_args );
			foreach ( $posts as $post ) {
				$post_meta = get_post_meta( $post->ID );
				if ( !empty( $post_meta['_lat'] ) && !empty( $post_meta['_lat'][0] ) && !empty( $post_meta['_lng'] ) && !empty( $post_meta['_lng'][0] ) ) {
					$point        = array(
						'id'    => $post->ID,
						'title' => $post->post_title,
					);
					$point['lat'] = $post_meta['_lat'][0];
					$point['lng'] = $post_meta['_lng'][0];
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
					$point['all'] = $point['title'] . ' ' . $point['description'] . ' ' . $point['company'] . ' ' . $point['address'] . ' ' . $point['address_2'] . ' ' . $point['city'] . ' ' . $point['postal_code'];
					$pickup_points[] = $point;
				}
			}
			$args['pickup_points'] = $pickup_points;
			$args['jquery_script'] = site_url( '/wp-includes/js/jquery/jquery.js' );
			echo $this->plugin->load_template( 'map-selector', '', $args );
			exit;
		}
	}

}