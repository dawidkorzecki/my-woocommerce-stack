<?php
/**
 * Class SingleRuleSettings
 *
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate;

/**
 * Can convert single rule settings to new format.
 */
class SingleRuleSettings {

	/**
	 * @var array
	 */
	private $rule_settings;

	/**
	 * SingleRuleSettings constructor.
	 *
	 * @param array $rule_settings .
	 */
	public function __construct( $rule_settings ) {
		$this->rule_settings = $this->convert_settings_if_old_format( $rule_settings );
	}

	/**
	 * Convert settings to actual format when in old format.
	 * Old format is detected when based_on setting exists.
	 *
	 * @param array $rule_settings .
	 *
	 * @return array
	 */
	private function convert_settings_if_old_format( $rule_settings ) {
		if ( ! isset( $rule_settings['conditions'] ) ) {
			$converted_rule_settings = $rule_settings;
			$converted_rule_settings['conditions'] = array();
			if ( isset( $converted_rule_settings['based_on'] ) ) {
				if ( in_array( $converted_rule_settings['based_on'], array( 'none', 'value', 'weight' ), true )
					&& ( ! empty( $converted_rule_settings['min'] ) || ! empty( $converted_rule_settings['max'] ) )
				) {
					$condition                               = array(
						'condition_id' => $converted_rule_settings['based_on'],
						'min'          => isset( $converted_rule_settings['min'] ) ? $converted_rule_settings['min'] : '',
						'max'          => isset( $converted_rule_settings['max'] ) ? $converted_rule_settings['max'] : '',
					);
					$converted_rule_settings['conditions'][] = $condition;
				}
			}
			unset( $converted_rule_settings['based_on'] );
			unset( $converted_rule_settings['min'] );
			unset( $converted_rule_settings['max'] );

			$rule_settings = apply_filters( 'flexible_shipping_converted_rule_settings', $converted_rule_settings, $rule_settings );

			if ( empty( $rule_settings['conditions'] ) ) {
				$rule_settings['conditions'] = array( array( 'condition_id' => 'none' ) );
			}
		}

		return $rule_settings;
	}

	/**
	 * @return array
	 */
	public function get_normalized_settings() {
		return $this->rule_settings;
	}

}
