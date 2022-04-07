<?php
/**
 * Class SettingsProcessor
 *
 * @package WPDesk\FS\TableRate\ShippingMethod
 */

namespace WPDesk\FS\TableRate\ShippingMethod;

use Exception;
use WPDesk\FS\TableRate\Rule\Condition\ConditionsFactory;
use WPDesk\FS\TableRate\Rule\Settings\Validator\Conditions;
use \WPDesk\FS\TableRate\Rule;
use WPDesk\FS\TableRate\Rule\Cost\RuleAdditionalCostFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleCostFieldsFactory;
use WPDesk_Flexible_Shipping;

/**
 * Can process Flexible Shipping Method settings.
 */
class SettingsProcessor {

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $instance_id;

	/**
	 * @var string
	 */
	private $shipping_methods_option;

	/**
	 * @var string
	 */
	private $shipping_method_order_option;

	/**
	 * Save_Rules constructor.
	 *
	 * @param string $id .
	 * @param string $instance_id .
	 * @param array  $shipping_methods_option .
	 * @param array  $shipping_method_order_option .
	 */
	public function __construct( $id, $instance_id, $shipping_methods_option, $shipping_method_order_option ) {
		$this->id                           = $id;
		$this->instance_id                  = $instance_id;
		$this->shipping_methods_option      = $shipping_methods_option;
		$this->shipping_method_order_option = $shipping_method_order_option;
	}

	/**
	 * @param string $action .
	 * @param array  $posted_data .
	 *
	 * @return array
	 *
	 * @throws Exception .
	 */
	public function process_and_save_settings( $action, $posted_data ) {
		$shipping_method_settings = $this->save_settings( $action, $posted_data );
		$this->add_method_creation_date();

		return $shipping_method_settings;
	}

	/**
	 * @param string $action           .
	 * @param array  $shipping_methods .
	 * @param array  $posted_data      .
	 *
	 * @return string
	 */
	private function get_method_id( $action, $shipping_methods, $posted_data ) {
		if ( 'new' === $action ) {
			$method_id = get_option( 'flexible_shipping_method_id', 0 );

			foreach ( $shipping_methods as $shipping_method ) {
				if ( intval( $shipping_method['id'] ) > $method_id ) {
					$method_id = intval( $shipping_method['id'] );
				}
			}
			$method_id ++;

			return $method_id;
		}

		return sanitize_text_field( wp_unslash( $posted_data['method_id'] ) );
	}

	/**
	 * @param string $action .
	 * @param array  $posted_data .
	 *
	 * @return array
	 */
	private function save_settings( $action, $posted_data ) {
		$shipping_method  = array();
		$shipping_methods = get_option( $this->shipping_methods_option, array() );

		$method_id = $this->get_method_id( $action, $shipping_methods, $posted_data );

		if ( 'new' === $action ) {
			$method_id_for_shipping = $this->id . '_' . $this->instance_id . '_' . $method_id;
		} else {
			$method_id_for_shipping = sanitize_text_field( wp_unslash( $posted_data['method_id_for_shipping'] ) );
			$shipping_method        = isset( $shipping_methods[ $method_id ] ) ? $shipping_methods[ $method_id ] : array();
		}

		$shipping_method = $this->update_shipping_methods( $method_id, $method_id_for_shipping, $shipping_method, $shipping_methods, $posted_data );
		$this->update_rates( $shipping_methods );

		if ( 'new' === $action ) {
			$shipping_method_order = get_option( $this->shipping_method_order_option, array() );

			$shipping_method_order[ $method_id ] = $method_id;

			update_option( $this->shipping_method_order_option, $shipping_method_order );
			update_option( 'flexible_shipping_method_id', $method_id );
		}

		return $shipping_method;
	}

	/**
	 * @param string $method_id .
	 * @param string $method_id_for_shipping .
	 * @param array  $shipping_method .
	 * @param array  $shipping_methods .
	 * @param array  $posted_data .
	 *
	 * @return array
	 */
	private function update_shipping_methods( $method_id, $method_id_for_shipping, $shipping_method, $shipping_methods, $posted_data ) {
		// Prepare fields for settings.
		$shipping_method_settings = $this->prepare_shipping_method_settings( $method_id, $method_id_for_shipping, $posted_data );

		// Add shipping method to list.
		$shipping_methods[ $method_id ] = array_merge( $shipping_method, $shipping_method_settings );

		// Save shipping methods.
		update_option( $this->shipping_methods_option, $shipping_methods );

		return $shipping_method_settings;
	}

	/**
	 * Add method creation date.
	 */
	private function add_method_creation_date() {
		if ( ! get_option( 'flexible_shipping_method_creation_date' ) ) {
			add_option( 'flexible_shipping_method_creation_date', current_time( 'mysql' ) );
		}
	}

	/**
	 * @param array $shipping_methods .
	 */
	private function update_rates( $shipping_methods ) {
		$rates = array();

		foreach ( $shipping_methods as $shipping_method ) {
			$id = sprintf( '%s_%s_%s', $this->id, $this->instance_id, sanitize_title( $shipping_method['method_title'] ) );

			$id = apply_filters( 'flexible_shipping_method_rate_id', $id, $shipping_method );

			if ( ! isset( $rates[ $id ] ) && 'yes' === $shipping_method['method_enabled'] ) {
				$rates[ $id ] = array(
					'identifier' => $id,
					'title'      => $shipping_method['method_title'],
				);
			}
		}
		update_option( 'flexible_shipping_rates', $rates );
	}


	/**
	 * @param int   $method_id .
	 * @param int   $method_id_for_shipping .
	 * @param array $posted_data .
	 *
	 * @return array
	 */
	private function prepare_shipping_method_settings( $method_id, $method_id_for_shipping, $posted_data ) {

		$shipping_method = array(
			'woocommerce_method_instance_id' => $this->instance_id,
			'id'                             => $method_id,
			'id_for_shipping'                => $method_id_for_shipping,
			'method_title'                   => sanitize_text_field( $this->get_field_value( 'method_title', $posted_data ) ),
			'method_description'             => sanitize_text_field( $this->get_field_value( 'method_description', $posted_data ) ),
		);

		$shipping_method[ WPDesk_Flexible_Shipping::FIELD_METHOD_FREE_SHIPPING ] = '';

		$method_free_shipping = $this->get_field_value( 'method_free_shipping', $posted_data );
		if ( ! empty( $method_free_shipping ) ) {
			$shipping_method[ WPDesk_Flexible_Shipping::FIELD_METHOD_FREE_SHIPPING ] = wc_format_decimal( sanitize_text_field( $method_free_shipping ) );
		}

		$method_integration = $this->get_field_value( 'method_integration', $posted_data );
		$shipping_method['method_integration'] = sanitize_text_field( $method_integration );

		$shipping_method['method_free_shipping_label']                                    = sanitize_text_field( $this->get_field_value( 'method_free_shipping_label', $posted_data ) );
		$shipping_method['method_calculation_method']                                     = sanitize_text_field( $this->get_field_value( 'method_calculation_method', $posted_data ) );
		$shipping_method[ WPDesk_Flexible_Shipping::SETTING_METHOD_FREE_SHIPPING_NOTICE ] = $this->get_value_of_yes_no( WPDesk_Flexible_Shipping::SETTING_METHOD_FREE_SHIPPING_NOTICE, $posted_data );

		$shipping_method['method_default']    = $this->get_value_of_yes_no( 'method_default', $posted_data );
		$shipping_method['method_visibility'] = $this->get_value_of_yes_no( 'method_visibility', $posted_data );
		$shipping_method['method_debug_mode'] = $this->get_value_of_yes_no( 'method_debug_mode', $posted_data );
		$shipping_method['method_enabled']    = $this->get_value_of_yes_no( 'method_enabled', $posted_data );

		$shipping_method[ WPDesk_Flexible_Shipping::SETTING_METHOD_RULES ] = $this->get_normalized_rules(
			isset( $posted_data[ WPDesk_Flexible_Shipping::SETTING_METHOD_RULES ] ) ? $posted_data[ WPDesk_Flexible_Shipping::SETTING_METHOD_RULES ] : array()
		);

		return apply_filters( 'flexible_shipping_process_admin_options', $shipping_method );
	}

	/**
	 * @param array $posted_rules_settings .
	 *
	 * @return array
	 */
	private function get_normalized_rules( $posted_rules_settings ) {
		$rules_settings_processor = new Rule\Settings\SettingsProcessor(
			$posted_rules_settings,
			( new ConditionsFactory() )->get_conditions(),
			( new RuleCostFieldsFactory() )->get_fields(),
			( new Rule\Cost\RuleAdditionalCostFieldsFactory( ( new RuleAdditionalCostFactory() )->get_additional_costs() ) )->get_fields(),
			( new Rule\SpecialAction\SpecialActionFieldsFactory( ( new Rule\SpecialAction\SpecialActionFactory() )->get_special_actions() ) )->get_fields()
		);
		return $rules_settings_processor->prepare_settings();
	}

	/**
	 * @param string $name .
	 * @param array  $posted_data .
	 *
	 * @return mixed
	 */
	private function get_field_value( $name, $posted_data ) {
		$key = sprintf( 'woocommerce_%s_%s', $this->id, $name );

		return isset( $posted_data[ $key ] ) ? wp_unslash( $posted_data[ $key ] ) : null;
	}

	/**
	 * @param string $name .
	 * @param array  $posted_data .
	 *
	 * @return string
	 */
	private function get_value_of_yes_no( $name, $posted_data ) {
		return filter_var( $this->get_field_value( $name, $posted_data ), FILTER_VALIDATE_BOOLEAN ) ? 'yes' : 'no';
	}

}
