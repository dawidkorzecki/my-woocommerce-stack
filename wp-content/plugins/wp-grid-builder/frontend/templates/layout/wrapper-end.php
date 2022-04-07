<?php
/**
 * Wrapper end template
 *
 * This template can be overridden by copying it to yourtheme/wp-grid-builder/templates/layout/wrapper-end.php.
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

$settings = wpgb_get_grid_settings();
$tag_name = apply_filters( 'wp_grid_builder/layout/wrapper_tag', 'div', $settings );

	do_action( 'wp_grid_builder/layout/do_area', 'area-bottom' );

?>
	</div>
	<?php

	do_action( 'wp_grid_builder/layout/do_sidebar', 'sidebar-right' );
	do_action( 'wp_grid_builder/layout/wrapper_end' );

	?>
	</div>
</<?php echo tag_escape( $tag_name ); ?>>
<?php
