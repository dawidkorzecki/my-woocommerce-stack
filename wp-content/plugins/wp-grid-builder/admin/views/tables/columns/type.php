<?php
/**
 * Table type
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
<div class="wpgb-list-table-column" data-colname="type">
	<span role="tooltip" aria-label="<?php echo esc_attr( $this->item['type'] ); ?>" data-tooltip>
		<svg><use xlink:href="<?php echo esc_url( $this->item['icon'] ); ?>"></use></svg>
	</span>
</div>
<?php
