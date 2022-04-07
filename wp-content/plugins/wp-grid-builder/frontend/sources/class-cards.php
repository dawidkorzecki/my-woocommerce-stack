<?php
/**
 * Query Custom cards
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

namespace WP_Grid_Builder\FrontEnd\Sources;

use WP_Grid_Builder\Includes\Helpers;
use WP_Grid_Builder\Includes\Database;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display cards
 *
 * @class WP_Grid_Builder\FrontEnd\Source\Cards;
 * @since 1.0.0
 */
class Cards {

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
	 * Holds queried posts
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $posts = [];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds grid settings.
	 */
	public function __construct( $settings ) {

		add_filter( 'wp_grid_builder/card/attributes', [ $this, 'card_attributes' ], 10, 2 );
		add_action( 'wp_grid_builder/card/wrapper_start', [ $this, 'card_header' ] );

		$this->settings = $settings;

	}

	/**
	 * Parse cards database query
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function parse_query() {

		$this->cards = Database::query_results(
			[
				'select'  => 'id, name, type, favorite, date, modified_date',
				'from'    => 'cards',
				'limit'   => ! empty( $this->settings->limit ) ? $this->settings->limit : $this->settings->posts_per_page,
				'paged'   => ! empty( $this->settings->paged ) ? $this->settings->paged : 1,
				'orderby' => $this->settings->orderby . ' ' . $this->settings->order,
				's'       => ! empty( $this->settings->s ) ? $this->settings->s : '',
			]
		);

	}

	/**
	 * Retrieves cards based on query variables
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array Queried objects
	 */
	public function get_results() {

		$objects = [];

		foreach ( $this->cards as $card ) {

			$this->settings->cards[ $card['id'] ] = $card['id'];

			$object = $this->default_post();
			$object['card_data'] = $card;
			$object['metadata']  = [
				'card' => $card['id'],
			];

			$objects[] = (object) $object;

		}

		return $objects;

	}

	/**
	 * Add card attributes
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $atts Holds card attributes.
	 * @param array $card Holds card settings.
	 */
	public function card_attributes( $atts, $card ) {

		if (
			isset( $card['settings']['type'] ) && 'masonry' === $card['settings']['type'] &&
			isset( $card['settings']['card_layout'] ) && 'horizontal' === $card['settings']['card_layout']
		) {
				$atts['columns'] = 2;
		}

		return $atts;

	}

	/**
	 * Add card header
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function card_header() {

		if ( ! is_admin() ) {
			return;
		}

		if ( empty( $this->settings->is_selector ) ) {
			$this->card_overview_header();
		} else {
			$this->card_selector_header();
		}

	}

	/**
	 * Add overview header
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function card_overview_header() {

		$post = wpgb_get_post();
		$card = $post->card_data['id'];
		$link = $this->get_edit_link( $post );
		$type = $post->card_data['favorite'] ? 'fill' : 'empty';

		if ( 'fill' === $type ) {
			$title = __( 'Remove from favorites', 'wp-grid-builder' );
		} else {
			$title = __( 'Add to favorites', 'wp-grid-builder' );
		}

		$nonce_action    = 'wpgb_actions_cards_%s_' . $post->card_data['id'];
		$duplicate_nonce = wp_create_nonce( sprintf( $nonce_action, 'duplicate' ) );
		$favorite_nonce  = wp_create_nonce( sprintf( $nonce_action, 'favorite' ) );
		$delete_nonce    = wp_create_nonce( sprintf( $nonce_action, 'delete' ) );

		?>
		<div class="wpgb-admin-card-header" data-id="<?php echo sanitize_html_class( $card ); ?>">

			<input type="checkbox" id="wpgb-<?php echo sanitize_html_class( $card ); ?>" class="wpgb-input wpgb-select-item wpgb-sr-only" name="cards[]" value="<?php echo esc_attr( $card ); ?>">
			<label for="wpgb-<?php echo sanitize_html_class( $card ); ?>">
				<?php Helpers::get_icon( 'check' ); ?>
			</label>

			<?php
			if ( $this->is_new( $post ) ) {
				?>
				<span><?php echo esc_html__( 'New!', 'wp-grid-builder' ); ?></span>&nbsp;
				<?php
			}
			?>

			<a href="<?php echo esc_url( $link ); ?>" title="<?php echo esc_attr__( 'Edit Card', 'wp-grid-builder' ); ?>">
				<?php
				echo esc_html( ucfirst( $post->card_data['name'] ) );
				?>
			</a>

			<div class="wpgb-admin-card-actions">
				<button type="button" class="<?php echo sanitize_html_class( 'wpgb-star-' . $type ); ?>" data-action="favorite" data-nonce="<?php echo esc_attr( $favorite_nonce ); ?>"  aria-label="<?php echo esc_attr( $title ); ?>" data-tooltip>
					<?php Helpers::get_icon( 'star' ); ?>
				</button>
				<button type="button" data-action="export" aria-label="<?php echo esc_attr__( 'Export', 'wp-grid-builder' ); ?>" data-tooltip>
					<?php Helpers::get_icon( 'export' ); ?>
				</button>
				<button type="button" data-action="delete" data-nonce="<?php echo esc_attr( $delete_nonce ); ?>" aria-label="<?php echo esc_attr__( 'Delete', 'wp-grid-builder' ); ?>" data-tooltip>
					<?php Helpers::get_icon( 'delete' ); ?>
				</button>
				<button type="button" data-action="duplicate" data-nonce="<?php echo esc_attr( $duplicate_nonce ); ?>" aria-label="<?php echo esc_attr__( 'Duplicate', 'wp-grid-builder' ); ?>"  data-tooltip>
					<?php Helpers::get_icon( 'duplicate' ); ?>
				</button>
				<a href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr__( 'Edit', 'wp-grid-builder' ); ?>" data-tooltip>
					<?php Helpers::get_icon( 'settings' ); ?>
				</a>
			</div>

		</div>
		<?php

	}

	/**
	 * Add selector header
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function card_selector_header() {

		$post = wpgb_get_post();
		$card = $post->card_data['id'];
		$link = $this->get_edit_link( $post );

		?>
		<div class="wpgb-admin-card-header" data-id="<?php echo sanitize_html_class( $card ); ?>">

			<div class="wpgb-admin-card-actions">
				<a href="<?php echo esc_url( $link ); ?>" target="_blank" aria-label="<?php echo esc_attr__( 'Edit', 'wp-grid-builder' ); ?>" data-tooltip>
					<?php Helpers::get_icon( 'settings' ); ?>
				</a>
			</div>

			<span class="wpgb-card-name"><?php echo esc_html( ucfirst( $post->card_data['name'] ) ); ?></span>

			<button type="button" class="wpgb-button wpgb-button-small wpgb-green" data-action="select">
				<?php echo esc_html__( 'Select Card', 'wp-grid-builder' ); ?>
			</button>

		</div>
		<?php

	}

	/**
	 * Get item link
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $post Holds post object.
	 */
	public function get_edit_link( $post ) {

		return add_query_arg(
			[
				'page' => WPGB_SLUG . '-card-builder',
				'id'   => $post->card_data['id'],
			],
			admin_url( 'admin.php' )
		);

	}

	/**
	 * Check if new item
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $post Holds post object.
	 */
	public function is_new( $post ) {

		$p_time = mysql2date( 'U', $post->card_data['date'], false );
		$d_time = current_time( 'U' ) - $p_time; // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

		if ( $d_time >= 0 && $d_time < 10 * 60 ) {
			return true;
		}

		return false;

	}

	/**
	 * Build default post array
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function default_post() {

		$content = [
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent dignissim massa nulla, sed accumsan libero imperdiet vitae.',
			'Phasellus quis tincidunt ipsum. Vestibulum blandit massa id nisi rhoncus, eu volutpat sem bibendum. Curabitur ac justo elit. Aliquam nunc massa, accumsan vitae dui id, vestibulum viverra elit.',
			'Donec tincidunt purus lorem, at rhoncus odio venenatis vel. Fusce quis nunc vel libero vulputate ultrices ut ac quam. Sed euismod nibh sit amet neque vulputate efficitur. Integer congue imperdiet mollis.',
		];

		return [
			// Default post fields.
			'ID'              => 0,
			'permalink'       => null,
			'comment_count'   => 0,
			'post_type'       => 'post',
			'post_sticky'     => false,
			'post_status'     => 'publish',
			'post_format'     => 'standard',
			'post_date'       => current_time( 'U' ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			'post_modified'   => current_time( 'U' ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			'post_title'      => __( 'The post title', 'wp-grid-builder' ),
			'post_name'       => __( 'The post name', 'wp-grid-builder' ),
			'post_content'    => implode( "\n", $content ),
			'post_excerpt'    => implode( ' ', $content ),
			'post_terms'      => [
				[
					'term_id'  => 0,
					'name'     => __( 'Term 1', 'wp-grid-builder' ),
					'taxonomy' => 'category',
				],
				[
					'term_id'  => 0,
					'name'     => __( 'Term 2', 'wp-grid-builder' ),
					'taxonomy' => 'category',
				],
				[
					'term_id'  => 0,
					'name'     => __( 'Term 3', 'wp-grid-builder' ),
					'taxonomy' => 'category',
				],
			],
			'post_author'     => [
				'ID'           => 0,
				'display_name' => __( 'Author Name', 'wp-grid-builder' ),
				'posts_url'    => null,
				'avatar'       => [
					'url'    => WPGB_URL . 'admin/assets/svg/avatar.svg',
					'height' => 96,
					'width'  => 96,
					'alt'    => 'avatar',
				],
			],
			'post_thumbnail'  => [
				'title'       => WPGB_NAME,
				'caption'     => WPGB_NAME,
				'description' => WPGB_NAME,
				'mime_type'   => 'image/svg+xml',
				'alt'         => WPGB_NAME,
				'sizes'       => [
					'thumbnail' => [
						'url'    => WPGB_URL . 'admin/assets/svg/placeholder.svg',
						'width'  => 640,
						'height' => 540,
					],
				],
			],
			// Product download fields.
			'product'         => (object) [
				'product_type'       => 'simple',
				'featured'           => true,
				'catalog_visibility' => true,
				'sku'                => '123456789',
				'is_purchasable'     => true,
				'add_to_cart_url'    => '#',
				'add_to_cart_text'   => __( 'Add to cart', 'wp-grid-builder' ),
				'ajax_add_to_cart'   => true,
				'price'              => '$99',
				'regular_price'      => '$179',
				'sale_price'         => '$99',
				'total_sales'        => 100,
				'on_sale'            => true,
				'date_on_sale_from'  => null,
				'date_on_sale_to'    => null,
				'stock_quantity'     => 500,
				'stock_status'       => 'in_stock',
				'in_stock'           => true,
				'downloads'          => false,
				'download_expiry'    => null,
				'downloadable'       => null,
				'download_limit'     => null,
				'rating_counts'      => 5,
				'average_rating'     => 4.5,
				'review_count'       => 10,
			],
			// User fields.
			'display_name'    => __( 'Display Name', 'wp-grid-builder' ),
			'first_name'      => __( 'First Name', 'wp-grid-builder' ),
			'last_name'       => __( 'Last Name', 'wp-grid-builder' ),
			'nickname'        => __( 'Nickname', 'wp-grid-builder' ),
			'user_login'      => __( 'Username', 'wp-grid-builder' ),
			'user_roles'      => __( 'author', 'wp-grid-builder' ),
			'user_email'      => 'user@email.com',
			'user_url'        => 'website.com',
			'user_locale'     => 'en_US',
			'user_post_count' => 10,
			'user_caps'       => [],
			// Term fields.
			'term_name'       => __( 'Term Name', 'wp-grid-builder' ),
			'term_slug'       => __( 'Term Slug', 'wp-grid-builder' ),
			'term_taxonomy'   => __( 'Term Taxonomy', 'wp-grid-builder' ),
			'term_parent'     => __( 'Term Parent', 'wp-grid-builder' ),
			'term_count'      => 10,
		];

	}
}
