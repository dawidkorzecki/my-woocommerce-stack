<?php
/**
 * Class Rule
 *
 * @package WPDesk\FS\TableRate\Calculate
 */

namespace WPDesk\FS\TableRate\Rule;

use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\FS\TableRate\Logger\ArrayLogger;
use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettings;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Rule\Condition\Condition;
use WPDesk\FS\TableRate\Rule\Cost\AdditionalCost;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;
use WPDesk\FS\TableRate\Rule\SpecialAction\None;
use WPDesk\FS\TableRate\Rule\SpecialAction\SpecialAction;

/**
 * Single rule.
 */
class Rule {

	const CONDITION_ID = 'condition_id';
	const CONDITIONS = 'conditions';
	const ADDITIONAL_COSTS = 'additional_costs';
	const SPECIAL_ACTION = 'special_action';

	/**
	 * @var array
	 */
	private $rule_settings;

	/**
	 * @var Condition[]
	 */
	private $available_conditions;

	/**
	 * @var Field[]
	 */
	private $cost_fields;

	/**
	 * @var AdditionalCost[]
	 */
	private $available_additional_costs;

	/**
	 * @var SpecialAction[]
	 */
	private $available_special_actions;

	/**
	 * @var int
	 */
	private $cost_rounding_precision;

	/**
	 * @var MethodSettings
	 */
	private $method_settings;

	/**
	 * Rule constructor.
	 *
	 * @param array            $rule_settings              .
	 * @param Condition[]      $available_conditions       .
	 * @param Field[]          $cost_fields                .
	 * @param AdditionalCost[] $available_additional_costs .
	 * @param SpecialAction[]  $available_special_actions  .
	 * @param int              $cost_rounding_precision    .
	 * @param MethodSettings   $method_settings            .
	 */
	public function __construct(
		$rule_settings,
		array $available_conditions,
		array $cost_fields,
		array $available_additional_costs,
		array $available_special_actions,
		$cost_rounding_precision,
		MethodSettings $method_settings
	) {
		$this->rule_settings              = $rule_settings;
		$this->available_conditions       = $available_conditions;
		$this->cost_fields                = $cost_fields;
		$this->available_additional_costs = $available_additional_costs;
		$this->available_special_actions  = $available_special_actions;
		$this->cost_rounding_precision    = $cost_rounding_precision;
		$this->method_settings            = $method_settings;
	}

	/**
	 * .
	 *
	 * @param ShippingContents $shipping_contents .
	 *
	 * @return ShippingContents
	 */
	public function process_shipping_contents( ShippingContents $shipping_contents ) {
		if ( $this->has_rule_conditions() ) {
			foreach ( $this->rule_settings[ self::CONDITIONS ] as $condition_settings_key => $condition_settings ) {
				if ( isset( $condition_settings[ self::CONDITION_ID ], $this->available_conditions[ $condition_settings[ self::CONDITION_ID ] ] ) ) {
					$condition         = $this->available_conditions[ $condition_settings[ self::CONDITION_ID ] ];
					$shipping_contents = $condition->process_shipping_contents( $shipping_contents, $condition_settings );
				}
			}
		}

		return $shipping_contents;
	}

	/**
	 * @param ShippingContents $shipping_contents .
	 * @param LoggerInterface  $logger            .
	 *
	 * @return bool
	 */
	public function is_rule_triggered( ShippingContents $shipping_contents, LoggerInterface $logger ) {
		$triggered = true;
		if ( $this->has_rule_conditions() ) {
			foreach ( $this->rule_settings[ self::CONDITIONS ] as $condition_settings_key => $condition_settings ) {
				if ( isset( $condition_settings[ self::CONDITION_ID ], $this->available_conditions[ $condition_settings[ self::CONDITION_ID ] ] ) ) {

					$condition           = $this->available_conditions[ $condition_settings[ self::CONDITION_ID ] ];
					$condition_triggered = $condition->is_condition_matched_with_method_settings( $condition_settings, $shipping_contents, $logger, $this->method_settings );
					$triggered           = $triggered && $condition_triggered;
				}
				if ( ! $triggered ) {
					break;
				}
			}
		}

		return $triggered;
	}

	/**
	 * @return bool
	 */
	public function has_rule_conditions() {
		return isset( $this->rule_settings[ self::CONDITIONS ] );
	}

	/**
	 * @param ShippingContents $shipping_contents .
	 * @param LoggerInterface  $logger            .
	 *
	 * @return float
	 */
	public function get_rule_cost( ShippingContents $shipping_contents, LoggerInterface $logger ) {
		$logger->debug( sprintf( '   %1$s', __( 'Rule costs:', 'flexible-shipping' ) ) );
		$cost = 0.0;
		foreach ( $this->cost_fields as $cost_field ) {
			if ( isset( $this->rule_settings[ $cost_field->get_name() ] ) ) {
				$field_cost = (float) $this->rule_settings[ $cost_field->get_name() ];
				$logger->debug( sprintf( '    %1$s: %2$s', $cost_field->get_label(), $field_cost ) );
				$cost += $field_cost;
			}
		}
		$cost += $this->get_additional_costs( $shipping_contents, $logger );

		return $cost;
	}

	/**
	 * @return SpecialAction
	 */
	public function get_special_action() {
		if ( isset( $this->rule_settings[ self::SPECIAL_ACTION ], $this->available_special_actions[ $this->rule_settings[ self::SPECIAL_ACTION ] ] ) ) {
			return $this->available_special_actions[ $this->rule_settings[ self::SPECIAL_ACTION ] ];
		} else {
			return new None();
		}
	}

	/**
	 * @param ShippingContents $shipping_contents .
	 * @param LoggerInterface  $logger            .
	 *
	 * @return float
	 */
	private function get_additional_costs( ShippingContents $shipping_contents, LoggerInterface $logger ) {
		$additional_costs          = 0.0;
		$additional_costs_settings = isset( $this->rule_settings[ self::ADDITIONAL_COSTS ] ) ? $this->rule_settings[ self::ADDITIONAL_COSTS ] : array();
		foreach ( $additional_costs_settings as $additional_cost_setting ) {
			if ( isset( $this->available_additional_costs[ $additional_cost_setting['based_on'] ] ) ) {
				$additional_costs += $this->available_additional_costs[ $additional_cost_setting['based_on'] ]->calculate_cost_with_method_settings( $shipping_contents, $additional_cost_setting, $logger, $this->method_settings );
			}
		}

		return $additional_costs;
	}

	/**
	 * @return array
	 */
	public function get_rules_settings() {
		return $this->rule_settings;
	}

	/**
	 * @param int $rule_number .
	 *
	 * @return string
	 */
	public function format_for_log( $rule_number ) {
		// Translators: rule number.
		return sprintf( __( 'Rule %1$s:', 'flexible-shipping' ), $rule_number );
	}

}
