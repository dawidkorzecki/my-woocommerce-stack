<?php
/**
 * Slider.
 *
 * @package Woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

if ( ! function_exists( 'woodmart_get_slider_data' ) ) {
	/**
	 * Get slider data.
	 */
	function woodmart_get_slider_data() {
		$data = array();

		if ( ! is_user_logged_in() ) {
			return $data;
		}

		$sliders = get_terms(
			array(
				'taxonomy'   => 'woodmart_slider',
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $sliders ) || ! $sliders ) {
			return $data;
		}

		foreach ( $sliders as $slider ) {
			$data[ $slider->term_id ] = array(
				'title' => $slider->name,
				'url'   => get_edit_term_link( $slider->term_id ),
			);

			$args = array(
				'posts_per_page' => -1,
				'post_type'      => 'woodmart_slide',
				'tax_query'      => array( // phpcs:ignore
					array(
						'taxonomy' => 'woodmart_slider',
						'field'    => 'id',
						'terms'    => $slider->term_id,
					),
				),
			);

			$slides = get_posts( $args );

			if ( ! $slides ) {
				continue;
			}

			foreach ( $slides as $slide ) {
				$data[ $slider->term_id ]['slides'][ $slide->ID ] = array(
					'title' => $slide->post_title,
					'url'   => get_edit_post_link( $slide->ID ),
				);
			}
		}

		wp_localize_script( 'woodmart-theme', 'woodmart_admin_bar_sliders', $data );

		return $data;
	}

	add_action( 'wp_enqueue_scripts', 'woodmart_get_slider_data', 40 );
}

if ( ! function_exists( 'woodmart_shortcode_slider' ) ) {
	/**
	 * Slider shortcode.
	 *
	 * @param array $atts Element settings.
	 *
	 * @return false|string|void
	 */
	function woodmart_shortcode_slider( $atts ) {
		$class = '';

		$parsed_atts = shortcode_atts(
			array(
				'slider'    => '',
				'el_class'  => '',
				'elementor' => false,
			),
			$atts
		);

		$class .= ' ' . $parsed_atts['el_class'];
		$class .= woodmart_get_old_classes( ' woodmart-slider' );

		$slider_term = get_term_by( 'slug', $parsed_atts['slider'], 'woodmart_slider' );

		if ( is_wp_error( $slider_term ) || empty( $slider_term ) ) {
			return;
		}

		$args = array(
			'posts_per_page' => -1,
			'post_type'      => 'woodmart_slide',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'tax_query'   => array( // phpcs:ignore
				array(
					'taxonomy' => 'woodmart_slider',
					'field'    => 'id',
					'terms'    => $slider_term->term_id,
				),
			),
		);

		$slides = get_posts( $args );

		if ( is_wp_error( $slides ) || empty( $slides ) ) {
			return;
		}

		$stretch_slider = get_term_meta( $slider_term->term_id, 'stretch_slider', true );

		$carousel_id = 'slider-' . $slider_term->term_id;

		$animation = get_term_meta( $slider_term->term_id, 'animation', true );

		$slide_speed = apply_filters( 'woodmart_slider_sliding_speed', 900 );

		$slider_atts = array(
			'carousel_id'             => $carousel_id,
			'hide_pagination_control' => get_term_meta( $slider_term->term_id, 'pagination_style', true ) === '0' ? 'yes' : 'no',
			'hide_prev_next_buttons'  => get_term_meta( $slider_term->term_id, 'arrows_style', true ) === '0' ? 'yes' : 'no',
			'autoplay'                => ( get_term_meta( $slider_term->term_id, 'autoplay', true ) === 'on' ) ? 'yes' : 'no',
			'speed'                   => get_term_meta( $slider_term->term_id, 'autoplay_speed', true ) ? get_term_meta( $slider_term->term_id, 'autoplay_speed', true ) : 9000,
			'sliding_speed'           => $slide_speed,
			'content_animation'       => true,
			'autoheight'              => 'yes',
			'wrap'                    => 'yes',
		);

		if ( ! $parsed_atts['elementor'] ) {
			ob_start();
		}

		woodmart_enqueue_js_library( 'flickity' );
		woodmart_enqueue_js_script( 'slider-element' );
		woodmart_enqueue_inline_style( 'slider' );

		if ( 'fade' === $animation ) {
			woodmart_enqueue_js_library( 'flickity-fade' );
		}

		$first_slide_key = array_key_first( $slides );

		?>
			<?php woodmart_get_slider_css( $slider_term->term_id, $carousel_id, $slides ); ?>

			<div id="<?php echo esc_attr( $carousel_id ); ?>" data-id="<?php echo esc_html( $slider_term->term_id ); ?>" class="wd-slider-wrapper<?php echo esc_attr( woodmart_get_slider_class( $slider_term->term_id ) ); ?>" <?php echo 'on' === $stretch_slider && 'wpb' === woodmart_get_current_page_builder() ? 'data-vc-full-width="true" data-vc-full-width-init="true" data-vc-stretch-content="true"' : ''; ?> <?php echo woodmart_get_owl_attributes( $slider_atts ); // phpcs:ignore ?>>
				<div class="wd-slider wd-autoplay-animations-off<?php echo esc_attr( $class ); ?>">
					<?php foreach ( $slides as $key => $slide ) : ?>
						<?php
							$slide_id        = 'slide-' . $slide->ID;
							$slide_animation = get_post_meta( $slide->ID, 'slide_animation', true );
							$slide_classes   = '';

						if ( $key === $first_slide_key ) {
							$slide_classes .= ' woodmart-loaded';
							woodmart_lazy_loading_deinit( true );
						}

						$slide_classes .= woodmart_get_old_classes( ' woodmart-slide' );
						// Distortion.
						$slide_attrs = '';
						if ( 'distortion' === $animation ) {
							woodmart_enqueue_js_script( 'slider-distortion' );
							$bg_image_desktop      = has_post_thumbnail( $slide->ID ) ? wp_get_attachment_url( get_post_thumbnail_id( $slide->ID ) ) : '';
							$meta_bg_image_desktop = get_post_meta( $slide->ID, 'bg_image_desktop', true );
							if ( $meta_bg_image_desktop ) {
								$bg_image_desktop = $meta_bg_image_desktop;
							}

							if ( $bg_image_desktop ) {
								$slide_attrs = ' data-image-url="' . $bg_image_desktop . '"';
							}
						}

						// Link.
						$link              = get_post_meta( $slide->ID, 'link', true );
						$link_target_blank = get_post_meta( $slide->ID, 'link_target_blank', true );

						if ( $link ) {
							$slide_classes .= ' cursor-pointer';
							if ( $link_target_blank ) {
								$slide_attrs .= ' onclick="window.open(\'' . esc_url( $link ) . '\',\'_blank\')"';
							} else {
								$slide_attrs .= ' onclick="window.location.href=\'' . esc_url( $link ) . '\'"';
							}
						}
						?>
						<div id="<?php echo esc_attr( $slide_id ); ?>" class="wd-slide<?php echo esc_attr( $slide_classes ); ?>" data-id="<?php echo esc_html( $slide->ID ); ?>" <?php echo $slide_attrs; // phpcs:ignore ?>>
							<?php
							if ( ! empty( $slide_animation ) && 'none' !== $slide_animation ) {
								woodmart_enqueue_inline_style( 'animations' );
								woodmart_enqueue_js_script( 'animations' );
								woodmart_enqueue_js_library( 'waypoints' );
							}
							?>
							<div class="container wd-slide-container<?php echo woodmart_get_old_classes( ' woodmart-slide-container' ); ?><?php echo woodmart_get_slide_class( $slide->ID ); // phpcs:ignore ?>">
								<div class="wd-slide-inner<?php echo woodmart_get_old_classes( ' woodmart-slide-inner' ); ?> <?php echo ( ! empty( $slide_animation ) && 'none' !== $slide_animation ) ? 'wd-animation-normal  wd-animation-' . esc_attr( $slide_animation ) : ''; // phpcs:ignore ?>">
									<?php if ( woodmart_is_elementor_installed() && Elementor\Plugin::$instance->documents->get( $slide->ID )->is_built_with_elementor() ) : ?>
										<?php echo woodmart_elementor_get_content( $slide->ID ); // phpcs:ignore ?>
									<?php else : ?>
										<?php echo apply_filters( 'the_content', $slide->post_content ); // phpcs:ignore ?>
									<?php endif; ?>
								</div>
							</div>

							<div class="wd-slide-bg wd-fill"></div>
						</div>

						<?php if ( $key === $first_slide_key ) : ?>
							<?php woodmart_lazy_loading_init(); ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( 'on' === $stretch_slider && 'wpb' === woodmart_get_current_page_builder() ) : ?>
				<div class="vc_row-full-width vc_clearfix"></div>
			<?php endif; ?>
		<?php

		if ( ! $parsed_atts['elementor'] ) {
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
	}
}

if ( ! function_exists( 'woodmart_get_slider_css' ) ) {
	/**
	 * Get slider CSS.
	 *
	 * @param int   $id     Post ID.
	 * @param int   $el_id  Element ID.
	 * @param array $slides Slides.
	 */
	function woodmart_get_slider_css( $id, $el_id, $slides ) {
		$height        = get_term_meta( $id, 'height', true );
		$height_tablet = get_term_meta( $id, 'height_tablet', true );
		$height_mobile = get_term_meta( $id, 'height_mobile', true );

		echo '<style>';
		?>
			#<?php echo esc_attr( $el_id ); ?> .wd-slide {
				min-height: <?php echo esc_attr( $height ); ?>px;
			}

			@media (min-width: 1025px) {
				.browser-Internet #<?php echo esc_attr( $el_id ); ?> .wd-slide {
					height: <?php echo esc_attr( $height ); ?>px;
				}
			}

			@media (max-width: 1024px) {
				#<?php echo esc_attr( $el_id ); ?> .wd-slide {
					min-height: <?php echo esc_attr( $height_tablet ); ?>px;
				}
			}

			@media (max-width: 767px) {
				#<?php echo esc_attr( $el_id ); ?> .wd-slide {
					min-height: <?php echo esc_attr( $height_mobile ); ?>px;
				}
			}

			<?php
			foreach ( $slides as $slide ) {
				$bg_color           = get_post_meta( $slide->ID, 'bg_color', true );
				$content_full_width = get_post_meta( $slide->ID, 'content_full_width', true );
				// Desktop.
				$bg_image_desktop      = has_post_thumbnail( $slide->ID ) ? wp_get_attachment_url( get_post_thumbnail_id( $slide->ID ) ) : '';
				$meta_bg_image_desktop = get_post_meta( $slide->ID, 'bg_image_desktop', true );
				if ( $meta_bg_image_desktop ) {
					$bg_image_desktop = $meta_bg_image_desktop;
				}
				$bg_image_size_desktop       = get_post_meta( $slide->ID, 'bg_image_size_desktop', true );
				$bg_image_position_desktop   = get_post_meta( $slide->ID, 'bg_image_position_desktop', true );
				$bg_image_position_x_desktop = get_post_meta( $slide->ID, 'bg_image_position_x_desktop', true );
				$bg_image_position_y_desktop = get_post_meta( $slide->ID, 'bg_image_position_y_desktop', true );
				$width_desktop               = get_post_meta( $slide->ID, 'content_width', true );

				// Tablet.
				$width_tablet               = get_post_meta( $slide->ID, 'content_width_tablet', true );
				$bg_image_tablet            = get_post_meta( $slide->ID, 'bg_image_tablet', true );
				$bg_image_size_tablet       = get_post_meta( $slide->ID, 'bg_image_size_tablet', true );
				$bg_image_position_tablet   = get_post_meta( $slide->ID, 'bg_image_position_tablet', true );
				$bg_image_position_x_tablet = get_post_meta( $slide->ID, 'bg_image_position_x_tablet', true );
				$bg_image_position_y_tablet = get_post_meta( $slide->ID, 'bg_image_position_y_tablet', true );

				// Mobile.
				$width_mobile               = get_post_meta( $slide->ID, 'content_width_mobile', true );
				$bg_image_mobile            = get_post_meta( $slide->ID, 'bg_image_mobile', true );
				$bg_image_size_mobile       = get_post_meta( $slide->ID, 'bg_image_size_mobile', true );
				$bg_image_position_mobile   = get_post_meta( $slide->ID, 'bg_image_position_mobile', true );
				$bg_image_position_x_mobile = get_post_meta( $slide->ID, 'bg_image_position_x_mobile', true );
				$bg_image_position_y_mobile = get_post_meta( $slide->ID, 'bg_image_position_y_mobile', true );

				?>
				#slide-<?php echo esc_attr( $slide->ID ); ?>.woodmart-loaded .wd-slide-bg {
					<?php woodmart_maybe_set_css_rule( 'background-image', $bg_image_desktop ); ?>
				}

				#slide-<?php echo esc_attr( $slide->ID ); ?> .wd-slide-bg {
					<?php woodmart_maybe_set_css_rule( 'background-color', $bg_color ); ?>
					<?php woodmart_maybe_set_css_rule( 'background-size', $bg_image_size_desktop ); ?>

					<?php if ( 'custom' !== $bg_image_position_desktop ) : ?>
						<?php woodmart_maybe_set_css_rule( 'background-position', $bg_image_position_desktop ); ?>
					<?php else : ?>
						<?php woodmart_maybe_set_css_rule( 'background-position', $bg_image_position_x_desktop . ' ' . $bg_image_position_y_desktop ); ?>
					<?php endif; ?>
				}

				<?php if ( ! $content_full_width ) : ?>
					#slide-<?php echo esc_attr( $slide->ID ); ?> .wd-slide-inner {
						<?php woodmart_maybe_set_css_rule( 'max-width', $width_desktop ); ?>
					}
				<?php endif; ?>

				@media (max-width: 1024px) {
					<?php if ( $bg_image_tablet ) : ?>
						#slide-<?php echo esc_attr( $slide->ID ); ?>.woodmart-loaded .wd-slide-bg {
							<?php woodmart_maybe_set_css_rule( 'background-image', $bg_image_tablet ); ?>
						}
					<?php endif; ?>

					<?php if ( ! $content_full_width ) : ?>
						#slide-<?php echo esc_attr( $slide->ID ); ?> .wd-slide-inner {
							<?php woodmart_maybe_set_css_rule( 'max-width', $width_tablet ); ?>
						}
					<?php endif; ?>

					#slide-<?php echo esc_attr( $slide->ID ); ?> .wd-slide-bg {
						<?php woodmart_maybe_set_css_rule( 'background-size', $bg_image_size_tablet ); ?>

						<?php if ( 'custom' !== $bg_image_position_tablet ) : ?>
							<?php woodmart_maybe_set_css_rule( 'background-position', $bg_image_position_tablet ); ?>
						<?php else : ?>
							<?php woodmart_maybe_set_css_rule( 'background-position', $bg_image_position_x_tablet . ' ' . $bg_image_position_y_tablet ); ?>
						<?php endif; ?>
					}
				}

				@media (max-width: 767px) {
					<?php if ( $bg_image_mobile ) : ?>
						#slide-<?php echo esc_attr( $slide->ID ); ?>.woodmart-loaded .wd-slide-bg {
							<?php woodmart_maybe_set_css_rule( 'background-image', $bg_image_mobile ); ?>
						}
					<?php endif; ?>

					<?php if ( ! $content_full_width ) : ?>
						#slide-<?php echo esc_attr( $slide->ID ); ?> .wd-slide-inner {
							<?php woodmart_maybe_set_css_rule( 'max-width', $width_mobile ); ?>
						}
					<?php endif; ?>

					#slide-<?php echo esc_attr( $slide->ID ); ?> .wd-slide-bg {
						<?php woodmart_maybe_set_css_rule( 'background-size', $bg_image_size_mobile ); ?>

						<?php if ( 'custom' !== $bg_image_position_mobile ) : ?>
							<?php woodmart_maybe_set_css_rule( 'background-position', $bg_image_position_mobile ); ?>
						<?php else : ?>
							<?php woodmart_maybe_set_css_rule( 'background-position', $bg_image_position_x_mobile . ' ' . $bg_image_position_y_mobile ); ?>
						<?php endif; ?>
					}
				}

				<?php if ( get_post_meta( $slide->ID, '_wpb_shortcodes_custom_css', true ) ) : ?>
						<?php echo get_post_meta( $slide->ID, '_wpb_shortcodes_custom_css', true ); // phpcs:ignore ?>
					<?php endif; ?>

				<?php if ( get_post_meta( $slide->ID, 'woodmart_shortcodes_custom_css', true ) ) : ?>
						<?php echo get_post_meta( $slide->ID, 'woodmart_shortcodes_custom_css', true ); // phpcs:ignore ?>
				<?php endif; ?>
				<?php
			}

			echo '</style>';
	}
}

if ( ! function_exists( 'woodmart_maybe_set_css_rule' ) ) {
	/**
	 * Get CSS rule.
	 *
	 * @param string $rule CSS rule.
	 * @param string $value Value.
	 * @param string $before Before value.
	 * @param string $after After value.
	 */
	function woodmart_maybe_set_css_rule( $rule, $value = '', $before = '', $after = '' ) {
		if ( in_array( $rule, array( 'width', 'height', 'max-width', 'max-height' ), true ) && empty( $after ) ) {
			$after = 'px';
		}

		if ( in_array( $rule, array( 'background-image' ), true ) && ( empty( $before ) || empty( $after ) ) ) {
			$before = 'url(';
			$after  = ')';
		}

		echo ! empty( $value ) ? $rule . ':' . $before . $value . $after . ';' : ''; // phpcs:ignore
	}
}

if ( ! function_exists( 'woodmart_get_slider_class' ) ) {
	/**
	 * Get slider classes.
	 *
	 * @param int $id Slider ID.
	 *
	 * @return string
	 */
	function woodmart_get_slider_class( $id ) {
		$class = '';

		$arrows_style         = get_term_meta( $id, 'arrows_style', true );
		$pagination_style     = get_term_meta( $id, 'pagination_style', true );
		$pagination_color     = get_term_meta( $id, 'pagination_color', true );
		$stretch_slider       = get_term_meta( $id, 'stretch_slider', true );
		$stretch_content      = get_term_meta( $id, 'stretch_content', true );
		$scroll_carousel_init = get_term_meta( $id, 'scroll_carousel_init', true );
		$animation            = get_term_meta( $id, 'animation', true );

		$class .= ' arrows-style-' . $arrows_style;
		$class .= ' pagin-style-' . $pagination_style;
		$class .= ' pagin-scheme-' . $pagination_color;
		$class .= ' anim-' . $animation;
		$class .= woodmart_get_old_classes( ' woodmart-slider-wrapper' );

		if ( 'on' === $scroll_carousel_init ) {
			woodmart_enqueue_js_library( 'waypoints' );
			$class .= ' scroll-init';
		}

		if ( 'on' === $stretch_slider ) {
			if ( 'wpb' === woodmart_get_current_page_builder() ) {
				$class .= ' vc_row vc_row-fluid';
			} else {
				$class .= ' wd-section-stretch';
			}

			if ( 'on' === $stretch_content ) {
				$class .= ' wd-full-width-content';
			}
		} else {
			$class .= ' slider-in-container';
		}

		return $class;
	}
}

if ( ! function_exists( 'woodmart_get_slide_class' ) ) {
	/**
	 * Get slide classes.
	 *
	 * @param int $id Post ID.
	 *
	 * @return string
	 */
	function woodmart_get_slide_class( $id ) {
		$class = '';

		$v_align         = get_post_meta( $id, 'vertical_align', true );
		$h_align         = get_post_meta( $id, 'horizontal_align', true );
		$v_align_tablet  = get_post_meta( $id, 'vertical_align_tablet', true );
		$h_align_tablet  = get_post_meta( $id, 'horizontal_align_tablet', true );
		$v_align_mobile  = get_post_meta( $id, 'vertical_align_mobile', true );
		$h_align_mobile  = get_post_meta( $id, 'horizontal_align_mobile', true );
		$full_width      = get_post_meta( $id, 'content_full_width', true );
		$without_padding = get_post_meta( $id, 'content_without_padding', true );

		$class .= ' wd-items-' . $v_align;
		$class .= ' wd-justify-' . $h_align;

		if ( $v_align_tablet ) {
			$class .= ' wd-items-md-' . $v_align_tablet;
		}
		if ( $h_align_tablet ) {
			$class .= ' wd-justify-md-' . $h_align_tablet;
		}

		if ( $v_align_mobile ) {
			$class .= ' wd-items-sm-' . $v_align_mobile;
		}
		if ( $h_align_mobile ) {
			$class .= ' wd-justify-sm-' . $h_align_mobile;
		}

		$class .= ' content-' . ( $full_width ? 'full-width' : 'fixed' );
		$class .= $without_padding ? ' slide-without-padding' : '';

		return apply_filters( 'woodmart_slide_classes', $class );
	}
}
