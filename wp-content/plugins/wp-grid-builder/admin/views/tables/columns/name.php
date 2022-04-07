<?php
/**
 * Table name
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edit_link = $this->get_edit_link();

?>
<div class="wpgb-list-table-column" data-colname="name">
	<a href="<?php echo esc_url( $edit_link ); ?>" title="<?php esc_attr_e( 'Edit Item', 'wp-grid-builder' ); ?>">
	<?php
	if ( $this->is_new() ) {
		?>
		<span><?php echo esc_html__( 'New!', 'wp-grid-builder' ); ?></span>&nbsp;
		<?php
	}
	echo esc_html( $this->item['name'] );
	?>
	</a>
</div>
<?php
