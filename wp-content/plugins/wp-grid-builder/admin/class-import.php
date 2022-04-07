<?php
/**
 * Import
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\File;
use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Import grids, cards, facets, blocks, and settings
 *
 * @class WP_Grid_Builder\Admin\Import
 * @since 1.0.0
 */
final class Import extends Async {

	/**
	 * Action name
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $action = WPGB_SLUG . '_import';

	/**
	 * Browse demos
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function browse_demo() {

		// Get demo content.
		$demo = $this->get_var( 'demo' );
		$json = self::get_demo_content( $demo, true );
		$list = self::get_json_content( $json );

		self::send_response(
			true,
			null,
			[
				'data' => $json,
				'list' => $this->get_import_list( $list ),
			]
		);

	}

	/**
	 * Check and get global $_FILES properties.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function check_file() {

		// Check if all needed properties are correctly set.
		$is_valid = isset(
			$_FILES['import']['tmp_name'],
			$_FILES['import']['name'],
			$_FILES['import']['type'],
			$_FILES['import']['size']
		);

		if ( ! $is_valid ) {
			$this->unknown_error();
		}

		$file = $_FILES;

		// Check file content type.
		if ( 'application/json' !== $file['import']['type'] ) {
			$this->send_response( false, __( 'File format is invalid. Please upload a .json file.', 'wp-grid-builder' ) );
		}

		return $file['import'];

	}

	/**
	 * Read json file
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function read_file() {

		// Check file extension.
		$file = $this->check_file();
		// Check for form action "wpgb_import" and unset type test to prevent issue with json mime type.
		$file = wp_handle_sideload(
			$file,
			[
				'action'    => 'wpgb_import',
				'test_type' => false,
			]
		);

		// If an error occured when uploading file.
		if ( ! empty( $file['error'] ) ) {
			$this->send_response( false, $file['error'] );
		}

		$filesystem = File::get_filesystem();

		if ( ! $filesystem ) {
			$this->send_response( false, __( 'Sorry, an error occured while fetching content.', 'wp-grid-builder' ) );
		}

		// Fetch json content.
		$json = $filesystem->get_contents( $file['file'] );
		$list = self::get_json_content( $json, true );

		// Delete file from WP upload dir.
		$filesystem->delete( $file['file'] );

		// Handle content error.
		if ( ! $list ) {
			$this->send_response( false, __( 'Sorry, an error occured while fetching content.', 'wp-grid-builder' ) );
		}

		$this->send_response(
			true,
			__( 'Content correctly fetched!', 'wp-grid-builder' ),
			[
				'data' => $json,
				'list' => $this->get_import_list( $list ),
			]
		);

	}

	/**
	 * Import items (grid, card, element)
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function import() {

		$count = 0;
		$demo  = $this->get_var( 'demo' );
		$data  = $this->get_var( 'data', false );
		$types = $this->get_var( 'types', false );
		$types = (array) json_decode( $types, true );

		// If it's a demo.
		if ( ! empty( $demo ) ) {
			$data = self::get_demo_content( $demo );
		}

		// If no data found.
		if ( empty( $data ) ) {
			$this->unknown_error();
		}

		// Decode content.
		$data = self::remove_bom( $data );
		$data = (array) json_decode( $data, true );

		// Loop through each data type (grids, cards, facets, settings).
		foreach ( $types as $type => $items ) {

			$items  = array_intersect_key( $data[ $type ], array_flip( $items ) );
			$count += count( $items );

			do_action( 'wp_grid_builder/import/' . $type, $items );

		}

		$this->send_response(
			true,
			/* translators: %d: number of imported items */
			sprintf( esc_html( _n( '%d item has been imported!', '%d items have been imported!', $count, 'wp-grid-builder' ) ), (int) $count ),
			$this->get_overview_page()
		);

	}

	/**
	 * Get .json file content (import)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string $json  Json content.
	 * @param  string $array Retrun array.
	 * @return string|array  string or array of markup and/or data
	 */
	public static function get_json_content( $json, $array = false ) {

		$list   = [];
		$data   = self::remove_bom( $json );
		$data   = (array) json_decode( $json, true );
		$base   = [ 'grids', 'cards', 'facets', 'settings' ];
		$facets = apply_filters( 'wp_grid_builder/facets', [] );

		if ( empty( $data ) ) {
			return false;
		}

		foreach ( $data as $type => $items ) {

			if ( ! in_array( $type, $base, true ) ) {
				continue;
			}

			$list[ $type ] = array_map(
				function( $item ) use ( $type, $facets ) {

					$name = isset( $item['name'] ) ? $item['name'] : __( 'Settings', 'wp-grid-builder' );
					$icon = isset( $item['type'] ) ? $item['type'] . '-' . rtrim( $type, 's' ) : $type;
					$icon = Helpers::get_icon( $icon . '-large', true );

					if ( 'facets' === $type ) {

						$icon = isset( $item['type'] ) && ! empty( $facets[ $item['type'] ]['icons']['large'] );
						$icon = $icon ? $facets[ $item['type'] ]['icons']['large'] : Helpers::get_icon( 'filter-action', true );

					}

					return [
						'type' => $type,
						'name' => $name,
						'icon' => $icon,
					];

				},
				$items
			);
		}

		if ( empty( $list ) ) {
			return false;
		}

		return $list;

	}

	/**
	 * Remove UTF-8 BOM
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string $json Json string.
	 * @return string
	 */
	public static function remove_bom( $json ) {

		if ( empty( $json ) ) {
			return $json;
		}

		if ( 0 === strncmp( $json, pack( 'CCC', 0xef, 0xbb, 0xbf ), 3 ) ) {
			$json = substr( $json, 3 );
		}

		return $json;

	}

	/**
	 * Get demo content
	 *
	 * @since 1.1.5 Added filters on json content.
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  string  $type   Demo type.
	 * @param  boolean $browse Importer browse mode.
	 * @return string
	 */
	public static function get_demo_content( $type, $browse = false ) {

		$file = 'admin/assets/json/' . $type . '-demo.json';
		$json = Helpers::file_get_contents( $file );

		return apply_filters( 'wp_grid_builder/' . $type . '_demo', $json, $browse );

	}

	/**
	 * Get overview page
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_overview_page() {

		$file = WPGB_PATH . 'admin/views/pages/' . $this->get_var( 'demo' ) . '.php';

		if ( ! file_exists( $file ) ) {
			return;
		}

		ob_start();
		require_once $file;
		return ob_get_clean();

	}

	/**
	 * Get import list
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $list Holds array of items to import.
	 * @return string
	 */
	public function get_import_list( $list ) {

		ob_start();
		require_once WPGB_PATH . 'admin/views/modules/import-list.php';
		return ob_get_clean();

	}
}
