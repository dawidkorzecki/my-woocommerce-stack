<?php
/**
 * Class None
 *
 * @package WPDesk\FS\TableRate\Rule\Condition
 */

namespace WPDesk\FS\TableRate\Rule\Condition;

use FSVendor\WPDesk\Forms\Field;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;

/**
 * None Condition.
 */
class None extends AbstractCondition {

	const CONDITION_ID = 'none';

	/**
	 * None constructor.
	 *
	 * @param int $priority .
	 */
	public function __construct( $priority = 10 ) {
		$this->condition_id = self::CONDITION_ID;
		$this->name         = __( 'Always', 'flexible-shipping' );
		$this->description  = __( 'Fixed shipping cost', 'flexible-shipping' );
		$this->priority     = $priority;
	}

	/**
	 * @param array            $condition_settings .
	 * @param ShippingContents $contents           .
	 * @param LoggerInterface  $logger             .
	 *
	 * @return bool
	 */
	public function is_condition_matched( array $condition_settings, ShippingContents $contents, LoggerInterface $logger ) {
		$logger->debug( $this->format_for_log( $condition_settings, true, '' ) );

		return true;
	}

	/**
	 * @return Field[]
	 */
	public function get_fields() {
		return [];
	}

	/**
	 * @param array  $condition_settings .
	 * @param bool   $condition_matched  .
	 * @param string $input_data         .
	 *
	 * @return string
	 */
	protected function format_for_log( array $condition_settings, $condition_matched, $input_data ) {
		// Translators: condition name.
		$formatted_for_log = '   ' . sprintf( __( 'Condition: %1$s;', 'flexible-shipping' ), $this->get_name() );
		// Translators: matched condition.
		$formatted_for_log .= sprintf( __( ' matched: %1$s', 'flexible-shipping' ), $condition_matched ? __( 'yes', 'flexible-shipping' ) : __( 'no', 'flexible-shipping' ) );

		return $formatted_for_log;
	}
}
