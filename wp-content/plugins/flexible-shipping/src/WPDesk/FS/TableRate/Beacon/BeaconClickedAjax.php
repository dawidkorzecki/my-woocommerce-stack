<?php
/**
 * Class BeaconAjax
 *
 * @package WPDesk\FS\TableRate
 */

namespace WPDesk\FS\TableRate\Beacon;

use FSVendor\WPDesk\Beacon\BeaconGetShouldShowStrategy;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can register Beacon click vi AJAX.
 */
class BeaconClickedAjax implements Hookable {

	const OPTION_NAME  = 'flexible_shipping_beacon_clicked';
	const NONCE_ACTION = 'flexible_shipping_beacon_clicked';
	const AJAX_ACTION  = 'flexible_shipping_beacon_clicked';

	/**
	 * @var BeaconDisplayStrategy
	 */
	private $strategy;

	/**
	 * @var string
	 */
	private $assets_url;

	/**
	 * @var string
	 */
	private $scripts_version;

	/**
	 * BeaconAjax constructor.
	 *
	 * @param BeaconDisplayStrategy $strategy .
	 * @param string                $assets_url .
	 * @param string                $scripts_version .
	 */
	public function __construct( BeaconDisplayStrategy $strategy, $assets_url, $scripts_version ) {
		$this->strategy = $strategy;
		$this->assets_url = $assets_url;
		$this->scripts_version = $scripts_version;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		if ( 0 === (int) get_option( self::OPTION_NAME, 0 ) ) {
			if ( $this->strategy->shouldDisplay() ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'add_script' ) );
			}
			add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'handle_ajax_action' ) );
		}
	}

	/**
	 * @internal
	 */
	public function add_script() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$handle = 'fs_beacon_clicked';
		wp_register_script(
			$handle,
			trailingslashit( $this->assets_url ) . 'js/beacon-clicked' . $suffix . '.js',
			array( 'jquery' ),
			$this->scripts_version
		);
		wp_localize_script(
			$handle,
			'fs_beacon_clicked',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => self::AJAX_ACTION,
				'nonce'    => wp_create_nonce( self::NONCE_ACTION ),
			)
		);
		wp_enqueue_script( $handle );
	}

	/**
	 * Handle AJAX action.
	 *
	 * @internal
	 */
	public function handle_ajax_action() {
		check_ajax_referer( self::AJAX_ACTION, 'nonce' );
		update_option( self::OPTION_NAME, 1 );
	}

}
