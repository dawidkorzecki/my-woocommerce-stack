<?php
/**
 * Iframe preview
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$class = ! empty( $settings['is_overview'] ) ? 'wpgb-cards-overview' : 'wpbg-grid-preview';

?>
<!DOCTYPE HTML>

<html <?php language_attributes(); ?> <?php echo 'class="' . sanitize_html_class( $class ) . '"'; ?>>

	<head>

		<title>Iframe - Preview</title>

		<?php
		do_action( 'admin_print_styles' );
		do_action( 'admin_print_scripts' );
		?>

	</head>

	<body <?php echo ( is_rtl() ? 'class="rtl"' : '' ); ?>>

		<?php wpgb_render_grid( $settings ); ?>

		<footer>

			<script>
			<?php echo "/* <![CDATA[ */\n"; ?>
			<?php echo 'var wpgb_preview_settings = ' . wp_json_encode( $settings ); ?>
			<?php echo "\n/* ]]> */"; ?>
			</script>

			<?php
			// Enqueue styles & scripts.
			wpgb_enqueue_styles();
			wpgb_enqueue_scripts();
			do_action( 'admin_print_footer_scripts' );
			?>

		</footer>

	</body>

</html>
<?php
