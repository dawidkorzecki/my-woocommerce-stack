<?php
/**
 * Header
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_page = Helpers::get_plugin_page();

$items = [
	[
		'title' => __( 'Dashboard', 'wp-grid-builder' ),
		'links' => [ 'dashboard' ],
		'icon'  => 'home',
		'info'  => null,
	],
	[
		'title' => __( 'Grids', 'wp-grid-builder' ),
		'links' => [ 'grids', 'grid-settings' ],
		'icon'  => 'grid',
		'info'  => null,
	],
	[
		'title' => __( 'Cards', 'wp-grid-builder' ),
		'links' => [ 'cards', 'card-builder' ],
		'icon'  => 'card',
		'info'  => null,
	],
	[
		'title' => __( 'Facets', 'wp-grid-builder' ),
		'links' => [ 'facets', 'facet-settings' ],
		'icon'  => 'facet',
		'info'  => null,
	],
	[
		'title' => __( 'Add-ons', 'wp-grid-builder' ),
		'links' => [ 'add-ons' ],
		'icon'  => 'add-on',
		'info'  => null,
	],
	[
		'title' => __( 'Settings', 'wp-grid-builder' ),
		'links' => [ 'settings' ],
		'icon'  => 'settings',
		'info'  => null,
	],
];

?>
<header id="wpgb-admin-header">

	<h1 id="wpgb-admin-plugin">
		<a href="<?php echo esc_url( add_query_arg( [ 'page' => WPGB_SLUG . '-dashboard' ], admin_url( 'admin.php' ) ) ); ?>">
			<img src="<?php echo esc_url( WPGB_URL . 'admin/assets/svg/logo.svg' ); ?>" alt="" width="156" height="48">
			<span class="wpgb-sr-only"><?php echo esc_html( WPGB_NAME ); ?></span>
		</a>
	</h1>

	<code id="wpgb-admin-version"><?php echo esc_html( 'v' . WPGB_VERSION ); ?></code>

	<ul id="wpgb-admin-navigation">
	<?php

	foreach ( $items as $item ) {

		$class = null;
		$links = $item['links'];

		if ( ! isset( $links[0] ) ) {
			continue;
		}

		foreach ( $links as $name ) {
			$class = $current_page === $name ? 'wpgb-active' : $class;
		}

		$args = [ 'page' => WPGB_SLUG . '-' . $links[0] ];

		?>
		<li class="<?php echo sanitize_html_class( $class ); ?>">
			<a href="<?php echo esc_url( add_query_arg( $args, admin_url( 'admin.php' ) ) ); ?>">
				<?php Helpers::get_icon( $item['icon'] ); ?>
				<span><?php echo esc_html( $item['title'] ); ?></span>

				<?php
				if ( $item['info'] ) {

					?>
					<span class="wpgb-bubble-info">
						<?php echo esc_html( $item['info'] ); ?>
					</span>
					<?php

				}
				?>

			</a>
		</li>
		<?php

	}
	?>

	</ul>

</header>
<?php
