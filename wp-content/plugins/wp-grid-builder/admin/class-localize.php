<?php
/**
 * Localize
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Admin;

use WP_Grid_Builder\Includes\I18n;
use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Animations;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Localize strings
 *
 * @class WP_Grid_Builder\Admin\Localize
 * @since 1.0.0
 */
class Localize {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', [ $this, 'localize_scripts' ] );

	}

	/**
	 * Localize all data attach to plugin scripts
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function localize_scripts() {

		$this->main();
		$this->date();
		$this->animations();
		$this->select_field();
		$this->popup_messages();
		$this->dialog_messages();
		$this->color_picker_field();

	}

	/**
	 * Localize script
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $handle      Script handle the data will be attached to.
	 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
	 * @param array  $l10n        The data itself. The data can be either a single or multi-dimensional array.
	 */
	public function localize_script( $handle, $object_name, $l10n ) {

		$l10n = apply_filters( 'wp_grid_builder/admin/localize_script', $l10n, $handle, $object_name );
		wp_localize_script( $handle, $object_name, $l10n );

	}

	/**
	 * Localize main data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function main() {

		$action = admin_url( 'admin-post.php' );
		$action = add_query_arg( 'wpgb-preview', true, $action );
		$action = apply_filters( 'wp_grid_builder/admin/preview_action', $action );

		$strings = [
			'RTL'            => is_rtl(),
			'lang'           => I18n::current_lang(),
			'index'          => wp_create_nonce( WPGB_SLUG . '_index_facets' ),
			'search'         => wp_create_nonce( WPGB_SLUG . '_search_content' ),
			'export'         => wp_create_nonce( WPGB_SLUG . '_export_items' ),
			'preview'        => wp_create_nonce( WPGB_SLUG . '_preview_grid' ),
			'preview_action' => esc_url_raw( $action ),
		];

		$this->localize_script( WPGB_SLUG . '-admin', WPGB_SLUG . '_L10n', $strings );
		$this->localize_script( WPGB_SLUG . '-editor', WPGB_SLUG . '_L10n', $strings );

	}

	/**
	 * Localize dialog messages
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function dialog_messages() {

		$dialog = [
			'delete'       => [
				'title'   => esc_html__( 'Delete Item(s)', 'wp-grid-builder' ),
				'message' => esc_html__( 'Are you sure you want to delete this?', 'wp-grid-builder' ),
				'confirm' => esc_html__( 'Yes, delete', 'wp-grid-builder' ),
				'cancel'  => esc_html__( 'Cancel', 'wp-grid-builder' ),
			],
			'duplicate'    => [
				'title'   => esc_html__( 'Duplicate Item(s)', 'wp-grid-builder' ),
				'message' => esc_html__( 'Are you sure you want to duplicate this?', 'wp-grid-builder' ),
				'confirm' => esc_html__( 'Yes, duplicate', 'wp-grid-builder' ),
				'cancel'  => esc_html__( 'Cancel', 'wp-grid-builder' ),
			],
			'reset'        => [
				'title'   => esc_html__( 'Reset Settings', 'wp-grid-builder' ),
				'message' => esc_html__( 'Are you sure you want to reset your settings?', 'wp-grid-builder' ),
				'confirm' => esc_html__( 'Yes, reset', 'wp-grid-builder' ),
				'cancel'  => esc_html__( 'Cancel', 'wp-grid-builder' ),
			],
			'delete_block' => [
				'title'   => esc_html__( 'Delete Block', 'wp-grid-builder' ),
				'message' => esc_html__( 'Are you sure you want to delete this?', 'wp-grid-builder' ),
				'confirm' => esc_html__( 'Yes, delete', 'wp-grid-builder' ),
				'cancel'  => esc_html__( 'Cancel', 'wp-grid-builder' ),
			],
		];

		$this->localize_script( WPGB_SLUG . '-admin', WPGB_SLUG . '_dialog_L10n', $dialog );

	}

	/**
	 * Localize popup messages
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function popup_messages() {

		$popup = [
			'unknown'            => esc_html__( 'Sorry, an unknown error occurred.', 'wp-grid-builder' ),
			'beforeunload'       => esc_html__( 'You have unsaved changes. If you proceed, they will be lost.', 'wp-grid-builder' ),
			'no_action'          => esc_html__( 'Please, select an action to apply.', 'wp-grid-builder' ),
			'no_selection'       => esc_html__( 'Please, select at least one item.', 'wp-grid-builder' ),
			'no_content'         => esc_html__( 'Please, select a content type to export.', 'wp-grid-builder' ),
			'shortcode'          => esc_html__( 'Shortcode copied to clipboard', 'wp-grid-builder' ),
			'processing'         => esc_html__( 'Please wait. Processing...', 'wp-grid-builder' ),
			'read_file'          => esc_html__( 'Please wait. Reading file content...', 'wp-grid-builder' ),
			'import'             => esc_html__( 'Please wait. Importing items...', 'wp-grid-builder' ),
			'installing'         => esc_html__( 'Installing...', 'wp-grid-builder' ),
			'activating'         => esc_html__( 'Activating...', 'wp-grid-builder' ),
			'activate_plugin'    => esc_html__( 'Please wait. Activating licence...', 'wp-grid-builder' ),
			'deactivate_plugin'  => esc_html__( 'Please wait. Deactivating licence...', 'wp-grid-builder' ),
			'refresh_status'     => esc_html__( 'Please wait. Refreshing license info...', 'wp-grid-builder' ),
			'clear_cache'        => esc_html__( 'Please wait. Clearing cache...', 'wp-grid-builder' ),
			'save_changes'       => esc_html__( 'Please wait. Saving changes...', 'wp-grid-builder' ),
			'reset_settings'     => esc_html__( 'Please wait. Reset settings...', 'wp-grid-builder' ),
			'stop_indexer'       => esc_html__( 'Please wait. Stopping indexer...', 'wp-grid-builder' ),
			'clear_index'        => esc_html__( 'Please wait. Clearing index table...', 'wp-grid-builder' ),
			'delete_stylesheets' => esc_html__( 'Please wait. Deleting style sheets...', 'wp-grid-builder' ),
			'check_index'        => esc_html__( 'Checking...', 'wp-grid-builder' ),
			'pending_index'      => esc_html__( 'Pending...', 'wp-grid-builder' ),
			'indexing_start'     => esc_html__( 'Indexing...', 'wp-grid-builder' ),
			'indexing_complete'  => esc_html__( 'Complete', 'wp-grid-builder' ),
		];

		$this->localize_script( WPGB_SLUG . '-admin', WPGB_SLUG . '_popup_L10n', $popup );

	}

	/**
	 * Localize Color Picker field
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function color_picker_field() {

		$color_picker = [
			'color'    => esc_html__( 'Color', 'wp-grid-builder' ),
			'solid'    => esc_html__( 'Solid Color', 'wp-grid-builder' ),
			'gradient' => esc_html__( 'Gradient Color', 'wp-grid-builder' ),
			'linear'   => esc_html__( 'Linear', 'wp-grid-builder' ),
			'radial'   => esc_html__( 'Radial', 'wp-grid-builder' ),
			'reverse'  => esc_html__( 'Reverse', 'wp-grid-builder' ),
			'presets'  => esc_html__( 'Presets', 'wp-grid-builder' ),
			'position' => esc_html__( 'Position', 'wp-grid-builder' ),
			'delete'   => esc_html__( 'Delete Point', 'wp-grid-builder' ),
			'edit'     => esc_html__( 'Edit Color', 'wp-grid-builder' ),
			'add'      => esc_html__( 'Add Point', 'wp-grid-builder' ),
			'clear'    => esc_html__( 'Clear', 'wp-grid-builder' ),
			'pick'     => esc_html__( 'Select Color', 'wp-grid-builder' ),
			'current'  => esc_html__( 'Current Color', 'wp-grid-builder' ),
			'default'  => esc_html__( 'Default', 'wp-grid-builder' ),
			'icons'    => [
				'reverse' => Helpers::get_icon( 'reverse', true ),
				'presets' => Helpers::get_icon( 'switch', true ),
				'delete'  => Helpers::get_icon( 'delete', true ),
				'edit'    => Helpers::get_icon( 'color', true ),
			],
		];

		$this->localize_script( WPGB_SLUG . '-helpers', WPGB_SLUG . '_colorPicker_L10n', $color_picker );

	}

	/**
	 * Localize Select field
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function select_field() {

		$select = [
			'search'    => esc_html__( 'Please enter 1 or more characters.', 'wp-grid-builder' ),
			'searching' => esc_html__( 'Searching...', 'wp-grid-builder' ),
			'no_result' => esc_html__( 'No results found.', 'wp-grid-builder' ),
			'error'     => esc_html__( 'Sorry, an unknown error occurred.', 'wp-grid-builder' ),
		];

		$this->localize_script( WPGB_SLUG . '-admin', WPGB_SLUG . '_select_L10n', $select );

	}

	/**
	 * Localize Animations
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function animations() {

		$plugin_page = Helpers::get_plugin_page();

		if ( 'grid-settings' !== $plugin_page ) {
			return;
		}

		$this->localize_script( WPGB_SLUG . '-admin', WPGB_SLUG . '_animations_L10n', Animations::get() );

	}

	/**
	 * Localize date
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function date() {

		global $wp_locale;

		$timezone = get_option( 'timezone_string' );
		$timezone = ! empty( $timezone ) ? $timezone : 'UTC';

		$date = [
			'monthNames'      => array_values( $wp_locale->month ),
			'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
			'dayNames'        => array_values( $wp_locale->weekday ),
			'dayNamesShort'   => array_values( $wp_locale->weekday_abbrev ),
			'timezone'        => $timezone,
			'timezone_abbr'   => gmdate( 'T' ),
		];

		$this->localize_script( WPGB_SLUG . '-builder', WPGB_SLUG . '_date_L10n', $date );

	}
}
