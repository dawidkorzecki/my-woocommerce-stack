<?php
/**
 * Import list
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nonce    = wp_create_nonce( WPGB_SLUG . '_import_content' );
$singular = esc_html__( 'item', 'wp-grid-builder' );
$plural   = esc_html__( 'items', 'wp-grid-builder' );
$count    = array_reduce(
	$list,
	function( $carry, $items ) {
		return $carry + count( $items );
	}
);

?>
<div class="wpgb-import-list">

	<div class="wpgb-import-list-controls">

		<div class="wpgb-search-field wpgb-import-list-search">
			<?php Helpers::get_icon( 'search' ); ?>
			<input type="search" class="wpgb-input" placeholder="<?php esc_attr_e( 'Search content', 'wp-grid-builder' ); ?>" aria-label="<?php esc_attr_e( 'Search content', 'wp-grid-builder' ); ?>">
		</div>

		<input type="checkbox" id="wpgb-bulk-select" class="wpgb-input wpgb-select-item wpgb-sr-only">
		<label for="wpgb-bulk-select">
			<?php Helpers::get_icon( 'check' ); ?>
			<?php esc_html_e( 'Select All', 'wp-grid-builder' ); ?>
		</label>

		<span class="wpgb-import-list-number" data-singular="<?php echo esc_attr( $singular ); ?>" data-plural="<?php echo esc_attr( $plural ); ?>">
			<?php
			/* translators: %d: number of items */
			echo esc_html( sprintf( _n( '%d item', '%d items', $count, 'wp-grid-builder' ), (int) $count ) );
			?>
		</span>

	</div>

	<ul class="wpgb-list wpgb-list-flex">
		<?php

		array_map(
			function( $demo ) {
				foreach ( $demo as $index => $item ) {

					?>
					<li class="wpgb-list-item">
						<input type="checkbox" id="wpgb-<?php echo esc_attr( $item['type'] . '-' . $index ); ?>" class="wpgb-input wpgb-select-item wpgb-sr-only" name="<?php echo esc_attr( $item['type'] ); ?>[]" value="<?php echo esc_attr( $index ); ?>">
						<label for="wpgb-<?php echo esc_attr( $item['type'] . '-' . $index ); ?>">
						<?php Helpers::get_icon( 'check' ); ?>
						<svg><use xlink:href="<?php echo esc_url( $item['icon'] ); ?>"></use></svg>
						<span><?php echo esc_html( $item['name'] ); ?></span>
						</label>
					</li>
					<?php

				}
			},
			$list
		);

		// Hack to have equal width items and to support IE without CSS grid auto-fill.
		for ( $i = 1; $i <= 10; $i++ ) {

			?>
			<li class="wpgb-list-item" hidden></li>
			<?php

		}

		?>

		<li class="wpgb-list-no-result"><h4><?php esc_html_e( 'Sorry, no item was found!', 'wp-grid-builder' ); ?></h4></li>
	</ul>

	<button type="button" class="wpgb-button wpgb-button-icon wpgb-import-items" data-demo="<?php echo esc_attr( isset( $demo ) ? $demo : '' ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>">
		<?php Helpers::get_icon( 'import' ); ?>
		<?php esc_html_e( 'Import Data', 'wp-grid-builder' ); ?>
	</button>

</div>
<?php
