<?php
/**
 * Table shortcode
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
<div class="wpgb-list-table-column" data-colname="shortcode">
	<span class="wpgb-copy-to-clipboard" aria-label="<?php esc_attr_e( 'Copy to clipboard', 'wp-grid-builder' ); ?>">
		<?php echo esc_html( $this->item['shortcode'] ); ?>
	</span>
	<?php Helpers::get_icon( 'clipboard' ); ?>
</div>
<?php
