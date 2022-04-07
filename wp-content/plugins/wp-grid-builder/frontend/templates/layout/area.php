<?php
/**
 * Area template
 *
 * This template can be overridden by copying it to yourtheme/wp-grid-builder/templates/layout/area.php.
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

?>
<div class="wpgb-area <?php echo sanitize_html_class( 'wpgb-' . $wpgb_template ); ?>">
	<?php do_action( 'wp_grid_builder/layout/do_facets', $wpgb_template ); ?>
</div>
<?php
