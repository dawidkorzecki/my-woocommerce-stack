<?php
/**
 * Class RuleAdditionalCostFactory
 *
 * @package WPDesk\FS\TableRate\Rule\Cost
 */

namespace WPDesk\FS\TableRate\Rule\Cost;

/**
 * Can provide rule additional costs.
 */
class RuleAdditionalCostFactory {

	/**
	 * @return array
	 */
	public function get_additional_costs() {
		return apply_filters( 'flexible_shipping_rule_additional_cost', $this->get_built_in_rule_additional_cost_fields() );
	}

	/**
	 * @return array
	 */
	private function get_built_in_rule_additional_cost_fields() {
		return array();
	}

}
