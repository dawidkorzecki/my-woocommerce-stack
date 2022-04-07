<?php
/**
 * Table modified date
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$m_date = $this->get_modified_date();

?>
<div class="wpgb-list-table-column" data-colname="modified_date">
	<abbr role="tooltip" aria-label="<?php echo esc_attr( $m_date['m_time'] ); ?>" data-tooltip><?php echo esc_html( $m_date['h_time'] ); ?></abbr>
</div>
<?php
