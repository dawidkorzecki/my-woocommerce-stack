<?php
/**
 * Class BlockEditing
 *
 * @package WPDesk\FS\TableRate\ShippingMethod\BlockEditing
 */

namespace WPDesk\FS\TableRate\ShippingMethod\BlockEditing;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Block edit settings of FS Group.
 */
class BlockEditing implements Hookable {
	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'flexible_shipping_method_script', array( $this, 'block_shipping_method_script' ), 10, 2 );
	}

	/**
	 * @param string $method_id   .
	 * @param int    $instance_id .
	 */
	public function block_shipping_method_script( $method_id, $instance_id ) {
		if ( 'flexible_shipping' !== $method_id ) {
			return;
		}

		if ( apply_filters( 'flexible-shipping/group-method/supports/edit', false ) === true ) {
			return;
		}

		include __DIR__ . '/views/block-settings.php';
	}
}
