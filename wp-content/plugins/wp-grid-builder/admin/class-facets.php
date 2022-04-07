<?php
/**
 * Facets
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Indexer;
use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle facet actions
 *
 * @class WP_Grid_Builder\Admin\Facets
 * @since 1.0.0
 */
final class Facets extends Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_facet';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct();

		add_action( 'wp_grid_builder/duplicate/facets', [ $this, 'duplicate' ] );
		add_action( 'wp_grid_builder/before_delete/facets', [ $this, 'delete' ] );
		add_action( 'wp_grid_builder/import/facets', [ $this, 'import' ] );

	}

	/**
	 * Save facet settings
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function save() {

		// Load facet setting fields.
		require WPGB_PATH . 'admin/settings/facet.php';

		$facet_id = $this->get_var( 'id' );

		// Process settings.
		$settings = $this->get_var( 'settings' );
		$settings = json_decode( $settings, true );
		$settings = wp_grid_builder()->settings->sanitize( $settings );

		// If facet name empty.
		if ( empty( $settings['name'] ) ) {
			$this->send_response( false, __( 'Please, enter a facet name', 'wp-grid-builder' ) );
		}

		$settings = $this->slugify( $settings );
		$settings = $this->set_type( $settings );
		$settings = $this->set_source( $settings );

		// Check if facet needs to be indexed.
		$should_index = apply_filters( 'wp_grid_builder/index/facet', false, $facet_id, $settings );
		$should_index = $this->can_index() && $should_index;

		$settings = Helpers::maybe_json_encode( $settings );

		try {
			$id = Database::save_row( 'facets', $settings, $facet_id );
		} catch ( \Exception $e ) {
			$this->send_response( false, $e->getMessage() );
		}

		$response = [
			'id'    => $id,
			'index' => $should_index,
		];

		do_action( 'wp_grid_builder/save/facet', $id );

		$this->clear_cache( $id );
		$this->send_response( true, __( 'Settings Saved!', 'wp-grid-builder' ), $response );

	}

	/**
	 * Normalize facet settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facets settings.
	 */
	public function normalize( $facet ) {

		$defaults = require WPGB_PATH . 'admin/settings/defaults/facet.php';
		return wp_parse_args( $facet['settings'], $defaults );

	}

	/**
	 * Generate and set slug if need
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds facets settings.
	 */
	public function slugify( $settings ) {

		// Generate a slug if missing.
		if ( empty( $settings['slug'] ) || ! trim( $settings['slug'] ) ) {
			$settings['slug'] = $settings['name'];
		}

		// Normalize slug.
		$settings['slug'] = sanitize_title( $settings['slug'] );
		$settings['slug'] = str_replace( '-', '_', $settings['slug'] );

		// Set slug.
		$settings['settings']['slug'] = $settings['slug'];

		return $settings;

	}

	/**
	 * Set facet type
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facets settings.
	 */
	public function set_type( $facet ) {

		$settings = $this->normalize( $facet );

		switch ( $settings['action'] ) {
			case 'sort':
			case 'reset':
			case 'apply':
				$type = $settings['action'];
				break;
			case 'load':
				$type = $settings['load_type'];
				break;
			default:
				$type = $settings['filter_type'];
		}

		$facet['type'] = $type;

		return $facet;

	}

	/**
	 * Set facet source
	 * Source is composed of the object type and object name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facets settings.
	 */
	public function set_source( $facet ) {

		$source   = '';
		$settings = $this->normalize( $facet );

		// Reset facet source.
		$facet['source'] = '';

		if (
			'filter' !== $settings['action'] ||
			'selection' === $settings['filter_type']
		) {
			return $facet;
		}

		if ( 'search' === $settings['filter_type'] ) {

			// phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled
			$facet['source'] = ! empty( $settings['search_engine'] ) ? $settings['search_engine'] : 'wordpress';
			return $facet;

		}

		switch ( $settings['source'] ) {
			case 'taxonomy':
				$source  = $settings['source'];
				$source .= '/' . $settings['taxonomy'];
				break;
			case 'metadata':
				$source  = $settings['field_type'];
				$source .= '_meta/' . $settings['meta_key'];
				break;
			case 'field':
				$field   = $settings['field_type'] . '_field';
				$source  = $settings['field_type'];
				$source .= '_field/' . $settings[ $field ];
				break;
		}

		$facet['source'] = $source;

		return $facet;

	}

	/**
	 * Import facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facets Holds facets to import.
	 */
	public function import( $facets ) {

		$facet_ids = [];

		// Load facet setting fields.
		require WPGB_PATH . 'admin/settings/facet.php';

		array_map(
			function( $facet ) use ( &$facet_ids ) {

				// Sanitize all fields and normalize.
				$facet = wp_grid_builder()->settings->sanitize( $facet );
				$facet = $this->slugify( $facet );
				$facet = $this->set_type( $facet );
				$facet = $this->set_source( $facet );
				$facet = Helpers::maybe_json_encode( $facet );

				try {
					$facet_ids[] = Database::import_row( 'facets', $facet );
				} catch ( \Exception $e ) {
					$this->send_response( false, $e->getMessage() );
				}

			},
			(array) $facets
		);

		if ( ! empty( $facet_ids ) && $this->can_index() ) {
			( new Indexer() )->index_facets( $facet_ids );
		}

	}

	/**
	 * Delete facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $ids Holds deleted facet ids.
	 */
	public function delete( $ids ) {

		$indexer = new Indexer();
		$facets  = Database::query_results(
			[
				'select' => 'id, slug',
				'from'   => 'facets',
				'id'     => (array) $ids,
			]
		);

		array_map(
			function( $facet ) use ( $indexer ) {

				$indexer->queue->cancel_item( $facet['id'] );
				Helpers::delete_index( $facet['slug'] );
				$this->clear_cache( $facet['id'] );

			},
			(array) $facets
		);

	}

	/**
	 * Duplicate facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $ids Holds duplicated facet ids.
	 */
	public function duplicate( $ids ) {

		if ( $this->can_index() ) {
			( new Indexer() )->index_facets( $ids );
		}

	}

	/**
	 * Clear facet html cache
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $id Holds duplicated facet id.
	 */
	public function clear_cache( $id ) {

		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options}
				WHERE option_name RLIKE %s
				OR option_name RLIKE %s",
				$wpdb->esc_like( '_site_transient_wpgb_G[0-9]+F' . $id ),
				$wpdb->esc_like( '_transient_wpgb_G[0-9]+F' . $id )
			)
		);

	}

	/**
	 * Check if we can auto index
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function can_index() {

		return wpgb_get_option( 'auto_index' );

	}
}
