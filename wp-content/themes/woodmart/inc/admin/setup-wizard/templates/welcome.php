<?php
/**
 * Welcome template.
 *
 * @package woodmart
 */

?>

<div class="xts-wizard-content-inner xts-wizard-welcome">

	<div class="xts-wizard-logo">
		<img class="xts-wizard-logo" src="<?php echo esc_url( $this->get_image_url( 'logo.svg' ) ); ?>" alt="logo">
		</div>

	<h3>
		<?php esc_html_e( 'Thank you for choosing our theme!', 'woodmart' ); ?>
	</h3>

	<p>
		<?php
		esc_html_e(
			'Congratulations! You have successfully installed our theme. Now you can start creating your amazing website with a help of our theme. It provides you with a full control on your website layout style, colors typography and more.',
			'woodmart'
		);
		?>
	</p>

	<p>
		<?php
		esc_html_e(
			'Check our next steps and enjoy creating your new project. Feel free to contact us if you will have any questions and check our other products.',
			'woodmart'
		);
		?>
	</p>

	<p class="xts-wizard-signature">
		<span>
			<?php esc_html_e( 'Good Luck!', 'woodmart' ); ?>
		</span>

		<img src="<?php echo esc_url( $this->get_image_url( 'signature.svg' ) ); ?>" alt="signature">
	</p>

	<div class="xts-wizard-buttons">
		<a class="xts-inline-btn xts-btn-underline" href="<?php echo esc_url( admin_url( 'admin.php?page=woodmart_dashboard&tab=home&skip_setup' ) ); ?>">
			<?php esc_html_e( 'Skip setup', 'woodmart' ); ?>
		</a>

		<a class="xts-btn xts-btn-alt xts-next" href="<?php echo esc_url( $this->get_page_url( 'activation' ) ); ?>">
			<?php esc_html_e( 'Let\'s start', 'woodmart' ); ?>
		</a>
	</div>

</div>
