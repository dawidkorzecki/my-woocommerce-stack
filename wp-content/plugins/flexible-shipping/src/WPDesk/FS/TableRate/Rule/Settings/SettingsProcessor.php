<?php
/**
 * Class SettingsProcessor
 *
 * @package WPDesk\FS\TableRate\Rule\Settings
 */

namespace WPDesk\FS\TableRate\Rule\Settings;

use FSVendor\WPDesk\Forms\Field;
use WPDesk\Forms\Field\InputNumberField;
use WPDesk\FS\TableRate\Rule\Condition\Condition;
use WPDesk\FS\TableRate\Rule\Condition\ConditionsFactory;
use WPDesk\FS\TableRate\Rule\Cost\Additional;
use WPDesk\FS\TableRate\Rule\Cost\AdditionalCost;
use WPDesk\FS\TableRate\Rule\Cost\Cost;
use WPDesk\FS\TableRate\Rule\SpecialAction\SpecialAction;

/**
 * Can process rules settings.
 */
class SettingsProcessor {

	/**
	 * @var array
	 */
	private $posted_settings;

	/**
	 * @var Condition[]
	 */
	private $conditions;

	/**
	 * @var Field[]
	 */
	private $costs_fields;

	/**
	 * @var Field[]
	 */
	private $additional_costs_fields;

	/**
	 * @var Field[]
	 */
	private $special_action_fields;

	/**
	 * SettingsProcessor constructor.
	 *
	 * @param array       $posted_settings .
	 * @param Condition[] $conditions .
	 * @param Field[]     $costs_fields .
	 * @param Field[]     $additional_costs_fields .
	 * @param Field[]     $special_action_fields .
	 */
	public function __construct(
		array $posted_settings,
		array $conditions,
		array $costs_fields,
		array $additional_costs_fields,
		array $special_action_fields
	) {
		$this->posted_settings         = $posted_settings;
		$this->conditions              = $conditions;
		$this->costs_fields            = $costs_fields;
		$this->additional_costs_fields = $additional_costs_fields;
		$this->special_action_fields   = $special_action_fields;
	}

	/**
	 * @return array
	 */
	public function prepare_settings() {
		$settings = array();

		foreach ( $this->posted_settings as $rule_id => $rule_posted_settings ) {
			$rule_settings = array(
				'conditions' => $this->prepare_conditions( $rule_posted_settings['conditions'] ),
			);
			$rule_settings = $this->prepare_costs( $rule_settings, $rule_posted_settings );
			$rule_settings = $this->prepare_costs_additional( $rule_settings, $rule_posted_settings );
			$rule_settings = $this->prepare_special_action( $rule_settings, $rule_posted_settings );
			$settings[] = $rule_settings;
		}

		return $settings;

	}

	/**
	 * @param array $rule_settings .
	 * @param array $rule_posted_settings .
	 *
	 * @return array
	 */
	private function prepare_costs( array $rule_settings, array $rule_posted_settings ) {
		foreach ( $this->costs_fields as $field ) {
			$rule_settings_value = isset( $rule_posted_settings[ $field->get_name() ] ) ? $rule_posted_settings[ $field->get_name() ] : '';
			$rule_settings[ $field->get_name() ] = $this->sanitize_and_format_field( $rule_settings_value, $field );
		}
		return $rule_settings;
	}

	/**
	 * @param array $rule_settings .
	 * @param array $rule_posted_settings .
	 *
	 * @return array
	 */
	private function prepare_costs_additional( array $rule_settings, array $rule_posted_settings ) {
		$rule_settings['additional_costs'] = array();
		if ( isset( $rule_posted_settings['additional_costs'] ) && is_array( $rule_posted_settings['additional_costs'] ) ) {
			foreach ( $rule_posted_settings['additional_costs'] as $posted_additional_cost ) {
				$additional_cost = array();
				foreach ( $this->additional_costs_fields as $field ) {
					$additional_cost_value = isset( $posted_additional_cost[ $field->get_name() ] ) ? $posted_additional_cost[ $field->get_name() ] : '';
					$additional_cost[ $field->get_name() ] = $this->sanitize_and_format_field( $additional_cost_value, $field );
				}
				$rule_settings['additional_costs'][] = $additional_cost;
			}
		}

		return $rule_settings;
	}

	/**
	 * @param array $rule_settings .
	 * @param array $rule_posted_settings .
	 *
	 * @return array
	 */
	private function prepare_special_action( array $rule_settings, array $rule_posted_settings ) {
		foreach ( $this->special_action_fields as $field ) {
			$rule_settings[ $field->get_name() ] = isset( $rule_posted_settings[ $field->get_name() ] ) ? sanitize_text_field( $rule_posted_settings[ $field->get_name() ] ) : '';
		}

		return $rule_settings;
	}

	/**
	 * @param array $rule_conditions .
	 *
	 * @return array
	 */
	private function prepare_conditions( array $rule_conditions ) {
		$conditions = array();
		foreach ( $rule_conditions as $rule_condition ) {
			$sanitized_condition = sanitize_key( $rule_condition['condition_id'] );
			$condition_normalized = array( 'condition_id' => $sanitized_condition );
			if ( isset( $this->conditions[ $sanitized_condition ] ) ) {
				list( $rule_condition, $condition_normalized ) = $this->prepare_condition_fields( $sanitized_condition, $rule_condition, $condition_normalized );
				$conditions[] = $condition_normalized;
			}
		}

		return $conditions;
	}

	/**
	 * @param string $sanitized_condition .
	 * @param array  $rule_condition .
	 * @param array  $condition_normalized .
	 *
	 * @return array
	 */
	private function prepare_condition_fields( $sanitized_condition, $rule_condition, array $condition_normalized ) {
		foreach ( $this->conditions[ $sanitized_condition ]->get_fields() as $field ) {
			$rule_condition_value = isset( $rule_condition[ $field->get_name() ] ) ? $rule_condition[ $field->get_name() ] : '';
			$condition_normalized[ $field->get_name() ] = $this->sanitize_and_format_field( $rule_condition_value, $field );
		}

		return array( $rule_condition, $condition_normalized );
	}

	/**
	 * @param string|array $value .
	 * @param Field        $field .
	 */
	private function sanitize_and_format_field( $value, $field ) {
		if ( $field instanceof Field\InputNumberField ) {
			return $this->format_number_field( sanitize_text_field( $value ) );
		} else {
			return $field->is_multiple() ? array_map( 'sanitize_text_field', is_array( $value ) ? $value : array() ) : sanitize_text_field( $value );
		}
	}

	/**
	 * @param string $value .
	 *
	 * @return string
	 */
	private function format_number_field( $value ) {
		return str_replace( ',', '.', $value );
	}

}
