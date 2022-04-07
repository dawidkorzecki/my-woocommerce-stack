<?php
/**
 * LQIP Resizer
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Low Quality Image Placeholders (LQIP) Resizer
 *
 * @class WP_Grid_Builder\Includes\LQIP_Resizer
 * @since 1.0.0
 */
final class LQIP_Resizer {

	/**
	 * Retrieve an image to represent an attachment
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $id   Image attachment ID.
	 * @param array   $size Image size arguments.
	 * @return false|array Returns an array (url, width, height, is_intermediate), or false, if no image is available.
	 */
	public static function get_attachment_image_src( $id = 0, $size = [] ) {

		if ( ! $id || empty( $size ) || ! isset( $size['ID'] ) ) {
			return;
		}

		// Normalize size.
		$size = wp_parse_args(
			(array) $size,
			[
				'ID'     => 'custom_size',
				'width'  => 42,
				'height' => 0,
				'crop'   => false,
			]
		);

		// Fetch the sized image.
		$meta = wp_get_attachment_metadata( $id );

		// If the size does not exist.
		if ( ! isset( $meta['sizes'][ $size['ID'] ] ) ) {

			// Schedule a cron now to generate the new size.
			wp_schedule_single_event( time(), 'wpgb_resizer_cron', [ $id, $size ] );
			return false;

		}

		// Return attachment src.
		return wp_get_attachment_image_src( $id, $size['ID'] );

	}

	/**
	 * Generate attachment meta for attachment ID
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $id   Image attachment ID.
	 * @param array   $size Image size arguments.
	 */
	public static function generate_attachment_metadata( $id, $size ) {

		// Get the attachment data.
		$meta = wp_get_attachment_metadata( $id );

		if ( ! $meta ) {
			return;
		}

		$new_size = self::resize_attachment( $id, $size );

		if ( ! $new_size ) {
			return;
		}

		// Generate new attachment size.
		$meta['sizes'][ $size['ID'] ] = $new_size;
		wp_update_attachment_metadata( $id, $meta );

	}

	/**
	 * Resize attachment
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $id   Image attachment ID.
	 * @param array   $size Image size arguments.
	 */
	public static function resize_attachment( $id, $size ) {

		$image = false;
		$file  = get_attached_file( $id );

		if ( ! is_file( $file ) && ! preg_match( '|^https?://|', $file ) ) {
			return false;
		}

		if ( extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) ) {
			$image = self::imagick_editor( $file, $size );
		} elseif ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ) {
			$image = self::gd_editor( $file, $size );
		}

		if ( ! $image ) {
			return false;
		}

		return [
			'file'      => self::get_basename( $file, $size ),
			'width'     => $size['width'],
			'height'    => $size['height'],
			'mime-type' => 'image/jpeg',
		];

	}

	/**
	 * Generate lqip from GD library
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $file Image file.
	 * @param array   $size Image size arguments.
	 */
	public static function gd_editor( $file, $size ) {

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$stream = file_get_contents( $file );
		$stream = imagecreatefromstring( $stream );

		list( $width, $height ) = getimagesize( $file );

		$nwidth  = (int) $size['width'];
		$nheight = $nwidth * $height / $width;
		$image   = imagecreatetruecolor( $nwidth, $nheight );

		imagecopyresampled( $image, $stream, 0, 0, 0, 0, $nwidth, $nheight, $width, $height );

		for ( $i = 0; $i < 10; $i++ ) {
			imagefilter( $image, IMG_FILTER_GAUSSIAN_BLUR );
		}

		return imagejpeg( $image, self::get_file_path( $file, $size ), 100 );

	}

	/**
	 * Generate lqip from ImageMagick library
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $file Image file.
	 * @param array   $size Image size arguments.
	 */
	public static function imagick_editor( $file, $size ) {

		$image = new \Imagick( $file );
		// To convert .gif to .jpg.
		$image = $image->mergeImageLayers( \imagick::LAYERMETHOD_FLATTEN );

		$image->setImageCompressionQuality( 100 );
		$image->scaleImage( (int) $size['width'], (int) $size['height'] );
		$image->blurImage( (int) $size['width'], 2.25 );
		$image->stripImage();

		return $image->writeImages( self::get_file_path( $file, $size ), true );

	}

	/**
	 * Get image basename
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $file Image file.
	 * @param array   $size Image size arguments.
	 */
	public static function get_basename( $file, $size ) {

		$info = pathinfo( $file );

		return $info['filename'] . '-' . $size['ID'] . '.jpg';

	}

	/**
	 * Get image file path
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $file Image file.
	 * @param array   $size Image size arguments.
	 */
	public static function get_file_path( $file, $size ) {

		$info = pathinfo( $file );
		$name = self::get_basename( $file, $size );

		return $info['dirname'] . '/' . $name;

	}
}
