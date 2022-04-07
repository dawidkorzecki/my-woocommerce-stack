<?php
/**
 * Class CommonMethodSettings
 *
 * @package WPDesk\FS\TableRate\ShippingMethod
 */

namespace WPDesk\FS\TableRate\ShippingMethod;

use FSVendor\WPDesk\FS\TableRate\CalculationMethodOptions;
use FSVendor\WPDesk\FS\TableRate\Settings\CartCalculationOptions;
use WPDesk\FS\TableRate\DefaultRulesSettings;
use WPDesk\FS\TableRate\RulesSettingsField;
use WPDesk_Flexible_Shipping;

/**
 * Common shipping method settings.
 */
class CommonMethodSettings implements MethodSettings {

	const METHOD_TITLE              = 'method_title';
	const METHOD_DESCRIPTION        = 'method_description';
	const METHOD_RULES              = 'method_rules';
	const CART_CALCULATION          = 'cart_calculation';

	const SETTING_METHOD_CALCULATION_METHOD = 'method_calculation_method';
	const METHOD_DEFAULT                    = 'method_default';

	/**
	 * @param array $method_settings           .
	 * @param bool  $with_integration_settings Append integration settings.
	 *
	 * @return array
	 */
	public function get_settings_fields( array $method_settings, $with_integration_settings ) {
		if ( ! isset( $method_settings['method_free_shipping_label'] ) ) {
			$method_settings['method_free_shipping_label'] = __( 'Free', 'flexible-shipping' );
		}

		$this->settings['method_free_shipping'] = isset( $method_settings['method_free_shipping'] ) ? $method_settings['method_free_shipping'] : '';

		if ( empty( $method_settings['method_integration'] ) ) {
			$method_settings['method_integration'] = '';
		}

		$method_free_shipping = '';
		if ( isset( $method_settings['method_free_shipping'] ) && '' !== $method_settings['method_free_shipping'] ) {
			$method_free_shipping = floatval( $method_settings['method_free_shipping'] );
		}

		$settings = [
			'method_enabled' => [
				'title'   => __( 'Enable/Disable', 'flexible-shipping' ),
				'type'    => 'checkbox',
				'default' => $this->get_value_from_settings( $method_settings, 'method_enabled', 'yes' ),
				'label'   => __( 'Enable this shipment method', 'flexible-shipping' ),
			],
			self::METHOD_TITLE => [
				'title'             => __( 'Method Title', 'flexible-shipping' ),
				'type'              => 'text',
				'description'       => __( 'This controls the title which the user sees during checkout.', 'flexible-shipping' ),
				'desc_tip'          => true,
				'default'           => $this->get_value_from_settings( $method_settings, self::METHOD_TITLE, 'Flexible Shipping' ),
				'custom_attributes' => [ 'required' => true ],
			],
			self::METHOD_DESCRIPTION => [
				'title'       => __( 'Method Description', 'flexible-shipping' ),
				'type'        => 'text',
				'description' => __( 'This controls method description which the user sees during checkout.', 'flexible-shipping' ),
				'desc_tip'    => true,
				'default'     => $this->get_value_from_settings( $method_settings, self::METHOD_DESCRIPTION, '' ),
			],
			'method_free_shipping' => [
				'title'       => __( 'Free Shipping', 'flexible-shipping' ),
				'type'        => 'price',
				'default'     => $method_free_shipping,
				'description' => __( 'Enter a minimum order amount for free shipment. This will override the costs configured below.', 'flexible-shipping' ),
				'desc_tip'    => true,
			],
			'method_free_shipping_label' => [
				'title'       => __( 'Free Shipping Label', 'flexible-shipping' ),
				'type'        => 'text',
				'default'     => $this->get_value_from_settings( $method_settings, 'method_free_shipping_label', '' ),
				'description' => __( 'Enter the text for the additional shipping method\'s label which will be displayed once the free shipping is triggered or calculated.', 'flexible-shipping' ),
				'desc_tip'    => true,
			],
			WPDesk_Flexible_Shipping::SETTING_METHOD_FREE_SHIPPING_NOTICE => [
				'title'       => __( '\'Left to free shipping\' notice', 'flexible-shipping' ),
				'type'        => 'checkbox',
				'default'     => $this->get_value_from_settings( $method_settings, WPDesk_Flexible_Shipping::SETTING_METHOD_FREE_SHIPPING_NOTICE, 'no' ),
				'label'       => __( 'Display the notice with the amount of price left to free shipping', 'flexible-shipping' ),
				'desc_tip'    => __( 'Tick this option to display the notice in the cart and on the checkout page.', 'flexible-shipping' ),
				'description' => sprintf(
				// Translators: documentation link.
					__( 'Learn %1$show to customize the displayed notice &rarr;%2$s', 'flexible-shipping' ),
					sprintf( '<a href="%s" target="_blank">', esc_url( get_locale() === 'pl_PL' ? 'https://wpde.sk/fs-fsn-pl' : 'https://wpde.sk/fs-fsn' ) ),
					'</a>'
				) . '<br /><br />' . __( 'Please mind that if you use any additional plugins to split the shipment into packages, the \'Left to free shipping notice\' will not be displayed.', 'flexible-shipping' ),
			],
			self::SETTING_METHOD_CALCULATION_METHOD => [
				'title'       => __( 'Rules Calculation', 'flexible-shipping' ),
				'type'        => 'select',
				'description' => __( 'Select how rules will be calculated. If you choose "sum" the rules order is important.', 'flexible-shipping' ),
				'default'     => $this->get_value_from_settings( $method_settings, self::SETTING_METHOD_CALCULATION_METHOD, '' ),
				'desc_tip'    => true,
				'options'     => ( new CalculationMethodOptions() )->get_options(),
			],
			self::CART_CALCULATION => [
				'title'       => __( 'Cart Calculation', 'flexible-shipping' ),
				'type'        => 'select',
				'default'     => $this->get_value_from_settings( $method_settings, self::CART_CALCULATION, isset( $method_settings[ self::METHOD_DESCRIPTION ] ) ? CartCalculationOptions::CART : CartCalculationOptions::PACKAGE ),
				'options'     => ( new CartCalculationOptions() )->get_options(),
				'description' => __( 'Choose Package value to exclude virtual products from rules calculation.', 'flexible-shipping' ),
				'desc_tip'    => true,
			],
			'method_visibility' => [
				'title'   => __( 'Visibility', 'flexible-shipping' ),
				'type'    => 'checkbox',
				'default' => $this->get_value_from_settings( $method_settings, 'method_visibility', 'no' ),
				'label'   => __( 'Show only for logged in users', 'flexible-shipping' ),
			],
			self::METHOD_DEFAULT => [
				'title'   => __( 'Default', 'flexible-shipping' ),
				'type'    => 'checkbox',
				'default' => $this->get_value_from_settings( $method_settings, self::METHOD_DEFAULT, 'no' ),
				'label'   => __( 'Check the box to set this option as the default selected choice on the cart page.', 'flexible-shipping' ),
			],
			'method_debug_mode' => [
				'title'       => __( 'FS Debug Mode', 'flexible-shipping' ),
				'type'        => 'checkbox',
				'default'     => $this->get_value_from_settings( $method_settings, 'method_debug_mode', 'no' ),
				'label'       => __( 'Enable FS Debug Mode', 'flexible-shipping' ),
				'description' => sprintf(
				// Translators: documentation link.
					__( 'Enable FS debug mode to verify the shipping methods\' configuration, check which one was used and how the shipping cost was calculated as well as identify any possible mistakes. %1$sLearn more how the Debug Mode works →%2$s', 'flexible-shipping' ),
					'<a href="' . ( 'pl_PL' !== get_locale() ? 'https://docs.flexibleshipping.com/article/916-flexible-shipping-debug-mode/?utm_source=flexible-shipping-method&utm_medium=link&utm_campaign=flexible-shipping-debug-mode' : 'https://www.wpdesk.pl/docs/tryb-debugowania-flexible-shipping/?utm_source=flexible-shipping-method&utm_medium=link&utm_campaign=flexible-shipping-debug-mode' ) . '" target="_blank">',
					'</a>'
				),
			],
		];

		if ( $with_integration_settings ) {
			$settings = $this->append_integration_settings_if_present( $settings, $method_settings );
		}

		if ( isset( $settings['method_max_cost'] ) ) {
			$this->settings['method_max_cost'] = $settings['method_max_cost']['default'];
		}

		$settings[ self::METHOD_RULES ] = [
			'title'            => __( 'Shipping Cost Calculation Rules', 'flexible-shipping' ),
			'type'             => RulesSettingsField::FIELD_TYPE,
			'default'          => $this->get_value_from_settings( $method_settings, self::METHOD_RULES, ( new DefaultRulesSettings() )->get_normalized_settings() ),
			self::METHOD_TITLE => $this->get_value_from_settings( $method_settings, self::METHOD_TITLE, __( 'Flexible Shipping', 'flexible-shipping' ) ),
		];

		return $settings;
	}

	/**
	 * @param array $settings        .
	 * @param array $method_settings .
	 *
	 * @return array
	 */
	private function append_integration_settings_if_present( array $settings, $method_settings ) {
		$integrations_options = apply_filters( 'flexible_shipping_integration_options', [ '' => __( 'None', 'flexible-shipping' ) ] );

		if ( 1 < count( $integrations_options ) ) {
			$settings['title_shipping_integration'] = [
				'title' => __( 'Shipping Integration', 'flexible-shipping' ),
				'type'  => 'title',
			];
			$settings['method_integration']         = [
				'title'    => __( 'Integration', 'flexible-shipping' ),
				'type'     => 'select',
				'desc_tip' => false,
				'options'  => $integrations_options,
				'default'  => $this->get_value_from_settings( $method_settings, 'method_integration' ),
			];
		}

		$filtered_settings = apply_filters( 'flexible_shipping_method_settings', $settings, $method_settings );

		$settings = [];

		foreach ( $filtered_settings as $settings_key => $settings_value ) {
			if ( 'method_enabled' === $settings_key ) {
				$settings['title_general_settings'] = [
					'title' => __( 'General Settings', 'flexible-shipping' ),
					'type'  => 'title',
				];
			}

			if ( 'method_free_shipping_requires' === $settings_key || ( 'method_free_shipping' === $settings_key && ! isset( $settings['method_free_shipping_requires'] ) ) ) {
				$settings['title_free_shipping'] = [
					'title' => __( 'Free Shipping', 'flexible-shipping' ),
					'type'  => 'title',
				];
			}

			if ( 'method_max_cost' === $settings_key || ( self::SETTING_METHOD_CALCULATION_METHOD === $settings_key && ! isset( $settings['method_max_cost'] ) ) ) {
				$settings['title_cost_calculation'] = [
					'title' => __( 'Cost Calculation', 'flexible-shipping' ),
					'type'  => 'title',
				];
			}

			if ( 'method_visibility' === $settings_key ) {
				$settings['title_advanced_options'] = [
					'title' => __( 'Advanced Options', 'flexible-shipping' ),
					'type'  => 'title',
				];
			}

			$settings[ $settings_key ] = $settings_value;
		}

		return $settings;
	}

	/**
	 * @param array        $settings   .
	 * @param string       $field_name .
	 * @param string|array $default    .
	 *
	 * @return string
	 */
	private function get_value_from_settings( array $settings, $field_name, $default = '' ) {
		return isset( $settings[ $field_name ] ) ? $settings[ $field_name ] : $default;
	}
}
