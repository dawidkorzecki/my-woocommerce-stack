<?php
/**
 * Dummy content template.
 *
 * @package woodmart
 */

?>

<div class="xts-wizard-content-inner xts-wizard-dummy<?php echo esc_attr( WOODMART_Registry()->import->get_imported_versions_css_classes() ); ?>">
	<div class="xts-wizard-base">
		<h3>
			<?php esc_html_e( 'Basic or full import', 'woodmart' ); ?>
		</h3>

		<p>
			<?php esc_html_e( 'Use our one-click dummy content importer mechanism', 'woodmart' ); ?>
		</p>

		<?php WOODMART_Registry()->import->imported_versions(); ?>
		<?php WOODMART_Registry()->import->base_import_screen(); ?>

		<p class="xts-wizard-note">
			<?php
			echo wp_kses(
				'<strong>Basic import</strong> includes default version form our demo and a few products, blog posts and portfolio projects. It is a required minimum to see how our theme built and to be able to import additional versions or pages.',
				woodmart_get_allowed_html()
			);
			?>
		</p>
	</div>

	<div class="xts-wizard-versions">
		<h3>
			<?php esc_html_e( 'Demo versions', 'woodmart' ); ?>
		</h3>

		<p class="xts-wizard-note xts-success">
			<?php esc_html_e( 'Base import has been successfully imported. Now you can select one of the demo versions or continue with the default one.', 'woodmart' ); ?>
		</p>

		<?php WOODMART_Registry()->import->versions_import_screen(); ?>

		<p class="xts-wizard-note">
			<?php
			echo wp_kses(
				'<strong>Demo version</strong> includes page content, slider and settings for one of our versions. Import will also change your home page and may add some widgets.',
				woodmart_get_allowed_html()
			);
			?>
		</p>
	</div>
</div>

<div class="xts-wizard-footer">
	<?php $this->get_prev_button( 'plugins' ); ?>
	<div>
		<?php $this->get_next_button( 'done' ); ?>
		<?php $this->get_skip_button( 'done' ); ?>
	</div>
</div>
