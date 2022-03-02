<?php
/**
 * Done template.
 *
 * @package woodmart
 */

update_option( 'woodmart_setup_status', 'done' );
?>

<div class="xts-wizard-content-inner xts-wizard-done">
	<div class="xts-wizard-logo">
		<img src="<?php echo esc_url( $this->get_image_url( 'logo.svg' ) ); ?>" alt="logo">
	</div>

	<h3>
		<?php esc_html_e( 'Everything is ready!', 'woodmart' ); ?>
	</h3>

	<p>
		<?php
		esc_html_e(
			'Congratulations! You have successfully installed our theme. Now you can start creating your amazing website with a help of our theme. It provides you with a full control on your website layout style.',
			'woodmart'
		);
		?>
	</p>

	<div class="xts-wizard-buttons">
		<a class="xts-btn xts-btn-alt" href="<?php echo esc_url( get_home_url() ); ?>">
			<?php esc_html_e( 'View home page', 'woodmart' ); ?>
		</a>

		<a class="xts-inline-btn" href="<?php echo esc_url( wc_admin_url( '&path=/setup-wizard' ) ); ?>">
			<?php esc_html_e( 'WooCommerce setup', 'woodmart' ); ?>
		</a>

		<a class="xts-inline-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=xtemos_options' ) ); ?>">
			<?php esc_html_e( 'Theme settings', 'woodmart' ); ?>
		</a>

		<a class="xts-inline-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=woodmart_dashboard&tab=builder' ) ); ?>">
			<?php esc_html_e( 'Header builder', 'woodmart' ); ?>
		</a>
	</div>
</div>
