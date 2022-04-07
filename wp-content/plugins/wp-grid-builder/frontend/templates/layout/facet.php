<?php
/**
 * Facet template
 *
 * This template can be overridden by copying it to yourtheme/wp-grid-builder/templates/layout/facet.php.
 *
 * Template files can change and you will need to copy the new files to your theme to
 * maintain compatibility.
 *
 * @package   wp-grid-builder/templates
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 * @version   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $wpgb_template['html'] ) ) {
	return;
}

$legend  = $wpgb_template['title'] ?: $wpgb_template['name'];
$tagname = apply_filters( 'wp_grid_builder/facet/title_tag', 'h4' );

if ( ! empty( $wpgb_template['title'] ) && ! empty( $tagname ) ) {

	?>
	<<?php echo tag_escape( $tagname ); ?> class="wpgb-facet-title"><?php echo esc_html( $wpgb_template['title'] ); ?></<?php echo tag_escape( $tagname ); ?>>
	<?php

}

if ( ! in_array( $wpgb_template['action'], [ 'load', 'reset', 'apply' ], true ) ) {

	?>
	<fieldset>
		<legend class="wpgb-facet-title wpgb-sr-only"><?php echo esc_html( $legend ); ?></legend>
		<?php echo $wpgb_template['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</fieldset>
	<?php

} else {
	echo $wpgb_template['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
