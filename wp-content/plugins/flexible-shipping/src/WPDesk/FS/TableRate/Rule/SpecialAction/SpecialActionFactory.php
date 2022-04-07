<?php
/**
 * Class SpecialActionFactory
 *
 * @package WPDesk\FS\TableRate\Rule\SpecialAction
 */

namespace WPDesk\FS\TableRate\Rule\SpecialAction;

/**
 * Can provide special actions.
 */
class SpecialActionFactory {

	/**
	 * @return SpecialAction[]
	 */
	public function get_special_actions() {
		$none = new None();
		$special_actions = array(
			$none->get_special_action_id() => $none,
		);

		$special_actions = apply_filters( 'flexible_shipping_special_actions', $special_actions );

		return array_filter(
			$special_actions,
			function ( $special_action ) {
				return $special_action instanceof SpecialAction;
			}
		);
	}

}
