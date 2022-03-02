<?php
/**
 * Variables.
 *
 * @package woodmart
 *
 * @var string $item_attributes
 * @var string $image
 * @var string $content
 * @var string $name
 * @var string $title
 */

?>

<div class="wd-testimon column<?php echo esc_attr( $item_classes ); ?>">
	<div class="wd-testimon-info">
		<?php if ( $image ) : ?>
			<div class="wd-testimon-thumb">
				<?php echo $image; // phpcs:ignore ?>
			</div>
		<?php endif ?>

		<div class="wd-testimon-bio">
			<?php if ( $name ) : ?>
				<div class="wd-testimon-name title">
					<?php echo esc_html( $name ); ?>
				</div>
			<?php endif ?>

			<?php if ( $title ) : ?>
				<div class="wd-testimon-pos">
					<?php echo esc_html( $title ); ?>
				</div>
			<?php endif ?>

			<div class="star-rating">
				<span style="width:100%"></span>
			</div>
		</div>
	</div>

	<?php if ( $content ) : ?>
		<div class="wd-testimon-text reset-last-child">
			<?php echo do_shortcode( $content ); ?>
		</div>
	<?php endif; ?>
</div>