<?php
/**
 * Layout template
 *
 * This template can be overridden by copying it to yourtheme/wp-grid-builder/templates/layout/layout.php.
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

$class = wpgb_get_grid_settings( 'type' );

?>
<div class="wpgb-layout">
	<div class="wpgb-viewport">
		<div class="<?php echo sanitize_html_class( 'wpgb-' . $class ); ?>">
			<?php do_action( 'wp_grid_builder/layout/do_loop' ); ?>
		</div>
	</div>
	<?php

	do_action( 'wp_grid_builder/layout/do_area', 'area-left' );
	do_action( 'wp_grid_builder/layout/do_area', 'area-right' );
	Helpers::get_template( 'layout/loader' );

	?>
</div>
<?php
