<?php
/**
 * Settings header toggle template
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

if ( empty( $this->settings['header']['toggle'] ) ) {
	return;
}

$checked = $this->is_collapsed() ? ' checked' : '';

echo '<span class="wpgb-settings-collapse-tabs" title="' . esc_attr__( 'Collapse menu', 'wp-grid-builder' ) . '">';
	echo '<label for="wpgb-collapse-button"><span class="wpgb-sr-only" >' . esc_html__( 'Collapse menu', 'wp-grid-builder' ) . '</span></label>';
	echo '<input type="checkbox" class="wpgb-settings-collapse" id="wpgb-collapse-button" value="wpgb_settings_collapsed"' . esc_attr( $checked ) . '>';
	Helpers::get_icon( 'left' );
echo '</span>';
