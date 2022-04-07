<?php
/**
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate;

use WPDesk\FS\TableRate\Rule\Condition\ConditionsFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleAdditionalCostFieldsFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleAdditionalCostFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleCostFieldsFactory;
use WPDesk\FS\TableRate\Rule\PreconfiguredScenarios\PreconfiguredScenariosFactory;
use WPDesk\FS\TableRate\Rule\PreconfiguredScenarios\Tracker\AjaxTracker;
use WPDesk\FS\TableRate\Rule\SpecialAction\SpecialActionFieldsFactory;
use WPDesk\FS\TableRate\Rule\SpecialAction\SpecialActionFactory;

/**
 * Class RulesSettings
 */
class RulesSettingsField {

	const FIELD_TYPE = 'shipping_rules';

	/**
	 * @var string
	 */
	protected static $assets_url;

	/**
	 * @var string
	 */
	private $settings_field_id;

	/**
	 * @var string
	 */
	private $settings_field_name;

	/**
	 * @var string
	 */
	private $settings_field_title;

	/**
	 * @var array
	 */
	private $settings;

	/**
	 * @var array
	 */
	private $value;

	/**
	 * RulesSettings constructor.
	 *
	 * @param string $settings_field_id    .
	 * @param string $settings_field_name  .
	 * @param string $settings_field_title .
	 * @param array  $settings             .
	 * @param array  $value                .
	 */
	public function __construct( $settings_field_id, $settings_field_name, $settings_field_title, $settings, $value = null ) {
		$this->settings_field_id    = $settings_field_id;
		$this->settings_field_name  = $settings_field_name;
		$this->settings_field_title = $settings_field_title;
		$this->settings             = $settings;
		$this->value                = $value;
	}

	/**
	 * @param string $assets_url .
	 */
	public static function set_assets_url( $assets_url ) {
		self::$assets_url = $assets_url;
	}

	/**
	 * Render settings.
	 *
	 * @return string
	 */
	public function render() {
		ob_start();
		$settings_field_id    = $this->settings_field_id;
		$settings_field_name  = $this->settings_field_name;
		$settings_field_title = $this->settings_field_title;
		$available_conditions = $this->get_available_conditions();
		$rules_settings       = $this->get_normalized_settings( $available_conditions );
		$available_conditions = array_values( $available_conditions );
		$translations         = $this->get_translations();
		$rules_table_settings = $this->get_table_settings();

		$cost_settings_fields    = $this->get_available_cost_settings();
		$additional_cost_fields  = $this->get_additional_cost_fields();
		$special_action_fields   = $this->get_special_actions_fields();
		$preconfigured_scenarios = $this->get_preconfigured_scenarios();

		include __DIR__ . '/views/shipping-method-settings-rules.php';

		return ob_get_clean();
	}

	/**
	 * @return array
	 */
	private function get_translations() {
		return array(
			'assets_url'                  => self::$assets_url,
			'ajax_url_scenarios_tracking' => admin_url( 'admin-ajax.php?action=' . AjaxTracker::AJAX_ACTION ),
			'scenarios_tracking_nonce'    => wp_create_nonce( AjaxTracker::AJAX_ACTION ),
			'scenarios_docs_link'         =>
				'pl_PL' === get_locale()
					? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=fs&utm_medium=link&utm_campaign=scenariusze#przyklady-zastosowania-flexible-shipping'
					: 'https://docs.flexibleshipping.com/category/24-ready-to-use-scenarios?utm_source=fs&utm_medium=link&utm_campaign=scenarios',
		);
	}

	/**
	 * @return array
	 */
	private function get_available_cost_settings() {
		$rule_costs_fields_factory = new RuleCostFieldsFactory();

		return $rule_costs_fields_factory->get_normalized_cost_fields();
	}

	/**
	 * @return array
	 */
	private function get_additional_cost_fields() {
		$rule_additional_costs_fields_factory = new RuleAdditionalCostFieldsFactory( ( new RuleAdditionalCostFactory() )->get_additional_costs() );

		return $rule_additional_costs_fields_factory->get_normalized_cost_fields();
	}

	/**
	 * @return array
	 */
	private function get_special_actions_fields() {
		$special_actions_fields_factory = new SpecialActionFieldsFactory( ( new SpecialActionFactory() )->get_special_actions() );

		return $special_actions_fields_factory->get_normalized_cost_fields();
	}

	/**
	 * @retrun array
	 */
	private function get_preconfigured_scenarios() {
		return ( new PreconfiguredScenariosFactory() )->get_scenarios();
	}

	/**
	 * @return array
	 */
	private function get_table_settings() {
		return ( new RulesTableSettings() )->get_table_settings();
	}

	/**
	 * @return Rule\Condition\Condition[]
	 */
	private function get_available_conditions() {
		return ( new ConditionsFactory() )->get_conditions();
	}

	/**
	 * @param Rule\Condition\Condition[] $available_conditions .
	 *
	 * @return array
	 */
	private function get_normalized_settings( $available_conditions ) {
		$rules_settings = RulesSettingsFactory::create_from_array( $this->get_field_value() );

		return $this->process_select_options_for_conditions( $rules_settings->get_normalized_settings(), $available_conditions );
	}

	/**
	 * @return array
	 */
	private function get_field_value() {
		return ! isset( $this->value ) ? $this->settings['default'] : $this->value;
	}

	/**
	 * @param array                      $settings             .
	 * @param Rule\Condition\Condition[] $available_conditions .
	 *
	 * @return array
	 */
	private function process_select_options_for_conditions( array $settings, $available_conditions ) {
		foreach ( $settings as $rule_key => $rule ) {
			$conditions = isset( $rule['conditions'] ) && is_array( $rule['conditions'] ) ? $rule['conditions'] : array();
			foreach ( $conditions as $condition_key => $condition ) {
				if ( isset( $available_conditions[ $condition['condition_id'] ] ) ) {
					$settings[ $rule_key ]['conditions'][ $condition_key ] = $available_conditions[ $condition['condition_id'] ]->prepare_settings( $condition );
				}
			}
		}

		return $settings;
	}
}
