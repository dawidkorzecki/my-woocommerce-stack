<?php
/**
 * @var Metabox[] $metaboxes .
 *
 * @package Flexible Shipping
 */

use WPDesk\FS\Info\Metabox;

?>
</table>

<div class="fs-info-wrapper">
	<?php foreach ( $metaboxes as $metabox ) : ?>
		<div id="<?php echo esc_attr( $metabox->get_id() ); ?>"
			 class="<?php echo esc_attr( $metabox->get_classes() ); ?>">
			<?php if ( $metabox->has_title() ) : ?>
				<h3><?php echo esc_html( $metabox->get_title() ); ?></h3>
			<?php endif; ?>

			<?php if ( $metabox->has_body() ) : ?>
				<div class="content"><?php echo $metabox->get_body(); // phpcs:ignore ?></div>
			<?php endif; ?>

			<?php if ( $metabox->has_footer() ) : ?>
				<div class="footer"><?php echo wp_kses_post( $metabox->get_footer() ); ?></div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

<table>
