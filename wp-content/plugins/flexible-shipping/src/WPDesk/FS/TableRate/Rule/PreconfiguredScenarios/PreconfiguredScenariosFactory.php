<?php
/**
 * Class PreconfiguredScenarios
 *
 * @package WPDesk\FS\TableRate\Rule\PreconfiguredScenarios
 */

namespace WPDesk\FS\TableRate\Rule\PreconfiguredScenarios;

/**
 * Can provide preconfigured scenarios.
 */
class PreconfiguredScenariosFactory {

	/**
	 * @return array
	 */
	public function get_scenarios() {
		$scenarios = array();

		$scenarios = $this->add_weight_scenarios( $scenarios );
		$scenarios = $this->add_value_scenarios( $scenarios );

		return apply_filters( 'flexible-shipping/method-rules/predefined-scenarios', $scenarios );
	}

	/**
	 * @param array $scenarios .
	 *
	 * @return PredefinedScenario[]
	 */
	private function add_weight_scenarios( array $scenarios ) {
		$pl = get_locale() === 'pl_PL';
		$url = $pl ? 'https://www.wpdesk.pl/docs/koszt-wysylki-waga-woocommerce/' : 'https://docs.flexibleshipping.com/article/32-weight-based-shipping-woocommerce';
		$scenarios['simple_weight'] = new PredefinedScenario(
			__( 'Weight', 'flexible-shipping' ),
			__( 'Weight-based shipping', 'flexible-shipping' ),
			__( 'Shipping cost increases in line with the cart total weight.', 'flexible-shipping' ),
			$url,
			'[{"conditions":[{"condition_id":"weight","min":"","max":"0.999"}],"cost_per_order":"10","additional_costs":[],"special_action":""},{"conditions":[{"condition_id":"weight","min":"1","max":"3.999"}],"cost_per_order":"11","additional_costs":[],"special_action":""},{"conditions":[{"condition_id":"weight","min":"4","max":"6.999"}],"cost_per_order":"12","additional_costs":[],"special_action":""},{"conditions":[{"condition_id":"weight","min":"7","max":"10"}],"cost_per_order":"13","additional_costs":[],"special_action":""}]'
		);

		return $scenarios;
	}

	/**
	 * @param array $scenarios .
	 *
	 * @return PredefinedScenario[]
	 */
	private function add_value_scenarios( array $scenarios ) {
		$pl = get_locale() === 'pl_PL';
		$url = $pl ? 'https://www.wpdesk.pl/docs/koszt-wysylki-cena-woocommerce/' : 'https://docs.flexibleshipping.com/article/33-price-based-shipping-woocommerce';
		$scenarios['simple_value'] = new PredefinedScenario(
			__( 'Price', 'flexible-shipping' ),
			__( 'Price-based shipping', 'flexible-shipping' ),
			__( 'Shipping cost decreases in line with the cart total. Free shipping once $300 threshold is reached.', 'flexible-shipping' ),
			$url,
			'[{"conditions":[{"condition_id":"value","min":"","max":"99.99"}],"cost_per_order":"20","additional_costs":[],"special_action":"none"},{"conditions":[{"condition_id":"value","min":"100","max":"199.99"}],"cost_per_order":"15","additional_costs":[],"special_action":"none"},{"conditions":[{"condition_id":"value","min":"200","max":"299.99"}],"cost_per_order":"10","additional_costs":[],"special_action":"none"},{"conditions":[{"condition_id":"value","min":"300","max":""}],"cost_per_order":"0","additional_costs":[],"special_action":"none"}]'
		);

		return $scenarios;
	}

}
