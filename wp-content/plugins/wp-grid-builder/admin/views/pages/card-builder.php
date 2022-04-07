<?php
/**
 * Card builder page
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Settings\Fields\Icons;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$card_id   = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
$drop_area = esc_html__( 'Drop Area', 'wp-grid-builder' );
$collapsed = isset( $_COOKIE[ WPGB_SLUG . '_builder_collapsed' ] ) ? (int) $_COOKIE[ WPGB_SLUG . '_builder_collapsed' ] : false;
$collapsed = $collapsed ? 'wpgb-settings-collapsed' : null;
$checked   = $collapsed ? 'checked' : null;

?>

<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wpgb_form" class="wpgb_card_settings_form " method="post" enctype="multipart/form-data">

	<input type="hidden" name="option_page" value="wpgb_card_settings">
	<input type="hidden" name="action" value="wpgb_form">
	<input type="hidden" name="wpgb_id" value="<?php echo (int) $card_id; ?>">
	<?php wp_nonce_field( WPGB_SLUG . '_card_settings', WPGB_SLUG . '_fields_nonce' ); ?>

	<div class="wpgb-builder-header <?php echo sanitize_html_class( $collapsed ); ?>">

		<div class="wpgb-builder-header-left">

			<span class="wpgb-settings-collapse-tabs" title="<?php esc_attr_e( 'Collapse menu', 'wp-grid-builder' ); ?>">
				<label for="wpgb-collapse-button"><span class="wpgb-sr-only" ><?php echo esc_html__( 'Collapse menu', 'wp-grid-builder' ); ?></span></label>
				<input type="checkbox" class="wpgb-settings-collapse" id="wpgb-collapse-button" value="<?php echo esc_attr( WPGB_SLUG ); ?>_builder_collapsed" <?php echo esc_attr( $checked ); ?>>
				<?php Helpers::get_icon( 'left' ); ?>
			</span>

		</div>

		<div class="wpgb-builder-header-right">

			<div>
				<div class="wpgb-builder-list">
					<?php Helpers::get_icon( 'list', false ); ?>
					<span><?php esc_html_e( 'No Layer/Block Selected', 'wp-grid-builder' ); ?></span>
				</div>
			</div>

			<div>

				<div data-action="preview">
					<input type="checkbox" class="wpgb-input wpgb-checkbox" id="wpgb-preview-card" name="wpgb-preview-card" value="1">
					<label class="wpgb-field-label" for="wpgb-preview-card"><?php esc_html_e( 'Preview', 'wp-grid-builder' ); ?></label>
					<span><?php esc_html_e( 'Preview', 'wp-grid-builder' ); ?></span>
				</div>

				<button type="button" class="wpgb-button wpgb-button-icon wpgb-green" data-action="save">
				<?php
					Helpers::get_icon( 'save' );
					esc_html_e( 'Save changes', 'wp-grid-builder' );
				?>
				</button>

			</div>

		</div>

	</div>

	<div class="wpgb-builder">

		<div class="wpgb-builder-left-panel"></div>

		<div class="wpgb-builder-right-panel">

			<?php include_once WPGB_PATH . 'admin/views/modules/loader.php'; ?>

			<div class="wpgb-builder-ruler">

				<div class="wpgb-builder-ruler-h">
					<canvas width="5000" height="50"></canvas>
					<div class="wpgb-builder-ruler-market"></div>
				</div>

				<div class="wpgb-builder-ruler-v">
					<canvas width="50" height="5000"></canvas>
					<div class="wpgb-builder-ruler-market"></div>
				</div>

			</div>

			<div class="wpgb-builder-holder">

				<div class="wpgb-card">
					<div class="wpgb-card-inner wpgb-layer">

						<div class="wpgb-card-header wpgb-scheme-dark wpgb-layer">
							<div class="wpgb-droppable" data-name="<?php echo esc_attr( $drop_area ); ?>"></div>
							<div class="wpgb-edit-layer" data-layer="wpgb-card-header">
								<?php Helpers::get_icon( 'settings' ); ?>
							</div>
						</div>

						<div class="wpgb-card-media wpgb-scheme-light wpgb-layer">

							<svg data-ratio="" viewBox="0 0 4 3"></svg>

							<div class="wpgb-card-media-thumbnail wpgb-layer">
								<div style="background-image:url(<?php echo esc_url( WPGB_URL . 'admin/assets/svg/placeholder.svg' ); ?>)"></div>
							</div>

							<div class="wpgb-card-media-overlay wpgb-layer"></div>

							<div class="wpgb-card-media-content wpgb-layer">
								<div class="wpgb-card-media-content-top">
									<div class="wpgb-droppable" data-name="<?php echo esc_attr( $drop_area ); ?>"></div>
								</div>
								<div class="wpgb-card-media-content-center">
									<div class="wpgb-droppable" data-name="<?php echo esc_attr( $drop_area ); ?>"></div>
								</div>
								<div class="wpgb-card-media-content-bottom">
									<div class="wpgb-droppable" data-name="<?php echo esc_attr( $drop_area ); ?>"></div>
								</div>
							</div>

							<div class="wpgb-edit-layer">
								<?php Helpers::get_icon( 'more' ); ?>
								<div class="wpgb-edit-layer-menu">
									<ul role="menu" aria-orientation="vertical">
										<li class="wpgb-edit-layer" data-layer="wpgb-card-media">
										<?php
											Helpers::get_icon( 'layer' );
											esc_html_e( 'Edit Media Holder', 'wp-grid-builder' );
										?>
										</li>
										<li class="wpgb-edit-layer" data-layer="wpgb-card-media-thumbnail">
										<?php
											Helpers::get_icon( 'layer' );
											esc_html_e( 'Edit Media Thumbnail', 'wp-grid-builder' );
										?>
										</li>
										<li class="wpgb-edit-layer" data-layer="wpgb-card-media-overlay">
										<?php
											Helpers::get_icon( 'layer' );
											esc_html_e( 'Edit Media Overlay', 'wp-grid-builder' );
										?>
										</li>
										<li class="wpgb-edit-layer" data-layer="wpgb-card-media-content">
										<?php
											Helpers::get_icon( 'layer' );
											esc_html_e( 'Edit Media Content', 'wp-grid-builder' );
										?>
										</li>
									</ul>
								</div>
							</div>

						</div>

						<div class="wpgb-card-content wpgb-scheme-dark wpgb-layer">

							<div class="wpgb-card-body wpgb-layer">
								<div class="wpgb-droppable" data-name="<?php echo esc_attr( $drop_area ); ?>"></div>
								<div class="wpgb-edit-layer" data-layer="wpgb-card-body">
									<?php Helpers::get_icon( 'settings' ); ?>
								</div>
							</div>

							<div class="wpgb-card-footer wpgb-layer">
								<div class="wpgb-droppable" data-name="<?php echo esc_attr( $drop_area ); ?>"></div>
								<div class="wpgb-edit-layer" data-layer="wpgb-card-footer">
									<?php Helpers::get_icon( 'settings' ); ?>
								</div>
							</div>

						</div>

						<div class="wpgb-edit-layer" data-layer="wpgb-card-inner">
							<?php Helpers::get_icon( 'settings' ); ?>
						</div>

					</div>
				</div>

			</div>

			<div class="wpgb-builder-grid">
				<div class="wpgb-builder-grid-inner"></div>
			</div>

			<div class="wpgb-builder-right-panel-footer">

				<div class="wpgb-field-input">
					<div class="wpgb-toggle">
						<input type="checkbox" class="wpgb-input wpgb-checkbox" id="wpgb-snap-to-grid" name="wpgb-snap-to-grid" value="1">
						<span></span>
					</div>
				</div>
				<label class="wpgb-field-label" for="wpgb-snap-to-grid"><?php esc_html_e( 'Snap to grid', 'wp-grid-builder' ); ?></label>

			</div>

		</div>

	</div>
</form>
<?php

( new Icons() )->render_popup();
