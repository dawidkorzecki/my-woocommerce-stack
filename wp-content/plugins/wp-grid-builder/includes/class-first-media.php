<?php
/**
 * First_Media
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
 * Find first media in post content
 *
 * @class WP_Grid_Builder\Includes\First_Media
 * @since 1.0.0
 */
final class First_Media {

	/**
	 * Holds post object
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var object
	 */
	private $post = [];

	/**
	 * Run the main function class
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $post Holds post object.
	 */
	public function __construct( $post = '' ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			$post = get_post();
		}

		$this->post = $post;

	}

	/**
	 * Get media according to post format
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $format Post format.
	 * @return array
	 */
	public function get( $format = '' ) {

		switch ( $format ) {
			case 'video':
			case 'audio':
				$media = $this->get_media( $format );
				break;
			case 'gallery':
				$media = $this->get_gallery();
				break;
			default:
				$media = $this->get_image();
				break;
		}

		return $media;

	}

	/**
	 * Get first image tag url in post text editor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_image() {

		// Check the content for wp-image-%d class.
		preg_match( '#class=[\'"](?:.*?)wp-image-([\d]*)[\'"]#i', $this->post->post_content, $id );

		// Get image id.
		if ( isset( $id[1] ) ) {
			return (int) $id[1];
		}

		// Search the post's content for the <img> tag and get its URL.
		preg_match( '#<img.*?src=[\'"](.*?)[\'"].*?>#i', $this->post->post_content, $matches );

		// If there isn't any match..
		if ( ! isset( $matches[1] ) ) {
			return;
		}

		return $this->get_image_id( $matches[1] );

	}

	/**
	 * Try to convert an attachment URL into an ID.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $url Image url.
	 * @return integer Image url
	 */
	public function get_image_id( $url ) {

		global $wpdb;

		if ( empty( $url ) ) {
			return;
		}

		$attachment_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE guid = %s",
				$url
			)
		);

		if ( ! $attachment_id ) {
			return;
		}

		return $attachment_id;

	}

	/**
	 * Get gallery images ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_gallery() {

		$gallery = get_post_gallery( $this->post->ID, false );

		if ( empty( $gallery['ids'] ) ) {
			return;
		}

		$ids = explode( ',', $gallery['ids'] );
		$ids = array_map( 'intval', $ids );

		return [
			'sources' => $ids,
		];

	}

	/**
	 * Get first media (video & audio) in content
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $type Media type to fetch..
	 * @return array
	 */
	public function get_media( $type = 'video' ) {

		// Media types to fetch.
		$types = 'video' === $type ? [ 'video', 'embed', 'iframe' ] : [ 'audio' ];

		// Partially render post content.
		$content = $this->post->post_content;
		$content = $this->do_media_shortcodes( $content );
		$content = $this->autoembed( $content );
		$embeds  = get_media_embedded_in_content( $content, $types );

		foreach ( (array) $embeds as $embed ) {

			$media = $this->get_hosted_media( $embed );

			if ( ! $media && 'video' === $type ) {
				$media = $this->get_embedded_media( $embed );
			}

			if ( $media ) {
				return $media;
			}
		}
	}

	/**
	 * Render native WordPress media shortcodes (audio, video, embed)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $content Post content.
	 * @return array
	 */
	public function do_media_shortcodes( $content ) {

		if ( false === strpos( $content, '[' ) ) {
			return $content;
		}

		// We only do audio/video to prevent unecessary queries from any other shortcode.
		$tagnames = [ 'audio', 'video' ];
		$pattern  = get_shortcode_regex( $tagnames );

		// Match unregistered embed shortcode and do audio and video shortcodes..
		$content = preg_replace( '|\[embed?(?:.*?)?\](.+?)\[\/embed\]|i', '<iframe src="$1"></iframe>', $content );
		$content = do_shortcodes_in_html_tags( $content, false, $tagnames );
		$content = preg_replace_callback( "/$pattern/", 'do_shortcode_tag', $content );

		return $content;

	}

	/**
	 * Passes any unlinked URLs that are on their own line for potential embedding.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $content Post content.
	 * @return array
	 */
	public function autoembed( $content ) {

		// Replace line breaks from all HTML elements with placeholders.
		$content = wp_replace_in_html_tags( $content, [ "\n" => '<!-- wp-line-break -->' ] );

		if ( preg_match( '#(^|\s|>)https?://#i', $content ) ) {

			// Find URLs on their own line.
			$content = preg_replace( '|^(\s*)(https?://[^\s<>"]+)(\s*)$|im', '<iframe src="$2"></iframe>', $content );
			// Find URLs in their own paragraph.
			$content = preg_replace( '|(<p(?: [^>]*)?>\s*)(https?://[^\s<>"]+)(\s*<\/p>)|i', '<iframe src="$2"></iframe>', $content );

		}

		// Put the line breaks back.
		return str_replace( '<!-- wp-line-break -->', "\n", $content );

	}

	/**
	 * Get first hosted media (video & audio) in content
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $embed Embed video.
	 * @return array
	 */
	public function get_hosted_media( $embed ) {

		$pattern = '#<(?:video|audio|source).*?src=[\'"](.*?)[\'"].*?>+#i';
		preg_match_all( $pattern, $embed, $sources );

		if ( empty( $sources[1] ) ) {
			return;
		}

		$media = [];

		$mimes  = 'asf|asx|wmv|wmx|wm|avi|divx|flv|mov|qt|mpeg|mpg|';
		$mimes .= 'mpe|mp4|m4v|ogv|webm|mkv|3gp|3gpp|3g2|3gp2|3gpp2|';
		$mimes .= 'mp3|m4a|m4b|ra|ram|wav|ogg|oga|mid|midi|wma|wax|mka';

		foreach ( $sources[1] as $source ) {

			preg_match( '/^.*\.(' . $mimes . ')/i', $source, $ext );

			if ( isset( $ext[0] ) ) {
				$media['sources'][] = $ext[0];
			}
		}

		if ( empty( $media ) ) {
			return;
		}

		preg_match( '#poster=[\'"](.*?)[\'"]+#i', $embed, $poster );

		if ( isset( $poster[1] ) ) {
			$media['poster'] = $poster[1];
		}

		$media['type'] = 'hosted';

		return $media;

	}

	/**
	 * Get embedded media video
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $embed Embed video.
	 * @return array
	 */
	public function get_embedded_media( $embed ) {

		$providers = Helpers::get_embed_providers();

		foreach ( $providers as $provider => $media ) {

			if ( ! preg_match( $provider, $embed, $match ) ) {
				continue;
			}

			return [
				'type'    => 'embedded',
				'sources' => [
					'provider' => $media,
					'url'      => $match[0],
					'id'       => $match[1],
				],
			];
		}
	}
}
