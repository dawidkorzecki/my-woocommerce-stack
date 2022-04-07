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

require_once WPGB_PATH . 'admin/settings/facet.php';
wp_grid_builder()->settings->render( 'facet', $facet_values );
