<?php
if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}




if ( ! function_exists( 'woodmart_shortcode_testimonials' ) ) {
	function woodmart_shortcode_testimonials( $atts = array(), $content = null ) {
		global $woodmart_testimonials_style;

		$class           = '';
		$wrapper_classes = '';
		$owl_atts        = '';

		$parsed_atts = shortcode_atts(
			array_merge(
				woodmart_get_owl_atts(),
				array(
					'layout'          => 'slider',
					'style'           => 'standard',
					'align'           => 'center',
					'text_size'       => '',
					'columns'         => 3,
					'spacing'         => 30,
					'name'            => '',
					'title'           => '',
					'stars_rating'    => 'no',
					'el_class'        => '',
					'woodmart_css_id' => '',
				)
			),
			$atts
		);

		extract( $parsed_atts );

		if ( ! $woodmart_css_id ) {
			$woodmart_css_id = uniqid();
		}
		$id = 'wd-' . $woodmart_css_id;

		$wrapper_classes .= ' testimonials-' . $layout;
		$wrapper_classes .= ' testimon-style-' . $style;

		$wrapper_classes .= ' ' . woodmart_get_new_size_classes( 'testimonials', $text_size, 'text' );

		if ( 'info-top' !== $style ) {
			$wrapper_classes .= ' testimon-align-' . $align;
		}

		if ( 'yes' === $stars_rating ) {
			$wrapper_classes .= ' testimon-with-rating';
		}

		$wrapper_classes .= ' ' . $el_class;

		ob_start();

		if ( 'slider' === $layout ) {
			woodmart_enqueue_inline_style( 'owl-carousel' );
			$custom_sizes = apply_filters( 'woodmart_testimonials_shortcode_custom_sizes', false );

			$parsed_atts['carousel_id']  = $id;
			$parsed_atts['custom_sizes'] = $custom_sizes;

			$owl_atts = woodmart_get_owl_attributes( $parsed_atts );
			$class   .= ' owl-carousel ' . woodmart_owl_items_per_slide( $slides_per_view, array(), false, false, $custom_sizes );

			$wrapper_classes .= ' wd-carousel-container';
			$wrapper_classes .= ' wd-carousel-spacing-' . $spacing;

			if ( woodmart_get_opt( 'disable_owl_mobile_devices' ) ) {
				$wrapper_classes .= ' disable-owl-mobile';
			}
		} else {
			$class .= ' row';
			$class .= ' wd-spacing-' . $spacing;
			$class .= ' wd-columns-' . $columns;
		}

		$woodmart_testimonials_style = $style;
		?>
			<?php if ( $title ) : ?>
				<h2 class="title slider-title"><?php echo esc_html( $title ); ?></h2>
			<?php endif ?>

			<div id="<?php echo esc_attr( $id ); ?>" class="testimonials testimonials-wrapper<?php echo esc_attr( $wrapper_classes ); ?>" <?php echo 'slider' === $layout ? $owl_atts : ''; ?>>
				<div class="<?php echo esc_attr( $class ); ?>" >
					<?php echo do_shortcode( $content ); ?>
				</div>
			</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		$woodmart_testimonials_style = '';

		return $output;
	}
}

if ( ! function_exists( 'woodmart_shortcode_testimonial' ) ) {
	function woodmart_shortcode_testimonial( $atts, $content ) {
		if ( ! function_exists( 'wpb_getImageBySize' ) ) {
			return;
		}

		global $woodmart_testimonials_style;

		$class = '';

		extract(
			shortcode_atts(
				array(
					'image'    => '',
					'img_size' => '100x100',
					'name'     => '',
					'title'    => '',
					'el_class' => '',
				),
				$atts
			)
		);

		$img_id = preg_replace( '/[^\d]/', '', $image );

		$class .= ' ' . $el_class;

		ob_start();

		if ( 'info-top' === $woodmart_testimonials_style ) {
			woodmart_enqueue_inline_style( 'testimonial' );
		} else {
			woodmart_enqueue_inline_style( 'testimonial-old' );
		}

		$image_output = '';

		if ( $img_id ) {
			$image_output = wpb_getImageBySize(
				array(
					'attach_id'  => $img_id,
					'thumb_size' => $img_size,
					'class'      => 'testimonial-avatar-image',
				)
			)['thumbnail'];
		}

		$template_name = 'default.php';

		if ( 'info-top' === $woodmart_testimonials_style ) {
			$template_name = 'info-top.php';
		}

		woodmart_get_element_template(
			'testimonials',
			[
				'image'        => $image_output,
				'title'        => $title,
				'name'         => $name,
				'content'      => $content,
				'item_classes' => $class,
			],
			$template_name
		);

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
