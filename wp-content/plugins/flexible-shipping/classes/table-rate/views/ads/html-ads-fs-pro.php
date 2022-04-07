<?php
/**
 * @package Flexible Shipping
 *
 * @var string $shipping_method_id .
 */

?>

<div class="fs-flexible-shipping-sidebar fs-flexible-shipping-sidebar-pro <?php echo esc_attr( isset( $shipping_method_id ) ? $shipping_method_id : '' ); ?>" style="height: auto;">
	<div class="wpdesk-metabox">
		<div class="wpdesk-stuffbox">
			<h3 class="title"><?php esc_html_e( 'Get Flexible Shipping PRO!', 'flexible-shipping' ); ?></h3>
			<?php
			$fs_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/sklep/flexible-shipping-pro-woocommerce/' : 'https://flexibleshipping.com/products/flexible-shipping-pro-woocommerce/';
			$utm     = get_locale() === 'pl_PL' ? '?utm_campaign=flexible-shipping&utm_source=user-site&utm_medium=button&utm_term=upgrade-now&utm_content=fs-shippingzone-upgradenow' : '?utm_source=fs-settings&utm_medium=link&utm_campaign=settings-upgrade-link';
			?>

			<div class="inside">
				<div class="main">
					<ul>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Shipping Classes support', 'flexible-shipping' ); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Product count based costs', 'flexible-shipping' ); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Stopping, Cancelling a rule', 'flexible-shipping' ); ?>
						</li>
						<li>
							<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Additional calculation methods', 'flexible-shipping' ); ?>
						</li>
					</ul>

					<a class="button button-primary" href="<?php echo esc_url( $fs_link . $utm ); ?>"
					   target="_blank"><?php esc_html_e( 'Upgrade now to PRO version &rarr;', 'flexible-shipping' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
