<?php
/**
 * Class DuplicateScript
 *
 * @package WPDesk\FS\TableRate\ShippingMethod\Duplicate
 */

namespace WPDesk\FS\TableRate\ShippingMethod\Duplicate;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Duplicate Script.
 */
class DuplicateScript implements Hookable {

	/**
	 * @var string
	 */
	private $assets_url;

	/**
	 * @var string
	 */
	private $scripts_version;

	/**
	 * DuplicateScript constructor.
	 *
	 * @param string $assets_url      .
	 * @param string $scripts_version .
	 */
	public function __construct( string $assets_url, string $scripts_version ) {
		$this->assets_url      = $assets_url;
		$this->scripts_version = $scripts_version;
	}

	/**
	 * Init hooks (actions and filters).
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'woocommerce_shipping_zone_after_methods_table', [ $this, 'add_duplicate_scripts' ] );
	}

	/**
	 * .
	 */
	public function add_duplicate_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$handle = 'fs_duplicate_methods';

		wp_enqueue_script(
			$handle,
			trailingslashit( $this->assets_url ) . 'js/duplicate-methods' . $suffix . '.js',
			[ 'jquery', 'wc-shipping-zone-methods' ],
			$this->scripts_version
		);

		wp_localize_script(
			$handle,
			'fs_duplicate_methods',
			[
				'param_name'      => DuplicateAction::PARAM_ID,
				'shipping_method' => __( 'Flexible Shipping', 'flexible-shipping' ),
				'duplicate_label' => __( 'Duplicate', 'flexible-shipping' ),
				'duplicate_url'   => wp_nonce_url( add_query_arg( 'action', DuplicateAction::ACTION, admin_url( 'admin-post.php' ) ), DuplicateAction::ACTION ),
			]
		);
	}
}
