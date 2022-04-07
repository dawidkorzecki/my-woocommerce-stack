<?php
/**
 * Class CostsCalculator
 *
 * @package WPDesk\FS\TableRate\Rule
 */

namespace WPDesk\FS\TableRate\Rule;

use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\FS\TableRate\Logger\ArrayLogger;
use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettings;
use Psr\Log\LoggerInterface;
use WC_Cart;
use WPDesk\FS\TableRate\Rule;
use WPDesk\FS\TableRate\Rule\Condition\Weight;
use WPDesk\FS\TableRate\Rule\Condition\ConditionsFactory;
use WPDesk\FS\TableRate\Rule\Cost\AdditionalCost;
use WPDesk\FS\TableRate\Rule\RoundingPrecision;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;
use WPDesk\FS\TableRate\Rule\SpecialAction\SpecialAction;
use WPDesk\FS\TableRate\RulesSettingsFactory;

/**
 * Can calculate shipping costs.
 */
class CostsCalculator {

	/**
	 * @var MethodSettings
	 */
	private $method_settings;

	/**
	 * @var Rule\Rule[]
	 */
	private $prepared_rules = [];

	/**
	 * @var float
	 */
	private $calculated_cost = 0.0;

	/**
	 * @var bool
	 */
	private $is_triggered = false;

	/**
	 * @var Rule\Condition\Condition[]
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
	 * @var ShippingContents
	 */
	private $shipping_contents;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var string
	 */
	private $shop_currency;

	/**
	 * CostsCalculator constructor.
	 *
	 * @param MethodSettings             $method_settings .
	 * @param ShippingContents           $shipping_contents .
	 * @param Rule\Condition\Condition[] $available_conditions .
	 * @param Field[]                    $cost_fields .
	 * @param AdditionalCost[]           $available_additional_costs .
	 * @param SpecialAction[]            $available_special_actions .
	 * @param int                        $cost_rounding_precision .
	 * @param string                     $shop_currency .
	 * @param LoggerInterface            $logger .
	 */
	public function __construct(
		MethodSettings $method_settings,
		ShippingContents $shipping_contents,
		array $available_conditions,
		$cost_fields,
		$available_additional_costs,
		$available_special_actions,
		$cost_rounding_precision,
		$shop_currency,
		$logger
	) {
		$this->method_settings            = $method_settings;
		$this->shipping_contents          = $shipping_contents;
		$this->available_conditions       = $available_conditions;
		$this->cost_fields                = $cost_fields;
		$this->available_additional_costs = $available_additional_costs;
		$this->available_special_actions  = $available_special_actions;
		$this->cost_rounding_precision    = $cost_rounding_precision;
		$this->shop_currency              = $shop_currency;
		$this->logger                     = $logger;
		$this->prepared_rules             = $this->prepare_rules();
	}

	/**
	 * .
	 */
	public function process_rules() {
		$this->shipping_contents->set_weight_rounding_precision( $this->calculate_weight_rounding_precision( $this->prepared_rules ) );
		$this->calculated_cost = $this->calculate_cost();
	}

	/**
	 * @return Rule\Rule[]
	 */
	private function prepare_rules() {
		$prepared_rules = [];
		foreach ( $this->get_rules_settings() as $rule_settings ) {
			$prepared_rules[] = new Rule\Rule(
				$rule_settings,
				$this->available_conditions,
				$this->cost_fields,
				$this->available_additional_costs,
				$this->available_special_actions,
				$this->cost_rounding_precision,
				$this->method_settings
			);
		}

		return $prepared_rules;
	}

	/**
	 * @return array
	 */
	private function get_rules_settings() {
		$rules_settings = $this->method_settings->get_rules_settings();
		$settings = RulesSettingsFactory::create_from_array( $rules_settings );
		return $settings->get_normalized_settings();
	}

	/**
	 * @param float $calculated_cost .
	 * @param float $rule_cost .
	 *
	 * @return float
	 *
	 * @internal
	 */
	public function sum_calculation( $calculated_cost, $rule_cost ) {
		if ( null === $calculated_cost ) {
			$calculated_cost = 0.0;
		}

		return $calculated_cost + $rule_cost;
	}

	/**
	 * @return float
	 */
	private function calculate_cost() {
		$calculated_cost = null;

		/**
		 * Rules calculation function.
		 * Default rules calculation is sum.
		 *
		 * @param callback $callbac Callback function.
		 * @param string   $callback_setting Callback setting.
		 */
		$calculation_method_callback = apply_filters(
			'flexible-shipping/shipping-method/rules-calculation-function',
			[ $this, 'sum_calculation' ],
			$this->method_settings->get_calculation_method()
		);

		$this->shipping_contents->reset_contents();
		foreach ( $this->prepared_rules as $rule_index => $calculated_rule ) {
			$this->shipping_contents = $calculated_rule->process_shipping_contents( $this->shipping_contents );

			$rule_cost = 0.0;
			$rule_logger = new ArrayLogger();
			$is_rule_triggered = false;
			if ( $calculated_rule->is_rule_triggered( $this->shipping_contents, $rule_logger ) ) {
				$is_rule_triggered = true;
				$this->is_triggered = true;
				$rule_cost = $calculated_rule->get_rule_cost( $this->shipping_contents, $rule_logger );
				$calculated_cost = $calculation_method_callback( $calculated_cost, $rule_cost );
			}

			$this->logger->debug( $calculated_rule->format_for_log( $rule_index + 1 ), $this->logger->get_rule_context( $is_rule_triggered ) );

			$this->logger->log_from_array_logger( $rule_logger, $this->logger->get_rule_context( $is_rule_triggered ) );

			if ( $is_rule_triggered ) {
				$this->logger->debug(
					// Translators: rule cost.
					sprintf( '   ' . __( 'Calculated rule cost: %1$s %2$s', 'flexible-shipping' ), $rule_cost, $this->shop_currency ),
					$this->logger->get_rule_context( $is_rule_triggered )
				);

				$special_action = $calculated_rule->get_special_action();
				if ( $special_action->is_stop() ) {
					break;
				}
				if ( $special_action->is_cancel() ) {
					$this->is_triggered = false;
					break;
				}
			}

			$this->shipping_contents->reset_contents();
		}

		if ( null === $calculated_cost ) {
			$calculated_cost = 0.0;
		}

		/**
		 * Calculated shipping method cost.
		 *
		 * @param float $calculated_cost          Calculated cost.
		 * @param array $shipping_method_settings Current shipping method settings.
		 */
		return apply_filters( 'flexible-shipping/shipping-method/calculated-cost', $calculated_cost, $this->method_settings->get_raw_settings() );
	}

	/**
	 * @param Rule\Rule[] $prepared_rules .
	 *
	 * @return int
	 */
	private function calculate_weight_rounding_precision( array $prepared_rules ) {
		$rounding_precision = new RoundingPrecision( $prepared_rules, $this->available_conditions );
		return $rounding_precision->calculate_max_precision_for_condition( Weight::CONDITION_ID );
	}

	/**
	 * @return float
	 */
	public function get_calculated_cost() {
		return $this->calculated_cost;
	}

	/**
	 * @return bool
	 */
	public function is_triggered() {
		return $this->is_triggered;
	}

}
