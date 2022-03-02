<?php
/**
 * Page builder template.
 *
 * @package woodmart
 */

?>

<div class="xts-wizard-content-inner xts-wizard-page-builder">

	<h3>
		<?php esc_html_e( 'Page Builder Plugin', 'woodmart' ); ?>
	</h3>

	<p>
		<?php esc_html_e( 'Choose one of the page builder plugins', 'woodmart' ); ?>
	</p>

	<div class="xts-wizard-builder-select">
		<div class="xts-wizard-elementor xts-active" data-builder="elementor">
			<div class="xts-page-builder-img">
				<img src="<?php echo esc_url( $this->get_image_url( 'elementor-builder.svg' ) ); ?>" alt="elementor logo">
			</div>

			<div class="xts-page-builder-title">
				<?php esc_attr_e( 'Elementor', 'woodmart' ); ?>
			</div>

			<p>
				<?php esc_attr_e( 'The World\'s Leading WordPress Website Builder', 'woodmart' ); ?>
			</p>
		</div>

		<span>
			<?php esc_attr_e( 'OR', 'woodmart' ); ?>
		</span>

		<div class="xts-wizard-wpb" data-builder="wpb">
			<div class="xts-page-builder-img">
				<img src="<?php echo esc_url( $this->get_image_url( 'wpb.svg' ) ); ?>" alt="wpb logo">
			</div>

			<div class="xts-page-builder-title">
				<?php esc_attr_e( 'WPBakery', 'woodmart' ); ?>
			</div>

			<p>
				<?php esc_attr_e( 'WPBakery Page Builder plugin for WordPress', 'woodmart' ); ?>
			</p>
		</div>
	</div>

</div>

<div class="xts-wizard-footer">
	<?php $this->get_prev_button( 'child-theme' ); ?>
	<?php $this->get_next_button( 'plugins', 'elementor' ); ?>
	<?php $this->get_next_button( 'plugins', 'wpb' ); ?>
</div>
