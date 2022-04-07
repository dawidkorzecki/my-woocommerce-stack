<?php
/**
 * Wrapper start template
 *
 * This template can be overridden by copying it to yourtheme/wp-grid-builder/templates/layout/wrapper-start.php.
 *
 * Template files can change and you will need to copy the new files to your theme to
 * maintain compatibility.
 *
 * @package   wp-grid-builder/templates
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 * @version   1.0.0
 */

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!-- Gridbuilder ᵂᴾ Plugin (https://wpgridbuilder.com) -->
<?php

$settings = wpgb_get_grid_settings();
$tag_name = apply_filters( 'wp_grid_builder/layout/wrapper_tag', 'div', $settings );

printf(
	'<%s class="%s" data-options="%s">',
	tag_escape( $tag_name ),
	esc_attr( $settings->class ),
	esc_attr( $settings->js_options )
);

Helpers::get_template( 'layout/icons', '', true );

?>
<div class="wpgb-wrapper">
<?php

do_action( 'wp_grid_builder/layout/wrapper_start' );
do_action( 'wp_grid_builder/layout/do_sidebar', 'sidebar-left' );

?>
	<div class="wpgb-main">
	<?php

	do_action( 'wp_grid_builder/layout/do_area', 'area-top' );
