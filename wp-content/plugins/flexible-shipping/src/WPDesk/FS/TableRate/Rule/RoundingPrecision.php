<?php
/**
 * Class RoundingPrecision
 *
 * @package WPDesk\FS\TableRate\Rule
 */

namespace WPDesk\FS\TableRate\Rule;

use WPDesk\FS\TableRate\Rule\Condition\Condition;
use WPDesk\FS\TableRate\Rule\Condition\Weight;

/**
 * Can calculate rounding precision from conditions settings.
 */
class RoundingPrecision {
	const CONDITION_ID = 'condition_id';

	/**
	 * @var Rule[]
	 */
	private $rules;

	/**
	 * @var Condition[]
	 */
	private $available_conditions;

	/**
	 * @param Rule[]      $rules .
	 * @param Condition[] $available_conditions .
	 */
	public function __construct( array $rules, array $available_conditions ) {
		$this->rules = $rules;
		$this->available_conditions = $available_conditions;
	}

	/**
	 * @param string $condition_id .
	 */
	public function calculate_max_precision_for_condition( $condition_id ) {
		$max_precision = 0;
		foreach ( $this->rules as $prepared_rule ) {
			$max_precision = max( $max_precision, $this->get_max_precision_for_conditions( $prepared_rule, $condition_id ) );
		}

		return $max_precision;
	}

	/**
	 * @param Rule   $prepared_rule .
	 * @param string $condition_id .
	 *
	 * @return int
	 */
	private function get_max_precision_for_conditions( $prepared_rule, $condition_id ) {
		$max_precision = 0;
		if ( $prepared_rule->has_rule_conditions() ) {
			$rule_settings = $prepared_rule->get_rules_settings();
			foreach ( $rule_settings['conditions'] as $condition_settings ) {
				$max_precision = max( $max_precision, $this->get_max_precision_for_single_condition( $condition_settings, $condition_id ) );
			}
		}

		return $max_precision;
	}

	/**
	 * @param array  $condition_settings .
	 * @param string $condition_id .
	 *
	 * @return int mixed
	 */
	private function get_max_precision_for_single_condition( $condition_settings, $condition_id ) {
		$max_precision = 0;
		if ( isset( $condition_settings[ self::CONDITION_ID ], $this->available_conditions[ $condition_settings[ self::CONDITION_ID ] ] )
			&& $condition_id === $condition_settings[ self::CONDITION_ID ]
		) {
			foreach ( $this->available_conditions[ $condition_settings[ self::CONDITION_ID ] ]->get_fields() as $field ) {
				$max_precision = max( $max_precision, $this->get_precision_for_field( $condition_settings, $field->get_name() ) );
			}
		}

		return $max_precision;
	}

	/**
	 * @param array  $condition_settings .
	 * @param string $field_name .
	 *
	 * @return int
	 */
	private function get_precision_for_field( $condition_settings, $field_name ) {
		$field_precision = 0;
		if ( isset( $condition_settings[ $field_name ] ) ) {
			$parts = explode( '.', $condition_settings[ $field_name ] );
			$field_precision = 0;
			if ( isset( $parts[1] ) ) {
				$field_precision = strlen( $parts[1] );
			}
		}

		return $field_precision;
	}

}
