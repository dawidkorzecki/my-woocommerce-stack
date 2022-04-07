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

$_error = wpgb_get_grid_settings( 'error' );

?>
<div class="wpgb-card wpgb-no-result" data-col="12" data-row="1">
	<div class="wpgb-card-wrapper">
		<?php echo wp_kses_post( $_error->get_error_message() ); ?>
	</div>
</div>
<?php
