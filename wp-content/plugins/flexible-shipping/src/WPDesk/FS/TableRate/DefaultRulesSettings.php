<?php
/**
 * Class DefaultRulesSettings
 *
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate;

use WPDesk\FS\TableRate\Rule\Condition\None;

/**
 * Can provide default settings for rules.
 */
class DefaultRulesSettings {
	const NEW_FIELD = 'new';

	/**
	 * @return array
	 */
	public function get_normalized_settings(): array {
		return apply_filters( 'flexible-shipping/shipping-method/default-rules-settings', $this->get_default_settings() );
	}

	/**
	 * @return array
	 */
	private function get_default_settings(): array {
		return [
			[
				'conditions'     => [
					[
						'condition_id' => None::CONDITION_ID,
					],
				],
				'cost_per_order' => '0',
				self::NEW_FIELD  => true,
			],
		];
	}
}
