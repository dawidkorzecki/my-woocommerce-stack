<?php
/**
 * Term
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings\Forms;

use WP_Grid_Builder\Includes\Settings\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle term fields
 *
 * @class WP_Grid_Builder\Includes\Settings\Forms\Term
 * @since 1.0.0
 */
class Term {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Register taxonomy meta boxed.
		add_action( 'current_screen', [ $this, 'add_meta_boxes' ] );
		// Save term fields on term creation.
		add_action( 'created_term', [ $this, 'save_term' ], 10, 3 );
		// Save term fields on term edit.
		add_action( 'edited_term', [ $this, 'save_term' ], 10, 3 );

	}

	/**
	 * Get settings for a taxonomy
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $taxonomy Settings Taxonomy name.
	 */
	public function get_taxonomy_settings( $taxonomy ) {

		return array_filter(
			Settings::get_instance()->get(),
			function( $setting ) use ( $taxonomy ) {
				return in_array( $taxonomy, $setting['taxonomies'], true );
			}
		);

	}

	/**
	 * Register meta box
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_meta_boxes() {

		global $pagenow;

		if ( 'term.php' !== $pagenow && 'edit-tags.php' !== $pagenow ) {
			return;
		}

		$taxonomy = get_current_screen()->taxonomy;

		add_action( $taxonomy . '_add_form_fields', [ $this, 'add_term' ], 10, 1 );
		add_action( $taxonomy . '_edit_form', [ $this, 'edit_term' ], 10, 2 );

	}

	/**
	 * Render meta box on add term page
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $taxonomy The taxonomy slug.
	 */
	public function add_term( $taxonomy ) {

		$settings = $this->get_taxonomy_settings( $taxonomy );

		foreach ( $settings as $setting ) {

			wp_nonce_field( 'wpgb_fields', 'wpgb_fields_nonce', false );
			Settings::get_instance()->render( $setting['id'] );

		}
	}

	/**
	 * Render meta box on edit term page
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param WP_Term $term     Current taxonomy term object.
	 * @param string  $taxonomy Current taxonomy slug.
	 */
	public function edit_term( $term, $taxonomy ) {

		$settings = $this->get_taxonomy_settings( $taxonomy );

		foreach ( $settings as $setting ) {

			$values = get_term_meta( $term->term_id, '_' . WPGB_SLUG, true );

			wp_nonce_field( 'wpgb_fields', 'wpgb_fields_nonce', false );
			Settings::get_instance()->render( $setting['id'], $values );

		}
	}

	/**
	 * Save term fields
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int    $term_id  Term ID.
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy slug.
	 */
	public function save_term( $term_id, $tt_id, $taxonomy ) {

		$taxonomy   = get_taxonomy( $taxonomy );
		$capability = $taxonomy->cap->manage_terms;

		// Check user capability.
		if ( ! current_user_can( $capability, $term_id ) ) {
			return;
		}

		Settings::get_instance()->save( $term_id, 'term' );

	}
}
