<?php
/**
 * Table source
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wpgb-list-table-column" data-colname="source">
	<?php echo esc_html( $this->item['source'] ); ?>
</div>
<?php
