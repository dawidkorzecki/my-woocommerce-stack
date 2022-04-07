<?php
/**
 * Class RuleCostFactory
 *
 * @package WPDesk\FS\TableRate\Rule\Cost
 */

namespace WPDesk\FS\TableRate\Rule\Cost;

use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\Forms\Field\InputTextField;
use FSVendor\WPDesk\Forms\FieldProvider;
use FSVendor\WPDesk\Forms\Renderer\JsonNormalizedRenderer;


/**
 * Can create additional costs fields.
 */
class RuleAdditionalCostFieldsFactory implements FieldProvider {

	const ADDITIONAL_COST = 'additional_cost';
	const PER_VALUE       = 'per_value';
	const BASED_ON        = 'based_on';

	/**
	 * @var AdditionalCost[]
	 */
	private $available_additional_costs;

	/**
	 * RuleAdditionalCostFieldsFactory constructor.
	 *
	 * @param AdditionalCost[] $available_additional_costs .
	 */
	public function __construct( array $available_additional_costs ) {
		$this->available_additional_costs = $available_additional_costs;
	}


	/**
	 * @return Field[]
	 */
	public function get_fields() {
		return apply_filters( 'flexible_shipping_rule_additional_cost_fields', $this->get_built_in_rule_additional_cost_fields() );
	}

	/**
	 * .
	 *
	 * @return array
	 */
	public function get_normalized_cost_fields() {
		$normalized_cost_fields = array();
		$renderer               = new JsonNormalizedRenderer();

		return $renderer->render_fields( $this, array() );
	}

	/**
	 * @return Field[]
	 */
	public function get_built_in_rule_additional_cost_fields() {
		return array(
			( new Field\InputNumberField() )
				->set_name( self::ADDITIONAL_COST )
				->add_class( 'wc_input_decimal' )
				->add_class( 'hs-beacon-search' )
				->add_class( 'additional-cost-cost' )
				->add_data( 'beacon_search', __( 'additional cost', 'flexible-shipping' ) )
				->set_placeholder( __( 'additional cost', 'flexible-shipping' ) )
				->set_label( __( 'additional cost is', 'flexible-shipping' ) )
				->add_data( 'suffix', get_woocommerce_currency_symbol() ),
			( new Field\InputNumberField() )
				->set_name( self::PER_VALUE )
				->add_class( 'wc_input_decimal' )
				->add_class( 'hs-beacon-search' )
				->add_class( 'additional-cost-per' )
				->add_data( 'beacon_search', __( 'additional cost per', 'flexible-shipping' ) )
				->set_placeholder( __( 'per', 'flexible-shipping' ) )
				->set_label( __( 'per', 'flexible-shipping' ) ),
			( new Field\SelectField() )
				->set_name( self::BASED_ON )
				->set_options( $this->get_based_on_options() )
				->add_class( 'wc_input_decimal' )
				->add_class( 'hs-beacon-search' )
				->add_class( 'additional-cost-based-on' )
				->add_data( 'beacon_search', __( 'additional cost per', 'flexible-shipping' ) ),
		);
	}

	/**
	 * @return array
	 */
	private function get_based_on_options() {
		$options = array();
		foreach ( $this->available_additional_costs as $additional_cost ) {
			$options[] = array(
				'value' => $additional_cost->get_based_on(),
				'label' => $additional_cost->get_name(),
			);
		}

		return $options;
	}

}
