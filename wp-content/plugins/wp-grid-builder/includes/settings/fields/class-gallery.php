<?php
/**
 * Gallery field
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Settings\Fields;

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings API Field
 *
 * @class WP_Grid_Builder\Includes\Settings\Fields\Gallery
 * @since 1.0.0
 * @see WP_Grid_Builder\Includes\Settings\Field
 */
class Gallery extends Field {

	/**
	 * Render HTML field
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function render( $args ) {

		$mime_type = wp_json_encode( $args['mime_type'] );

		echo '<ul class="wpgb-gallery" data-mime-type="' . esc_attr( $mime_type ) . '">';
			$this->render_item( $args );
			$this->loop( $args );
		echo '</ul>';

	}

	/**
	 * Query attachments
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function query( $args ) {

		$ids = (array) $args['value'];

		if ( empty( $ids ) ) {
			return;
		}

		return new \WP_Query(
			[
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => count( $ids ),
				'post__in'       => $ids,
				'orderby'        => 'post__in',
				'no_found_rows'  => true,
			]
		);

	}

	/**
	 * Loop
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 */
	public function loop( $args ) {

		$query = $this->query( $args );

		if ( empty( $query ) || ! $query->have_posts() ) {
			return;
		}

		while ( $query->have_posts() ) {

			$query->the_post();
			$data = $this->get_data();

			$this->render_item( $args, $data );

		}

		wp_reset_postdata();

	}


	/**
	 * Get attachment data
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_data() {

		$id = get_the_ID();

		if ( wp_attachment_is_image( $id ) ) {

			$url = wp_get_attachment_image_src( $id, 'medium' );

			return [
				'url' => isset( $url[0] ) ? $url[0] : '',
			];

		}

		return [
			'url'      => get_the_post_thumbnail_url( $id, 'medium' ),
			'filename' => basename( get_attached_file( $id ) ),
		];

	}

	/**
	 * Render gallery item
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Holds field parameters.
	 * @param array $data Holds item data.
	 */
	public function render_item( $args, $data = '' ) {

		echo '<li class="wpgb-image" title="' . esc_attr__( 'Add media', 'wp-grid-builder' ) . '">';

			printf(
				'<input type="hidden" name="%s" value="%s">',
				esc_attr( $args['name'] ),
				esc_attr( $data ? get_the_ID() : '' )
			);

			$this->add_icon( $data );
			$this->background( $data );
			$this->delete_icon( $data );
			$this->filename( $data );

		echo '</li>';

	}

	/**
	 * Render add icon
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data Holds item data.
	 */
	public function add_icon( $data ) {

		if ( ! empty( $data ) ) {
			return;
		}

		Helpers::get_icon( 'plus' );

	}

	/**
	 * Render delete icon
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data Holds item data.
	 */
	public function delete_icon( $data ) {

		echo '<span class="wpgb-image-delete" title="' . esc_attr__( 'Delete media', 'wp-grid-builder' ) . '">';
			Helpers::get_icon( 'cross' );
		echo '</span>';

	}

	/**
	 * Render item background
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data Holds item data.
	 */
	public function background( $data ) {

		printf(
			'<div class="wpgb-image-background" title="%s"%s></div>',
			esc_attr__( 'Drag &#38; Drop', 'wp-grid-builder' ),
			isset( $data['url'] ) && ! empty( $data['url'] ) ? ' style="background-image: url(' . esc_url( $data['url'] ) . ')"' : ''
		);

	}

	/**
	 * Render filename
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data Holds item data.
	 */
	public function filename( $data ) {

		if ( ! isset( $data['filename'] ) || empty( $data['filename'] ) ) {
			return;
		}

		echo '<span class="wpgb-image-filename">';
			echo esc_html( $data['filename'] );
		echo '</span>';

	}

	/**
	 * Normalize field parameters
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $field Holds field parameters.
	 * @return array
	 */
	public function normalize( $field ) {

		return wp_parse_args(
			$field,
			[
				'default'   => '',
				'multiple'  => true,
				'mime_type' => [ 'image' ],
			]
		);

	}

	/**
	 * Sanitize field value
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $val Field value.
	 * @param array $args Holds field parameters.
	 * @return array
	 */
	public function sanitize( $val, $args = [] ) {

		if ( ! is_array( $val ) ) {
			return '';
		}

		$val = array_filter( $val );
		$val = array_unique( $val );
		$val = array_map( 'intval', $val );

		return $val;

	}
}
