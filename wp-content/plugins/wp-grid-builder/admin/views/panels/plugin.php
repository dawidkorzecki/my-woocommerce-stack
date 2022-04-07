<?php
/**
 * Plugin panel
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

$account_url = 'https://wpgridbuilder.com/account/';
$plugin_info = wp_parse_args(
	get_option( WPGB_SLUG . '_plugin_info', [] ),
	[
		'license_key'      => '',
		'license_type'     => __( 'Unknown', 'wp-grid-builder' ),
		'new_version'      => WPGB_VERSION,
		'license_limit'    => '',
		'site_count'       => '',
		'is_local'         => '',
		'expires'          => '',
		'activations_left' => '',
	]
);

?>
<div class="wpgb-admin-section">

	<h2><?php esc_html_e( 'Plugin License', 'wp-grid-builder' ); ?></h2>

	<p>
		<?php if ( empty( $plugin_info['license_key'] ) ) { ?>
			<?php esc_html_e( 'Enter your license email and key to enable remote updates.', 'wp-grid-builder' ); ?>
			<br>
			<?php esc_html_e( 'When activated, you will benefit of automatic updates from WordPress dashboard.', 'wp-grid-builder' ); ?>
		<?php } else { ?>
			<?php esc_html_e( 'You can refresh information at any time to view change from your account.', 'wp-grid-builder' ); ?>
		<?php } ?>
	</p>

</div>
<?php

if ( empty( $plugin_info['license_key'] ) ) {

	?>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wpgb-settings">

		<input type="hidden" name="action" value="<?php echo esc_attr( WPGB_SLUG . '_form' ); ?>">
		<?php wp_nonce_field( WPGB_SLUG . '_plugin_activate', WPGB_SLUG . '_plugin_activate_field' ); ?>

		<div>

			<div class="wpgb-settings-field">
				<label class="wpgb-field-label" for="wpgb-license-email"><?php esc_html_e( 'License Email', 'wp-grid-builder' ); ?></label>
				<input type="email" id="wpgb-license-email" name="license_email" placeholder="<?php esc_attr_e( 'Enter your email', 'wp-grid-builder' ); ?>" value="" autocomplete="new-password">
			</div>

			<div class="wpgb-settings-field">
				<label class="wpgb-field-label" for="wpgb-license-key"><?php esc_html_e( 'License Key', 'wp-grid-builder' ); ?></label>
				<div class="wpgb-password-holder">
					<input type="password" id="wpgb-license-key" class="wpgb-input wpgb-password" name="license_key" placeholder="<?php esc_attr_e( 'Enter your license key', 'wp-grid-builder' ); ?>" value="" autocomplete="new-password">
					<?php Helpers::get_icon( 'preview' ); ?>
				</div>
			</div>

			<div class="wpgb-settings-field">
				<div class="wpgb-field-label"></div>
				<div class="wpgb-input-submit-wrapper wpgb-button wpgb-button-icon wpgb-green">
					<?php Helpers::get_icon( 'padlock-open' ); ?>
					<input type="submit" data-method="activate_plugin" value="<?php esc_attr_e( 'Activate License', 'wp-grid-builder' ); ?>">
				</div>
			</div>

			<p class="wpgb-notice">
				<?php
					printf(
						/* translators: %s: account url */
						wp_kses_post( __( 'License key is available in order confirmation email or in <a href="%s" target="_blank">your account page</a>.', 'wp-grid-builder' ) ),
						esc_url( $account_url )
					);
				?>
			</p>

		</div>

	</form>
	<?php

} else {

	$available   = (int) $plugin_info['license_limit'];
	$activated   = (int) $plugin_info['site_count'];
	$expiration  = date_i18n( get_option( 'date_format' ), (int) $plugin_info['expires'] );
	$has_expired = $plugin_info['expires'] <= current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
	$has_update  = version_compare( (string) $plugin_info['new_version'], WPGB_VERSION, '>' );
	$activations = sprintf(
		/* translators: %1$d: number of activations, %2$d: number of license */
		_n( '%1$d on %2$s License', '%1$d on %2$s Licenses', (int) $plugin_info['license_limit'], 'wp-grid-builder' ),
		$activated,
		$available
	);

	if ( 'lifetime' === $plugin_info['expires'] ) {

		$has_expired = false;
		$expiration  = __( 'Lifetime', 'wp-grid-builder' );

	}

	if ( $available <= 0 ) {

		$activations = sprintf(
			/* translators: %1$d: number of activations, %2$d: number of license */
			__( '%1$d on Unlimited', 'wp-grid-builder' ),
			$activated
		);

	}

	$refresh_nonce    = wp_create_nonce( WPGB_SLUG . '_plugin_refresh' );
	$deactivate_nonce = wp_create_nonce( WPGB_SLUG . '_plugin_deactivate' );

	?>
	<ul class="wpgb-list-table-rows wpgb-plugin-info">

		<li class="wpgb-list-table-row">
			<div class="wpgb-list-table-column"><?php Helpers::get_icon( 'license' ); ?></div>
			<div class="wpgb-list-table-column"><?php esc_html_e( 'License Type', 'wp-grid-builder' ); ?></div>
			<div class="wpgb-list-table-column"><?php echo esc_html( ucfirst( $plugin_info['license_type'] ) ); ?></div>
			<div class="wpgb-list-table-column"></div>
		</li>

		<li class="wpgb-list-table-row">
			<div class="wpgb-list-table-column"><?php Helpers::get_icon( 'padlock-open' ); ?></div>
			<div class="wpgb-list-table-column"><?php esc_html_e( 'Activations', 'wp-grid-builder' ); ?></div>
			<div class="wpgb-list-table-column"><?php echo esc_html( $activations ); ?></div>
			<div class="wpgb-list-table-column">
				<?php
				if ( $plugin_info['is_local'] ) {
					esc_html_e( '(Dev. Environment)', 'wp-grid-builder' );
				}
				?>
			</div>
		</li>

		<li class="wpgb-list-table-row">
			<div class="wpgb-list-table-column"><?php Helpers::get_icon( 'box' ); ?></div>
			<div class="wpgb-list-table-column"><?php esc_html_e( 'Last Version', 'wp-grid-builder' ); ?></div>
			<div class="wpgb-list-table-column" data-state="<?php echo $has_update ? 'false' : ''; ?>">
				<?php echo esc_html( 'v' . $plugin_info['new_version'] ); ?>
			</div>
			<div class="wpgb-list-table-column"></div>
		</li>

		<li class="wpgb-list-table-row">
			<div class="wpgb-list-table-column"><?php Helpers::get_icon( 'expiration' ); ?></div>
			<div class="wpgb-list-table-column"><?php esc_html_e( 'Expiration Date', 'wp-grid-builder' ); ?></div>
			<div class="wpgb-list-table-column" data-state="<?php echo $has_expired ? 'false' : ''; ?>">
				<?php echo esc_html( $expiration ); ?>
			</div>
			<div class="wpgb-list-table-column">

				<?php if ( $has_expired ) { ?>

					<a class="wpgb-button wpgb-button-small wpgb-red" href="<?php echo esc_url( $account_url ); ?>" target="_blank" rel="external noopener noreferrer">
						<?php Helpers::get_icon( 'license' ); ?>
						<?php esc_html_e( 'Renew license', 'wp-grid-builder' ); ?>
					</a>

				<?php } ?>

			</div>
		</li>

	</ul>

	<button type="button" class="wpgb-button wpgb-button-small wpgb-green" data-method="refresh_status" data-nonce="<?php echo esc_attr( $refresh_nonce ); ?>">
		<?php Helpers::get_icon( 'reset' ); ?>
		<?php esc_html_e( 'Refresh info', 'wp-grid-builder' ); ?>
	</button>

	<?php if ( current_user_can( 'manage_options' ) ) { ?>

		<button type="button" class="wpgb-button wpgb-button-icon wpgb-red" data-method="deactivate_plugin" data-nonce="<?php echo esc_attr( $deactivate_nonce ); ?>">
			<?php Helpers::get_icon( 'padlock-close' ); ?>
			<?php esc_html_e( 'Deactivate', 'wp-grid-builder' ); ?>
		</button>

	<?php } ?>

	<a class="wpgb-button wpgb-blue" href="<?php echo esc_url( $account_url ); ?>" target="_blank" rel="external noopener noreferrer">
		<?php Helpers::get_icon( 'account' ); ?>
		<?php esc_html_e( 'My Account', 'wp-grid-builder' ); ?>
	</a>
	<?php

}
