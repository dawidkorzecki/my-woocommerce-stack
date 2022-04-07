<?php
/**
 * Class TaxableMethodSettings
 *
 * @package WPDesk\FS\TableRate\ShippingMethod
 */

namespace WPDesk\FS\TableRate\ShippingMethod;

/**
 * Settings fields with taxable option.
 */
class SingleMethodSettings implements MethodSettings {

	/**
	 * @param array $method_settings           .
	 * @param bool  $with_integration_settings Append integration settings.
	 *
	 * @return array
	 */
	public function get_settings_fields( array $method_settings, $with_integration_settings ) {
		$settings_fields = $this->append_tax_settings(
			( new CommonMethodSettings() )->get_settings_fields( $method_settings, $with_integration_settings )
		);
		unset( $settings_fields['method_enabled'], $settings_fields[ CommonMethodSettings::METHOD_DEFAULT ] );

		return $settings_fields;
	}

	/**
	 * @param array $settings_fields .
	 *
	 * @return array
	 */
	private function append_tax_settings( array $settings_fields ) {
		$tax_docs_link = 'pl_PL' !== get_locale()
			? 'https://flexibleshipping.com/woocommerce-shipping-tax/?utm_source=taxes&utm_medium=link&utm_campaign=flexible-shipping-method'
			: 'https://wpde.sk/podatek-wysylka';
		$new_settings_fields = [];
		foreach ( $settings_fields as $key => $settings_field ) {
			$new_settings_fields[ $key ] = $settings_field;
			if ( CommonMethodSettings::METHOD_DESCRIPTION === $key ) {
				$new_settings_fields['tax_heading'] = [
					'title'       => __( 'Tax', 'flexible-shipping' ),
					'type'        => 'title',
					'description' => sprintf(
						// Translators: new line and link.
						__( 'Adjust shipping taxes for this shipping method. Determine its tax status and whether you want to enter shipping costs with or without taxes.%1$sNeed more information? Read our %2$scomprehensive guide about WooCommerce shipping taxes →%3$s', 'flexible-shipping' ),
						'<br/>',
						'<a target="_blank" href="' . $tax_docs_link . '">',
						'</a>'
					),
				];
				$new_settings_fields['tax_status'] = [
					'title'    => __( 'Tax Status', 'flexible-shipping' ),
					'type'     => 'select',
					'default'  => 'taxable',
					'options'  => [
						'taxable' => __( 'Taxable', 'flexible-shipping' ),
						'none'    => _x( 'None', 'Tax status', 'flexible-shipping' ),
					],
					'desc_tip' => __( 'If you select to apply the tax, the plugin will use the tax rates defined in the WooCommerce settings at <strong>WooCommerce → Settings → Tax</strong>.', 'flexible-shipping' ),
				];
				$new_settings_fields['prices_include_tax']  = [
					'title'    => __( 'Tax included in shipping cost', 'flexible-shipping' ),
					'type'     => 'select',
					'default'  => 'no',
					'options'  => [
						'yes' => __( 'Yes, I will enter the shipping cost inclusive of tax', 'flexible-shipping' ),
						'no'  => __( 'No, I will enter the shipping cost exclusive of tax', 'flexible-shipping' ),
					],
					'desc_tip' => __( 'Choose whether the shipping cost defined in the rules table should be inclusive or exclusive of tax.', 'flexible-shipping' ),
				];
			}
		}

		return $new_settings_fields;
	}
}
