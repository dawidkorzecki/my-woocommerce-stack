<?php
/**
 * @package Flexible Shipping
 *
 * @var string $shipping_method_id .
 */

$fsie_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/sklep/flexible-shipping-import-export-woocommerce/?utm_source=flexible-shipping-method&utm_medium=button&utm_campaign=cross-fsie' : 'https://flexibleshipping.com/products/flexible-import-export-shipping-methods-woocommerce/?utm_source=flexible-shipping-method&utm_medium=button&utm_campaign=cross-fsie';
?>

<div class="fs-flexible-shipping-sidebar fs-flexible-shipping-sidebar-fsie <?php echo esc_attr( isset( $shipping_method_id ) ? $shipping_method_id : '' ); ?>"
	 style="height: auto;">
	<div class="wpdesk-metabox">
		<div class="wpdesk-stuffbox">
			<h3 class="title"><?php esc_html_e( 'Import and Export your shipping methods with Flexible Shipping Import/Export plugin', 'flexible-shipping' ); ?></h3>

			<div class="inside">
				<div class="main">
					<ul>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Edit the shipping cost for multiple methods simultaneously directly in the CSV file', 'flexible-shipping' ); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Backup or transfer easily your shipping methods between the shipping zones or WooCommerce stores', 'flexible-shipping' ); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Upload the edited CSV, update the outdated shipping pricing and organize your shipping methods during the import', 'flexible-shipping' ); ?>
						</li>
					</ul>

					<a class="button button-primary" href="<?php echo esc_url( $fsie_link ); ?>"
					   target="_blank"><?php esc_html_e( 'Buy Flexible Shipping Import/Export &rarr;', 'flexible-shipping' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
