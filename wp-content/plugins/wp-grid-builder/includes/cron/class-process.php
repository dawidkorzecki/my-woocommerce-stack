<?php
/**
 * Background process (Modified version)
 *
 * @package WP-Background-Processing
 * https://github.com/A5hleyRich/wp-background-processing
 */

namespace WP_Grid_Builder\Includes\Cron;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Process
 *
 * @abstract WP_Grid_Builder\Includes\Cron\Process
 * @since 1.0.0
 */
abstract class Process extends Async {

	/**
	 * Action
	 *
	 * @var string
	 * @access protected
	 */
	protected $prefix = 'wpgb';


	/**
	 * Start time of current process.
	 *
	 * @var int
	 * @access protected
	 */
	protected $start_time = 0;

	/**
	 * Cron identifier
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $cron_hook_id;

	/**
	 * Cron interval identifier
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $cron_interval_id;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct();

		$this->queue            = new Queue();
		$this->cron_hook_id     = $this->prefix . '_cron';
		$this->cron_interval_id = $this->prefix . '_cron_interval';

		if ( is_admin() ) {

			add_action( $this->cron_hook_id, [ $this, 'handle_cron_healthcheck' ] );
			// phpcs:ignore WordPress.WP.CronInterval.ChangeDetected
			add_filter( 'cron_schedules', [ $this, 'schedule_cron_healthcheck' ] );

		}

	}

	/**
	 * Dispatch
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function dispatch() {

		// Schedule the cron healthcheck.
		$this->schedule_event();
		// Perform remote post.
		parent::dispatch();

	}

	/**
	 * Push to item queue
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $key Item key.
	 * @param mixed  $data Item data.
	 *
	 * @return boolean
	 */
	public function add_to_queue( $key, $data ) {

		$cancel = $this->queue->cancel_item( $key );
		$queue  = $this->queue->get();
		$length = count( (array) $queue );

		$this->queue->add_item( $key, $data );

		return $cancel || ! $length;

	}

	/**
	 * Check if we need to handle current request and start queue.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function maybe_handle() {

		// Don't lock up other requests while processing.
		session_write_close();

		if ( $this->is_processing() ) {
			wp_die();
		}

		if ( $this->queue->is_empty() ) {
			wp_die();
		}

		check_ajax_referer( $this->action, 'nonce' );
		$this->handle();

		wp_die();

	}

	/**
	 * Process items in queue
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function handle() {

		$this->run_process();

		// Because the queue can change when processing.
		do {

			$queue = $this->queue->get();

			foreach ( $queue as $key => $item ) {

				$success = true;

				if ( ! $this->queue->is_canceled( $key ) ) {
					$success = $this->task( $item['data'], $key );
				}

				if ( ! $success ) {
					break;
				}

				if ( $this->time_exceeded() || $this->memory_exceeded() ) {
					break;
				}

				$this->queue->delete_item( $key );

			}
		} while (
			! $this->time_exceeded() &&
			! $this->memory_exceeded() &&
			! $this->queue->is_empty()
		);

		$this->stop_process();

		// Start again or complete process.
		if ( ! $this->queue->is_empty() ) {
			$this->dispatch();
		} else {
			$this->complete();
		}

		wp_die();

	}

	/**
	 * Run process before to process queue
	 * Define start_time
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function run_process() {

		// Set start time of current process.
		$this->start_time = time();

		$lock_duration = $this->get_max_execution_time();

		wp_using_ext_object_cache( false );
		set_site_transient( $this->prefix . '_process_lock', microtime(), $lock_duration );

	}

	/**
	 * Stop process by dleeting transient process
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function stop_process() {

		wp_using_ext_object_cache( false );
		delete_site_transient( $this->prefix . '_process_lock' );

	}

	/**
	 * Check if we are currently processing the queue
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function is_processing() {

		// Delete transient option cache before to get.
		// Prevent issue with multiple instances.
		wp_using_ext_object_cache( false );

		if ( get_site_transient( $this->prefix . '_process_lock' ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function memory_exceeded() {

		// Set 80% of memory limit.
		$limit  = $this->get_memory_limit() * 0.8;
		$memory = (int) memory_get_usage( true );

		return $memory >= $limit;

	}

	/**
	 * Get memory limit
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return integer
	 */
	protected function get_memory_limit() {

		// Set a default limit.
		$memory_limit = '128M';

		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		}

		// If unlimited memory.
		if ( ! $memory_limit || -1 === (int) $memory_limit ) {
			$memory_limit = '32G';
		}

		$memory_limit = $this->let_to_num( $memory_limit );

		return $memory_limit;

	}

	/**
	 * Convert number notation (e.g.: '2M') to an integer
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $size Size value.
	 * @return integer
	 */
	protected function let_to_num( $size ) {

		$let = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );

		switch ( strtoupper( $let ) ) {
			case 'P':
				$ret *= 1024;
				// no break.
			case 'T':
				$ret *= 1024;
				// no break.
			case 'G':
				$ret *= 1024;
				// no break.
			case 'M':
				$ret *= 1024;
				// no break.
			case 'K':
				$ret *= 1024;
		}

		return $ret;

	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function time_exceeded() {

		// Set 80% of max execution limit.
		$limit  = $this->get_max_execution_time() * 0.8;
		$finish = $this->start_time + $limit;

		return time() >= $finish;

	}

	/**
	 * Get PHP max execution time
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return integer
	 */
	protected function get_max_execution_time() {

		$max_execution_time = 0;

		if ( function_exists( 'ini_get' ) ) {
			$max_execution_time = (int) ini_get( 'max_execution_time' );
		}

		if ( $max_execution_time <= 0 ) {
			$max_execution_time = 30;
		}

		// A maximum of 60s to prevent to be stalled a long time.
		$max_execution_time = min( 60, $max_execution_time );
		$max_execution_time = apply_filters( 'wp_grid_builder/cron/max_execution_time', $max_execution_time );

		return $max_execution_time;

	}

	/**
	 * Schedule cron healthcheck
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $schedules Schedules.
	 * @return mixed
	 */
	public function schedule_cron_healthcheck( $schedules ) {

		$interval = apply_filters( 'wp_grid_builder/cron/interval', 60 );

		// Adds every 30 seconds to the existing schedules.
		$schedules[ $this->prefix . '_cron_interval' ] = [
			'interval' => $interval,
			/* translators: %d: time in minutes */
			'display'  => __( 'Every %d minutes', 'wp-grid-builder' ),
		];

		return $schedules;

	}

	/**
	 * Handle cron healthcheck
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function handle_cron_healthcheck() {

		// Process already running.
		if ( $this->is_processing() ) {
			exit;
		}

		// No data to process.
		if ( $this->queue->is_empty() ) {
			$this->clear_scheduled_event();
			exit;
		}

		$this->handle();

		exit;

	}

	/**
	 * Schedule event
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function schedule_event() {

		if ( ! wp_next_scheduled( $this->cron_hook_id ) ) {
			wp_schedule_event( time(), $this->cron_interval_id, $this->cron_hook_id );
		}

	}

	/**
	 * Clear scheduled event
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function clear_scheduled_event() {

		$timestamp = wp_next_scheduled( $this->cron_hook_id );

		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $this->cron_hook_id );
		}

	}

	/**
	 * Cancel Process (Clear all items in queue)
	 *
	 * Stop processing queue items, clear cronjob and delete batch.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function cancel() {

		if ( $this->queue->is_empty() ) {
			return;
		}

		$this->queue->delete();
		$this->stop_process();

		wp_clear_scheduled_hook( $this->cron_hook_id );

	}

	/**
	 * Complete.
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function complete() {

		$this->queue->delete();
		$this->stop_process();

		// Unschedule the cron healthcheck.
		$this->clear_scheduled_event();

	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions on each queued item.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param  mixed $item Queue item to iterate over.
	 * @param  mixed $key  Item key in queue.
	 * @return mixed
	 */
	abstract protected function task( $item, $key );
}
