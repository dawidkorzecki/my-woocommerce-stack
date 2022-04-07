<?php
/**
 * Form
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings;

use WP_Grid_Builder\Includes\Settings\Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hanlde szettings form
 *
 * @class WP_Grid_Builder\Includes\Settings\Form
 * @since 1.0.0
 */
class Form extends Fields {

	/**
	 * Holds defaults tab args.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $tab = [
		'id'       => '',
		'icon'     => '',
		'label'    => '',
		'title'    => '',
		'subtitle' => '',
	];

	/**
	 * Holds defaults button args.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $button = [
		'icon'   => '',
		'title'  => '',
		'color'  => '',
		'action' => '',
	];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		parent::__construct();

		new Forms\Post();
		new Forms\User();
		new Forms\Term();

	}

	/**
	 * Output settings
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function output() {

		include 'views/settings.php';

	}

	/**
	 * Check if settings has form (options)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function has_form() {

		$is_postmeta = ! empty( $this->settings['post_types'] );
		$is_termmeta = ! empty( $this->settings['taxonomies'] );
		$is_usermeta = ! empty( $this->settings['users'] );

		return ! $is_postmeta && ! $is_termmeta && ! $is_usermeta;

	}

	/**
	 * Get tab collapsed value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function is_collapsed() {

		if ( empty( $_COOKIE[ $this->slug . '_settings_collapsed' ] ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Get tabs
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Holds settings tabs.
	 */
	public function get_tabs() {

		if ( empty( $this->settings['tabs'] ) ) {
			return [];
		}

		return array_map(
			function( $tab ) {
				return wp_parse_args( $tab, $this->tab );
			},
			$this->settings['tabs']
		);

	}

	/**
	 * Get active tab id
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Active tab id.
	 */
	public function get_active_tab() {

		$tabs = array_map(
			function( $tab ) {
				return $tab['id'];
			},
			$this->get_tabs()
		);

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['tab'] ) ) {

			$id = sanitize_title( wp_unslash( $_GET['tab'] ) );

			if ( in_array( $id, $tabs, true ) ) {
				return $id;
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return reset( $tabs );

	}

	/**
	 * Get header buttons
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Holds header buttons.
	 */
	public function get_header_buttons() {

		if ( empty( $this->settings['header']['buttons'] ) ) {
			return [];
		}

		return array_map(
			function( $button ) {
				return wp_parse_args( $button, $this->button );
			},
			$this->settings['header']['buttons']
		);

	}
}
