<?php
/**
 * Actions
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
 * Handle database actions
 *
 * @class WP_Grid_Builder\Admin\Actions
 * @since 1.0.0
 */
final class Actions extends Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_actions';

	/**
	 * Delete items
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function delete() {

		$ids  = $this->get_var( 'ids' );
		$type = $this->get_var( 'type' );

		do_action( 'wp_grid_builder/before_delete/' . $type, $ids );

		try {
			$ids = Database::delete_row( $type, (array) $ids );
		} catch ( \Exception $e ) {
			$this->send_response( false, $e->getMessage() );
		}

		do_action( 'wp_grid_builder/delete/' . $type, $ids );

		$number = count( $ids );
		/* translators: %d: number of deleted items */
		$message = $number > 1 ? sprintf( __( '%d items have been deleted!', 'wp-grid-builder' ), (int) $number ) : null;

		$this->query( $message );

	}

	/**
	 * Duplicate items
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function duplicate() {

		$ids  = $this->get_var( 'ids' );
		$type = $this->get_var( 'type' );

		try {
			$ids = Database::duplicate_row( $type, (array) $ids );
		} catch ( \Exception $e ) {
			$this->send_response( false, $e->getMessage() );
		}

		do_action( 'wp_grid_builder/duplicate/' . $type, $ids );

		$number = count( $ids );
		/* translators: %d: number of duplicated items */
		$message = $number > 1 ? sprintf( __( '%d items have been duplicated!', 'wp-grid-builder' ), (int) $number ) : null;

		$this->query( $message );

	}

	/**
	 * Favorite items
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function favorite() {

		$ids  = $this->get_var( 'ids' );
		$type = $this->get_var( 'type' );

		try {

			$items = Database::query_results(
				[
					'select' => 'id, favorite',
					'from'   => $type,
					'id'     => (array) $ids,
				]
			);

		} catch ( \Exception $e ) {
			$this->send_response( false, $e->getMessage() );
		}

		foreach ( $items as $item ) {

			try {

				Database::update_row(
					$type,
					[ 'favorite' => ! $item['favorite'] ],
					$item['id']
				);

			} catch ( \Exception $e ) {
				$this->send_response( false, $e->getMessage() );
			}
		}

		do_action( 'wp_grid_builder/favorite/' . $type, $ids );

		$this->query();

	}

	/**
	 * Query items
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $message Holds message to display on backend.
	 */
	public function query( $message = '' ) {

		$type = $this->get_var( 'type' );
		$file = WPGB_PATH . 'admin/views/pages/' . $type . '.php';

		if ( ! file_exists( $file ) ) {
			$this->unknown_error();
		}

		ob_start();
		require_once $file;
		$this->send_response( true, $message, ob_get_clean() );

	}
}
