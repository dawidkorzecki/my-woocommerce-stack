<?php
/**
 * Query Attachment
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Sources;

use WP_Grid_Builder\Includes\LQIP_Resizer;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query Post attachments separately to reduce query number
 *
 * @class Attachment
 * @since 1.0.0
 */
class Attachment {

	/**
	 * Holds grid settings
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Holds attachment ids
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $attachment = [];

	/**
	 * Holds queried posts
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $posts = [];

	/**
	 * Holds attachment IDs
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $ids = [];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds grid settings.
	 */
	public function __construct( $settings ) {

		$this->settings = $settings;

	}

	/**
	 * Query Attachments from IDs
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $posts Holds queried posts.
	 */
	public function query( $posts ) {

		$this->posts = $posts;
		$this->ids   = array_unique( $this->ids );
		$this->ids   = array_filter( $this->ids );

		if ( empty( $this->ids ) ) {
			return;
		}

		$query = new \WP_Query(
			apply_filters(
				'wp_grid_builder/attachment/query_args',
				[
					'post_type'              => [ 'attachment' ], // To prevent issues with WPML Media plugin that ignores suppress_filter when 'attachment' string is set.
					'post_status'            => 'inherit',
					'posts_per_page'         => count( $this->ids ),
					'post__in'               => $this->ids,
					'orderby'                => 'post__in',
					'no_found_rows'          => true,
					'suppress_filters'       => true,
					'update_post_term_cache' => false,
				],
				$this->settings
			)
		);

		if ( ! $query->have_posts() ) {
			return;
		}

		while ( $query->have_posts() ) {

			global $post;

			$query->the_post();
			$this->attachments[ $post->ID ] = $this->get_attachment( $post );

		}

		$this->set_attachments();
		wp_reset_postdata();

	}

	/**
	 * Parse all attachment ids from object
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array $object Holds queried object.
	 */
	public function parse_attachment_ids( $object ) {

		if (
			! empty( $object->post_thumbnail ) &&
			is_numeric( $object->post_thumbnail )
		) {

			$this->ids[] = $object->post_thumbnail;
		}

		if (
			! empty( $object->post_media['format'] ) &&
			! empty( $object->post_media['sources'] ) &&
			'gallery' === $object->post_media['format']
		) {

			foreach ( $object->post_media['sources'] as $id ) {

				if ( is_numeric( $id ) ) {
					$this->ids[] = $id;
				}
			}
		}
	}

	/**
	 * Get Attachment arguments
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $attachment Holds post object.
	 * @return array
	 */
	public function get_attachment( $attachment ) {

		return [
			'title'       => $attachment->post_title,
			'caption'     => $attachment->post_excerpt,
			'description' => $attachment->post_content,
			'mime_type'   => $attachment->post_mime_type,
			'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
			'sizes'       => $this->get_sizes( $attachment ),
		];

	}

	/**
	 * Set Attachment sizes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $attachment Attachment object.
	 */
	public function get_sizes( $attachment ) {

		$size = wp_is_mobile() ? $this->settings->thumbnail_size_mobile : $this->settings->thumbnail_size;

		$sizes = [
			'thumbnail' => wp_get_attachment_image_src( $attachment->ID, $size, false ),
			'lightbox'  => wp_get_attachment_image_src( $attachment->ID, 'thumbnail', false ),
			'full'      => wp_get_attachment_image_src( $attachment->ID, $this->settings->lightbox_image_size, false ),
		];

		if ( $this->settings->lazy_load && $this->settings->lazy_load_blurred_image ) {

			$sizes['lazy'] = LQIP_Resizer::get_attachment_image_src(
				$attachment->ID,
				[
					'ID'    => WPGB_SLUG . '-lqip',
					'width' => 42,
				]
			);

		}

		return array_map(
			function( $size ) {

				return [
					'url'    => isset( $size[0] ) ? $size[0] : null,
					'width'  => isset( $size[1] ) ? $size[1] : null,
					'height' => isset( $size[2] ) ? $size[2] : null,
				];

			},
			$sizes
		);

	}

	/**
	 * Set Attachments to posts
	 *
	 * @since 1.1.5 Get first product gallery attachment.
	 * @since 1.0.0
	 * @access public
	 */
	public function set_attachments() {

		if ( empty( $this->attachments ) ) {
			return;
		}

		$this->posts = array_map(
			function( $post ) {

				$format = $post->post_format;
				$media  = $post->post_media;
				$thumb  = $post->post_thumbnail;

				if ( is_numeric( $thumb ) && isset( $this->attachments[ $thumb ] ) ) {
					$post->post_thumbnail = $this->attachments[ $thumb ];
				}

				if ( ! empty( $post->product->first_gallery_image ) && isset( $this->attachments[ $post->product->first_gallery_image ] ) ) {
					$post->product->first_gallery_image = $this->attachments[ $post->product->first_gallery_image ];
				}

				if ( 'gallery' !== $format || empty( $media['sources'] ) || ! is_array( $media['sources'] ) ) {
					return $post;
				}

				$post->post_media['sources'] = $this->set_gallery( $media['sources'] );

				return $post;

			},
			$this->posts
		);

	}

	/**
	 * Set Gallery images.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $gallery Holds attachment ids of the gallery.
	 */
	public function set_gallery( $gallery ) {

		return array_map(
			function( $id ) {

				if ( ! is_numeric( $id ) || ! isset( $this->attachments[ $id ] ) ) {
					return;
				}

				return $this->attachments[ $id ];

			},
			$gallery
		);

	}
}
