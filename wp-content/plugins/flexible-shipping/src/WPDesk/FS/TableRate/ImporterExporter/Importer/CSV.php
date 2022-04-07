<?php
/**
 * Import shipping methods and rules from CSV.
 *
 * @package WPDesk\FS\TableRate\Importer
 */

namespace WPDesk\FS\TableRate\ImporterExporter\Importer;

use WC_Admin_Settings;
use WPDesk\FS\TableRate\ImporterExporter\Importer\Exception\ImportCSVException;
use WPDesk\FS\TableRate\Rule\Condition\ConditionsFactory;
use WPDesk_Flexible_Shipping;

/**
 * Class CSV
 *
 * @package WPDesk\FS\TableRate\Importer
 */
class CSV extends AbstractImporter {
	const CSV_DELIMITER = ';';

	/**
	 * Delimiter used in CSV file.
	 *
	 * @return string
	 */
	public static function get_csv_delimiter() {
		return apply_filters( 'flexible_shipping_csv_delimiter', self::CSV_DELIMITER );
	}

	/**
	 * Load CSV from file.
	 *
	 * @param string $tmp_name File name.
	 *
	 * @return array
	 */
	private function load_csv_from_file( $tmp_name ) {
		return array_map(
			function ( $v ) {
				return str_getcsv( $v, self::get_csv_delimiter() );
			},
			file( $tmp_name )
		);
	}

	/**
	 * Add columns to row.
	 *
	 * @param array $row     Row.
	 * @param array $columns Columns.
	 *
	 * @return array
	 */
	private function add_columns_to_row( array $row, array $columns ) {
		foreach ( $columns as $col_key => $col ) {
			$row[ $col ] = $row[ $col_key ];
		}

		return $row;
	}

	/**
	 * Convert rows to named values.
	 *
	 * @param array $csv_array CSV.
	 *
	 * @return array
	 */
	private function convert_rows_to_named_values( array $csv_array ) {
		$first   = true;
		$columns = array();
		foreach ( $csv_array as $row_key => $csv_row ) {
			if ( $first ) {
				$columns = $csv_row;
			} else {
				$csv_array[ $row_key ] = $this->add_columns_to_row( $csv_array[ $row_key ], $columns );
			}
			$first = false;
		}

		return $csv_array;
	}


	/**
	 * Create new shipping method.
	 *
	 * @param array $csv_row          CSV row.
	 * @param int   $import_row_count Rows count.
	 *
	 * @return array
	 * @throws ImportCSVException Exception.
	 */
	private function new_shipping_method( array $csv_row, $import_row_count ) {
		$new_shipping_method = array( 'method_enabled' => 'no' );
		if ( ! isset( $csv_row['Method Title'] ) || '' === trim( $csv_row['Method Title'] ) ) {
			throw new ImportCSVException(
				__(
					'Sorry, there has been an error. The CSV is invalid or incorrect file type.',
					'flexible-shipping'
				)
			);
		}

		$new_shipping_method['id']                 = $this->flexible_shipping_method->shipping_method_next_id( $this->shipping_methods );
		$new_shipping_method['id_for_shipping']    = $this->flexible_shipping_method->id . '_' . $this->flexible_shipping_method->instance_id . '_' . $new_shipping_method['id'];
		$new_shipping_method['method_title']       = $this->get_new_method_title( $csv_row['Method Title'] );
		$new_shipping_method['instance_id']        = $this->flexible_shipping_method->instance_id;
		$new_shipping_method['method_description'] = $csv_row['Method Description'];

		if ( '' !== trim( $csv_row['Free Shipping'] ) && ! is_numeric( str_replace( ',', '.', $csv_row['Free Shipping'] ) ) ) {
			throw new ImportCSVException(
				sprintf(
				// Translators: free shipping value and row number.
					__( 'Free Shipping value %1$s is not valid number. Row number %2$d.', 'flexible-shipping' ),
					$csv_row['Free Shipping'],
					$import_row_count
				)
			);
		}
		$new_shipping_method[ WPDesk_Flexible_Shipping::FIELD_METHOD_FREE_SHIPPING ] = str_replace( ',', '.', $csv_row['Free Shipping'] );
		if ( trim( $csv_row['Maximum Cost'] ) !== '' && ! is_numeric( str_replace( ',', '.', $csv_row['Maximum Cost'] ) ) ) {
			throw new ImportCSVException(
				sprintf(
				// Translators: maximum cost value and row number.
					__( 'Maximum Cost value %1$s is not valid number. Row number %2$d.', 'flexible-shipping' ),
					$csv_row['Maximum Cost'],
					$import_row_count
				)
			);
		}
		$new_shipping_method['method_max_cost']           = str_replace( ',', '.', $csv_row['Maximum Cost'] );
		$new_shipping_method['method_calculation_method'] = $csv_row['Calculation Method'];
		$new_shipping_method['cart_calculation']          = isset( $csv_row['Cart Calculation'] ) ? $csv_row['Cart Calculation'] : '';
		if ( ! in_array(
			$new_shipping_method['method_calculation_method'],
			array( 'sum', 'lowest', 'highest' ),
			true
		) ) {
			throw new ImportCSVException(
				sprintf(
				// Translators: row number.
					__( 'Invalid value for Calculation Method in row number %d.', 'flexible-shipping' ),
					$import_row_count
				)
			);
		}
		$new_shipping_method['method_visibility'] = $csv_row['Visibility'];
		if ( 'yes' !== $new_shipping_method['method_visibility'] ) {
			$new_shipping_method['method_visibility'] = 'no';
		}
		$new_shipping_method['method_default'] = $csv_row['Default'];
		if ( 'yes' !== $new_shipping_method['method_default'] ) {
			$new_shipping_method['method_default'] = 'no';
		}
		$new_shipping_method['method_rules'] = array();

		return $new_shipping_method;
	}

	/**
	 * Get numeric value from row.
	 *
	 * @param array  $csv_row          CSV row.
	 * @param string $column           Column.
	 * @param int    $import_row_count Row count.
	 *
	 * @return string
	 * @throws ImportCSVException Exception.
	 */
	private function get_numeric_value_from_row( array $csv_row, $column, $import_row_count ) {
		if ( '' !== trim( $csv_row[ $column ] ) && ! is_numeric( str_replace( ',', '.', $csv_row[ $column ] ) ) ) {
			throw new ImportCSVException(
				sprintf(
				// Translators: column name, value and row number.
					__( '%1$s value %2$s is not valid number. Row number %3$d.', 'flexible-shipping' ),
					$column,
					$csv_row['Min'],
					$import_row_count
				)
			);
		}

		return str_replace( ',', '.', $csv_row[ $column ] );
	}

	/**
	 * Maybe populate and create shipping classes.
	 *
	 * @param string $shipping_classes Shipping Classes.
	 *
	 * @return array
	 * @throws ImportCSVException Exception.
	 */
	private function maybe_populate_and_create_shipping_classes( $shipping_classes ) {
		$data = array();

		$rule_shipping_classes = explode( ',', trim( $shipping_classes ) );

		foreach ( $rule_shipping_classes as $rule_shipping_class ) {
			if ( ! in_array( $rule_shipping_class, array( 'all', 'any', 'none' ), true ) ) {
				$term_id = $this->find_shipping_class_by_name( $rule_shipping_class );
				if ( null === $term_id ) {
					$term_id = $this->create_shipping_class( $rule_shipping_class, $rule_shipping_class );
				}
				$data[] = $term_id;
			} else {
				$data[] = $rule_shipping_class;
			}
		}

		return $data;
	}

	/**
	 * New shipping method rule.
	 *
	 * @param array $csv_row          CSV row.
	 * @param int   $import_row_count Row count.
	 *
	 * @return array
	 * @throws ImportCSVException Exception.
	 */
	private function new_rule( array $csv_row, $import_row_count ) {
		$rule             = array();
		$rule['based_on'] = $csv_row['Based on'];

		$conditions = array_keys( ( new ConditionsFactory() )->get_conditions() );

		if ( ! in_array( $rule['based_on'], $conditions, true ) ) {
			throw new ImportCSVException(
				sprintf(
				// Translators: row number.
					__( 'Invalid value for Based On in row number %d.', 'flexible-shipping' ),
					$import_row_count
				)
			);
		}

		$rule['cost_per_order'] = $this->get_numeric_value_from_row( $csv_row, 'Cost per order', $import_row_count );

		// Conditions.
		$rule['conditions'] = array();

		$condition = array(
			'condition_id' => $csv_row['Based on'],
			'min'          => $this->get_numeric_value_from_row( $csv_row, 'Min', $import_row_count ),
			'max'          => $this->get_numeric_value_from_row( $csv_row, 'Max', $import_row_count ),
		);

		if ( ! empty( $condition['min'] ) || ! empty( $condition['max'] ) ) {
			$rule['conditions'][] = $condition;
		}

		// Shipping Classes.
		$rule['shipping_class'] = trim( $csv_row['Shipping Class'] );

		if ( ! empty( $rule['shipping_class'] ) && 'all' !== $rule['shipping_class'] ) {
			$rule['conditions'][] = array(
				'condition_id'   => 'shipping_class',
				'shipping_class' => $this->maybe_populate_and_create_shipping_classes( $rule['shipping_class'] ),
			);
		}

		// Special Actions.
		if ( 'yes' === $csv_row['Cancel'] ) {
			$rule['special_action'] = 'cancel';
		} elseif ( 'yes' === $csv_row['Stop'] ) {
			$rule['special_action'] = 'stop';
		} else {
			$rule['special_action'] = 'none';
		}

		// Additional Costs.
		$cost_additional = $this->get_numeric_value_from_row( $csv_row, 'Additional cost', $import_row_count );

		if ( ! empty( $cost_additional ) ) {
			$rule['additional_costs'][] = array(
				'additional_cost' => $cost_additional,
				'per_value'       => $this->get_numeric_value_from_row( $csv_row, 'Value', $import_row_count ),
				'based_on'        => $rule['based_on'],
			);
		}

		return $rule;
	}

	/**
	 * Import file.
	 *
	 * @throws ImportCSVException Exception.
	 */
	public function import() {
		$csv_array = $this->load_csv_from_file( $this->file['tmp_name'] );
		$csv_array = $this->convert_rows_to_named_values( $csv_array );

		$first                    = true;
		$current_method_title     = '';
		$method_title             = '';
		$imported_shipping_method = array();
		$import_row_count         = 0;
		foreach ( $csv_array as $row_key => $csv_row ) {
			$import_row_count ++;
			$new_method = false;
			if ( ! $first ) {
				if ( ! isset( $csv_row['Method Title'] ) || $current_method_title !== $csv_row['Method Title'] || ! isset( $csv_row['Based on'] ) || '' === $csv_row['Based on'] ) {
					$new_method = true;

					$imported_shipping_method = $this->new_shipping_method( $csv_row, $import_row_count );

					$current_method_title = $csv_row['Method Title'];
					$method_title         = $imported_shipping_method['method_title'];

				} else {
					$imported_shipping_method['method_rules'][] = $this->new_rule( $csv_row, $import_row_count );
				}
			}
			if ( ! $first ) {
				$this->shipping_methods[ $imported_shipping_method['id'] ] = $imported_shipping_method;
				if ( $new_method ) {
					WC_Admin_Settings::add_message(
						sprintf(
						// Translators: imported method title and method title.
							__( 'Shipping method %1$s imported as %2$s.', 'flexible-shipping' ),
							$current_method_title,
							$method_title
						)
					);
				}
			}
			$first = false;
		}
	}
}
