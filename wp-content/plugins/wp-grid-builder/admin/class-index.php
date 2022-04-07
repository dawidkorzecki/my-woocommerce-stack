<?php
/**
 * Index
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Indexer;
use WP_Grid_Builder\Includes\Database;
use WP_Grid_Builder\Includes\Cron\Queue;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle facet indexation
 *
 * @class WP_Grid_Builder\Admin\Index
 * @since 1.0.0
 */
final class Index extends Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_index';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct();

		add_filter( 'wp_grid_builder/index/facet', [ $this, 'should_index' ], 10, 3 );

	}

	/**
	 * Check if the facet needs to be indexed
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param boolean $index Index state.
	 * @param integer $id Facet id.
	 * @param array   $settings Holds current facet settings.
	 * @return boolean
	 */
	public function should_index( $index, $id, $settings ) {

		$previous  = $this->get_previous( $id );
		$settings  = $this->normalize( $settings );
		$has_index = $this->has_index( $previous );
		$can_index = $this->can_index( $settings );

		// New grid, not indexable or need re-index..
		if ( ! $id || ! $previous || ! $can_index ) {

			// Delete previous index.
			if ( $has_index ) {
				Helpers::delete_index( $previous['slug'] );
			}

			return $can_index;

		}

		// If filter settings changed.
		if ( $this->has_diff( $settings, $previous ) ) {
			return true;
		}

		// No change requires to entirely re-index.
		// Return previous slug to change it in the indexer.
		if ( $previous['slug'] !== $settings['slug'] && $has_index ) {
			return $previous['slug'];
		}

		// If index not exists.
		if ( ! $has_index ) {
			return true;
		}

		return false;

	}

	/**
	 * Get previous facet settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $id Facet id.
	 * @return mixed
	 */
	public function get_previous( $id ) {

		if ( empty( $id ) ) {
			return;
		}

		$previous = Database::query_row(
			[
				'select' => 'id, slug, source, settings',
				'from'   => 'facets',
				'id'     => $id,
			]
		);

		if ( ! $previous ) {
			return;
		}

		return $this->normalize( $previous );

	}

	/**
	 * Normalize facet settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds facet settings.
	 * @return array
	 */
	public function normalize( $settings ) {

		$defaults  = require WPGB_PATH . 'admin/settings/defaults/facet.php';
		$normalize = $settings['settings'];

		if ( ! is_array( $normalize ) ) {
			$normalize = json_decode( $normalize, true );
		}

		$settings['settings'] = wp_parse_args( $normalize, $defaults );

		return $settings;

	}

	/**
	 * Check if facet has been indexed
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $previous Holds previous facet settings.
	 * @return boolean
	 */
	public function has_index( $previous ) {

		global $wpdb;

		if ( ! $previous ) {
			return false;
		}

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}wpgb_index
				WHERE 1=1 AND slug = %s",
				$previous['slug']
			)
		);

	}

	/**
	 * Check if facet is indexable
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Holds facet settings.
	 * @return boolean
	 */
	public function can_index( $facet ) {

		$settings  = $facet['settings'];
		$is_filter = 'filter' === $settings['action'];
		$is_search = 'search' === $settings['filter_type'];
		$selection = 'selection' === $settings['filter_type'];

		return $is_filter && ! $is_search && ! $selection;

	}

	/**
	 * Check if facet settings changed
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $current  Holds current facet settings.
	 * @param array $previous Holds previous facet settings.
	 * @return boolean
	 */
	public function has_diff( $current, $previous ) {

		if ( $previous['source'] !== $current['source'] ) {
			return true;
		}

		$fields   = [ 'filter_type', 'parent', 'include', 'exclude', 'hierarchical' ];
		$current  = $current['settings'];
		$previous = $previous['settings'];

		$diff = array_filter(
			$fields,
			function( $field ) use ( $current, $previous ) {
				return $current[ $field ] !== $previous[ $field ];
			}
		);

		return ! empty( $diff );

	}

	/**
	 * Update index slug
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function update_slug() {

		global $wpdb;

		$old_slug = $this->get_var( 'old_slug' );
		$new_slug = $this->get_var( 'new_slug' );

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}wpgb_index
				SET slug = %s
				WHERE slug = %s",
				$new_slug,
				$old_slug
			)
		);

		$this->send_response( true );

	}

	/**
	 * Index facets
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function index() {

		global $wpdb;

		$id = $this->get_var( 'id' );

		if ( empty( $id ) ) {
			$this->send_response( true );
		}

		$state = ( new Indexer() )->index_facets( $id );
		$this->send_response( true, null, $state );

	}

	/**
	 * Handle indexer progression
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function get_progress() {

		$progress = 0;
		$message  = null;

		$queued = ( new Queue() )->get();
		$status = $this->get_status( $queued );

		switch ( $status ) {
			case 'facets_progress':
				$progress = $this->get_percent( $queued );
				$message  = sprintf(
					/* translators: %.1f: Indexer progress value */
					__( 'Indexing&hellip; %.1f&#37;', 'wp-grid-builder' ),
					(float) $progress
				);
				break;
			case 'facet_progress':
				$progress = get_site_transient( 'wpgb_cron_progress' );
				$message  = sprintf(
					/* translators: %.1f: Indexer progress value */
					__( 'Indexing&hellip; %.1f&#37;', 'wp-grid-builder' ),
					(float) $progress
				);
				break;
			case 'canceled':
				$message = __( 'Cancelling&hellip;', 'wp-grid-builder' );
				break;
			case 'pending':
				$message = __( 'Pending&hellip;', 'wp-grid-builder' );
				break;
		}

		$this->send_response(
			true,
			null,
			[
				'progress' => $progress,
				'message'  => $message,
			]
		);

	}

	/**
	 * Get indexer state
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @param array $queued Queued items to index.
	 * @return string
	 */
	public function get_status( $queued ) {

		$current = reset( $queued );
		$facets  = array_column( $queued, 'key' );
		$facet   = (int) $this->get_var( 'id' );

		if ( $facet < 0 && isset( $current['state'] ) && 'canceled' === $current['state'] ) {
			return 'canceled';
		} elseif ( $facet < 0 && $queued ) {
			return 'facets_progress';
		} elseif ( isset( $current['key'] ) && $facet === (int) $current['key'] ) {
			return 'facet_progress';
		} elseif ( array_search( $facet, $facets, true ) ) {
			return 'pending';
		}

		return '';

	}

	/**
	 * Get indexer progress in percent
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @param array $queued Queued items to index.
	 * @return float
	 */
	public function get_percent( $queued ) {

		$facets = Helpers::get_indexable_facets( -1 );
		$queued = array_filter(
			$queued,
			function( $item ) {
				return 'pending' === $item['state'];
			}
		);

		$total    = count( $facets );
		$length   = count( $queued );
		$progress = get_site_transient( 'wpgb_cron_progress' ) ?: 0;
		$progress = $progress / $total + 100 / $total * ( $total - $length );

		return $progress;

	}
}
