<?php
/**
 * Add ACF support
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Third_Party;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle ACF facet values
 *
 * @class WP_Grid_Builder\Includes\Third_Party\ACF
 * @since 1.0.0
 */
class ACF {

	/**
	 * Holds parent field type (repeater/group)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var object
	 */
	public $parent_type = [];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		if ( ! function_exists( 'acf' ) ) {
			return;
		}

		$this->acf_v5 = version_compare( acf()->settings['version'], '5.0', '>=' );

		add_filter( 'wp_grid_builder/custom_fields', [ $this, 'custom_fields' ], 10, 2 );
		add_filter( 'wp_grid_builder/facet/sort_query_vars', [ $this, 'sort_query_vars' ] );
		add_filter( 'wp_grid_builder/indexer/index_object', [ $this, 'index' ], 10, 3 );
		add_filter( 'wp_grid_builder/block/custom_field', [ $this, 'custom_field_block' ], 10, 2 );

	}

	/**
	 * Retrieve all ACF fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $fields Holds registered custom fields.
	 * @param string $key Key type to retrieve.
	 * @return array
	 */
	public function custom_fields( $fields, $key = 'key' ) {

		$this->get_fields();

		if ( ! empty( $this->fields ) ) {

			$fields['Advanced Custom Fields'] = array_combine(
				array_column( $this->fields, $key ),
				array_column( $this->fields, 'label' )
			);

		}

		return $fields;

	}

	/**
	 * Change sort query variables
	 *
	 * @since 1.5.2
	 * @access public
	 *
	 * @param array $query_vars Holds query sort variables.
	 * @return array
	 */
	public function sort_query_vars( $query_vars ) {

		if ( empty( $query_vars['meta_key'] ) ) {
			return $query_vars;
		}

		$acf = explode( 'acf/', $query_vars['meta_key'] );

		if ( empty( $acf[1] ) ) {
			return $query_vars;
		}

		$keys = explode( '/', $acf[1] );

		// Try to handle repeater...
		if ( count( $keys ) > 1 ) {
			$query_vars['meta_key'] = implode( '_$_', $keys );
		} else {
			$query_vars['meta_key'] = $acf[1];
		}

		return $query_vars;

	}

	/**
	 * Suppress all filters from WPML to get fields in all languages
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $query The WP_Query instance.
	 */
	public function supress_filter( $query ) {

		$query->set( 'suppress_filters', true );
		$query->set( 'lang', '' );

	}

	/**
	 * Retrieve all ACF fields
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_fields() {

		if ( isset( $this->fields ) ) {
			return;
		}

		$this->fields = [];
		$field_groups = $this->get_fields_group();

		foreach ( $field_groups as $field_group ) {

			$fields = $this->get_acf_fields( $field_group );

			if ( ! empty( $fields ) ) {
				$this->set_fields( $fields, $field_group );
			}
		}
	}

	/**
	 * Get ACF fields group
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_fields_group() {

		if ( $this->acf_v5 ) {

			add_action( 'pre_get_posts', [ $this, 'supress_filter' ] );
			$field_groups = acf_get_field_groups();
			remove_action( 'pre_get_posts', [ $this, 'supress_filter' ] );

			return $field_groups;

		}

		return apply_filters( 'acf/get_field_groups', [] );

	}

	/**
	 * Get ACF fields from group
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field_group Group attributes.
	 * @return array
	 */
	public function get_acf_fields( $field_group ) {

		if ( $this->acf_v5 ) {
			return acf_get_fields( $field_group );
		}

		return apply_filters( 'acf/field_group/get_fields', [], $field_group['id'] );

	}

	/**
	 * Set each field group in a flat array
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $fields      Field arguments.
	 * @param array  $field_group Field group arguments.
	 * @param string $field_key   Field key.
	 * @param string $field_name  Field Name.
	 * @param string $parents     Parent field key.
	 * @param string $type        Sub field type.
	 */
	public function set_fields( $fields, $field_group, $field_key = '', $field_name = '', $parents = '', $type = '' ) {

		foreach ( $fields as $field ) {

			// Build hierarchical field key.
			$key  = $field_key . '/' . $field['key'];
			$glue = 'group' !== $type ? '/' : '_';
			$name = $field_name . $glue . $field['name'];

			// Ignore flexible content fields.
			if ( 'flexible_content' === $field['type'] ) {
				continue;
			}

			// Get sub fields.
			if ( ! empty( $field['sub_fields'] ) && ( 'repeater' === $field['type'] || 'group' === $field['type'] ) ) {

				$new_parents = $parents . $field['label'] . ' &rsaquo; ';
				$this->parent_type[ $field['key'] ] = $field['type'];
				$this->set_fields( $field['sub_fields'], $field_group, $key, $name, $new_parents, $field['type'] );

			} else {

				$label = $field_group['title'] . ' &rsaquo; ' . $parents . $field['label'];

				$this->fields[] = [
					'label' => 'ACF &rsaquo; ' . $label,
					'name'  => 'acf/' . trim( $name, '/' ),
					'key'   => 'acf/' . trim( $key, '/' ),
				];

			}
		}
	}

	/**
	 * Index Advanced Custom Fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $rows      Holds rows to index.
	 * @param array $object_id Object id to index.
	 * @param array $facet     Holds facet settings.
	 */
	public function index( $rows, $object_id, $facet ) {

		$source = explode( '/', $facet['source'] );
		$source = reset( $source );

		if ( 'post_meta' === $source || 'user_meta' === $source || 'term_meta' === $source ) {
			$rows = $this->index_acf( $rows, $object_id, $facet );
		}

		return $rows;

	}

	/**
	 * Index ACF fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $rows      Holds rows to index.
	 * @param array $object_id Object id to index.
	 * @param array $facet     Holds facet settings.
	 */
	public function index_acf( $rows, $object_id, $facet ) {

		$field = explode( '/acf/', $facet['source'] );

		if ( empty( $field[1] ) ) {
			return $rows;
		}

		$this->get_fields();

		$prefix = '';
		$hierarchy = explode( '/', $field[1] );

		// Handle user and term fields.
		if ( 'post_meta' !== $field[0] ) {
			$prefix = str_replace( 'meta', '', $field[0] );
		}

		$value = get_field( $hierarchy[0], $prefix . $object_id, false );

		// Handle repeater values.
		if ( count( $hierarchy ) > 1 ) {

			$parent = array_shift( $hierarchy );
			$values = $this->process_field( $value, $hierarchy, $parent );
			$field  = $this->get_field_object( $hierarchy[0], $object_id );

			foreach ( $values as $key => $value ) {
				$rows = array_merge( $rows, $this->index_field( $object_id, $facet, $field, $value ) );
			}
		} else {

			$field = $this->get_field_object( $hierarchy[0], $object_id );
			$rows = array_merge( $rows, $this->index_field( $object_id, $facet, $field, $value ) );

		}

		return $rows;

	}

	/**
	 * Get field object depending of ACF version
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string  $selector  Field name or field key.
	 * @param integer $object_id Object id where the value is saved.
	 */
	public function get_field_object( $selector, $object_id ) {

		if ( $this->acf_v5 ) {
			return get_field_object( $selector, $object_id, false, false );
		}

		return get_field_object( $selector, $object_id, [ 'load_value' => false ] );

	}

	/**
	 * Get values for repeated/grouped fields.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string  $value      Field value.
	 * @param string  $hierarchy  Field key (hierarchically formatted).
	 * @param integer $parent_key Key of the parent field.
	 */
	public function process_field( $value, $hierarchy, $parent_key ) {

		$temp_val = [];

		if ( ! is_array( $value ) ) {
			return $temp_val;
		}

		if ( empty( $this->parent_type[ $parent_key ] ) ) {
			return $temp_val;
		}

		// Recursively reduce the hierarchy array.
		$field_key   = array_shift( $hierarchy );
		$parent_type = $this->parent_type[ $parent_key ];

		if ( 'group' === $parent_type ) {

			if ( count( $hierarchy ) ) {
				return $this->process_field( $value[ $field_key ], $hierarchy, $field_key );
			}

			$temp_val[] = $value[ $field_key ];

		} elseif ( 'repeater' === $parent_type ) {

			if ( count( $hierarchy ) ) {

				foreach ( $value as $outer ) {

					if ( ! isset( $outer[ $field_key ] ) ) {
						continue;
					}

					foreach ( $outer[ $field_key ] as $inner ) {
						$temp_val[] = $inner;
					}
				}

				return $this->process_field( $temp_val, $hierarchy, $field_key );

			}

			foreach ( $value as $val ) {
				$temp_val[] = $val[ $field_key ];
			}
		}

		return $temp_val;
	}

	/**
	 * Index ACF field value(s).
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $object_id Object id to index.
	 * @param array $facet     Holds facet settings.
	 * @param array $field     Holds field parameters.
	 * @param array $value     Holds field value.
	 * @return array
	 */
	public function index_field( $object_id, $facet, $field, $value ) {

		$value = maybe_unserialize( $value );

		if ( empty( $field['type'] ) ) {
			return [];
		}

		switch ( $field['type'] ) {
			case 'radio':
			case 'select':
			case 'checkbox':
				$rows = $this->selection_field( $field, $value );
				break;
			case 'page_link':
			case 'post_object':
			case 'relationship':
				$rows = $this->relationship_field( $value );
				break;
			case 'user':
				$rows = $this->user_field( $value );
				break;
			case 'taxonomy':
				$rows = $this->taxonomy_field( $value );
				break;
			case 'date_picker':
				$rows = $this->date_picker_field( $value );
				break;
			case 'time_picker':
				$rows = $this->time_picker_field( $field, $value );
				break;
			case 'true_false':
				$rows = $this->toggle_field( $value );
				break;
			case 'google_map':
				$rows = $this->google_map_field( $value );
				break;
			default:
				$rows = $this->standard_field( $value );
		}

		return $rows;

	}

	/**
	 * Handle ACF choices (select/checkbox/radio fields)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field  Holds field parameters.
	 * @param array $values Holds field value.
	 */
	public function selection_field( $field, $values ) {

		$rows = [];

		if ( ! $values ) {
			return $rows;
		}

		$choices = array_keys( $field['choices'] );
		$orders  = array_flip( $choices );

		foreach ( (array) $values as $value ) {

			$order = 0;
			$name  = $value;

			if ( isset( $field['choices'][ $value ] ) ) {

				$name  = $field['choices'][ $value ];
				$order = $orders[ $value ];

			}

			$rows[] = [
				'facet_value' => $value,
				'facet_name'  => $name,
				'facet_order' => $order,
			];

		}

		return $rows;

	}

	/**
	 * Handle ACF relationship (relationship/post_object fields)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $values Holds field value.
	 */
	public function relationship_field( $values ) {

		$rows = [];

		if ( ! $values ) {
			return $rows;
		}

		foreach ( (array) $values as $value ) {

			$rows[] = [
				'facet_value' => $value,
				'facet_name'  => get_the_title( $value ),
			];

		}

		return $rows;

	}

	/**
	 * Handle ACF user field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $values Holds field value.
	 */
	public function user_field( $values ) {

		$rows = [];

		if ( ! $values ) {
			return $rows;
		}

		foreach ( (array) $values as $value ) {

			$user = get_user_by( 'id', $value );

			if ( ! $user ) {
				continue;
			}

			$rows[] = [
				'facet_value' => $value,
				'facet_name'  => $user->display_name,
			];

		}

		return $rows;

	}

	/**
	 * Handle ACF taxonomy field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $values Holds field value.
	 */
	public function taxonomy_field( $values ) {

		global $wpdb;

		$rows = [];

		if ( empty( $values ) ) {
			return $rows;
		}

		foreach ( (array) $values as $value ) {

			$term = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT name, slug FROM {$wpdb->terms} WHERE term_id = %d LIMIT 1",
					$value
				)
			);

			if ( ! $term ) {
				continue;
			}

			$rows[] = [
				'facet_value' => $term->slug,
				'facet_name'  => $term->name,
				'facet_id'    => (int) $value,
			];

		}

		return $rows;

	}

	/**
	 * Handle ACF date picker field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $value Holds field value.
	 */
	public function date_picker_field( $value ) {

		// Format date value in YYYY-MM-DD.
		if ( 8 === strlen( $value ) && ctype_digit( $value ) ) {
			$value = substr( $value, 0, 4 ) . '-' . substr( $value, 4, 2 ) . '-' . substr( $value, 6, 2 );
		}

		return [
			[
				'facet_value' => $value,
				'facet_name'  => $value,
			],
		];

	}

	/**
	 * Handle ACF time picker field
	 *
	 * @since 1.5.9
	 * @access public
	 *
	 * @param array  $field Holds field parameters.
	 * @param string $value Holds field value.
	 */
	public function time_picker_field( $field, $value ) {

		if ( ! empty( $value ) && ! empty( $field['return_format'] ) ) {
			$value = date( $field['return_format'], strtotime( $value ) ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		}

		return [
			[
				'facet_value' => $value,
				'facet_name'  => $value,
			],
		];

	}

	/**
	 * Handle ACF toggle (treu/false) field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $value Holds field value.
	 */
	public function toggle_field( $value ) {

		$name = (int) $value > 0 ? __( 'Yes', 'wp-grid-builder' ) : __( 'No', 'wp-grid-builder' );

		return [
			[
				'facet_value' => $value,
				'facet_name'  => $name,
			],
		];

	}

	/**
	 * Handle ACF Google Map field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $value Holds field value.
	 */
	public function google_map_field( $value ) {

		if ( ! isset( $value['lat'], $value['lng'] ) ) {
			return [];
		}

		return [
			[
				'facet_value' => $value['lat'],
				'facet_name'  => $value['lng'],
			],
		];

	}

	/**
	 * Handle ACF standard field (string)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $value Holds field value.
	 */
	public function standard_field( $value ) {

		return [
			[
				'facet_value' => $value,
				'facet_name'  => $value,
			],
		];

	}

	/**
	 * Output ACF field value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $output   Custom field output.
	 * @param string $field_id Field identifier.
	 * @return string
	 */
	public function custom_field_block( $output, $field_id ) {

		$post = wpgb_get_post();

		if ( ! isset( $post->metadata ) ) {
			return false;
		}

		$field = explode( 'acf/', $field_id );

		if ( empty( $field[1] ) ) {
			return false;
		}

		$fields = explode( '/', $field[1] );

		// Handle repeater values.
		if ( count( $fields ) > 1 ) {

			$counter   = 0;
			$metadata  = [];
			$repeater  = implode( '_%d_', $fields );
			$sub_field = sprintf( $repeater, $counter );

			while ( isset( $post->metadata[ $sub_field ] ) ) {

				++$counter;
				$metadata[] = $post->metadata[ $sub_field ];
				$sub_field  = sprintf( $repeater, $counter );

				if ( $counter > 50 ) {
					break;
				}
			}

			return $metadata;

		} elseif ( isset( $post->metadata[ $field[1] ] ) ) {
			return $post->metadata[ $field[1] ];
		}

		return false;

	}
}
