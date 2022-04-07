<?php
/**
 * @package Flexible Shipping
 *
 * @var string $shipping_method_id .
 */

$show_fs_pro_ads = ! wpdesk_is_plugin_active( 'flexible-shipping-pro/flexible-shipping-pro.php' );

if ( $show_fs_pro_ads ) {
	include 'ads/html-ads-fs-pro.php';
}

$show_fs_ie_ads = wpdesk_is_plugin_active( 'flexible-shipping-pro/flexible-shipping-pro.php' ) && ! wpdesk_is_plugin_active( 'flexible-shipping-import-export/flexible-shipping-import-export.php' );

if ( $show_fs_ie_ads ) {
	include 'ads/html-ads-fsie.php';
}
?>

<div class="clear"></div>
