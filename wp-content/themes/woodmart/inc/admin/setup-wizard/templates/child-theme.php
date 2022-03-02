<?php
/**
 * Child theme template.
 *
 * @package woodmart
 */

?>

<div class="xts-wizard-content-inner xts-wizard-child-theme<?php echo is_child_theme() ? ' xts-installed' : ''; ?>">

	<div class="xts-child-theme-response"></div>

	<h3>
		<?php esc_html_e( 'Setup Woodmart Child Theme', 'woodmart' ); ?>
	</h3>

	<p>
		<?php esc_html_e( 'Install the child theme in a single click', 'woodmart' ); ?>
	</p>

	<div class="xts-theme-images">
		<div class="xts-main-image">
			<img  src="<?php echo esc_url( $this->get_image_url( 'parent.png' ) ); ?>" alt="parent">
		</div>
		<div class="xts-child-image">
			<img  src="<?php echo esc_url( $this->get_image_url( 'child.png' ) ); ?>" alt="child">
		</div>
		<span class="xts-child-checkmark"></span>
	</div>

	<p>
		<?php
		esc_html_e(
			'If you are going to make changes to the theme source code please use the Child Theme rather than modifying the main theme HTML/CSS/PHP code. This allows the parent theme to receive updates without overwriting your source code changes. Use the button below to create and activate the Child Theme.',
			'woodmart'
		);
		?>
	</p>

	<a href="#" class="xts-btn xts-btn-primary xts-install-child-theme">
		<?php esc_html_e( 'Install child theme', 'woodmart' ); ?>
	</a>
</div>

<div class="xts-wizard-footer">
	<?php $this->get_prev_button( 'activation' ); ?>
	<div>
		<?php $this->get_next_button( 'page-builder' ); ?>
		<?php $this->get_skip_button( 'page-builder' ); ?>
	</div>
</div>