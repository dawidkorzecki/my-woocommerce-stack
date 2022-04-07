<?php
/**
 * @var $integration_checkbox bool
 * @var $show_fs_connect bool
 */


$pl = get_locale() === 'pl_PL';
$youtube_url = 'https://www.youtube.com/embed/qsFvYoiNDgU';
$general_settings_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=general-settings#Ustawienia_glowne' : 'https://docs.flexibleshipping.com/article/25-general-settings/?utm_campaign=flexible-shipping&utm_source=user-site&utm_medium=link&utm_term=general-settings&utm_content=fs-configuration-flexibleshippingtablerate';
$adding_a_shipping_method_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=adding-a-shipping-method#Metody_wysylki' : 'https://docs.flexibleshipping.com/article/29-shipping-methods/?utm_campaign=flexible-shipping&utm_source=user-site&utm_medium=link&utm_term=adding-a-shipping-method&utm_content=fs-configuration-flexibleshippingtablerate';
$currency_support_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=currency-support#Waluty' : 'https://docs.flexibleshipping.com/article/30-currency-support/?utm_campaign=flexible-shipping&utm_source=user-site&utm_medium=link&utm_term=currency-support&utm_content=fs-configuration-flexibleshippingtablerate';
$weight_based_shipping_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=weight-based-shipping#Koszt_na_wage' : 'https://docs.flexibleshipping.com/article/32-weight-based-shipping-woocommerce/?utm_campaign=flexible-shipping&utm_source=user-site&utm_medium=link&utm_term=weight-based-shipping&utm_content=fs-configuration-flexibleshippingtablerate';
$shipping_insurance_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=shipping-insurance#Ubezpieczenie_przesylki' : 'https://docs.flexibleshipping.com/article/34-shipping-insurance/?utm_campaign=flexible-shipping&utm_source=user-site&utm_medium=link&utm_term=shipping-insurance&utm_content=fs-configuration-flexibleshippingtablerate';
$conditional_cash_on_delivery_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=conditional-cash-on-delivery#Przesylka_za_pobraniem' : 'https://docs.flexibleshipping.com/article/35-conditional-cash-on-delivery/?utm_campaign=flexible-shipping&utm_source=user-site&utm_medium=link&utm_term=contitional-cash-on-delivery&utm_content=fs-configuration-flexibleshippingtablerate';
?>
</table>
<div class="fs-page-wrap">
	<div class="fs-box">
		<h3 class="wc-settings-sub-title"><?php _e( 'How to use Flexible Shipping?', 'flexible-shipping' ); ?></h3>

		<ol>
			<li>
				<?php
				echo sprintf(
					__( 'To add first Flexible Shipping method go to %sShipping zones%s and add Flexible Shipping to a shipping zone.', 'flexible-shipping' ),
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section' ) . '">',
					'</a>'
				);
				?>
			</li>

			<li><?php _e( 'You can start the configuration by clicking the Flexible Shipping link in the Shipping methods table.', 'flexible-shipping' ); ?></li>
		</ol>

		<h4><?php _e( 'Quick Video Overview', 'flexible-shipping' ); ?></h4>

		<div class="flexible-shipping-video">
			<iframe width="688" height="387" src="<?php echo $youtube_url?>?rel=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
		</div>

		<h4><?php _e( 'More resources', 'flexible-shipping' ); ?></h4>

		<ul>
			<li><a href="<?php echo $general_settings_url; ?>"><?php _e( 'General Settings', 'flexible-shipping' ); ?></a></li>
			<li><a href="<?php echo $adding_a_shipping_method_url; ?>"><?php _e( 'Adding a shipping method', 'flexible-shipping' ); ?></a></li>
			<li><a href="<?php echo $currency_support_url; ?>"><?php _e( 'Currency Support', 'flexible-shipping' ); ?></a></li>
			<li><a href="<?php echo $weight_based_shipping_url; ?>"><?php _e( 'Weight Based Shipping', 'flexible-shipping' ); ?></a></li>
			<li><a href="<?php echo $shipping_insurance_url; ?>"><?php _e( 'Shipping Insurance', 'flexible-shipping' ); ?></a></li>
			<li><a href="<?php echo $conditional_cash_on_delivery_url; ?>"><?php _e( 'Conditional Cash on Delivery', 'flexible-shipping' ); ?></a></li>
		</ul>

        <?php if ( $show_fs_connect ) : ?>
            <h3 class="wc-settings-sub-title fs-connect-box-header"><?php _e( 'Integrations', 'flexible-shipping' ); ?></h3>
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="enable-fs-connect-box"><?php _e( 'FS Connect', 'flexible-shipping' ); ?></label>
                    </th>
                    <td class="forminp">
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'FS Connect', 'flexible-shipping' ); ?></span></legend>
                            <label for="enable-fs-connect-box">
                                <input class="enable-fs-connect-box" <?php checked( $integration_checkbox, 1 ); ?> type="checkbox" name="fsconnect_box" id="enable-fs-connect-box" style="" value="1"> <?php _e( 'Enable integration with Flexible Shipping Connect', 'flexible-shipping' ); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                </tbody>
            </table>
        <?php endif; ?>

	</div>
</div>
<table>
