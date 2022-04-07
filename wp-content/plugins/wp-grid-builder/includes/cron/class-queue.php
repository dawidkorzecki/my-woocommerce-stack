<?php
/**
 * Queue
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Cron;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Queue Handle items in queue
 *
 * @class WP_Grid_Builder\Includes\Cron\Queue
 * @since 1.0.0
 */
class Queue {

	/**
	 * Prefix
	 *
	 * @var string
	 * @access protected
	 */
	protected $prefix = 'wpgb';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		$this->table = $this->get_table();

	}

	/**
	 * Get table name and columns to query items in queue.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return object
	 */
	protected function get_table() {

		global $wpdb;

		$table = [
			'name' => $wpdb->options,
			'id'   => 'option_id',
			'key'  => 'option_name',
			'val'  => 'option_value',
		];

		if ( is_multisite() ) {

			$table = [
				'name' => $wpdb->sitemeta,
				'id'   => 'meta_id',
				'key'  => 'meta_key',
				'val'  => 'meta_value',
			];

		}

		return (object) $table;

	}

	/**
	 * Generate unique identifier for item in queue
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string|integer $key Item unique key.
	 */
	protected function generate_uid( $key ) {

		// phpcs:ignore WordPress.WP.AlternativeFunctions.rand_rand
		$unique  = md5( microtime() . rand() );
		$prepend = $this->prefix . '_cron_item_';

		return substr( $prepend . $unique, 0, 64 );

	}

	/**
	 * Clear WordPress object cache for site options.
	 * Prevent issues with multiple instances of WP OBject Cache.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string|integer $key Item unique key.
	 */
	protected function clear_cache( $key ) {

		$cache_key   = '';
		$cache_group = 'options';

		if ( is_multisite() ) {

			$cache_key  .= get_current_network_id() . ':';
			$cache_group = 'site-options';

		}

		$cache_key .= $key;

		wp_cache_delete( $cache_key, $cache_group );

	}

	/**
	 * Get all items in queue
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get() {

		global $wpdb;

		$key = $wpdb->esc_like( $this->prefix . '_cron_item_' ) . '%';

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT *
				FROM {$this->table->name}
				WHERE {$this->table->key} LIKE %s
				ORDER BY {$this->table->id} ASC",
				$key
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$items = [];

		if ( ! $results ) {
			return $items;
		}

		foreach ( $results as $item ) {

			$key = $item->{$this->table->key};
			$val = $item->{$this->table->val};

			$items[ $key ] = maybe_unserialize( $val );

		}

		return $items;

	}

	/**
	 * Delete all items in queue
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function delete() {

		global $wpdb;

		$key = $wpdb->esc_like( $this->prefix . '_cron_item_' ) . '%';

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$query = $wpdb->get_results(
			$wpdb->prepare(
				"DELETE FROM {$this->table->name}
				WHERE {$this->table->key} LIKE %s",
				$key
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	}

	/**
	 * Add item in queue
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string|integer $key   Item unique key.
	 * @param mixed          $data  Item data to attached in option.
	 * @param string         $state Item state in queue.
	 */
	public function add_item( $key, $data, $state = 'pending' ) {

		$uid = $this->generate_uid( $key );

		$this->clear_cache( $uid );

		update_site_option(
			$uid,
			[
				'key'   => $key,
				'data'  => $data,
				'state' => $state,
			]
		);

	}

	/**
	 * Get item in queue
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string|integer $key Item unique key.
	 * @return mixed
	 */
	public function get_item( $key ) {

		$this->clear_cache( $key );

		return get_site_option( $key, [] );

	}

	/**
	 * Update item in queue
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string|integer $key Item key.
	 * @param mixed          $data Item data.
	 */
	public function update_item( $key, $data ) {

		$item = $this->get_item( $key );

		if ( ! $item ) {
			return;
		}

		$item['data'] = $data;

		$this->clear_cache( $key );
		update_site_option( $key, $item );

	}

	/**
	 * Delete item in queue
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string|integer $key Item key.
	 */
	public function delete_item( $key ) {

		delete_site_option( $key );

	}

	/**
	 * Cancel item in queue
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string|integer $key Item key.
	 */
	public function cancel_item( $key ) {

		$items = $this->get();

		foreach ( $items as $uid => $item ) {

			if ( $key === $item['key'] ) {

				$item['state'] = 'canceled';

				$this->clear_cache( $key );
				update_site_option( $uid, $item );

			}
		}

	}

	/**
	 * Check if item is canceled in queue
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string|integer $key Item key.
	 * @return boolean
	 */
	public function is_canceled( $key ) {

		$item = $this->get_item( $key );

		return isset( $item['state'] ) && 'canceled' === $item['state'];

	}

	/**
	 * Check if queue is empty
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_empty() {

		$queue = $this->get();

		return count( $queue ) < 1;

	}
}
