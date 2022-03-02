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
 * @var string $item_classes
 */

?>

<div class="testimonial column<?php echo esc_attr( $item_classes ); ?>">
	<div class="testimonial-inner">
		<?php if ( $image ) : ?>
			<div class="testimonial-avatar">
				<?php echo $image; // phpcs:ignore ?>
			</div>
		<?php endif ?>

		<div class="testimonial-content">
			<div class="testimonial-rating">
				<span class="star-rating">
					<span style="width:100%"></span>
				</span>
			</div>

			<?php echo do_shortcode( $content ); ?>

			<footer>
				<?php echo esc_html( $name ); ?>

				<?php if ( $title ) : ?>
					<span>
						<?php echo esc_html( $title ); ?>
					</span>
				<?php endif ?>
			</footer>
		</div>
	</div>
</div>