<?php
/**
 * Elementor helpers.
 *
 * @package woodmart
 */

use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit; // Direct access not allowed.
}


if ( ! function_exists( 'woodmart_elementor_get_content' ) ) {
	/**
	 * Retrieve builder content for display.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id The post ID.
	 *
	 * @return string
	 */
	function woodmart_elementor_get_content( $id ) {
		$inline_css = apply_filters( 'woodmart_elementor_content_inline_css', true );

		$content = Plugin::$instance->frontend->get_builder_content_for_display( $id, $inline_css );

		if ( $inline_css ) {
			wp_deregister_style( 'elementor-post-' . $id );
			wp_dequeue_style( 'elementor-post-' . $id );
		}

		return $content;
	}
}

if ( ! function_exists( 'woodmart_get_link_attrs' ) ) {
	/**
	 * Get image url
	 *
	 * @since 1.0.0
	 *
	 * @param array $link Link data array.
	 *
	 * @return string
	 */
	function woodmart_get_link_attrs( $link ) {
		$link_attrs = '';

		if ( isset( $link['url'] ) && $link['url'] ) {
			$link_attrs = ' href="' . esc_url( $link['url'] ) . '"';

			if ( isset( $link['is_external'] ) && 'on' === $link['is_external'] ) {
				$link_attrs .= ' target="_blank"';
			}

			if ( isset( $link['nofollow'] ) && 'on' === $link['nofollow'] ) {
				$link_attrs .= ' rel="nofollow noopener"';
			}
		}

		if ( isset( $link['class'] ) ) {
			$link_attrs .= ' class="' . esc_attr( $link['class'] ) . '"';
		}

		if ( isset( $link['data'] ) ) {
			$link_attrs .= $link['data'];
		}

		if ( isset( $link['custom_attributes'] ) ) {
			$custom_attributes = Utils::parse_custom_attributes( $link['custom_attributes'] );
			foreach ( $custom_attributes as $key => $value ) {
				$link_attrs .= ' ' . $key . '="' . $value . '"';
			}
		}

		return $link_attrs;
	}
}

if ( ! function_exists( 'woodmart_elementor_get_render_icon' ) ) {
	/**
	 * Render Icon
	 *
	 * @since 1.0.0
	 *
	 * @param array  $icon       Icon Type, Icon value.
	 * @param array  $attributes Icon HTML Attributes.
	 * @param string $tag        Icon HTML tag, defaults to <i>.
	 *
	 * @return mixed|string
	 */
	function woodmart_elementor_get_render_icon( $icon, $attributes = [], $tag = 'i' ) {
		ob_start();
		Icons_Manager::render_icon( $icon, $attributes, $tag );
		return ob_get_clean();
	}
}

if ( ! function_exists( 'woodmart_elementor_has_location' ) ) {
	/**
	 * Custom shapes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $location Location.
	 *
	 * @return bool
	 */
	function woodmart_elementor_has_location( $location ) {
		if ( ! woodmart_is_elementor_pro_installed() ) {
			return false;
		}

		$conditions_manager = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' )->get_conditions_manager();
		$documents          = $conditions_manager->get_documents_for_location( $location );

		return ! empty( $documents );
	}
}

if ( ! function_exists( 'woodmart_get_image_html' ) ) {
	/**
	 * Get image url
	 *
	 * @since 1.0.0
	 *
	 * @param array  $settings       Control settings.
	 * @param string $image_size_key Settings key for image size.
	 *
	 * @return string
	 */
	function woodmart_get_image_html( $settings, $image_size_key = '' ) {
		if ( ! woodmart_is_elementor_installed() ) {
			return wp_get_attachment_image( $settings[ $image_size_key ]['id'], $settings[ $image_size_key . '_size' ] );
		}

		return Group_Control_Image_Size::get_attachment_image_html( $settings, $image_size_key );
	}
}

if ( ! function_exists( 'woodmart_elementor_is_edit_mode' ) ) {
	/**
	 * Whether the edit mode is active.
	 *
	 * @since 1.0.0
	 */
	function woodmart_elementor_is_edit_mode() {
		if ( ! woodmart_is_elementor_installed() ) {
			return false;
		}

		return Plugin::$instance->editor->is_edit_mode();
	}
}

if ( ! function_exists( 'woodmart_elementor_is_preview_mode' ) ) {
	/**
	 * Whether the preview mode is active.
	 *
	 * @since 1.0.0
	 */
	function woodmart_elementor_is_preview_mode() {
		return Plugin::$instance->preview->is_preview_mode();
	}
}

if ( ! function_exists( 'woodmart_elementor_is_preview_page' ) ) {
	/**
	 * Whether the preview page.
	 *
	 * @since 1.0.0
	 */
	function woodmart_elementor_is_preview_page() {
		return isset( $_GET['preview_id'] ); // phpcs:ignore
	}
}

if ( ! function_exists( 'woodmart_get_image_url' ) ) {
	/**
	 * Get image url
	 *
	 * @since 1.0.0
	 *
	 * @param integer $id             Image id.
	 * @param string  $image_size_key Settings key for image size.
	 * @param array   $settings       Control settings.
	 *
	 * @return string
	 */
	function woodmart_get_image_url( $id, $image_size_key, $settings ) {
		if ( ! woodmart_is_elementor_installed() ) {
			return wp_get_attachment_image_src( $id, $settings[ $image_size_key . '_size' ] )[0];
		}

		return Group_Control_Image_Size::get_attachment_image_src( $id, $image_size_key, $settings );
	}
}

if ( ! function_exists( 'woodmart_get_all_image_sizes' ) ) {
	/**
	 * Retrieve available image sizes
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function woodmart_get_all_image_sizes() {
		global $_wp_additional_image_sizes;

		$default_image_sizes = array( 'thumbnail', 'medium', 'medium_large', 'large' );
		$image_sizes         = array();

		foreach ( $default_image_sizes as $size ) {
			$image_sizes[ $size ] = array(
				'width'  => (int) get_option( $size . '_size_w' ),
				'height' => (int) get_option( $size . '_size_h' ),
				'crop'   => (bool) get_option( $size . '_crop' ),
			);
		}

		if ( $_wp_additional_image_sizes ) {
			$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
		}

		$image_sizes['full'] = array();

		return $image_sizes;
	}
}

if ( ! function_exists( 'woodmart_get_all_image_sizes_names' ) ) {
	/**
	 * Retrieve available image sizes names
	 *
	 * @since 1.0.0
	 *
	 * @param string $style Array output style.
	 *
	 * @return array
	 */
	function woodmart_get_all_image_sizes_names( $style = 'default' ) {
		$available_sizes = woodmart_get_all_image_sizes();
		$image_sizes     = array();

		foreach ( $available_sizes as $size => $size_attributes ) {
			$name = ucwords( str_replace( '_', ' ', $size ) );
			if ( is_array( $size_attributes ) && ( isset( $size_attributes['width'] ) || isset( $size_attributes['height'] ) ) ) {
				$name .= ' - ' . $size_attributes['width'] . ' x ' . $size_attributes['height'];
			}

			if ( 'elementor' === $style ) {
				$image_sizes[ $size ] = $name;
			} elseif ( 'header_builder' === $style ) {
				$image_sizes[ $size ] = array(
					'label' => $name,
					'value' => $size,
				);
			} elseif ( 'default' === $style ) {
				$image_sizes[ $size ] = array(
					'name'  => $name,
					'value' => $size,
				);
			} elseif ( 'widget' === $style ) {
				$image_sizes[ $name ] = $size;
			}
		}

		if ( 'elementor' === $style ) {
			$image_sizes['custom'] = esc_html__( 'Custom', 'woodmart' );
		} elseif ( 'header_builder' === $style ) {
			$image_sizes['custom'] = array(
				'label' => esc_html__( 'Custom', 'woodmart' ),
				'value' => 'custom',
			);
		} elseif ( 'default' === $style ) {
			$image_sizes['custom'] = array(
				'name'  => esc_html__( 'Custom', 'woodmart' ),
				'value' => 'custom',
			);
		} elseif ( 'widget' === $style ) {
			$image_sizes[ esc_html__( 'Custom', 'woodmart' ) ] = 'custom';
		}

		return $image_sizes;
	}
}
