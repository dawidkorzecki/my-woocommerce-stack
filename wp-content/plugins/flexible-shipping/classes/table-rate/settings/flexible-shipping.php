<?php
/**
 * Settings for flexible shipment
 *
 * @package FlexibleShipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$docs_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/' : 'https://docs.flexibleshipping.com/collection/20-fs-table-rate/';

$docs_link .= '?utm_source=flexible-shipping-settings&utm_medium=link&utm_campaign=flexible-shipping-docs-link';

$settings = array(
	array(
		'title'       => __( 'Flexible Shipping', 'flexible-shipping' ),
		'type'        => 'title',
		// Translators: link.
		'description' => sprintf( __( 'See how to %1$sconfigure Flexible Shipping%2$s.', 'flexible-shipping' ), '<a href="' . $docs_link . '" target="_blank">', '</a>' ),
	),
	'enabled'                => array(
		'title'   => __( 'Enable/Disable', 'flexible-shipping' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable Flexible Shipping', 'flexible-shipping' ),
		'default' => 'no',
	),
	'title'                  => array(
		'title'       => __( 'Shipping title', 'flexible-shipping' ),
		'type'        => 'text',
		'description' => __( 'Visible only to admin in WooCommerce settings.', 'flexible-shipping' ),
		'default'     => __( 'Flexible Shipping', 'flexible-shipping' ),
		'desc_tip'    => true,
	),
	'tax_status'             => array(
		'title'    => __( 'Tax Status', 'flexible-shipping' ),
		'type'     => 'select',
		'default'  => 'taxable',
		'desc_tip' => __( 'If you select to apply the tax, the plugin will use the tax rates defined in the WooCommerce settings at <strong>WooCommerce → Settings → Tax</strong>.', 'flexible-shipping' ),
		'options'  => array(
			'taxable' => __( 'Taxable', 'flexible-shipping' ),
			'none'    => _x( 'None', 'Tax status', 'flexible-shipping' ),
		),
	),
	'title_shipping_methods' => array(
		'title'       => __( 'Shipping Methods', 'flexible-shipping' ),
		'type'        => 'title_shipping_methods',
		'description' => '',
	),
	'shipping_methods'       => array(
		'title'    => __( 'Shipping Methods', 'flexible-shipping' ),
		'type'     => 'shipping_methods',
		'desc_tip' => true,
	),
);

if ( version_compare( WC()->version, '2.6' ) >= 0 && $this->get_option( 'enabled', 'yes' ) == 'yes' ) {
	unset( $settings['enabled'] );
}

return $settings;
