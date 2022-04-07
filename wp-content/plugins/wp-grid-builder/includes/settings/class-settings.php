<?php
/**
 * Registry
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings;

use WP_Grid_Builder\Includes\Singleton;

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
class Settings extends Form {

	use Registry;
	use Singleton;

	/**
	 * Holds settings.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $settings = [];

	/**
	 * Field nonce.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $nonce = 'wpgb_fields_nonce';

	/**
	 * Save acton name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $action = 'wpgb_fields';

	/**
	 * Metadata key.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $key = '_wpgb';

	/**
	 * Fields slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $slug = 'wpgb';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found

		parent::__construct();

	}

	/**
	 * Register settings
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds settings.
	 * @param array $defaults Holds default setting values.
	 */
	public function register( $settings = [], $defaults = [] ) {

		$this->add( $settings, $defaults );

	}

	/**
	 * Render settings form
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $id     Settings id.
	 * @param array  $values Holds field values.
	 */
	public function render( $id = '', $values = [] ) {

		$this->settings = $this->get( $id );

		if ( empty( $this->settings ) ) {
			return;
		}

		$this->settings['values'] = $values;
		do_action( 'wp_grid_builder/settings/render_fields', $this->settings );

		$this->output();

	}

	/**
	 * Save setting fields (metadata)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $id   Settings id.
	 * @param string $type Metadata type.
	 */
	public function save( $id = '', $type = '' ) {

		$post = $this->post();

		if ( false === $post ) {
			return;
		}

		$old = get_metadata( $type, $id, $this->key, true );
		$new = $this->sanitize( $post );
		$new = wp_parse_args( $new, $old );
		$new = apply_filters( 'wp_grid_builder/settings/save_fields', $new, $type, $id );
		$new = wp_slash( $new );

		update_metadata( $type, $id, $this->key, $new );

	}

	/**
	 * Sanitize setting fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds settings values.
	 * @param array $fields   Holds settings fields.
	 * @param array $values   Holds Sanitized values.
	 * @return array Sanitized values.
	 */
	public function sanitize( $settings = [], $fields = [], $values = [] ) {

		if ( empty( $fields ) ) {
			$fields = $this->get_fields();
		}

		foreach ( $settings as $key => $value ) {

			if ( ! empty( $fields[ $key ]['fields'] ) ) {
				// Recursively sanitize subfield values.
				$value = $this->sanitize( $value, $fields[ $key ]['fields'] );
			}

			if ( isset( $fields[ $key ] ) ) {
				// Sanitize field value or all subfield values from table, repeater, builder, etc...
				$values[ $key ] = $this->sanitize_field_value( $fields[ $key ], $value );
			} elseif ( is_array( $value ) ) {

				// We sanitize unknown key.
				$key = sanitize_key( $key );
				// Allow to sanitize array of values if sub keys exist.
				$values[ $key ] = $this->sanitize( $value, $fields );

			}
		}

		return $values;

	}

	/**
	 * Check nonce and get field values.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return array Holds field values.
	 */
	private function post() {

		$data = wp_unslash( $_POST );

		if ( empty( $data[ $this->slug ] ) ) {
			return false;
		}

		if ( empty( $data[ $this->nonce ] ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( $data[ $this->nonce ], $this->action ) ) {
			return false;
		}

		return $data[ $this->slug ];

	}
}
