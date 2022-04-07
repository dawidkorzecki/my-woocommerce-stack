<?php
/**
 * List_Table
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build list table from object type: grids, facets, cards
 *
 * @class WP_Grid_Builder\Admin\List_Table
 * @since 1.0.0
 */
class List_Table {

	/**
	 * Query args
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $query_args = [
		'paged'   => 1,
		'limit'   => 10,
		'order'   => 'DESC',
		'orderby' => 'modified_date',
		's'       => '',
	];

	/**
	 * Queried table
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string
	 */
	private $table;

	/**
	 * Column properties
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $columns;

	/**
	 * Number of items founded
	 *
	 * @since 1.0.0
	 * @access private
	 * @var integer
	 */
	private $found;

	/**
	 * Holds queried items
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $items;

	/**
	 * Holds curent item
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private $item;

	/**
	 * Current item index
	 *
	 * @since 1.0.0
	 * @access private
	 * @var integer
	 */
	private $index = 0;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $columns Holds columns to select.
	 */
	public function __construct( $columns = [] ) {

		$this->columns = $columns;

	}

	/**
	 * Query items
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $table Custom table name.
	 * @return array Holds queried items.
	 */
	public function query( $table = 'grids' ) {

		$this->table = $table;
		$query_args  = $this->query_args();
		$this->found = Database::count_items( $query_args );
		$this->items = Database::query_results( $query_args );

		return $this->items;

	}

	/**
	 * Get query args
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function query_args() {

		if ( wp_doing_ajax() ) {
			$this->set_cookie();
		} else {
			$this->get_cookie();
		}

		return [
			'select'  => join( ',', $this->columns ),
			'from'    => $this->table,
			'limit'   => $this->query_args['limit'],
			'paged'   => $this->query_args['paged'],
			'orderby' => $this->query_args['orderby'] . ' ' . $this->query_args['order'],
			's'       => $this->query_args['s'],
		];

	}

	/**
	 * Set query args in cookie
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function set_cookie() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$params = wp_unslash( $_GET );

		foreach ( $this->query_args as $key => $val ) {

			// Check if allowed in query args.
			if ( empty( $params[ $key ] ) ) {
				continue;
			}

			$this->query_args[ $key ] = sanitize_text_field( $params[ $key ] );

		}

		$name  = WPGB_SLUG . '_' . $this->table . '_list';
		$value = wp_json_encode( $this->query_args );

		// Save query args in cookie.
		setcookie( $name, $value );

	}

	/**
	 * Get query args from cookie
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_cookie() {

		$name   = WPGB_SLUG . '_' . $this->table . '_list';
		$cookie = (array) wp_unslash( $_COOKIE );

		if ( ! array_key_exists( $name, $cookie ) ) {
			return;
		}

		$cookie = json_decode( $cookie[ $name ], true );

		foreach ( $this->query_args as $key => $val ) {

			// Ignore paged and search arguments.
			if ( in_array( $key, [ 'paged', 's' ], true ) ) {
				continue;
			}

			// Check if allowed in cookie.
			if ( empty( $cookie[ $key ] ) ) {
				continue;
			}

			// Push query args.
			$this->query_args[ $key ] = sanitize_text_field( $cookie[ $key ] );

		}

	}

	/**
	 * Get current item in loop
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function the_item() {

		$this->item = $this->items[ $this->index ];
		++$this->index;

	}

	/**
	 * Set current item property
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $key Item property.
	 * @param mixed  $val Item property value.
	 */
	public function set( $key, $val ) {

		if ( empty( $this->item ) || empty( $key ) ) {
			return;
		}

		$this->item[ $key ] = $val;

	}

	/**
	 * Get item link
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Settings link page.
	 */
	public function get_edit_link() {

		$page = substr( $this->table, 0, -1 ) . '-settings';

		return add_query_arg(
			[
				'page' => WPGB_SLUG . '-' . $page,
				'id'   => $this->item['id'],
			],
			admin_url( 'admin.php' )
		);

	}

	/**
	 * Check if new item
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_new() {

		$p_time = mysql2date( 'U', $this->item['date'], false );
		$d_time = current_time( 'U' ) - $p_time; // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

		if ( $d_time >= 0 && $d_time < 10 * 60 ) {
			return true;
		}

		return false;

	}

	/**
	 * Get item modified date
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_modified_date() {

		$m_time = $this->item['modified_date'];
		$c_time = current_time( 'U' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		$p_time = mysql2date( 'U', $m_time, false );
		$d_time = $c_time - $p_time;

		if ( $d_time >= 0 && $d_time < DAY_IN_SECONDS ) {
			/* translators: %s: Human time diff */
			$h_time = sprintf( __( '%s ago', 'wp-grid-builder' ), human_time_diff( $c_time, $p_time ) );
		} else {
			$h_time = mysql2date( __( 'Y/m/d', 'wp-grid-builder' ), $m_time );
		}

		return [
			'm_time' => $m_time,
			'h_time' => $h_time,
		];

	}

	/**
	 * Get table controls
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_controls() {

		require_once WPGB_PATH . 'admin/views/tables/layout/controls.php';

	}

	/**
	 * Get table head
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $columns Holds table head columns.
	 */
	public function get_header( $columns ) {

		require_once WPGB_PATH . 'admin/views/tables/layout/header.php';

	}

	/**
	 * Get table footer
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_footer() {

		require_once WPGB_PATH . 'admin/views/tables/layout/footer.php';

	}

	/**
	 * Get table column template
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $columns Holds column names.
	 */
	public function get_columns( $columns ) {

		foreach ( $columns as $column ) {

			$column = str_replace( '_', '-', $column );
			$file   = WPGB_PATH . 'admin/views/tables/columns/' . $column . '.php';

			if ( file_exists( $file ) ) {
				require $file;
			}
		}

	}
}
