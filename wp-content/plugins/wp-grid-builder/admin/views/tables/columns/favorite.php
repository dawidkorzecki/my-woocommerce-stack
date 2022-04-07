<?php
/**
 * Table favorite button
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

$token = 'wpgb_actions_' . $this->table . '_favorite_' . $this->item['id'];
$nonce = wp_create_nonce( $token );
$state = $this->item['favorite'] ? 'fill' : 'empty';

if ( 'fill' === $state ) {
	$label = __( 'Remove from favorites', 'wp-grid-builder' );
} else {
	$label = __( 'Add to favorites', 'wp-grid-builder' );
}

?>
<div class="wpgb-list-table-column" data-colname="favorite">
	<button type="button" class="<?php echo sanitize_html_class( 'wpgb-star-' . $state ); ?>" data-action="favorite" data-nonce="<?php echo esc_attr( $nonce ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" data-tooltip>
		<?php Helpers::get_icon( 'star' ); ?>
	</button>
</div>
<?php
