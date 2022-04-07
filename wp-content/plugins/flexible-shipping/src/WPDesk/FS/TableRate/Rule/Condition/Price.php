<?php
/**
 * Class Price
 *
 * @package WPDesk\FS\TableRate\Rule\Condition
 */

namespace WPDesk\FS\TableRate\Rule\Condition;

use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\Forms\Field\InputTextField;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Exception\ConditionInvalidNumberValue;
use WPDesk\FS\TableRate\Exception\ConditionRequiredField;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;

/**
 * Price condition.
 */
class Price extends AbstractCondition {

	const MIN = 'min';
	const MAX = 'max';

	const CONDITION_ID = 'value';

	/**
	 * Price constructor.
	 *
	 * @param int $priority .
	 */
	public function __construct( $priority = 10 ) {
		$this->condition_id = self::CONDITION_ID;
		$this->name         = __( 'Price', 'flexible-shipping' );
		$this->description  = __( 'Shipping cost based on the cart total or package value', 'flexible-shipping' );
		$this->group        = __( 'Cart', 'flexible-shipping' );
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
		$min = (float) ( isset( $condition_settings[ self::MIN ] ) && 0 !== strlen( $condition_settings[ self::MIN ] ) ? $condition_settings[ self::MIN ] : 0 );
		$max = (float) ( isset( $condition_settings[ self::MAX ] ) && 0 !== strlen( $condition_settings[ self::MAX ] ) ? $condition_settings[ self::MAX ] : INF );
		$min = (float) apply_filters( 'flexible_shipping_value_in_currency', $min );
		$max = (float) apply_filters( 'flexible_shipping_value_in_currency', $max );

		$contents_cost = $this->get_contents_cost( $contents );

		$condition_matched = $contents_cost >= $min && $contents_cost <= $max;

		$logger->debug( $this->format_for_log( $condition_settings, $condition_matched, $contents_cost ) );

		return $condition_matched;
	}

	/**
	 * @param ShippingContents $contents .
	 *
	 * @return float
	 */
	protected function get_contents_cost( ShippingContents $contents ) {
		$contents_cost =
			/**
			 * Can modify contents cost passed to Price (value) condition.
			 *
			 * @param float $contents_cost Contents cost.
			 *
			 * @since 4.1.1
			 */
			apply_filters( 'flexible-shipping/condition/contents_value', $contents->get_contents_cost() );

		return (float) $contents_cost;
	}

	/**
	 * @return Field[]
	 */
	public function get_fields() {
		return array(
			( new Field\InputNumberField() )
				->set_name( self::MIN )
				->add_class( 'wc_input_decimal' )
				->add_class( 'hs-beacon-search' )
				->add_class( 'parameter_min' )
				->add_data( 'beacon_search', __( 'price is from', 'flexible-shipping' ) )
				->set_placeholder( __( 'min', 'flexible-shipping' ) )
				->set_label( __( 'is from', 'flexible-shipping' ) ),
			( new Field\InputNumberField() )
				->set_name( self::MAX )
				->add_class( 'wc_input_decimal' )
				->add_class( 'hs-beacon-search' )
				->add_class( 'parameter_max' )
				->add_data( 'beacon_search', __( 'price to', 'flexible-shipping' ) )
				->set_placeholder( __( 'max', 'flexible-shipping' ) )
				->set_label( __( 'to', 'flexible-shipping' ) )
				->add_data( 'suffix', get_woocommerce_currency_symbol() ),
		);
	}

}
