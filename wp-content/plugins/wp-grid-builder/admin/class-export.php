<?php
/**
 * Export
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Export grids, cards, facets, blocks, and settings
 *
 * @class WP_Grid_Builder\Admin\Export
 * @since 1.0.0
 */
final class Export {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'admin_post_' . WPGB_SLUG . '_export', [ $this, 'export' ] );

	}

	/**
	 * Export items
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function export() {

		// Check user role capability and properties..
		if ( ! Helpers::current_user_can() ) {

			wp_safe_redirect( esc_url_raw( admin_url() ), 302, WPGB_NAME );
			exit;

		}

		// Check referer.
		if ( check_ajax_referer( WPGB_SLUG . '_export_items', 'nonce', false ) === false ) {

			wp_safe_redirect( esc_url_raw( wp_get_referer() ), 302, WPGB_NAME );
			exit;

		}

		$request = wp_unslash( $_GET );

		// Process data.
		$data = $this->process_data( $request );
		$name = $this->generate_file_name( $data );

		// Count number of items to export.
		$number = count( (array) $request['ids'] );
		/* translators: %d: number of items exported */
		$message = $number > 1 ? sprintf( __( '%d items have been exported!', 'wp-grid-builder' ), (int) $number ) : 'false';
		// Set cookie message for back-end popup notification.
		setcookie( WPGB_SLUG . '_items_exported', $message );

		// Ignore user aborts.
		ignore_user_abort( true );

		// Prepare header.
		nocache_headers();

		// phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
		@header( 'Content-Type: application/json; charset=utf-8' );
		@header( 'Content-Disposition: attachment; filename=' . $name . '.json' );
		@header( 'Pragma: no-cache' );
		@header( 'Expires: 0' );
		// phpcs:enable WordPress.PHP.NoSilencedErrors.Discouraged

		echo wp_json_encode( (array) $data );

		exit;

	}

	/**
	 * Process data to export
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $request Holds request key/value pairs.
	 * @return array
	 */
	public function process_data( $request ) {

		$allowed = [ 'grids', 'facets', 'cards', 'settings' ];

		// Validate type.
		if ( ! isset( $request['type'], $request['ids'] ) || ! in_array( $request['type'], $allowed, true ) ) {

			wp_safe_redirect( esc_url_raw( wp_get_referer() ), 302, WPGB_NAME );
			exit;

		}

		if ( 'settings' === $request['type'] ) {
			return $this->export_settings();
		}

		return $this->export_items( $request );

	}

	/**
	 * Export settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function export_settings() {

		$data['settings'][ 'Settings (' . gmdate( 'm-d-Y' ) . ')' ] = wpgb_get_options();

		return $data;

	}

	/**
	 * Query items data
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $request Holds request key/value pairs.
	 * @return array
	 */
	public function export_items( $request ) {

		$args = [
			'select' => '*',
			'from'   => $request['type'],
		];

		if ( (int) $request['ids'] > 0 || is_array( $request['ids'] ) ) {
			$args['id'] = array_map( 'intval', (array) $request['ids'] );
		}

		try {

			$results = Database::query_results( $args );
			$results = Helpers::maybe_json_decode( $results, true );

			$data[ $request['type'] ] = $results;

			return $data;

		} catch ( \Exception $e ) {

			wp_safe_redirect( esc_url_raw( wp_get_referer() ), 302, WPGB_NAME );
			exit;

		}

	}

	/**
	 * Generate file name to export.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data Holds items to export.
	 * @return string
	 */
	public function generate_file_name( $data ) {

		$name = WPGB_SLUG . '-' . gmdate( 'm-d-Y' );

		if ( 1 < count( $data ) ) {
			return $name;
		}

		$name = array_keys( $data );
		$name = isset( $name[0] ) ? $name[0] : $name;

		$name = array_reduce(
			$data,
			function( $carry, $type ) use ( $name ) {

				if ( 1 < count( $type ) ) {
					return $name;
				}

				$item = array_values( $type );

				if ( isset( $item[0]['name'] ) ) {
					return $item[0]['name'];
				}

				return $name;

			}
		);

		$name = sanitize_file_name( $name );

		if ( empty( $name ) ) {
			$name = 'wp-grid-builder';
		}

		return $name;

	}
}
