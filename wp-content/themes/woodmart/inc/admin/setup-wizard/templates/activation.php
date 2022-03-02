<?php
/**
 * Activation template.
 *
 * @package woodmart
 */

?>

<div class="xts-wizard-content-inner">

	<?php WOODMART_Registry()->license->form(); ?>

	<p class="xts-wizard-note">
		<?php
		echo wp_kses(
			'<strong>Note:</strong> you are allowed to use our theme only on one domain if you have purchased a regular license. But we give you an ability to activate our theme to turn on auto updates on two domains: for the development website and for your production (live) website.
		If you need to check all your active domains or you want to remove some of them you should visit <a href="https://xtemos.com/" target="_blank">our website</a> and check the activation list in your account.',
			woodmart_get_allowed_html()
		);
		?>
	</p>

</div>

<div class="xts-wizard-footer">
	<?php $this->get_prev_button( 'welcome' ); ?>

	<div>
		<?php if ( woodmart_is_license_activated() ) : ?>
			<?php $this->get_next_button( 'child-theme' ); ?>
		<?php else : ?>
			<?php $this->get_skip_button( 'child-theme' ); ?>
		<?php endif; ?>
	</div>
</div>