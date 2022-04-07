<?php
/**
 * Post type blocks
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
 * Retrieve post object from custom loop
 *
 * @since 1.0.0
 *
 * @return object
 */
function wpgb_get_post() {

	return wpgb_get_object();

}

/**
 * Retrieve the post ID
 *
 * @since 1.0.0
 *
 * @return integer
 */
function wpgb_get_the_id() {

	$post = wpgb_get_post();

	if ( ! isset( $post->ID ) ) {
		return false;
	}

	return (int) $post->ID;

}

/**
 * Display the post ID
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_id( $block = [], $action = [] ) {

	$is_overview = wpgb_is_overview();

	wpgb_block_start( $block, $action );
		echo esc_html( ! $is_overview ? wpgb_get_the_id() : 9 );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the post permalink
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_the_permalink() {

	$post = wpgb_get_post();

	if ( ! isset( $post->permalink ) ) {
		return;
	}

	return $post->permalink;

}

/**
 * Retrieve the post status
 *
 * @since 1.0.0
 * @global object $wp_post_statuses
 *
 * @return string
 */
function wpgb_get_post_status() {

	global $wp_post_statuses;

	$post = wpgb_get_post();

	if ( ! isset( $post->post_status ) ) {
		return;
	}

	if ( ! isset( $wp_post_statuses[ $post->post_status ] ) ) {
		return $post->post_status;
	}

	return $wp_post_statuses[ $post->post_status ]->label;

}

/**
 * Display the post status
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_post_status( $block = [], $action = [] ) {

	$status = wpgb_get_post_status();

	if ( empty( $status ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $status );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the format slug
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_post_format() {

	$post = wpgb_get_post();

	if ( ! isset( $post->post_format ) ) {
		return;
	}

	return $post->post_format;

}

/**
 * Display the format slug
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_post_format( $block = [], $action = [] ) {

	$format = wpgb_get_post_format();

	if ( empty( $format ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( get_post_format_string( $format ) );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the post type
 *
 * @since 1.0.0
 * @global object $wp_post_types
 *
 * @return string
 */
function wpgb_get_post_type() {

	$post = wpgb_get_post();

	if ( ! isset( $post->post_type ) ) {
		return;
	}

	return $post->post_type;

}

/**
 * Display the post type (singular name)
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_post_type( $block = [], $action = [] ) {

	global $wp_post_types;

	$post_type = wpgb_get_post_type();

	if ( empty( $post_type ) ) {
		return;
	}

	if ( isset( $wp_post_types[ $post_type ] ) ) {
		$post_type = $wp_post_types[ $post_type ]->labels->singular_name;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $post_type );
	wpgb_block_end( $block, $action );

}

/**
 * Filters title.
 *
 * @since 1.0.0
 */
add_filter( 'wp_grid_builder/the_title', 'capital_P_dangit' );
add_filter( 'wp_grid_builder/the_title', 'wptexturize' );
add_filter( 'wp_grid_builder/the_title', 'convert_chars' );
add_filter( 'wp_grid_builder/the_title', 'trim' );

/**
 * Retrieve the post title
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_the_title() {

	$post = wpgb_get_post();

	if ( ! isset( $post->post_title ) ) {
		return;
	}

	return $post->post_title;

}

/**
 * Display the post title
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_title( $block = [], $action = [] ) {

	$title = wpgb_get_the_title();

	if ( 0 === strlen( $title ) ) {
		return;
	}

	$title = apply_filters( 'wp_grid_builder/the_title', $title );

	wpgb_block_start( $block, $action );
		echo wp_kses_post( $title );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the post name
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_the_name() {

	$post = wpgb_get_post();

	if ( ! isset( $post->post_name ) ) {
		return;
	}

	return $post->post_name;

}

/**
 * Display the post name
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_name( $block = [], $action = [] ) {

	$name = wpgb_get_the_name();

	if ( 0 === strlen( $name ) ) {
		return;
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $name );
	wpgb_block_end( $block, $action );

}

/**
 * Filters content.
 *
 * We do not apply the_content filter to prevent layout issue.
 * The aim of this custom filter is to prevent any issue with themes and plugins.
 * By using the native WordPress filter it may create conflicts.
 *
 * @since 1.0.0
 */
add_filter( 'wp_grid_builder/the_content', 'capital_P_dangit' );
add_filter( 'wp_grid_builder/the_content', 'shortcode_unautop' );
add_filter( 'wp_grid_builder/the_content', 'wpgb_strip_blocks' );
add_filter( 'wp_grid_builder/the_content', 'wptexturize' );
add_filter( 'wp_grid_builder/the_content', 'convert_smilies', 20 );
add_filter( 'wp_grid_builder/the_content', 'convert_chars' );
add_filter( 'wp_grid_builder/the_content', 'wpautop' );
add_filter( 'wp_grid_builder/the_content', 'do_shortcode', 11 );
add_filter( 'wp_grid_builder/the_content', 'trim' );

if ( function_exists( 'do_blocks' ) ) {
	add_filter( 'wp_grid_builder/the_content', 'do_blocks', 9 );
}

/**
 * Filters excerpt.
 *
 * We do not apply the_excerpt filter to prevent layout issue.
 * The aim of this custom filter is to prevent any issue with themes and plugins.
 * By using the native WordPress filter it may create conflicts.
 *
 * @since 1.1.8
 */
add_filter( 'wp_grid_builder/the_excerpt', 'capital_P_dangit' );
add_filter( 'wp_grid_builder/the_excerpt', 'shortcode_unautop' );
add_filter( 'wp_grid_builder/the_excerpt', 'wpgb_strip_blocks' );
add_filter( 'wp_grid_builder/the_excerpt', 'wpgb_strip_shortcodes' );
add_filter( 'wp_grid_builder/the_excerpt', 'strip_shortcodes' );
add_filter( 'wp_grid_builder/the_excerpt', 'wptexturize' );
add_filter( 'wp_grid_builder/the_excerpt', 'convert_smilies' );
add_filter( 'wp_grid_builder/the_excerpt', 'convert_chars' );
add_filter( 'wp_grid_builder/the_excerpt', 'trim' );

/**
 * Strip internal shortcodes and blocks.
 *
 * @since 1.0.0
 *
 * @param string $string Content to strip.
 * @return string
 */
function wpgb_strip_blocks( $string = '' ) {

	if ( empty( $string ) ) {
		return '';
	}

	// Strip plugin blocks.
	$string = preg_replace( '/<!--\s+\/?wp:wp-grid-builder.*?-->\r?\n?/m', '', $string );
	// Strip plugin shortcodes (in case someone hook into strip_shortcodes_tagnames).
	$string = preg_replace( '/\[wpgb_.*?\]/m', '', $string );

	return $string;

}

/**
 * Strip shortcodes and keep content.
 *
 * @since 1.0.0
 *
 * @param string $string Content to strip.
 * @return string
 */
function wpgb_strip_shortcodes( $string = '' ) {

	if ( empty( $string ) ) {
		return '';
	}

	// Strip all shortcodes.
	$string = preg_replace( '~(?:\[/?)[^/\]]+/?\]~s', '', $string ); // More aggressive regex '~(\[(?:\[??[^\[]*?\]))~s'.
	$string = preg_replace( '/\[(\/?(vc_column|vc_column_text).*?(?=\]))\]/', '', $string ); // Because vc_column include slash (1/4) that breaks regex.

	return $string;

}

/**
 * Filters content and keeps only allowable HTML elements.
 * Prevent layout and style conflicts.
 *
 * @since 1.0.0
 *
 * @param string $string Content to filter through kses.
 * @return string
 */
function wpgb_kses_post( $string = '' ) {

	if ( empty( $string ) ) {
		return;
	}

	// Get allowed post tags.
	$allowed = wp_kses_allowed_html( 'post' );

	// Remove img, audio, video and iframe tags.
	unset( $allowed['img'] );
	unset( $allowed['audio'] );
	unset( $allowed['video'] );
	unset( $allowed['iframe'] );

	// Remove not allowed HTML tags.
	$string = wp_kses( $string, $allowed );

	return $string;

}

/**
 * Retrieve the post content
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_the_content() {

	$post = wpgb_get_post();

	if ( ! isset( $post->post_content ) ) {
		return;
	}

	return $post->post_content;

}

/**
 * Display the post content
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_content( $block = [], $action = [] ) {

	$content = wpgb_get_the_content();

	if ( empty( $content ) ) {
		return;
	}

	// We escape before to apply the_content filter (post content is also sanitized on input).
	// It allows to preserve blocks and shortcodes markup from 3rd party plugins.
	$content = wpgb_kses_post( $content );
	$content = apply_filters( 'wp_grid_builder/the_content', $content );

	// If HTML tags in the content make sure to be W3C compliant.
	// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
	if ( strip_tags( $content ) !== $content ) {
		$block['tag'] = 'div';
	}

	wpgb_block_start( $block, $action );
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the post excerpt
 *
 * @since 1.1.8 Added excerpt_remove_blocks() support.
 * @since 1.0.0
 *
 * @param integer $length Excerpt length words count.
 * @param string  $suffix Excerpt suffix.
 * @return string
 */
function wpgb_get_the_excerpt( $length = 55, $suffix = '...' ) {

	$post = wpgb_get_post();

	if ( empty( $post->post_excerpt ) ) {
		$excerpt = wpgb_get_the_content();
	} else {
		$excerpt = $post->post_excerpt;
	}

	// Strip inappropriate blocks from excerpts.
	if ( function_exists( 'excerpt_remove_blocks' ) ) {

		$excerpt = excerpt_remove_blocks( $excerpt );
		// Reformat paragraphs from striped blocks.
		$excerpt = wpautop( $excerpt );

	}

	$excerpt = apply_filters( 'wp_grid_builder/the_excerpt', $excerpt );

	// Keep markup if not trimmed.
	if ( -1 >= (int) $length ) {
		return $excerpt;
	}

	if ( '' === $excerpt ) {
		$length = 55;
	}

	$trimmed = wp_trim_words( $excerpt, 0 === (int) $length ? 55 : (int) $length, '' );
	// PHP_INT_MAX makes calculation error in wp_trim_words (no int casting).
	$stripped = wp_trim_words( $excerpt, 999999, '' );

	// Keep markup if full excerpt.
	if ( $stripped === $trimmed ) {
		return $excerpt;
	}

	return $trimmed . $suffix;

}

/**
 * Display the post excerpt
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_excerpt( $block = [], $action = [] ) {

	$length  = ! empty( $block['excerpt_length'] ) ? (int) $block['excerpt_length'] : 35;
	$suffix  = isset( $block['excerpt_suffix'] ) ? $block['excerpt_suffix'] : '';
	$excerpt = wpgb_get_the_excerpt( $length, $suffix );

	if ( empty( $excerpt ) ) {
		return;
	}

	// If HTML tags in the excerpt make sure to be W3C compliant.
	// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
	if ( strip_tags( $excerpt ) !== $excerpt ) {
		$block['tag'] = 'div';
	}

	wpgb_block_start( $block, $action );
		echo wpgb_kses_post( $excerpt ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the terms of the taxonomies that are attached to the post
 *
 * @since 1.0.0
 *
 * @return object
 */
function wpgb_get_the_terms() {

	$post = wpgb_get_post();

	if ( ! isset( $post->post_terms ) ) {
		return new stdClass();
	}

	return (object) $post->post_terms;

}

/**
 * Display the terms of the taxonomies that are attached to the post
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_terms( $block = [], $action = [] ) {

	$terms = wpgb_get_the_terms();

	if ( empty( $terms ) ) {
		return;
	}

	$list       = [];
	$taxonomy   = isset( $block['taxonomy'] ) ? $block['taxonomy'] : '';
	$term_glue  = isset( $block['term_glue'] ) ? $block['term_glue'] : '';
	$term_link  = isset( $block['term_link'] ) ? $block['term_link'] : false;
	$term_color = isset( $block['term_color'] ) ? $block['term_color'] : false;

	// Color scheme need to be applied on each term.
	$color_scheme = wpgb_block_color_scheme( $block );

	// Prevent applying scheme on the block.
	unset( $block['idle_scheme'] );
	unset( $block['hover_scheme'] );

	// Unset term links to prevent markup issue.
	if ( wpgb_get_block_link( $action ) ) {
		$term_link = false;
	}

	foreach ( $terms as $index => $term ) {

		$style = '';

		$term = (object) wp_parse_args(
			$term,
			[
				'term_id'    => 0,
				'name'       => '',
				'taxonomy'   => 'category',
				'link'       => null,
				'color'      => null,
				'background' => null,
			]
		);

		if ( ! empty( $taxonomy ) && ! in_array( $term->taxonomy, $taxonomy, true ) ) {
			continue;
		}

		if ( $term_color ) {

			$style .= $term->color ? 'color:' . $term->color . ';' : '';
			$style .= $term->background ? 'background:' . $term->background : '';
			$style  = rtrim( $style, ';' );
			$style  = ! empty( $style ) ? ' style="' . esc_attr( $style ) . '"' : '';

		}

		if ( $term_link && ! empty( $term->link ) ) {

			$list[] = sprintf(
				'<a class="wpgb-block-term%1$s" href="%2$s" rel="category" data-id="%3$d"%5$s>%4$s</a>',
				esc_attr( $color_scheme ),
				esc_url( $term->link ),
				esc_attr( $term->term_id ),
				esc_html( $term->name ),
				$style
			);

		} else {

			$list[] = sprintf(
				'<span class="wpgb-block-term%1$s" data-id="%2$d"%4$s>%3$s</span>',
				esc_attr( $color_scheme ),
				esc_attr( $term->term_id ),
				esc_html( $term->name ),
				$style
			);

		}
	}

	if ( empty( $list ) ) {
		return;
	}

	if ( ! empty( $term_glue ) ) {

		$term_glue = sprintf(
			'<span%1$s>%2$s</span>',
			$color_scheme ? ' class="' . esc_attr( $color_scheme ) . '"' : '',
			esc_html( $term_glue )
		);

	}

	wpgb_block_start( $block, $action );
		echo join( $term_glue, $list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	wpgb_block_end( $block, $action );

}

/**
 * Format date
 *
 * @since 1.0.0
 *
 * @param string $date Date.
 * @param string $format Date format to return.
 * @return string
 */
function wpgb_format_date( $date = '', $format = '' ) {

	if ( empty( $date ) ) {
		return;
	}

	if ( empty( $format ) ) {
		$format = get_option( 'date_format' );
	}

	if ( 'ago' === strtolower( $format ) ) {

		$date = human_time_diff( $date, date_i18n( 'U' ) );
		/* translators: %s: Human time diff */
		$date = sprintf( __( '%s ago', 'wp-grid-builder' ), $date );

	} else {
		$date = date_i18n( $format, $date );
	}

	return $date;

}

/**
 * Retrieve the date of the current post was written (once per date)
 *
 * @since 1.0.0
 *
 * @param string $format Date format.
 * @return string
 */
function wpgb_get_the_date( $format = '' ) {

	$post = wpgb_get_post();

	if ( ! isset( $post->post_date ) ) {
		return;
	}

	$date = wpgb_format_date( $post->post_date, $format );

	return $date;

}

/**
 * Display the date of the current post was written (once per date)
 *
 * @since 1.1.8 Added datetime attribute for time HTML tag.
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_date( $block = [], $action = [] ) {

	$format = isset( $block['date_format'] ) ? $block['date_format'] : '';
	$date   = wpgb_get_the_date( $format );

	if ( empty( $date ) ) {
		return;
	}

	if ( isset( $block['tag'] ) && 'time' === $block['tag'] ) {

		$block['attr'] = [
			'datetime' => wpgb_get_the_date( 'c' ),
		];

	}

	wpgb_block_start( $block, $action );
		echo wp_kses_post( $date );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the date on which the post was last modified
 *
 * @since 1.0.0
 *
 * @param string $format Date format.
 * @return string
 */
function wpgb_get_the_modified_date( $format = '' ) {

	$post = wpgb_get_post();

	if ( ! isset( $post->post_modified ) ) {
		return;
	}

	$date = wpgb_format_date( $post->post_modified, $format );

	return $date;

}

/**
 * Display the date on which the post was last modified
 *
 * @since 1.1.8 Added datetime attribute for time HTML tag.
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_modified_date( $block = [], $action = [] ) {

	$format = isset( $block['date_format'] ) ? $block['date_format'] : '';
	$date   = wpgb_get_the_modified_date( $format );

	if ( empty( $date ) ) {
		return;
	}

	if ( isset( $block['tag'] ) && 'time' === $block['tag'] ) {

		$block['attr'] = [
			'datetime' => wpgb_get_the_modified_date( 'c' ),
		];

	}

	wpgb_block_start( $block, $action );
		echo wp_kses_post( $date );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieves the requested data of the author of the current post
 *
 * @since 1.0.0
 *
 * @param string $field The Author field to retrieve.
 * @return string
 */
function wpgb_get_the_author_meta( $field = '' ) {

	$post = wpgb_get_post();

	if ( ! isset( $post->post_author ) || empty( $field ) ) {
		return;
	}

	if ( ! isset( $post->post_author[ $field ] ) ) {
		return;
	}

	return $post->post_author[ $field ];

}

/**
 * Retrieve author posts url
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_author_posts_url() {

	return wpgb_get_the_author_meta( 'posts_url' );

}
/**
 * Retrieve the post author display name
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_the_author() {

	return wpgb_get_the_author_meta( 'display_name' );

}

/**
 * Display the post author display name
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_author( $block = [], $action = [] ) {

	$author = wpgb_get_the_author();

	if ( empty( $author ) ) {
		return;
	}

	$prefix = isset( $block['author_prefix'] ) ? $block['author_prefix'] : '';

	wpgb_block_start( $block, $action );
		echo esc_html( $prefix . $author );
	wpgb_block_end( $block, $action );

}

/**
 * Retrieve default data about the avatar.
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_avatar_data() {

	$avatar = wpgb_get_the_author_meta( 'avatar' );

	if ( empty( $avatar ) ) {
		return;
	}

	$avatar = wp_parse_args(
		$avatar,
		[
			'url'    => '',
			'width'  => 96,
			'height' => 96,
		]
	);

	return $avatar;

}

/**
 * Retrieve the post avatar url
 *
 * @since 1.0.0
 *
 * @return string
 */
function wpgb_get_avatar_url() {

	$avatar = wpgb_get_avatar_data();

	if ( empty( $avatar['url'] ) ) {
		return;
	}

	return $avatar['url'];

}

/**
 * Retrieve the post avatar
 *
 * @since 1.0.0
 *
 * @param array $atts Holds image attributes.
 * @return string
 */
function wpgb_get_avatar( $atts = [] ) {

	$avatar = wpgb_get_avatar_data();
	$author = wpgb_get_the_author();

	if ( empty( $avatar['url'] ) ) {
		return;
	}

	return sprintf(
		'<img%s alt="%s" src="%s" height="%d" width="%d">',
		! empty( $atts['class'] ) ? ' class="' . esc_attr( $atts['class'] ) . '"' : '',
		/* translators: %s: Avatar name */
		esc_attr( sprintf( __( 'Avatar for %s', 'wp-grid-builder' ), $author ) ),
		esc_url( $avatar['url'] ),
		(int) $avatar['height'],
		(int) $avatar['width']
	);

}

/**
 * Display the post avatar
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_the_avatar( $block = [], $action = [] ) {

	$avatar = wpgb_get_avatar_data();

	if ( empty( $avatar['url'] ) ) {
		return;
	}

	$lazy_load = wpgb_get_grid_settings( 'lazy_load' );

	wpgb_block_start( $block, $action );

	if ( $lazy_load ) {

		echo '<span class="wpgb-block-avatar wpgb-lazy-load" data-wpgb-src="' . esc_url( $avatar['url'] ) . '"></span>';
		echo '<noscript>' . wpgb_get_avatar( [ 'class' => 'wpgb-noscript-img' ] ) . '</noscript>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	} else {
		echo '<span class="wpgb-block-avatar" style="background-image: url(' . esc_url( $avatar['url'] ) . ')"></span>';
	}

	wpgb_block_end( $block, $action );

}

/**
 * Retrieve the amount of comments the post has
 *
 * @since 1.0.0
 *
 * @return integer
 */
function wpgb_get_comments_number() {

	$post = wpgb_get_post();

	if ( ! isset( $post->comment_count ) ) {
		return;
	}

	return $post->comment_count;

}

/**
 * Display the amount of comments the post has
 *
 * @since 1.0.0
 *
 * @param array $block  Holds block args.
 * @param array $action Holds action args.
 */
function wpgb_comments_number( $block = [], $action = [] ) {

	$number = wpgb_get_comments_number();

	if ( ! is_numeric( $number ) ) {
		return;
	}

	$type = isset( $block['count_format'] ) ? $block['count_format'] : 'text';

	if ( 'text' === $type ) {

		if ( (int) $number > 1 ) {
			/* translators: %s: number of comments */
			$number = sprintf( _n( '%s Comment', '%s Comments', (int) $number, 'wp-grid-builder' ), number_format_i18n( $number ) );
		} elseif ( 0 === (int) $number ) {
			$number = __( 'No Comments', 'wp-grid-builder' );
		} else {
			$number = __( '1 Comment', 'wp-grid-builder' );
		}
	} else {
		$number = Helpers::shorten_number_format( $number );
	}

	wpgb_block_start( $block, $action );
		echo esc_html( $number );
	wpgb_block_end( $block, $action );

}
