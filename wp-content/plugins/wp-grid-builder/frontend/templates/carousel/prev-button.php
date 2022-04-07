<?php
/**
 * Carousel prev button template
 *
 * This template can be overridden by copying it to yourtheme/wp-grid-builder/templates/carousel/prev-button.php.
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
<button type="button" class="wpgb-prev-button" aria-label="<?php esc_attr_e( 'Previous slide', 'wp-grid-builder' ); ?>" hidden>
	<?php wpgb_svg_icon( 'wpgb/arrows/arrow-left' ); ?>
</button>
<?php
