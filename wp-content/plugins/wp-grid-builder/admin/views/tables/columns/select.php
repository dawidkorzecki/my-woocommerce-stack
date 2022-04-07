<?php
/**
 * Table select field
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

?>
<div class="wpgb-list-table-column" data-colname="select">
	<input type="checkbox" id="<?php echo esc_attr( 'wpgb-' . $this->item['id'] ); ?>" class="wpgb-input wpgb-select-item wpgb-sr-only" name="<?php echo esc_attr( $this->table ); ?>[]" value="<?php echo esc_attr( $this->item['id'] ); ?>">
	<label for="<?php echo esc_attr( 'wpgb-' . $this->item['id'] ); ?>">
		<span>
			<?php
			printf(
				/* translators: %s: $name Grid name */
				esc_html__( 'Select %s', 'wp-grid-builder' ),
				esc_html( $this->item['name'] )
			);
			?>
		</span>
		<?php Helpers::get_icon( 'check' ); ?>
	</label>
</div>
<?php
