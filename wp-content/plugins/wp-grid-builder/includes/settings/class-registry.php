<?php
/**
 * Registry
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setting fields API
 *
 * @class WP_Grid_Builder\Includes\Settings\Settings
 * @since 1.0.0
 */
trait Registry {

	/**
	 * Holds registered settings.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $registry = [];

	/**
	 * Holds defaults settings args.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $defaults = [
		'post_types' => [],
		'taxonomies' => [],
		'users'      => [],
		'fields'     => [],
		'header'     => [],
		'title'      => '',
		'tabs'       => [],
	];

	/**
	 * Add settings in registry
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds settings.
	 * @param array $defaults Holds default field values.
	 */
	public function add( $settings = [], $defaults = [] ) {

		if ( empty( $settings['id'] ) ) {
			return;
		}

		$settings['defaults'] = $defaults;
		$settings = $this->normalize( $settings );

		$this->registry[ $settings['id'] ] = $settings;

	}

	/**
	 * Get settings from registry
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $id Settings id.
	 * @return array Holds settings.
	 */
	public function get( $id = '' ) {

		if ( empty( $id ) ) {
			return $this->registry;
		}

		if ( empty( $this->registry[ $id ] ) ) {
			return [];
		}

		return $this->registry[ $id ];

	}

	/**
	 * Delete settings from registry
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $id Settings id.
	 */
	public function delete( $id = '' ) {

		unset( $this->registry[ $id ] );

	}

	/**
	 * Normalize settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds settings.
	 * @return array Normalized settings.
	 */
	public function normalize( $settings ) {

		$settings = wp_parse_args( $settings, $this->defaults );

		array_walk(
			$settings,
			function( &$value, $key ) use ( $settings ) {

				$filter = 'wp_grid_builder/settings/' . $settings['id'] . '_' . $key;
				$value  = apply_filters( $filter, $value );

			}
		);

		return $settings;

	}
}
