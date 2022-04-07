<?php
/**
 * Table header
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

$defaults = [
	'select'        => null,
	'favorite'      => __( 'Favorite', 'wp-grid-builder' ),
	'type'          => __( 'Type', 'wp-grid-builder' ),
	'name'          => __( 'Name', 'wp-grid-builder' ),
	'source'        => __( 'Source Type', 'wp-grid-builder' ),
	'shortcode'     => __( 'Shortcode', 'wp-grid-builder' ),
	'modified_date' => __( 'Modified At', 'wp-grid-builder' ),
	'actions'       => __( 'Actions', 'wp-grid-builder' ),
];

if ( 'grids' === $this->table ) {
	$defaults['source'] = __( 'Content Type', 'wp-grid-builder' );
} elseif ( 'facets' === $this->table ) {
	$defaults['source'] = __( 'Data Source', 'wp-grid-builder' );
}

?>
<div class="wpgb-list-table-counter">
	<?php
	$plural = max( 1, $this->found );
	/* translators: %d: number of items */
	echo esc_html( sprintf( _n( '%d item', '%d items', $plural, 'wp-grid-builder' ), (int) $this->found ) );
	?>
</div>

<div class="wpgb-list-table-header" data-type="<?php echo esc_attr( $this->table ); ?>">
	<div class="wpgb-list-table-row">
	<?php

	foreach ( $columns as $column ) {

		?>
		<div class="wpgb-list-table-column" data-colname="<?php echo esc_attr( $column ); ?>">
			<?php

			if ( 'select' === $column ) {

				?>
				<input type="checkbox" id="wpgb-bulk-select" class="wpgb-input wpgb-select-item wpgb-sr-only">
				<label for="wpgb-bulk-select">
					<span><?php esc_html_e( 'Select All', 'wp-grid-builder' ); ?></span>
					<?php Helpers::get_icon( 'check' ); ?>
				</label>
				<?php

			}

			if ( ! empty( $defaults[ $column ] ) ) {

				?>
				<span><?php echo esc_html( $defaults[ $column ] ); ?></span>
				<?php

			}

			if ( 'select' !== $column && 'actions' !== $column ) {

				$asc = null;
				$desc = null;
				$column = 'shortcode' === $column ? 'id' : $column;

				if ( in_array( $column, $this->query_args, true ) ) {

					if ( 'ASC' === $this->query_args['order'] ) {
						$asc  = 'wpgb-sorting-active';
					} else {
						$desc = 'wpgb-sorting-active';
					}
				}

				?>
				<div class="wpgb-list-table-indicators">
					<span class="<?php echo sanitize_html_class( $asc ); ?>" data-orderby="<?php echo esc_attr( $column ); ?>" data-order="ASC"></span>
					<span class="<?php echo sanitize_html_class( $desc ); ?>" data-orderby="<?php echo esc_attr( $column ); ?>" data-order="DESC"></span>
				</div>
				<?php

			}
			?>

		</div>
		<?php

	}

	?>
	</div>
</div>
<?php
