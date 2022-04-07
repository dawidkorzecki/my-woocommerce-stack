<?php
/**
 * Trait ImporterExporter
 *
 * @package WPDesk\FS\TableRate\ImporterExporter
 */

namespace WPDesk\FS\TableRate\ImporterExporter;

use stdClass;
use WPDesk\FS\TableRate\ImporterExporter\Importer\Exception\ImportCSVException;

/**
 * Trait ShippingClassTrait
 *
 * @package WPDesk\FS\TableRate\ImporterExporter
 */
trait ShippingClassTrait {
	/**
	 * Hashmap for shipping classes with name->term_id data.
	 *
	 * @var stdClass[]
	 */
	public $wc_shipping_classes_hashmap;

	/**
	 * Init action.
	 */
	public function init_shipping_class() {
		$this->wc_shipping_classes_hashmap = $this->prepare_shipping_class_hashmap();
	}

	/**
	 * Prepares hashmap for fast checking the term_id of given shipment class.
	 *
	 * @return array
	 */
	public function prepare_shipping_class_hashmap() {
		$shipping_classes = array();

		foreach ( WC()->shipping()->get_shipping_classes() as $class ) {
			$shipping_classes[ html_entity_decode( $class->name ) ] = (int) $class->term_id;
		}

		return $shipping_classes;
	}

	/**
	 * Find and returns shipping class term id
	 *
	 * @param string $name Shipping class name to search.
	 *
	 * @return int|null Term id
	 */
	public function find_shipping_class_by_name( $name ) {
		$name = html_entity_decode( $name );
		if ( isset( $this->wc_shipping_classes_hashmap[ $name ] ) ) {
			return (int) $this->wc_shipping_classes_hashmap[ $name ];
		}

		return null;
	}

	/**
	 * Creates a shipping class
	 *
	 * @param string $name        Shipping class name.
	 * @param string $description Shipping class description.
	 *
	 * @return int Term id
	 * @throws ImportCSVException When can't create the class.
	 */
	public function create_shipping_class( $name, $description ) {
		$term_id = wp_insert_term( $name, 'product_shipping_class', array( 'description' => $description ) );
		if ( is_wp_error( $term_id ) ) {
			throw new ImportCSVException(
				sprintf(
				// Translators: rule shipping class and wp_error message.
					__( 'Error while creating shipping class: %1$s, %2$s', 'flexible-shipping' ),
					$name,
					$term_id->get_error_message()
				)
			);
		}
		$term_id                                                          = (int) $term_id['term_id'];
		$this->wc_shipping_classes_hashmap[ html_entity_decode( $name ) ] = $term_id;

		return $term_id;
	}
}
