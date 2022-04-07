<?php
/**
 * Term blocks
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retrieve term object from custom loop
 *
 * @since 1.0.0
 *
 * @return object
 */
function wpgb_get_term() {

	return wpgb_get_object();

}

/**
 * Retrieve the term ID
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_the_term_id() {

	return wpgb_get_the_id();

}

/**
 * Display the term ID
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_term_id( $block = [], $action = [] ) {

	wpgb_the_id( $block, $action );

}

/**
 * Retrieve the term name
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_term_name() {

	$post = wpgb_get_post();

	if ( ! isset( $post->term_name ) ) {
		return;
	}

	return $post->term_name;

}

/**
 * Display the term name
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_term_name( $block = [], $action = [] ) {

	$name = wpgb_get_term_name();

	if ( empty( $name ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $name );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the term slug
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_term_slug() {

	$post = wpgb_get_post();

	if ( ! isset( $post->term_slug ) ) {
		return;
	}

	return $post->term_slug;

}

/**
 * Display the term slug
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_term_slug( $block = [], $action = [] ) {

	$slug = wpgb_get_term_slug();

	if ( empty( $slug ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $slug );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the term taxonomy
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_term_taxonomy() {

	$post = wpgb_get_post();

	if ( ! isset( $post->term_taxonomy ) ) {
		return;
	}

	return $post->term_taxonomy;

}

/**
 * Display the term taxonomy
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_term_taxonomy( $block = [], $action = [] ) {

	global $wp_taxonomies;

	$taxonomy = wpgb_get_term_taxonomy();

	if ( empty( $taxonomy ) ) {
		return;
	}

	if ( isset( $wp_taxonomies[ $taxonomy ]->label ) ) {
		$taxonomy = $wp_taxonomies[ $taxonomy ]->label;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( ucfirst( $taxonomy ) );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the term parent
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_term_parent() {

	$post = wpgb_get_post();

	if ( ! isset( $post->term_parent ) ) {
		return;
	}

	return $post->term_parent;

}

/**
 * Display the term parent
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_term_parent( $block = [], $action = [] ) {

	$parent = wpgb_get_term_parent();

	if ( empty( $parent ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $parent );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the term description
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_term_description() {

	return wpgb_get_the_content();

}

/**
 * Display the term description
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_term_description( $block = [], $action = [] ) {

	wpgb_the_excerpt( $block, $action );

}

/**
 * Retrieve the term count
 *
 * @since 1.0.0
 *
 * @return array
 */
function wpgb_get_term_count() {

	$post = wpgb_get_post();

	if ( ! isset( $post->term_count ) ) {
		return;
	}

	return $post->term_count;

}

/**
 * Display the term count
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_term_count( $block = [], $action = [] ) {

	$number = wpgb_get_term_count();

	if ( ! is_numeric( $number ) ) {
		return;
	}

	$type = isset( $block['count_format'] ) ? $block['count_format'] : 'text';

	if ( 'text' === $type ) {

		if ( $number > 1 ) {
			/* translators: %s: number of posts */
			$number = sprintf( _n( '%s Post', '%s Posts', (int) $number, 'wp-grid-builder' ), number_format_i18n( $number ) );
		} elseif ( 0 === $number ) {
			$number = __( 'No Posts', 'wp-grid-builder' );
		} else {
			$number = __( '1 Post', 'wp-grid-builder' );
		}
	} else {
		$number = Helpers::shorten_number_format( $number );
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $number );
	wpgb_block_end( $block, $action );

}
