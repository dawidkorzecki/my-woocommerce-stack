<?php
/**
 * Table list footer
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

use WP_Grid_Builder\Includes\Paginate;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current = max( 1, $this->query_args['paged'] );
$limit   = max( 5, $this->query_args['limit'] );
$total   = ceil( $this->found / $limit );

?>
<div class="wpgb-list-table-footer">
	<?php

	if ( $this->found > 0 ) {

		?>
		<label class="wpgb-sr-only" for="wpgb-per-page"><?php esc_html_e( 'Number of items per page', 'wp-grid-builder' ); ?></label>
		<select id="wpgb-per-page" class="wpgb-select wpgb-list-table-per-page">
		<?php

		foreach ( [ 5, 10, 25, 50 ] as $val ) {

			?>
			<option <?php echo selected( $val, $limit, false ); ?> value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $val ); ?></option>
			<?php

		}

		?>
		</select>
		<?php

	}

	new Paginate(
		[
			'current'   => $current,
			'total'     => $total,
			'show_all'  => false,
			'prev_next' => true,
			'prev_text' => __( 'Prev', 'wp-grid-builder' ),
			'next_text' => __( 'Next', 'wp-grid-builder' ),
			'end_size'  => 1,
			'mid_size'  => 2,
			'classes'   => [
				'page'    => 'wpgb-list-table-page',
				'dots'    => 'wpgb-list-table-dots-page',
				'current' => 'wpgb-list-table-current-page',
				'holder'  => 'wpgb-list-table-pagination',
			],
		]
	);

	?>
</div>
<?php

