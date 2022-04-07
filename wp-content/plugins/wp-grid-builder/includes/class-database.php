<?php
/**
 * Database
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle custom table query
 *
 * @class WP_Grid_Builder\Includes\Database
 * @since 1.0.0
 */
final class Database {

	/**
	 * Custom tables
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private static $tables = [
		'grids'  => '(
			id BIGINT(20) unsigned NOT NULL auto_increment,
			name VARCHAR(191) NOT NULL,
			date DATETIME NOT NULL default "0000-00-00 00:00:00",
			modified_date DATETIME NOT NULL default "0000-00-00 00:00:00",
			favorite BOOLEAN NULL default "0",
			type VARCHAR(32) NOT NULL,
			source VARCHAR(32) NOT NULL,
			settings MEDIUMTEXT NOT NULL,
			PRIMARY KEY id (id),
			INDEX (name),
			INDEX (modified_date)
		)',
		'cards'  => '(
			id BIGINT(20) unsigned NOT NULL auto_increment,
			name VARCHAR(191) NOT NULL,
			date DATETIME NOT NULL default "0000-00-00 00:00:00",
			modified_date DATETIME NOT NULL default "0000-00-00 00:00:00",
			favorite BOOLEAN NULL default "0",
			type VARCHAR(32) NOT NULL,
			layout MEDIUMTEXT NOT NULL,
			settings MEDIUMTEXT NOT NULL,
			css MEDIUMTEXT NOT NULL,
			PRIMARY KEY id (id),
			INDEX (name),
			INDEX (modified_date)
		)',
		'facets' => '(
			id BIGINT(20) unsigned NOT NULL auto_increment,
			slug VARCHAR(191) NOT NULL,
			name VARCHAR(191) NOT NULL,
			date DATETIME NOT NULL default "0000-00-00 00:00:00",
			modified_date DATETIME NOT NULL default "0000-00-00 00:00:00",
			favorite BOOLEAN NULL default "0",
			type VARCHAR(32) NOT NULL,
			source VARCHAR(191) NOT NULL,
			settings MEDIUMTEXT NOT NULL,
			PRIMARY KEY id (id),
			INDEX slug (slug),
			INDEX slug_id (slug, id),
			INDEX (modified_date)
		)',
		'index'  => '(
			id BIGINT(20) unsigned NOT NULL auto_increment,
			object_id INT unsigned,
			slug VARCHAR(50),
            facet_value VARCHAR(191),
            facet_name VARCHAR(191),
			facet_id INT UNSIGNED default "0",
            facet_parent INT UNSIGNED default "0",
			facet_order INT UNSIGNED default "0",
			PRIMARY KEY (id),
			INDEX object_id_idx (object_id),
            INDEX slug_idx (slug),
			INDEX slug_value_idx (slug, facet_value)
		)',
	];

	/**
	 * Column Placeholders
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	private static $placeholders = [
		'id'            => '%d',
		'favorite'      => '%d',
		'slug'          => '%s',
		'name'          => '%s',
		'date'          => '%s',
		'modified_date' => '%s',
		'type'          => '%s',
		'source'        => '%s',
		'layout'        => '%s',
		'settings'      => '%s',
		'styles'        => '%s',
		'css'           => '%s',
		's'             => '%s',
	];

	/**
	 * Internal cache
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected static $cache = [];

	/**
	 * Create Database Tables (multisite condition)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param boolean $network_wide Whether to enable the plugin for all sites in the network.
	 * @param boolean $force        Force database table creation.
	 */
	public static function create_tables( $network_wide = false, $force = false ) {

		global $wpdb;

		if ( $network_wide && is_multisite() ) {

			// Save current blog ID.
			$current  = $wpdb->blogid;
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

			// Create tables for each blog ID.
			foreach ( $blog_ids as $blog_id ) {

				switch_to_blog( $blog_id );
				self::_create_tables( $network_wide, $force );

			}

			// Go back to current blog.
			switch_to_blog( $current );

		} else {
			self::_create_tables( $network_wide, $force );
		}

	}

	/**
	 * Create Tables
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param boolean $network_wide Whether to enable the plugin for all sites in the network.
	 * @param boolean $force Force database table creation.
	 */
	private static function _create_tables( $network_wide, $force ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		global $wpdb;

		$version = get_option( WPGB_SLUG . '_db_version', '0' );

		if ( version_compare( $version, '1', '<' ) || $force ) {

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}

			$charset_collate = $wpdb->get_charset_collate();

			foreach ( self::$tables as $table => $sql ) {

				$table_name = self::get_table_name( $table );
				dbDelta( "CREATE TABLE IF NOT EXISTS $table_name $sql $charset_collate;" );

			}

			update_option( WPGB_SLUG . '_db_version', '1' );

		} else {
			self::tables_exist( $network_wide );
		}

	}

	/**
	 * Check if tables exist
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param boolean $network_wide Whether to enable the plugin for all sites in the network.
	 * @return void
	 */
	private static function tables_exist( $network_wide ) {

		global $wpdb;

		foreach ( self::$tables as $table => $sql ) {

			$table_name = self::get_table_name( $table );
			$table = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

			if ( $table !== $table_name ) {

				self::create_tables( $network_wide, true );
				return;

			}
		}

	}

	/**
	 * Check if table name exists
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  string $name Table name (unprefixed).
	 * @return string
	 * @throws \Exception If wrong table name.
	 */
	private static function get_table_name( $name = null ) {

		global $wpdb;

		if ( isset( self::$tables[ $name ] ) ) {
			return $wpdb->prefix . WPGB_SLUG . '_' . $name;
		}

		$error_msg = __( 'Sorry, database table could not be reached.', 'wp-grid-builder' );
		throw new \Exception( $error_msg );

	}

	/**
	 * Check data to insert in table.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  array $data Array key/value pairs of query parameters.
	 * @return void
	 * @throws \Exception If no data found.
	 */
	private static function check_data( $data = null ) {

		if ( ! empty( $data ) && is_array( $data ) ) {
			return;
		}

		$error_msg = __( 'Sorry, no data were found.', 'wp-grid-builder' );
		throw new \Exception( $error_msg );

	}

	/**
	 * Check if column name exists in table.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string  $table Table name (unprefixed).
	 * @param array   $data Holds Row data.
	 * @param integer $id Row ID.
	 * @return void
	 * @throws \Exception If row name exists.
	 */
	private static function check_name( $table = null, $data = [], $id = 0 ) {

		global $wpdb;

		if ( ! isset( $data['name'] ) ) {
			return;
		}

		$exists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM $table WHERE name = %s AND id != %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				[ $data['name'], $id ]
			)
		);

		if ( empty( $exists ) ) {
			return;
		}

		$error_msg = __( 'This name already exists. Please, enter another name.', 'wp-grid-builder' );
		throw new \Exception( $error_msg );

	}

	/**
	 * Check if column slug exists in table.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string  $table Table name (unprefixed).
	 * @param array   $data Holds Row data.
	 * @param integer $id Row ID.
	 * @return void
	 * @throws \Exception If row name exists.
	 */
	private static function check_slug( $table = null, $data = [], $id = 0 ) {

		global $wpdb;

		if ( ! isset( $data['slug'] ) ) {
			return;
		}

		$exists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM $table WHERE slug = %s AND id != %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				[ $data['slug'], $id ]
			)
		);

		if ( empty( $exists ) ) {
			return;
		}

		$error_msg = __( 'This slug already exists. Please, enter another slug.', 'wp-grid-builder' );
		throw new \Exception( $error_msg );

	}

	/**
	 * Check data base error
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 * @throws \Exception If wpdb error.
	 */
	private static function check_error() {

		global $wpdb;

		if ( ! $wpdb->last_error ) {
			return;
		}

		$error_msg = $wpdb->last_error;
		throw new \Exception( $error_msg );

	}

	/**
	 * Prepare placeholder to insert/update row in table
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  array $data Holds column names.
	 * @return array
	 */
	private static function set_placeholders( $data = [] ) {

		$placeholders = [];

		foreach ( $data as $key => $val ) {
			$placeholders[ $key ] = isset( self::$placeholders[ $key ] ) ? self::$placeholders[ $key ] : '%s';
		}

		return $placeholders;

	}

	/**
	 * Generate unique name in table
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  string $table Table name (unprefixed).
	 * @param  string $name  Row Name.
	 * @return string
	 */
	private static function unique_name( $table, $name ) {

		global $wpdb;

		$suffix = 1;
		$table_name = self::get_table_name( $table );

		do {

			$new_name = $suffix > 1 ? substr( $name, 0, 200 - ( strlen( $suffix ) + 1 ) ) . '-' . $suffix : $name;
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM $table_name WHERE name = %s", $new_name ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			++$suffix;

		} while ( $exists );

		return $new_name;

	}

	/**
	 * Generate unique slug in table
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  string $table Table name (unprefixed).
	 * @param  string $slug  Row slug.
	 * @return string
	 */
	private static function unique_slug( $table, $slug ) {

		global $wpdb;

		$suffix = 1;
		$table_name = self::get_table_name( $table );

		do {

			$new_slug = $suffix > 1 ? substr( $slug, 0, 200 - ( strlen( $suffix ) + 1 ) ) . '_' . $suffix : $slug;
			$new_slug = str_replace( '-', '_', sanitize_title( $new_slug ) );
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM $table_name WHERE slug = %s", $new_slug ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			++$suffix;

		} while ( $exists );

		return $new_slug;

	}

	/**
	 * Query results from SQL parameters
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  boolean $args Holds SQL parameters.
	 * @return Array
	 */
	public static function query_results( $args = [] ) {

		global $wpdb;

		$args = self::build_query( $args );

		if ( ! $args ) {
			return false;
		}

		return $wpdb->get_results( $args, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	}

	/**
	 * Query row from SQL parameters
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  boolean $args Holds SQL parameters.
	 * @return Array
	 */
	public static function query_row( $args = [] ) {

		global $wpdb;

		$args = self::build_query( $args );

		if ( ! $args ) {
			return false;
		}

		return $wpdb->get_row( $args, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	}


	/**
	 * Query var from SQL parameters
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  boolean $args Holds SQL parameters.
	 * @return Array
	 */
	public static function query_var( $args = [] ) {

		global $wpdb;

		$args = self::build_query( $args );

		if ( ! $args ) {
			return false;
		}

		return $wpdb->get_var( $args ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	}

	/**
	 * Count number of rows in table
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  Array|String $args Holds SQL parameters or table name.
	 * @return integer
	 */
	public static function count_items( $args = null ) {

		global $wpdb;

		if ( ! is_array( $args ) ) {

			$args = [
				'from'  => $args,
				'count' => 1,
			];

		} else {

			$args['fields']  = '';
			$args['count']   = 1;
			$args['limit']   = 0;
			$args['paged']   = 0;
			$args['orderby'] = null;

		}

		$args  = self::build_query( $args );
		$cache = md5( $args );

		if ( isset( self::$cache['count_items'][ $cache ] ) ) {
			return self::$cache['count_items'][ $cache ];
		}

		$var = $wpdb->get_var( $args ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		self::$cache['count_items'][ $cache ] = $var;

		return $var;

	}

	/**
	 * Parse and build query (SQL clauses & values)
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  Array $query Holds SQL clauses & value.
	 * @return string
	 */
	private static function build_query( $query ) {

		global $wpdb;

		$query = wp_parse_args(
			(array) $query,
			[
				'select'  => '*',
				'count'   => null,
				'from'    => null,
				's'       => null,
				'orderby' => null,
				'paged'   => 0,
				'limit'   => 0,
				'offset'  => 0,
			]
		);

		$sql = [];
		$sql = self::parse_select( $query, $sql );
		$sql = self::parse_from( $query, $sql );
		$sql = self::parse_where( $query, $sql );
		$sql = self::parse_orderby( $query, $sql );
		$sql = self::parse_limit( $query, $sql );
		$sql = self::parse_offset( $query, $sql );

		if ( ! empty( $sql['args'] ) ) {
			return $wpdb->prepare( $sql['query'], $sql['args'] ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		return $sql['query'];

	}

	/**
	 * Parse field SQL parameter
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  array $query  Holds query arguments.
	 * @param  array $sql    Holds sql parameters (sql clauses/values).
	 * @return array
	 */
	private static function parse_select( $query, $sql ) {

		$sql['query'] = 'SELECT ';

		if ( $query['count'] ) {

			$sql['query'] .= 'COUNT(*)';
			return $sql;

		}

		$allowed = array_keys( self::$placeholders );
		$columns = array_map( 'trim', explode( ',', $query['select'] ) );
		$columns = array_intersect( $allowed, $columns );

		$sql['query'] .= $columns ? implode( ', ', $columns ) : '*';
		return $sql;

	}

	/**
	 * Parse from SQL parameter
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  array $query  Holds query arguments.
	 * @param  array $sql    Holds sql parameters (sql clauses/values).
	 * @return array
	 */
	private static function parse_from( $query, $sql ) {

		$sql['query'] .= ' FROM ' . self::get_table_name( $query['from'] );
		return $sql;

	}

	/**
	 * Parse where SQL parameter
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  array $query  Holds query arguments.
	 * @param  array $sql    Holds sql parameters (sql clauses/values).
	 * @return array
	 */
	private static function parse_where( $query, $sql ) {

		global $wpdb;

		$sql['args'] = [];
		$conditions  = [];

		foreach ( $query as $column => $value ) {

			if ( ! isset( self::$placeholders[ $column ] ) || empty( $value ) ) {
				continue;
			}

			$placeholder = self::$placeholders[ $column ];

			if ( 's' === $column ) {

				$conditions[]   = 'name like %s';
				$sql['args'][] .= '%' . $wpdb->esc_like( trim( $value ) ) . '%';

			} elseif ( is_array( $value ) ) {

				$placeholders = implode( ', ', array_fill( 0, count( $value ), $placeholder ) );
				$conditions[] = $column . ' IN(' . $placeholders . ')';
				$sql['args']  = array_merge( $sql['args'], $value );

			} else {

				$conditions[]   = $column . ' = ' . $placeholder;
				$sql['args'][] .= $value;

			}
		}

		$sql['query'] .= $conditions ? ' WHERE ' . implode( 'AND ', $conditions ) : '';
		return $sql;

	}

	/**
	 * Parse orderby SQL parameter
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  array $query  Holds query arguments.
	 * @param  array $sql    Holds sql parameters (sql clauses/values).
	 * @return array
	 */
	private static function parse_orderby( $query, $sql ) {

		$clause  = [];
		$allowed = [ 'DESC', 'ASC' ];
		$orderby = array_map( 'trim', explode( ',', $query['orderby'] ) );

		foreach ( $orderby as $order ) {

			$param = array_map( 'trim', explode( ' ', $order ) );
			$by    = isset( $param[0] ) ? $param[0] : null;
			$order = isset( $param[1] ) ? $param[1] : null;

			if ( array_key_exists( $by, self::$placeholders ) ) {

				$order = in_array( strtoupper( $order ), $allowed, true ) ? $order : 'DESC';
				$clause[] = $by . ' ' . $order;

			}
		}

		// Add id to have a deterministic order (prevent duplicated rows with pagination).
		$sql['query'] .= $clause ? ' ORDER BY ' . implode( ', ', $clause ) . ', id' : '';
		return $sql;

	}

	/**
	 * Parse limit SQL parameter
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  array $query  Holds query arguments.
	 * @param  array $sql    Holds sql parameters (sql clauses/values).
	 * @return array
	 */
	private static function parse_limit( $query, $sql ) {

		if ( empty( $query['limit'] ) ) {
			return $sql;
		}

		$sql['query'] .= ' LIMIT %d';
		$sql['args'][] = absint( $query['limit'] );
		return $sql;

	}

	/**
	 * Parse offset SQL parameter
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param  array $query  Holds query arguments.
	 * @param  array $sql    Holds sql parameters (sql clauses/values).
	 * @return array
	 */
	private static function parse_offset( $query, $sql ) {

		if ( (int) $query['paged'] < 1 ) {
			return $sql;
		}

		$offset = absint( $query['limit'] ) * ( absint( $query['paged'] ) - 1 );

		if ( ! $offset ) {
			return $sql;
		}

		$sql['query'] .= ' OFFSET %d';
		$sql['args'][] = absint( $offset );
		return $sql;

	}

	/**
	 * Save rows in table (create or update)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string  $table Table name (unprefixed).
	 * @param  array   $data  Holds data to insert/update.
	 * @param  integer $id    Row id to update.
	 * @return integer
	 */
	public static function save_row( $table = null, $data = [], $id = 0 ) {

		if ( absint( $id ) > 0 ) {
			$id = self::update_row( $table, $data, $id );
		} else {
			$id = self::insert_row( $table, $data );
		}

		return $id;

	}

	/**
	 * Insert rows in table
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string $table Table name (unprefixed).
	 * @param  array  $data  Holds data to insert in row columns.
	 * @return integer
	 */
	public static function insert_row( $table = null, $data = [] ) {

		global $wpdb;

		$table_name = self::get_table_name( $table );
		self::check_data( $data );
		self::check_name( $table_name, $data );
		self::check_slug( $table_name, $data );

		$current_time = current_time( 'mysql' );
		$data['date'] = $current_time;
		$data['modified_date'] = $current_time;

		$wpdb->insert(
			$table_name,
			$data,
			self::set_placeholders( $data )
		);

		self::check_error();

		return $wpdb->insert_id;

	}

	/**
	 * Update rows in table
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string  $table  Table name (unprefixed).
	 * @param  array   $data   Holds data to insert in row columns.
	 * @param  integer $id     Row id to update.
	 * @return integer
	 */
	public static function update_row( $table = null, $data = [], $id = 0 ) {

		global $wpdb;

		$table_name = self::get_table_name( $table );
		self::check_data( $data );
		self::check_name( $table_name, $data, $id );
		self::check_slug( $table_name, $data, $id );

		// If more than one column is updated.
		if ( count( $data ) > 1 ) {

			$current_time = current_time( 'mysql' );
			$data['modified_date'] = $current_time;

		}

		$wpdb->update(
			$table_name,
			$data,
			[ 'id' => $id ],
			self::set_placeholders( $data ),
			[ 'id' => '%d' ]
		);

		self::check_error();

		return $id;

	}

	/**
	 * Delete rows in table
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $table Table name (unprefixed).
	 * @param array  $ids   Holds row ids to delete.
	 * @throws \Exception If wpdb error on delete.
	 */
	public static function delete_row( $table, $ids ) {

		global $wpdb;

		if ( ! is_array( $ids ) ) {

			$error_msg  = __( 'Sorry, an unknown issue occured.', 'wp-grid-builder' );
			throw new \Exception( $error_msg );

		}

		$table_name   = self::get_table_name( $table );
		$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );

		$query = "DELETE FROM $table_name WHERE id IN($placeholders)";
		$ids   = array_map( 'absint', $ids );

		if ( ! $wpdb->query( $wpdb->prepare( $query, $ids ) ) ) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			$error_msg = __( 'Sorry, item could not be deleted', 'wp-grid-builder' );
			throw new \Exception( $error_msg );

		}

		return $ids;

	}

	/**
	 * Duplicate rows in table
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $table Table name (unprefixed).
	 * @param array  $ids   Holds row ids to duplicate.
	 * @throws \Exception If nod row ids found.
	 */
	public static function duplicate_row( $table, $ids ) {

		global $wpdb;

		if ( ! is_array( $ids ) ) {

			$error_msg  = __( 'Sorry, an unknown issue occured.', 'wp-grid-builder' );
			throw new \Exception( $error_msg );

		}

		$duplicated   = [];
		$table_name   = self::get_table_name( $table );
		$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );

		$query = "SELECT * FROM $table_name WHERE id IN($placeholders)";
		$ids   = array_map( 'absint', $ids );
		$rows  = $wpdb->get_results( $wpdb->prepare( $query, $ids ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		foreach ( $rows as $row ) {
			$duplicated[] = self::import_row( $table, $row );
		}

		return $duplicated;

	}

	/**
	 * Import row in table
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $table Table name (unprefixed).
	 * @param array  $row   Holds row to import.
	 * @throws \Exception If no row found.
	 */
	public static function import_row( $table, $row ) {

		if ( ! is_array( $row ) ) {

			$error_msg  = __( 'Sorry, an unknown issue occured.', 'wp-grid-builder' );
			throw new \Exception( $error_msg );

		}

		// Remove unvalid columns.
		foreach ( $row as $column => $args ) {

			if ( isset( self::$placeholders[ $column ] ) ) {
				continue;
			}

			unset( $row[ $column ] );

		}

		unset( $row['id'] );
		unset( $row['date'] );
		unset( $row['modified_date'] );

		$row['name'] = self::unique_name( $table, $row['name'] );

		if ( isset( $row['slug'] ) ) {
			$row['slug'] = self::unique_slug( $table, $row['slug'] );
		}

		$id = self::insert_row( $table, $row );

		return $id;

	}
}
