<?php
/**
 * Wishlist DB query.
 *
 * @package woodmart
 */

namespace XTS\WC_Wishlist;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Singleton;

/**
 * Wishlist DB query.
 */
class Wishlist_DB_Query extends Singleton {
	/**
	 * Is wishlist installed.
	 *
	 * @var string
	 */
	public static $is_installed = '';

	/**
	 * Constructor.
	 */
	public function init() {
		self::$is_installed = $this->is_installed();
	}

	/**
	 * Is wishlists table installed.
	 *
	 * @since 1.0
	 *
	 * @return boolean
	 */
	public function is_installed() {
		global $wpdb;

		$products_table  = $wpdb->prefix . 'woodmart_wishlist_products';
		$wishlists_table = $wpdb->prefix . 'woodmart_wishlists';

		$wishlists_table_count = $wpdb->query( "SHOW TABLES WHERE `Tables_in_{$wpdb->dbname}` LIKE '{$products_table}%' OR `Tables_in_{$wpdb->dbname}` LIKE '{$wishlists_table}%'" );//phpcs:ignore

		return (bool) ( 2 === $wishlists_table_count );
	}
}

Wishlist_DB_Query::get_instance();
