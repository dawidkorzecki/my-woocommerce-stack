<?php
/**
 * Class ConditionsFactory
 *
 * @package WPDesk\FS\TableRate\Rule\Condition
 */

namespace WPDesk\FS\TableRate\Rule\Condition;

/**
 * Can provide rules conditions.
 */
class ConditionsFactory {

	/**
	 * @return Condition[]
	 */
	public function get_conditions() {
		$none   = new None( 0 );
		$price  = new Price( 10 );
		$weight = new Weight( 25 );

		$conditions = array(
			$none->get_condition_id()   => $none,
			$price->get_condition_id()  => $price,
			$weight->get_condition_id() => $weight,
		);

		$conditions = apply_filters( 'flexible_shipping_rule_conditions', $conditions );
		$conditions = $this->filter_conditions( $conditions );

		return $this->sort_conditions( $conditions );
	}

	/**
	 * @param Condition[] $conditions .
	 *
	 * @return Condition[]
	 */
	private function filter_conditions( $conditions ) {
		return array_filter(
			$conditions,
			function ( $condition ) {
				return $condition instanceof Condition;
			}
		);
	}

	/**
	 * @param Condition[] $conditions .
	 *
	 * @return Condition[]
	 */
	private function sort_conditions( $conditions ) {
		uasort(
			$conditions,
			function ( Condition $condition1, Condition $condition2 ) {
				return $condition1->get_priority() <=> $condition2->get_priority(); // phpcs:ignore.
			}
		);

		return $conditions;
	}
}
