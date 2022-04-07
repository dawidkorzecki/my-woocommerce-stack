<?php
/**
 * Check WP and PHP compatibility
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2022 Loïc Blascos
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Compatibility class
 *
 * @class WPGB_Compatibility
 * @since 1.0.0
 */
class WPGB_Compatibility {

	/**
	 * Minium required WordPress version
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $wp_min;

	/**
	 * Minium required PHP version
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $php_min;

	/**
	 * Plugin name
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $plugin;

	/**
	 * Plugin basename
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $basename;

	/**
	 * Holds notice messages
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $wp_min   Minium required WordPress version.
	 * @param string $php_min  Minium required PHP version.
	 * @param string $plugin   Plugin name.
	 * @param string $basename Plugin basename.
	 */
	public function __construct( $wp_min = '4.7', $php_min = '5.2.4', $plugin = 'Plugin', $basename = '' ) {

		$this->wp_min   = $wp_min;
		$this->php_min  = $php_min;
		$this->plugin   = $plugin;
		$this->basename = $basename;

	}

	/**
	 * Check compatibility against PHP and WP
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function check() {

		$this->compare_versions();

		if ( empty( $this->notices ) ) {
			return true;
		}

		add_action( 'admin_init', array( $this, 'add_notices' ) );

		return false;

	}

	/**
	 * Compare required PHP and WP versions.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function compare_versions() {

		global $wp_version;

		if ( version_compare( $wp_version, $this->wp_min, '<' ) ) {

			$this->notices[] = sprintf(
				/* translators: %1$s: Plugin name, %2$s: Minium required WordPress version */
				__( "<strong>%1\$s</strong> requires at least <code>WordPress %2\$s</code>.\nPlease update WordPress to activate the plugin.", 'wp-grid-builder' ),
				$this->plugin,
				$this->wp_min
			);

		}

		if ( version_compare( PHP_VERSION, $this->php_min, '<' ) ) {

			$this->notices[] = sprintf(
				/* translators: %1$s: Plugin name, %2$s: Minium required PHP version */
				__( "<strong>%1\$s</strong> requires at least <code>PHP %2\$s</code>.\nPlease contact your hosting provider to upgrade your PHP version.", 'wp-grid-builder' ),
				$this->plugin,
				$this->php_min
			);

		}

	}

	/**
	 * Add admin notices.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_notices() {

		// To take into account translations after localization.
		$this->notices = array();
		$this->compare_versions();

		if ( ! empty( $this->basename ) ) {

			// Suppress "Plugin activated" notice.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			unset( $_GET['activate'] );
			deactivate_plugins( $this->basename );

		}

		add_action( 'admin_notices', array( $this, 'display_notices' ) );

	}

	/**
	 * Display admin notices.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function display_notices() {

		array_map(
			function( $notice ) {
				echo '<div class="error">' . wp_kses_post( wpautop( $notice ) ) . '</div>';
			},
			$this->notices
		);

	}

}

$compatibility = new WPGB_Compatibility( WPGB_MIN_WP, WPGB_MIN_PHP, WPGB_NAME, WPGB_BASE );
