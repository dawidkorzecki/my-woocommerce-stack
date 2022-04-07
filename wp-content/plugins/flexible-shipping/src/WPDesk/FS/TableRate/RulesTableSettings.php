<?php
/**
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate;

/**
 * Rules table settings
 */
class RulesTableSettings {

	const MULTIPLE_CONDITIONS_AVAILABLE       = 'multiple_conditions_available';
	const MULTIPLE_ADDITIONAL_COSTS_AVAILABLE = 'multiple_additional_costs_available';
	const SPECIAL_ACTIONS_AVAILABLE           = 'special_actions_available';

	/**
	 * @return array
	 */
	public function get_table_settings() {
		/**
		 * Rules table settings.
		 *
		 * @param array $settings Table settings.
		 *
		 * @return array Table settings.
		 *
		 * Available settings:
		 *     multiple_conditions_available
		 *     multiple_additional_costs_available
		 *     special_actions_available
		 */
		return apply_filters(
			'flexible_shipping_rules_table_settings',
			array(
				self::MULTIPLE_CONDITIONS_AVAILABLE       => false,
				self::MULTIPLE_ADDITIONAL_COSTS_AVAILABLE => false,
				self::SPECIAL_ACTIONS_AVAILABLE           => false,
			)
		);
	}

	/**
	 * @return bool
	 */
	public function is_multiple_conditions_available() {
		return $this->is( self::MULTIPLE_CONDITIONS_AVAILABLE );
	}

	/**
	 * @return bool
	 */
	public function is_multiple_additional_costs_available() {
		return $this->is( self::MULTIPLE_ADDITIONAL_COSTS_AVAILABLE );
	}

	/**
	 * @return bool
	 */
	public function is_special_actions_available() {
		return $this->is( self::SPECIAL_ACTIONS_AVAILABLE );
	}

	/**
	 * @param string $field_name .
	 *
	 * @return bool
	 */
	private function is( $field_name ) {
		$table_settings = $this->get_table_settings();

		return isset( $table_settings[ $field_name ] ) && is_bool( $table_settings[ $field_name ] ) ? $table_settings[ $field_name ] : false;
	}

}
