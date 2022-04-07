<?php
/**
 * Cards
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd;

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build card class
 *
 * @class WP_Grid_Builder\FrontEnd\Cards
 * @since 1.0.0
 */
final class Cards implements Models\Cards_Interface {

	/**
	 * Holds cards
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public static $cards = [];

	/**
	 * Holds card attributes
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $atts = [];

	/**
	 * Default card layout
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $layout = [
		'layers' => [
			'inner' => [
				'media' => [
					'media-thumbnail' => true,
				],
			],
		],
	];

	/**
	 * Holds allowed layer names
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $layers = [
		'inner'                => true,
		'header'               => true,
		'media'                => true,
		'media-thumbnail'      => true,
		'media-overlay'        => true,
		'media-content'        => true,
		'media-content-top'    => true,
		'media-content-center' => true,
		'media-content-bottom' => true,
		'content'              => true,
		'body'                 => true,
		'footer'               => true,
	];

	/**
	 * Holds allowed blocks and associated default HTML tags.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $blocks = [
		// Post blocks.
		'the_id'                 => 'div',
		'the_title'              => 'h3',
		'the_name'               => 'div',
		'the_content'            => 'div',
		'the_excerpt'            => 'p',
		'the_post_type'          => 'div',
		'the_post_format'        => 'div',
		'the_post_status'        => 'div',
		'the_date'               => 'time',
		'the_modified_date'      => 'time',
		'the_terms'              => 'div',
		'the_author'             => 'div',
		'the_avatar'             => 'div',
		'comments_number'        => 'div',
		// Product/Download blocks.
		'the_full_price'         => 'div',
		'the_price'              => 'div',
		'the_regular_price'      => 'div',
		'the_sale_price'         => 'div',
		'the_cart_button'        => 'a',
		'the_star_rating'        => 'div',
		'the_text_rating'        => 'div',
		'the_on_sale_badge'      => 'div',
		'the_in_stock_badge'     => 'div',
		'the_out_of_stock_badge' => 'div',
		// User blocks.
		'the_user_id'            => 'div',
		'the_user_display_name'  => 'h3',
		'the_user_first_name'    => 'div',
		'the_user_last_name'     => 'div',
		'the_user_nickname'      => 'div',
		'the_user_login'         => 'div',
		'the_user_description'   => 'p',
		'the_user_email'         => 'div',
		'the_user_url'           => 'div',
		'the_user_roles'         => 'div',
		'the_user_post_count'    => 'div',
		// term blocks.
		'the_term_id'            => 'div',
		'the_term_name'          => 'h3',
		'the_term_slug'          => 'div',
		'the_term_taxonomy'      => 'div',
		'the_term_parent'        => 'div',
		'the_term_description'   => 'p',
		'the_term_count'         => 'div',
		// General blocks.
		'metadata'               => 'div',
		'raw_content_block'      => 'div',
		'media_button_block'     => 'div',
		'social_share_block'     => 'a',
		'svg_icon_block'         => 'div',
		'custom_block'           => 'div',
	];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object Settings $settings Settings class instance.
	 */
	public function __construct( Settings $settings ) {

		$this->settings = $settings;

	}

	/**
	 * Get cards
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get() {

		return self::$cards;

	}

	/**
	 * Get card params
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function query() {

		$cards = (array) $this->settings->cards;
		$cards = array_values( $cards );
		$cards = array_filter( $cards, 'is_numeric' );
		$cards = array_unique( $cards );
		// Check if there are new card(s) to query.
		$cards = array_diff( $cards, array_keys( self::$cards ) );

		if ( empty( $cards ) ) {
			return;
		}

		$type = 'masonry' !== $this->settings->type ? 'metro' : null;

		$cards = Database::query_results(
			[
				'select' => 'id, type, layout, css',
				'from'   => 'cards',
				'type'   => $type,
				'id'     => $cards,
			]
		);

		foreach ( $cards as $card ) {

			$layout = json_decode( $card['layout'], true );
			$layout = apply_filters( 'wp_grid_builder/card/settings', $layout );

			self::$cards[ $card['id'] ] = $layout;
			self::$cards[ $card['id'] ]['css'] = $card['css'];

		}

		return $this;

	}

	/**
	 * Build card
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $atts Post attributes.
	 * @param string $type Layout type.
	 */
	public function render( $atts, $type = 'masonry' ) {

		$this->atts = $atts;

		$card = $this->check( $type );
		$card = $this->prepare( $card, $type );

		$this->normalize( $card );
		$this->process( $card );

	}

	/**
	 * Check card
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $type Layout type.
	 * @return array Card layout.
	 */
	protected function check( $type ) {

		// Allow to override card.
		$this->atts['card'] = apply_filters( 'wp_grid_builder/card/id', $this->atts['card'] );

		$card = $this->atts['card'];
		$type = 'masonry' !== $type ? 'metro' : null;

		// If card exists.
		if ( isset( self::$cards[ $card ] ) ) {
			return self::$cards[ $card ];
		}

		$customs   = apply_filters( 'wp_grid_builder/cards', [] );
		$card_type = isset( $customs[ $card ]['type'] ) ? $customs[ $card ]['type'] : '';
		$is_valid  = ! $type || ( $type && 'masonry' !== $card_type );

		if ( isset( $customs[ $card ]['render_callback'] ) && $is_valid ) {
			return $customs[ $card ];
		}

		// Set card name to default.
		$this->atts['card'] = 'default';
		// Return default card layout.
		return $this->layout;

	}

	/**
	 * Prepare card layout
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array  $card Holds card layers.
	 * @param string $type Layout type.
	 * @return array Card layout.
	 */
	protected function prepare( $card, $type ) {

		$this->atts['format'] = wpgb_get_media_format();

		if ( isset( $card['settings']['responsive'] ) && $card['settings']['responsive'] ) {
			$this->atts['fluid'] = ' data-fluid';
		}

		if ( ! isset( $card['layers']['inner'] ) ) {
			return $card;
		}

		// Metro/Justified card does not support content holders.
		if ( 'masonry' !== $type ) {

			unset(
				$card['layers']['inner']['header'],
				$card['layers']['inner']['content']
			);

		} elseif ( ! wpgb_has_post_media() ) {

			unset( $card['layers']['inner']['media'] );
			return $card;

		}

		if ( wpgb_has_post_thumbnail() ) {
			return $card;
		}

		$this->atts['nothumb'] = ' data-nothumb';

		// Exception: Audio format without poster.
		if ( 'masonry' === $type && 'audio' === $this->atts['format'] ) {

			unset(
				$card['layers']['inner']['media']['media-overlay'],
				$card['layers']['inner']['media']['media-content']
			);

		}

		return $card;

	}

	/**
	 * Normalize card attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $card Holds card layers and blocks.
	 */
	protected function normalize( $card ) {

		$this->atts = apply_filters( 'wp_grid_builder/card/attributes', $this->atts, $card );
		$this->atts = wp_parse_args(
			$this->atts,
			[
				'class'                => '',
				'columns'              => 1,
				'rows'                 => 1,
				'format'               => 'standard',
				'nothumb'              => '',
				'fluid'                => '',
				'content_color_scheme' => 'dark',
				'overlay_color_scheme' => 'light',
				'content_background'   => '',
				'overlay_background'   => '',
			]
		);

		$this->atts['class']   = Helpers::sanitize_html_classes( $this->atts['class'] );
		$this->atts['columns'] = max( 1, (int) $this->atts['columns'] );
		$this->atts['rows']    = max( 1, (int) $this->atts['rows'] );

	}

	/**
	 * Precess card
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $card Holds card layers and blocks.
	 */
	protected function process( $card ) {

		$tag_name = apply_filters( 'wp_grid_builder/card/tag', 'article', $card, $this->settings );

		printf(
			'<%s class="wpgb-card wpgb-card-%s %s" data-col="%d" data-row="%d" data-format="%s"%s%s>',
			tag_escape( $tag_name ),
			esc_attr( $this->atts['card'] ),
			esc_attr( $this->atts['class'] ),
			esc_attr( $this->atts['columns'] ),
			esc_attr( $this->atts['rows'] ),
			esc_attr( $this->atts['format'] ),
			esc_attr( $this->atts['fluid'] ),
			esc_attr( $this->atts['nothumb'] )
		);

			do_action( 'wp_grid_builder/card/wrapper_start', $card );

			echo '<div class="wpgb-card-wrapper">';
				$this->do_card( $card );
				$this->do_action( $card );
			echo '</div>';

			do_action( 'wp_grid_builder/card/wrapper_end', $card );

		echo '</' . tag_escape( $tag_name ) . '>';

	}


	/**
	 * Do card
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $card Holds card layers and blocks.
	 */
	protected function do_card( $card ) {

		$native = ! isset( $card['layers'] ) ?: $card['layers'];
		$custom = ! isset( $card['render_callback'] ) ?: $card['render_callback'];

		if ( $native && is_array( $native ) ) {
			$this->do_layers( $native );
		} elseif ( $custom && is_callable( $custom ) ) {
			call_user_func( $custom );
		}
	}

	/**
	 * Do card layers
	 *
	 * @since 1.0.3 Added layer action argument to wpgb_the_post_media().
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $layers Holds card layers.
	 */
	protected function do_layers( $layers ) {

		foreach ( (array) $layers as $layer => $args ) {

			// If layer name does not exist.
			if ( ! isset( $this->layers[ $layer ] ) ) {
				continue;
			}

			// Exception for thumbnail.
			if ( 'media-thumbnail' === $layer ) {

				$layer  = $layers[ $layer ];
				$action = empty( $layer['action'] ) ?: $layer['action'];

				wpgb_the_post_media( $action );
				continue;

			}

			$this->do_layer( $layers, $layer );

		}
	}

	/**
	 * Do card layer
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array  $layers Holds layers.
	 * @param string $layer Layer name.
	 */
	protected function do_layer( $layers, $layer ) {

		$layers = $layers[ $layer ];

		printf(
			'<div class="%s"%s%s>',
			$this->get_layer_classes( $layers, $layer ),
			$this->get_layer_background( $layers, $layer ),
			$this->has_action( $layers ) ? ' data-action' : ''
		);

			$this->do_layers( $layers );
			$this->do_blocks( $layers );
			$this->do_action( $layers );

		echo '</div>';

	}

	/**
	 * Do blocks
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $layer Holds layer properties.
	 */
	protected function do_blocks( $layer ) {

		if ( ! isset( $layer['blocks'] ) ) {
			return;
		}

		foreach ( (array) $layer['blocks'] as $block ) {

			$source   = $this->get_block( $block );
			$function = WPGB_SLUG . '_' . $source;

			// If block has no content.
			if ( empty( $block['content'] ) ) {
				continue;
			}

			// If not a native block.
			if ( empty( $source ) || ! function_exists( $function ) ) {
				$function = 'wpgb_custom_block';
			}

			// Get default block tag if not set.
			if ( empty( $block['content']['tag'] ) && isset( $this->blocks[ $source ] ) ) {
				$block['content']['tag'] = $this->blocks[ $source ];
			}

			$function( $block['content'], $block['action'] );

		}
	}

	/**
	 * Do layer action
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $layer Holds layer properties.
	 */
	protected function do_action( $layer ) {

		if ( ! $this->has_action( $layer ) ) {
			return;
		}

		$link = wpgb_get_block_link( $layer['action'] );

		if ( empty( $link ) ) {
			return;
		}

		// Add layer action class.
		$layer['action']['class'] = 'wpgb-card-layer-link';

		wpgb_block_action( $layer['action'], $link );
		wpgb_block_end( $layer, $layer['action'] );

	}

	/**
	 * Check if layer has action (link only)
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $layer Holds layer properties.
	 * @return boolean
	 */
	protected function has_action( $layer ) {

		if ( isset( $layer['action']['action_type'] ) && 'link' === $layer['action']['action_type'] ) {
			return true;
		}

		return false;

	}

	/**
	 * Get block
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $block Holds block properties.
	 * @return string
	 */
	protected function get_block( $block ) {

		if ( empty( $block['content'] ) ) {
			return '';
		}

		$content = $block['content'];
		$source  = isset( $content['source'] ) ? $content['source'] : 'post_field';
		$source  = isset( $content[ $source ] ) ? $content[ $source ] : $source;

		// If block source does not exist.
		if ( ! isset( $this->blocks[ $source ] ) ) {
			return '';
		}

		return $source;

	}

	/**
	 * Get layer background color
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $layer Layer arguments.
	 * @param string $name  Layer name.
	 * @return string
	 */
	protected function get_layer_background( $layer, $name ) {

		$background = '';

		if ( 'media-overlay' === $name ) {
			$background .= $this->atts['overlay_background'];
		} elseif ( 'header' === $name || 'body' === $name || 'footer' === $name ) {
			$background .= $this->atts['content_background'];
		}

		if ( empty( $background ) ) {
			return '';
		}

		return ' style="background:' . esc_attr( $background ) . '"';

	}

	/**
	 * Get layer classes
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param string $layer Layer arguments.
	 * @param string $name  Layer name.
	 * @return string
	 */
	protected function get_layer_classes( $layer, $name ) {

		$class = 'wpgb-card-' . $name;

		if ( isset( $layer['action']['action_type'] ) && 'open_media' === $layer['action']['action_type'] ) {
			$class .= ' wpgb-card-media-button';
		}

		if ( 'media' === $name ) {
			$class .= ' wpgb-scheme-' . $this->atts['overlay_color_scheme'];
		} elseif ( 'header' === $name || 'content' === $name ) {
			$class .= ' wpgb-scheme-' . $this->atts['content_color_scheme'];
		}

		$class = Helpers::sanitize_html_classes( $class );

		return $class;

	}
}
