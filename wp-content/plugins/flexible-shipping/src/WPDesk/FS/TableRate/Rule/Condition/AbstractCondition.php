<?php
/**
 * Class AbstractCondition
 *
 * @package WPDesk\FS\TableRate\Rule\Condition
 */

namespace WPDesk\FS\TableRate\Rule\Condition;

use FSVendor\WPDesk\Forms\Field;
use FSVendor\WPDesk\Forms\FieldProvider;
use FSVendor\WPDesk\Forms\Renderer\JsonNormalizedRenderer;
use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettings;
use JsonSerializable;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Rule\ShippingContents\ShippingContents;

/**
 * Abstract condition.
 */
abstract class AbstractCondition implements Condition, FieldProvider, JsonSerializable {

	/**
	 * @var string
	 */
	protected $condition_id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $description = '';

	/**
	 * @var string
	 */
	protected $group;

	/**
	 * @var int
	 */
	protected $priority = 10;

	/**
	 * @return string
	 */
	public function get_condition_id() {
		return $this->condition_id;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @return int
	 */
	public function get_priority() {
		return $this->priority;
	}

	/**
	 * @return string
	 */
	public function get_group() {
		return $this->group ? $this->group : _x( 'General', 'Default Condition Group', 'flexible-shipping' );
	}

	/**
	 * @param ShippingContents $shipping_contents  .
	 * @param array            $condition_settings .
	 *
	 * @return ShippingContents
	 */
	public function process_shipping_contents( ShippingContents $shipping_contents, array $condition_settings ) {
		return $shipping_contents;
	}

	/**
	 * @param array            $condition_settings .
	 * @param ShippingContents $contents           .
	 * @param LoggerInterface  $logger             .
	 *
	 * @return bool
	 */
	public function is_condition_matched( array $condition_settings, ShippingContents $contents, LoggerInterface $logger ) {
		return false;
	}

	/**
	 * @param array            $condition_settings .
	 * @param ShippingContents $contents           .
	 * @param LoggerInterface  $logger             .
	 * @param MethodSettings   $method_settings    .
	 *
	 * @return bool
	 */
	public function is_condition_matched_with_method_settings( array $condition_settings, ShippingContents $contents, LoggerInterface $logger, MethodSettings $method_settings ) {
		return $this->is_condition_matched( $condition_settings, $contents, $logger );
	}

	/**
	 * @return Field[]
	 */
	public function get_fields() {
		return array();
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
		foreach ( $this->get_fields() as $field ) {
			$value = '';
			if ( isset( $condition_settings[ $field->get_name() ] ) ) {
				$value = $condition_settings[ $field->get_name() ];
			}
			if ( $field instanceof Field\SelectField ) {
				$options = $field->get_meta_value( 'possible_values' );
				foreach ( $options as $option ) {
					if ( $option['value'] === $value ) {
						$value = $option['label'];
					}
				}
			}
			$formatted_for_log .= sprintf( ' %1$s: %2$s;', $field->get_name(), is_array( $value ) ? implode( ', ', $value ) : $value );
		}
		// Translators: input data.
		$formatted_for_log .= sprintf( __( ' input data: %1$s;', 'flexible-shipping' ), $input_data );
		// Translators: matched condition.
		$formatted_for_log .= sprintf( __( ' matched: %1$s', 'flexible-shipping' ), $condition_matched ? __( 'yes', 'flexible-shipping' ) : __( 'no', 'flexible-shipping' ) );

		return $formatted_for_log;
	}

	/**
	 * @param array $condition_settings .
	 *
	 * @return array
	 */
	public function prepare_settings( $condition_settings ) {
		return $condition_settings;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		$renderer = new JsonNormalizedRenderer();

		return array(
			'condition_id' => $this->get_condition_id(),
			'label'        => $this->get_name(),
			'group'        => $this->get_group(),
			'description'  => $this->get_description(),
			'parameters'   => $renderer->render_fields( $this, array() ),
		);
	}

}
