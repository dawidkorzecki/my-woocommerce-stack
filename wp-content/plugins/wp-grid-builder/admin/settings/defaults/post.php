<?php
/**
 * Default Post settings
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return [
	// General.
	'post_format'          => '',
	'permalink'            => '',
	'columns'              => 1,
	'rows'                 => 1,
	'content_background'   => '',
	'overlay_background'   => '',
	'content_color_scheme' => '',
	'overlay_color_scheme' => '',
	// Altenative image.
	'attachment_id'        => '',
	'gallery_ids'          => '',
	// Audio format.
	'mp3_url'              => '',
	'ogg_url'              => '',
	// Video format.
	'mp4_url'              => '',
	'ogv_url'              => '',
	'webm_url'             => '',
	'embed_video_url'      => '',
	'video_ratio'          => '16:9',
];
