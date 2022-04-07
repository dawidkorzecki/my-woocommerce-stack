<?php
// @codingStandardsIgnoreFile
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

unset( $data['csstidy']['all_properties']['binding'] );

$data['csstidy']['all_properties']['text-size-adjust'] = 'CSS3.0';
$data['csstidy']['all_properties']['fill'] = 'CSS3.0';
$data['csstidy']['all_properties']['touch-action'] = 'CSS3.0';

// Support browser prefixes for properties only in the latest CSS draft.
foreach ( $data['csstidy']['all_properties'] as $property => $levels ) {
	if ( strpos( $levels, "," ) === false ) {
		$data['csstidy']['all_properties']['-moz-' . $property] = $levels;
		$data['csstidy']['all_properties']['-webkit-' . $property] = $levels;
		$data['csstidy']['all_properties']['-ms-' . $property] = $levels;
		$data['csstidy']['all_properties']['-o-' . $property] = $levels;
		$data['csstidy']['all_properties']['-khtml-' . $property] = $levels;

		if ( in_array( $property, $data['csstidy']['unit_values'] ) ) {
			$data['csstidy']['unit_values'][] = '-moz-' . $property;
			$data['csstidy']['unit_values'][] = '-webkit-' . $property;
			$data['csstidy']['unit_values'][] = '-ms-' . $property;
			$data['csstidy']['unit_values'][] = '-o-' . $property;
			$data['csstidy']['unit_values'][] = '-khtml-' . $property;
		}

		if ( in_array( $property, $data['csstidy']['color_values'] ) ) {
			$data['csstidy']['color_values'][] = '-moz-' . $property;
			$data['csstidy']['color_values'][] = '-webkit-' . $property;
			$data['csstidy']['color_values'][] = '-ms-' . $property;
			$data['csstidy']['color_values'][] = '-o-' . $property;
			$data['csstidy']['color_values'][] = '-khtml-' . $property;
		}
	}
}

// Add `display` to the list of properties that can be used multiple times in a single selector.
$data['csstidy']['multiple_properties'][] = 'display';

// Allow vendor prefixes for any property that is allowed to be used multiple times inside a single selector.
foreach ( $data['csstidy']['multiple_properties'] as $property ) {
	if ( '-' != $property[0] ) {
		$data['csstidy']['multiple_properties'][] = '-o-' . $property;
		$data['csstidy']['multiple_properties'][] = '-ms-' . $property;
		$data['csstidy']['multiple_properties'][] = '-webkit-' . $property;
		$data['csstidy']['multiple_properties'][] = '-moz-' . $property;
		$data['csstidy']['multiple_properties'][] = '-khtml-' . $property;
	}
}

/**
 * CSS Animation
 *
 * @see https://developer.mozilla.org/en/CSS/CSS_animations
 */
$data['csstidy']['at_rules']['-webkit-keyframes'] = 'at';
$data['csstidy']['at_rules']['-moz-keyframes'] = 'at';
$data['csstidy']['at_rules']['-ms-keyframes'] = 'at';
$data['csstidy']['at_rules']['-o-keyframes'] = 'at';

/**
 * Non-standard viewport rule.
 */
$data['csstidy']['at_rules']['viewport'] = 'is';
$data['csstidy']['at_rules']['-webkit-viewport'] = 'is';
$data['csstidy']['at_rules']['-moz-viewport'] = 'is';
$data['csstidy']['at_rules']['-ms-viewport'] = 'is';

/**
 * Non-standard CSS properties.  They're not part of any spec, but we say
 * they're in all of them so that we can support them.
 */

$data['csstidy']['all_properties']['mix-blend-mode'] = 'CSS2.0,CSS2.1,CSS3.0';
$data['csstidy']['all_properties']['-webkit-filter'] = 'CSS2.0,CSS2.1,CSS3.0';
$data['csstidy']['all_properties']['-moz-filter'] = 'CSS2.0,CSS2.1,CSS3.0';
$data['csstidy']['all_properties']['-ms-filter'] = 'CSS2.0,CSS2.1,CSS3.0';
$data['csstidy']['all_properties']['filter'] = 'CSS2.0,CSS2.1,CSS3.0';
$data['csstidy']['all_properties']['scrollbar-face-color'] = 'CSS2.0,CSS2.1,CSS3.0';
$data['csstidy']['all_properties']['-ms-interpolation-mode'] = 'CSS2.0,CSS2.1,CSS3.0';
$data['csstidy']['all_properties']['text-rendering'] = 'CSS2.0,CSS2.1,CSS3.0';
$data['csstidy']['all_properties']['-webkit-transform-origin-x'] = 'CSS3.0';
$data['csstidy']['all_properties']['-webkit-transform-origin-y'] = 'CSS3.0';
$data['csstidy']['all_properties']['-webkit-transform-origin-z'] = 'CSS3.0';
$data['csstidy']['all_properties']['-webkit-font-smoothing'] = 'CSS3.0';
$data['csstidy']['all_properties']['-moz-osx-font-smoothing'] = 'CSS3.0';
$data['csstidy']['all_properties']['-font-smooth'] = 'CSS3.0';
$data['csstidy']['all_properties']['-o-object-fit'] = 'CSS3.0';
$data['csstidy']['all_properties']['object-fit'] = 'CSS3.0';
$data['csstidy']['all_properties']['-o-object-position'] = 'CSS3.0';
$data['csstidy']['all_properties']['object-position'] = 'CSS3.0';
$data['csstidy']['all_properties']['text-overflow'] = 'CSS3.0';
$data['csstidy']['all_properties']['zoom'] = 'CSS3.0';
$data['csstidy']['all_properties']['pointer-events'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-feature-settings'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-kerning'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-language-override'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-synthesis'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-variant-alternates'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-variant-caps'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-variant-east-asian'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-variant-ligatures'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-variant-numeric'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-variant-position'] = 'CSS3.0';
$data['csstidy']['all_properties']['font-variation-settings'] = 'CSS3.0';
$data['csstidy']['all_properties']['line-height-step'] = 'CSS3.0';

$data['csstidy']['all_properties']['order'] = 'CSS3.0';
$data['csstidy']['all_properties']['flex'] = 'CSS3.0';
$data['csstidy']['all_properties']['flex-align'] = 'CSS3.0';
$data['csstidy']['all_properties']['flex-basis'] = 'CSS3.0';
$data['csstidy']['all_properties']['flex-direction'] = 'CSS3.0';
$data['csstidy']['all_properties']['-ms-flex-direction'] = 'CSS3.0';
$data['csstidy']['all_properties']['-webkit-box-orient'] = 'CSS3.0';
$data['csstidy']['all_properties']['-webkit-box-direction'] = 'CSS3.0';
$data['csstidy']['all_properties']['flex-flow'] = 'CSS3.0';
$data['csstidy']['all_properties']['flex-grow'] = 'CSS3.0';
$data['csstidy']['all_properties']['flex-wrap'] = 'CSS3.0';
$data['csstidy']['all_properties']['-webkit-box-flex'] = 'CSS3.0';
$data['csstidy']['all_properties']['-ms-flex-positive'] = 'CSS3.0';
$data['csstidy']['all_properties']['justify-content'] = 'CSS3.0';
$data['csstidy']['all_properties']['align-items'] = 'CSS3.0';
$data['csstidy']['all_properties']['stroke-width'] = 'CSS3.0';
$data['csstidy']['all_properties']['-webkit-line-clamp'] = 'CSS3.0';

$data['csstidy']['all_properties']['scroll-margin-top'] = 'CSS3.0';
$data['csstidy']['all_properties']['scroll-margin-bottom'] = 'CSS3.0';
$data['csstidy']['all_properties']['scroll-padding-top'] = 'CSS3.0';
$data['csstidy']['all_properties']['scroll-padding-bottom'] = 'CSS3.0';
$data['csstidy']['all_properties']['scroll-behaviour'] = 'CSS3.0';
