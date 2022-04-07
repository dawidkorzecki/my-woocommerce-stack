<?php
/**
 * Post fields
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

$nonce = wp_create_nonce( 'wpgb_fields_save_' . $object_id );
$i18n  = [
	'post' => __( 'Post Settings', 'wp-grid-builder' ),
	'term' => __( 'Term Settings', 'wp-grid-builder' ),
	'user' => __( 'User Settings', 'wp-grid-builder' ),
];

$settings_values = get_metadata( $object_type, $object_id, '_' . WPGB_SLUG, true );

// Include post fields.
require_once WPGB_PATH . 'admin/settings/post.php';

?>
<div class="wpgb-card-settings-holder">
	<div class="wpgb-card-settings">
		<h2>
		<?php
			echo esc_html( ! empty( $i18n[ $object_type ] ) ? $i18n[ $object_type ] : $i18n[0] );
		?>
		</h2>
		<?php wp_grid_builder()->settings->render( 'post', $settings_values ); ?>
		<button type="button" class="wpgb-button wpgb-button-small wpgb-green" data-nonce="<?php echo esc_attr( $nonce ); ?>">
			<?php
				Helpers::get_icon( 'save' );
				esc_html_e( 'Save Changes', 'wp-grid-builder' );
			?>
		</button>
		<button type="button" class="wpgb-button wpgb-card-settings-close" aria-label="<?php esc_attr_e( 'Close', 'wp-grid-builder' ); ?>">
			<?php Helpers::get_icon( 'cross' ); ?>
		</button>
	</div>
</div>
<?php
