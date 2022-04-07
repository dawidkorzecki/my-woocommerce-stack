<?php
/**
 * Dashboard page
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

$plugin_info = get_option( WPGB_SLUG . '_plugin_info', [] );
$activated   = ! empty( $plugin_info['license_key'] );
$addons_url  = 'https://wpgridbuilder.com/add-ons/';
$dashboard   = add_query_arg( [ 'page' => WPGB_SLUG . '-dashboard' ], admin_url( 'admin.php' ) );
$addons      = ! empty( $plugin_info['addons'] ) ? $plugin_info['addons'] : [];
$addons      = wp_parse_args(
	[
		'meta-box'     => [
			'name'    => 'Meta Box',
			'slug'    => 'wp-grid-builder-meta-box/wp-grid-builder-meta-box.php',
			'content' => __( 'Easily integrate WP Grid Builder with Meta Box plugin.', 'wp-grid-builder' ),
			'image'   => WPGB_URL . 'admin/assets/imgs/meta-box.png',
		],
		'oxygen'       => [
			'name'    => 'Oxygen',
			'slug'    => 'wp-grid-builder-oxygen/wp-grid-builder-oxygen.php',
			'content' => __( 'Integrate WP Grid Builder with Oxygen plugin.', 'wp-grid-builder' ),
			'image'   => WPGB_URL . 'admin/assets/imgs/oxygen.png',
		],
		'elementor'    => [
			'name'    => 'Elementor',
			'slug'    => 'wp-grid-builder-elementor/wp-grid-builder-elementor.php',
			'content' => __( 'Integrate WP Grid Builder with Elementor plugin.', 'wp-grid-builder' ),
			'image'   => WPGB_URL . 'admin/assets/imgs/elementor.png',
		],
		'multilingual' => [
			'name'    => 'Multilingual',
			'slug'    => 'wp-grid-builder-multilingual/wp-grid-builder-multilingual.php',
			'content' => __( 'Easily integrate WP Grid Builder with Polylang and WPML plugins.', 'wp-grid-builder' ),
			'image'   => WPGB_URL . 'admin/assets/imgs/multilingual.png',
		],
		'learndash'    => [
			'name'    => 'LearnDash',
			'slug'    => 'wp-grid-builder-learndash/wp-grid-builder-learndash.php',
			'content' => __( 'Add new blocks to the card builder to display courses information.', 'wp-grid-builder' ),
			'image'   => WPGB_URL . 'admin/assets/imgs/learndash.png',
		],
		'caching'      => [
			'name'    => 'Caching',
			'slug'    => 'wp-grid-builder-caching/wp-grid-builder-caching.php',
			'content' => __( 'Speed up loading time when filtering grids by caching content and facets.', 'wp-grid-builder' ),
			'image'   => WPGB_URL . 'admin/assets/imgs/caching.png',
		],
		'map-facet'    => [
			'name'    => 'Map Facet',
			'slug'    => 'wp-grid-builder-map-facet/wp-grid-builder-map-facet.php',
			'content' => __( 'Add maps from Google Maps, Mapbox or Leaflet to display markers and to filter.', 'wp-grid-builder' ),
			'image'   => WPGB_URL . 'admin/assets/imgs/map-facet.png',
		],
	],
	array_reverse( $addons )
);

?>
<div class="wpgb-admin-panel">

	<div class="wpgb-admin-section">

		<h2><?php esc_html_e( 'Add-Ons', 'wp-grid-builder' ); ?></h2>

		<p class="wpgb-admin-section-subtitle">
			<?php esc_html_e( 'Easily download and install add-ons for Gridbuilder ᵂᴾ plugin!', 'wp-grid-builder' ); ?>
			<br>
			<?php esc_html_e( 'Add-ons allow to extend possibilities of the plugin depending of your needs.', 'wp-grid-builder' ); ?>
		</p>

		<?php if ( ! $activated ) { ?>
			<p class="wpgb-nota-bene wpgb-warning">
				<span>
				<?php
				Helpers::get_icon( 'warning' );
				printf(
					/* translators: %1&s: Dashboard url, %2$s: plugin name */
					wp_kses_post( '<strong>' . __( 'In order to download and install add-ons you must <a href="%1$s">activate %2$s</a>.', 'wp-grid-builder' ) . '</strong>' ),
					esc_url( $dashboard ),
					esc_html( WPGB_NAME )
				);
				?>
				<br>
				<?php esc_html_e( 'When activated, you will benefit of all add-ons and be able to install them.', 'wp-grid-builder' ); ?>
				</span>
			</p>
		<?php } ?>

	</div>

	<div class="wpgb-admin-section">

		<div class="wpgb-add-ons-cards">
		<?php

		$installed = get_plugins();

		foreach ( $addons as $addon ) {

			$active   = is_plugin_active( $addon['slug'] );
			$exists   = isset( $installed[ $addon['slug'] ] );
			$button   = $exists ? __( 'Activate', 'wp-grid-builder' ) : __( 'Install', 'wp-grid-builder' );
			$button   = $active ? __( 'Active', 'wp-grid-builder' ) : $button;
			$color    = $active ? 'green' : 'blue';
			$disabled = $active || ! $activated ? ' disabled' : '';
			$method   = $exists ? 'activate' : 'install';
			$symbol   = sanitize_title( $addon['name'] );
			$nonce    = wp_create_nonce( 'wpgb_plugin_' . $method . '_addon_' . $symbol );

			?>
			<div class="wpgb-add-ons-card<?php echo ( ! $activated ? ' wpgb-disabled' : '' ); ?>">
				<img src="<?php echo esc_url( $addon['image'] . '?ver=' . WPGB_VERSION ); ?>" alt="<?php echo esc_attr( $addon['name'] ); ?>">
				<h3><?php echo esc_html( $addon['name'] ); ?></h3>
				<p><?php echo wp_kses_post( $addon['content'] ); ?></p>
				<?php
				if ( ! $activated ) {
					echo '<button type="button" class="wpgb-button" disabled>' . esc_html__( 'Not available', 'wp-grid-builder' ) . '</button>';
				} elseif ( ! current_user_can( 'install_plugins' ) ) {
					echo '<button type="button" class="wpgb-button" disabled>' . esc_html__( 'Not allowed', 'wp-grid-builder' ) . '</button>';
				} else {

					printf(
						'<button type="button" class="wpgb-button wpgb-%s" data-name="%s" data-slug="%s" data-method="%s" data-nonce="%s"%s>%s</button>',
						esc_attr( $color ),
						esc_attr( $addon['name'] ),
						esc_attr( $addon['slug'] ),
						esc_attr( $method ),
						esc_attr( $nonce ),
						esc_attr( $disabled ),
						esc_html( $button )
					);

				}
				?>
			</div>
			<?php

		}

		?>
		</div>

	</div>

	<p class="wpgb-notice">
		<?php
		printf(
			/* translators: %s: Website add-ons url */
			wp_kses_post( __( 'Add-ons can also be downloaded on <a href="%s" target="_blank">wpgridbuilder.com</a>.', 'wp-grid-builder' ) ),
			esc_url( $addons_url )
		);
		?>
	</p>

</div>
<?php
