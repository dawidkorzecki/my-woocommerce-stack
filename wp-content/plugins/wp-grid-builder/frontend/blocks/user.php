<?php
/**
 * User blocks
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
 * Retrieve user object from custom loop
 *
 * @since 1.0.0
 *
 * @return object
 */
function wpgb_get_user() {

	return wpgb_get_object();

}

/**
 * Retrieve the user ID
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_the_user_id() {

	return wpgb_get_the_id();

}

/**
 * Display the user ID
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_id( $block = [], $action = [] ) {

	wpgb_the_id( $block, $action );

}

/**
 * Retrieve the user display name
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_user_display_name() {

	$post = wpgb_get_post();

	if ( ! isset( $post->display_name ) ) {
		return;
	}

	return $post->display_name;

}

/**
 * Display the user display name
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_display_name( $block = [], $action = [] ) {

	$display_name = wpgb_get_user_display_name();

	if ( empty( $display_name ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $display_name );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the user first name
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_user_first_name() {

	$post = wpgb_get_post();

	if ( ! isset( $post->first_name ) ) {
		return;
	}

	return $post->first_name;

}

/**
 * Display the user first name
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_first_name( $block = [], $action = [] ) {

	$first_name = wpgb_get_user_first_name();

	if ( empty( $first_name ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $first_name );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the user last name
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_user_last_name() {

	$post = wpgb_get_post();

	if ( ! isset( $post->last_name ) ) {
		return;
	}

	return $post->last_name;

}

/**
 * Display the user last name
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_last_name( $block = [], $action = [] ) {

	$last_name = wpgb_get_user_last_name();

	if ( empty( $last_name ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $last_name );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the user nickname
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_user_nickname() {

	$post = wpgb_get_post();

	if ( ! isset( $post->nickname ) ) {
		return;
	}

	return $post->nickname;

}

/**
 * Display the user nickname
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_nickname( $block = [], $action = [] ) {

	$nickname = wpgb_get_user_nickname();

	if ( empty( $nickname ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $nickname );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the user login
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_user_login() {

	$post = wpgb_get_post();

	if ( ! isset( $post->user_login ) ) {
		return;
	}

	return $post->user_login;

}

/**
 * Display the user login
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_login( $block = [], $action = [] ) {

	$login = wpgb_get_user_login();

	if ( empty( $login ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $login );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the user description
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_user_description() {

	return wpgb_get_the_content();

}

/**
 * Display the user description
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_description( $block = [], $action = [] ) {

	wpgb_the_excerpt( $block, $action );

}

/**
 * Retrieve the user email
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_user_email() {

	$post = wpgb_get_post();

	if ( ! isset( $post->user_email ) ) {
		return;
	}

	return $post->user_email;

}

/**
 * Display the user email
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_email( $block = [], $action = [] ) {

	$email = wpgb_get_user_email();

	if ( empty( $email ) ) {
		return;
	}

	$email = sanitize_email( $email );
	$class = wpgb_get_block_class( $block );
	$label = esc_html__( 'Send email', 'wp-grid-builder' );

	printf(
		'<a class="%s" href="%s" aria-label="%s">%s</a>',
		esc_attr( $class ),
		esc_url( 'mailto:' . $email ),
		esc_attr( $label ),
		esc_html( $email )
	);

}

/**
 * Retrieve the user url
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_user_url() {

	$post = wpgb_get_post();

	if ( ! isset( $post->user_url ) ) {
		return;
	}

	return $post->user_url;

}

/**
 * Display the user email
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_url( $block = [], $action = [] ) {

	$url = wpgb_get_user_url();

	if ( empty( $url ) ) {
		return;
	}

	$text  = isset( $block['website_text'] ) && ! empty( $block['website_text'] ) ? $block['website_text'] : $url;
	$class = wpgb_get_block_class( $block );
	$label = esc_html__( 'Visit website', 'wp-grid-builder' );

	printf(
		'<a class="%s" href="%s" rel="external noopener noreferrer" target="_blank" aria-label="%s">%s</a>',
		esc_attr( $class ),
		esc_url( $url ),
		esc_attr( $label ),
		esc_html( $text )
	);

}

/**
 * Retrieve the user roles
 *
 * @since 1.0.0
 *
 * @return array
 */
function wpgb_get_user_roles() {

	$post = wpgb_get_post();

	if ( ! isset( $post->user_roles ) ) {
		return;
	}

	return $post->user_roles;

}

/**
 * Display the user roles
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_roles( $block = [], $action = [] ) {

	global $l10n;

	static $domain_loaded = false;

	$roles = wpgb_get_user_roles();

	if ( empty( $roles ) ) {
		return;
	}

	// Role translations are not loaded on frontend.
	if ( ! $domain_loaded && ! is_admin() ) {

		load_textdomain( 'default', WP_LANG_DIR . '/admin-' . get_locale() . '.mo' );
		$domain_loaded = true;

	}

	$wp_roles = Helpers::get_roles();

	$roles = array_map(
		function( $role ) use ( $wp_roles ) {

			if ( isset( $wp_roles[ $role ] ) ) {
				return $wp_roles[ $role ];
			}

		},
		(array) $roles
	);

	wpgb_block_start( $block, $action );
		echo esc_html( join( ', ', $roles ) );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the user post count
 *
 * @since 1.0.0
 *
 * @return array
 */
function wpgb_get_user_post_count() {

	$post = wpgb_get_post();

	if ( ! isset( $post->user_post_count ) ) {
		return;
	}

	return $post->user_post_count;

}

/**
 * Display the user posts count
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_user_post_count( $block = [], $action = [] ) {

	$number = wpgb_get_user_post_count();

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
