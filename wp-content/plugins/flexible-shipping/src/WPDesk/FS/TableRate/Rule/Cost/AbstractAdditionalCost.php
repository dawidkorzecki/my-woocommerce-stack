<?php
/**
 * Class AbstractAdditionalCost
 *
 * @package WPDesk\FS\TableRate\Rule\Cost
 */

namespace WPDesk\FS\TableRate\Rule\Cost;

use FSVendor\WPDesk\Forms\Renderer\JsonNormalizedRenderer;
use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettings;
use JsonSerializable;
use Psr\Log\LoggerInterface;
use Throwable;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;

/**
 * Abstract Additional Cost.
 */
abstract class AbstractAdditionalCost implements AdditionalCost, JsonSerializable {

	/**
	 * @var string
	 */
	protected $based_on;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var MethodSettings
	 */
	protected $method_settings;

	/**
	 * @return string
	 */
	public function get_based_on() {
		return $this->based_on;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param array $additional_cost_settings .
	 *
	 * @return float|null
	 */
	public function get_per_value( array $additional_cost_settings ) {
		$setting_value = $this->get_settings_value( RuleAdditionalCostFieldsFactory::PER_VALUE, $additional_cost_settings );

		return isset( $setting_value ) ? (float) $setting_value : null;
	}

	/**
	 * @param array $additional_cost_settings .
	 *
	 * @return float|null
	 */
	public function get_additional_cost( array $additional_cost_settings ) {
		$setting_value = $this->get_settings_value( RuleAdditionalCostFieldsFactory::ADDITIONAL_COST, $additional_cost_settings );

		return isset( $setting_value ) ? (float) $setting_value : null;
	}

	/**
	 * @param string $name     .
	 * @param array  $settings .
	 */
	private function get_settings_value( $name, array $settings ) {
		return isset( $settings[ $name ] ) ? $settings[ $name ] : null;
	}

	/**
	 * @param ShippingContents $shipping_contents        .
	 * @param array            $additional_cost_settings .
	 * @param LoggerInterface  $logger                   .
	 *
	 * @return float
	 */
	public function calculate_cost( ShippingContents $shipping_contents, array $additional_cost_settings, LoggerInterface $logger ) {
		$calculated_additional_cost = 0;
		$per_value                  = $this->get_per_value( $additional_cost_settings );
		$additional_cost            = $this->get_additional_cost( $additional_cost_settings );
		$shipment_contents_value    = $this->get_value_from_shipment_contents( $shipping_contents );
		try {
			if ( isset( $per_value, $additional_cost ) && 0.0 !== (float) $per_value ) {
				$calculated_additional_cost = ceil( (float) $shipment_contents_value / (float) $per_value ) * (float) $additional_cost;
			}
		} catch ( Throwable $e ) {
			$logger->debug( $e->getMessage() );
			$calculated_additional_cost = 0;
		}

		$logger->debug(
			sprintf(
				'    %1$s %2$s; %3$s; %4$s; %5$s',
				__( 'additional cost:', 'flexible-shipping' ),
				// Translators: cost per.
				sprintf( __( '%1$s per %2$s', 'flexible-shipping' ), $additional_cost, $per_value ),
				// Translators: based on.
				sprintf( __( 'based on: %1$s', 'flexible-shipping' ), $this->get_name() ),
				// Translators: input data.
				sprintf( __( 'input data: %1$s', 'flexible-shipping' ), $shipment_contents_value ),
				// Translators: calculated.
				sprintf( __( 'calculated: %1$s', 'flexible-shipping' ), $calculated_additional_cost )
			)
		);

		return $calculated_additional_cost;
	}

	/**
	 * @param ShippingContents $shipping_contents        .
	 * @param array            $additional_cost_settings .
	 * @param LoggerInterface  $logger                   .
	 * @param MethodSettings   $method_settings          .
	 *
	 * @return float
	 */
	public function calculate_cost_with_method_settings( ShippingContents $shipping_contents, array $additional_cost_settings, LoggerInterface $logger, MethodSettings $method_settings ) {
		$this->method_settings = $method_settings;

		return $this->calculate_cost( $shipping_contents, $additional_cost_settings, $logger );
	}

	/**
	 * Returns value from shipment contents to calculate cost.
	 *
	 * @param ShippingContents $shipping_contents .
	 *
	 * @return float
	 */
	abstract protected function get_value_from_shipment_contents( $shipping_contents );

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		$renderer = new JsonNormalizedRenderer();

		return array(
			'additional_cost_id' => $this->get_based_on(),
			'label'              => $this->get_name(),
			'parameters'         => $renderer->render_fields( $this, array() ),
		);
	}

}
