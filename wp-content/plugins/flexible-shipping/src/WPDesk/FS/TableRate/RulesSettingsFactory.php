<?php

namespace WPDesk\FS\TableRate;

/**
 * Class RulesSettingsFactory
 *
 * @package WPDesk\FS\TableRate
 */
class RulesSettingsFactory {
	/**
	 * @var SingleRuleSettings[]
	 */
	private $rules = array();

	/**
	 * @param $settings_array
	 *
	 * @return RulesSettingsFactory
	 */
	public static function create_from_array( $settings_array ) {
		$rules_settings = new self();

		foreach ( $settings_array as $single_rule_settings_row ) {
			$rules_settings->add_single_rule_settings( new SingleRuleSettings( $single_rule_settings_row ) );
		}

		return $rules_settings;
	}

	/**
	 * @return array
	 */
	public function get_normalized_settings() {
		$normalized_settings = [];

		foreach ( $this->rules as $rule ) {
			$normalized_settings[] = $rule->get_normalized_settings();
		}

		return $normalized_settings;
	}

	/**
	 * @param SingleRuleSettings $single_rule_settings
	 */
	private function add_single_rule_settings( SingleRuleSettings $single_rule_settings ) {
		$this->rules[] = $single_rule_settings;
	}
}