<?php
/**
 * Code field
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings\Fields;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings API Field
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields\Code
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Code extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		if ( 'css' === $args['mode'] ) {
			$content = wp_strip_all_tags( $args['value'] );
		} else {

			$content = wp_kses_decode_entities( $args['value'] );
			$content = html_entity_decode( $content );

		}

		printf(
			'<textarea class="wpgb-code" id="%s" name="%s" data-mode="%s" data-height="%s">%s</textarea>',
			esc_attr( $args['uid'] ),
			esc_attr( $args['name'] ),
			esc_attr( $args['mode'] ),
			(int) $args['height'],
			esc_textarea( $content )
		);

	}

	/**
	 * Normalize field parameters
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field Holds field parameters.
	 * @return array
	 */
	public function normalize( $field ) {

		return wp_parse_args(
			$field,
			[
				'default'      => '',
				'mode'         => 'css',
				'height'       => '502',
				'compress'     => false,
				'declarations' => false,
			]
		);

	}

	/**
	 * Sanitize field value
	 * JS: Source => Custom JavaScript Editor by Automattic
	 * CSS: Source => Jetpack by Automattic
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $val Field value.
	 * @param array $args Holds field parameters.
	 * @return string
	 */
	public function sanitize( $val, $args = [] ) {

		if ( 'javascript' === $args['mode'] ) {

			// The $val variable is explicitly not sanitized, as JavaScript is allowed.
			// and other HTML elements could be constructed piece by piece even if filtered.
			return esc_html( $val );

		}

		if ( 'text/html' === $args['mode'] ) {
			return wp_kses_post( $val );
		}

		if ( ! class_exists( 'csstidy' ) ) {
			require_once WPGB_PATH . 'includes/csstidy/class-csstidy.php';
		}

		// If it's only CSS declarations then recreate a rule set.
		if ( $args['declarations'] ) {
			$val = '.test {' . $val . '}';
		}

		$csstidy = new \csstidy();
		$csstidy->set_cfg( 'remove_bslash', false );
		$csstidy->set_cfg( 'compress_colors', $args['compress'] );
		$csstidy->set_cfg( 'compress_font-weight', $args['compress'] );
		$csstidy->set_cfg( 'optimise_shorthands', $args['compress'] );
		$csstidy->set_cfg( 'remove_last_;', $args['compress'] );
		$csstidy->set_cfg( 'discard_invalid_properties', true );
		$csstidy->set_cfg( 'discard_invalid_selectors', $args['compress'] );
		$csstidy->set_cfg( 'css_level', 'CSS3.0' );
		$csstidy->set_cfg( 'preserve_css', ! $args['compress'] );
		$csstidy->set_cfg( 'template', $args['compress'] ? 'highest' : WPGB_PATH . 'includes/csstidy/wordpress-standard.tpl' );

		$val = preg_replace( '/\\\\([0-9a-fA-F]{4})/', '\\\\\\\\$1', $val );
		// Prevent content: '\1234' from turning into '\\1234'.
		$val = str_replace( [ '\'\\\\', '"\\\\' ], [ '\'\\', '"\\' ], $val );
		// Some people put weird stuff in their CSS, KSES tends to be greedy.
		$val = str_replace( '<=', '&lt;=', $val );
		// KSES to strip tags.
		$val = wp_kses_split( $val, [], [] );
		// Kses replaces lone '>' with &gt;.
		$val = str_replace( '&gt;', '>', $val );
		// Because '>' was added previously.
		$val = wp_strip_all_tags( $val );
		// Prevent using @import CSS rules.
		$val = preg_replace( '/@import[ ]*[\'\"]{0,}(url\()*[\'\"]*([^;\'\"\)]*)[\'\"\)]*/', '', $val );

		// Parse and print plain CSS.
		$csstidy->parse( $val );
		$plain = $csstidy->print->plain();

		// If it's only CSS declarations then remove previously added rule set.
		if ( $args['declarations'] ) {

			// Remove multiple line breaks.
			$plain = preg_replace( '/^.+\n/', '', $plain );
			// Remove tabs.
			$plain = trim( preg_replace( '/\t+/', '', $plain ) );
			// Remove selector placeholder.
			$plain = preg_replace( '/.test\s+\{/', '', $plain );
			// Remove last curly bracket.
			$plain = substr( trim( $plain ), 0, -1 );

		}

		return $plain;

	}
}
