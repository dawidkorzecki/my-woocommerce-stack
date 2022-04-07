<?php
/**
 * Grid settings page
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WPGB_PATH . 'admin/settings/grid.php';
wp_grid_builder()->settings->render( 'grid', $grid_values );
require_once WPGB_PATH . 'admin/views/modules/grid-preview.php';
