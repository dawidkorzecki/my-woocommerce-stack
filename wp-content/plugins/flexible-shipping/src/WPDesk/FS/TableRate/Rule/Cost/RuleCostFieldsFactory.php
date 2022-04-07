<?php
/**
 * Class RuleCostFactory
 *
 * @package WPDesk\FS\TableRate\Rule\Cost
 */

namespace WPDesk\FS\TableRate\Rule\Cost;

use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\Forms\FieldProvider;
use FSVendor\WPDesk\Forms\Renderer\JsonNormalizedRenderer;

/**
 * Can create costs fields.
 */
class RuleCostFieldsFactory implements FieldProvider {

	const COST_PER_ORDER = 'cost_per_order';

	/**
	 * @return Field[]
	 */
	public function get_fields() {
		return apply_filters( 'flexible_shipping_rule_cost_fields', $this->get_built_in_rule_cost_fields() );
	}

	/**
	 * .
	 *
	 * @return array
	 */
	public function get_normalized_cost_fields() {
		$normalized_cost_fields = array();
		$renderer               = new JsonNormalizedRenderer();

		return $renderer->render_fields( $this, array() );
	}

	/**
	 * @return Field[]
	 */
	public function get_built_in_rule_cost_fields() {
		return array(
			( new Field\InputNumberField() )
				->set_name( self::COST_PER_ORDER )
				->add_class( 'wc_input_decimal' )
				->add_class( 'hs-beacon-search' )
				->add_class( 'cost_per_order' )
				->add_data( 'beacon_search', __( 'Cost per order', 'flexible-shipping' ) )
				->set_label( __( 'rule cost is', 'flexible-shipping' ) )
				->set_description_tip( __( 'Enter shipment cost for this rule.', 'flexible-shipping' ) )
				->add_data( 'suffix', get_woocommerce_currency_symbol() ),
		);
	}

}
