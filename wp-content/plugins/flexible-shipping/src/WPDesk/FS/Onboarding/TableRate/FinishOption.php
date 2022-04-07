<?php
/**
 * Onboarding clicked option.
 *
 * @package WPDesk\FS\Onboarding
 */

namespace WPDesk\FS\Onboarding\TableRate;

/**
 * Can update option when onboarding is finish.
 */
class FinishOption {
	const OPTION_NAME = 'flexible_shipping_onboarding_table_rate';

	/**
	 * Get option value.
	 *
	 * @param string $key     .
	 * @param mixed  $default .
	 *
	 * @return mixed
	 */
	public function get_option_value( string $key = '', $default = false ) {
		$options = $this->get_options();

		if ( $key ) {
			return $options[ $key ] ?? $default;
		}

		return $options;
	}

	/**
	 * Checks if option is set.
	 *
	 * @return bool Option status.
	 */
	public function is_option_set(): bool {
		return false !== get_option( self::OPTION_NAME, false );
	}

	/**
	 * @param string $option_key   .
	 * @param mixed  $option_value .
	 *
	 * @return bool
	 */
	public function update_option( string $option_key, $option_value ): bool {
		$options = $this->get_options();

		$options[ $option_key ] = $option_value;

		return update_option( self::OPTION_NAME, $options );
	}

	/**
	 * @return array
	 */
	private function get_options(): array {
		$options = get_option( self::OPTION_NAME, [] );

		if ( ! is_array( $options ) ) {
			$options = [];
		}

		return wp_parse_args( $options, $this->get_default_option_values() );
	}

	/**
	 * @return array
	 */
	private function get_default_option_values(): array {
		return [
			'clicks'          => 0,
			'event'           => 'none',
			'auto_show_popup' => 0,
			'step'            => 0,
		];
	}
}
