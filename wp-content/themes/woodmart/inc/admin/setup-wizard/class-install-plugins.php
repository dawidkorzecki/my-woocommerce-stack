<?php
/**
 * Install plugins class.
 *
 * @package woodmart
 */

namespace XTS;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Install plugins class
 *
 * @since 1.0.0
 */
class Install_Plugins extends Singleton {
	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ), 10, 1 );
		add_action( 'wp_ajax_woodmart_deactivate_plugin', array( $this, 'ajax_deactivate_plugin' ) );
		add_action( 'wp_ajax_woodmart_check_plugins', array( $this, 'ajax_check_plugin' ) );
	}

	/**
	 * Deactivate plugin.
	 *
	 * @since 1.0.0
	 */
	public function ajax_deactivate_plugin() {
		$plugins = $this->get_plugins();

		if ( ! $plugins ) {
			wp_send_json(
				array(
					'message' => esc_html__( 'Plugins list is empty.', 'woodmart' ),
					'status'  => 'error',
				)
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json(
				array(
					'message' => esc_html__( 'You not have access.', 'woodmart' ),
					'status'  => 'error',
				)
			);
		}

		if ( is_multisite() && is_plugin_active_for_network( $plugins[ $_POST['xts_plugin'] ]['file_path'] ) ) { // phpcs:ignore
			wp_send_json(
				array(
					'message' => esc_html__( 'You cannot deactivate the plugin on a multisite.', 'woodmart' ),
					'status'  => 'error',
				)
			);
		}

		if ( isset( $_POST['xts_plugin'] ) && is_plugin_active( $plugins[ $_POST['xts_plugin'] ]['file_path'] ) ) { // phpcs:ignore
			deactivate_plugins( $plugins[ $_POST['xts_plugin'] ]['file_path'] ); // phpcs:ignore
		}

		wp_send_json(
			array(
				'data'   => $plugins[ $_POST['xts_plugin'] ]['status'], // phpcs:ignore
				'status' => 'success',
			)
		);
	}

	/**
	 * Check plugin.
	 *
	 * @since 1.0.0
	 */
	public function ajax_check_plugin() {
		$plugins = $this->get_plugins();

		if ( ! $plugins ) {
			wp_send_json(
				array(
					'message' => esc_html__( 'Plugins list is empty.', 'woodmart' ),
					'status'  => 'error',
				)
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json(
				array(
					'message' => esc_html__( 'You not have access.', 'woodmart' ),
					'status'  => 'error',
				)
			);
		}

		wp_send_json(
			array(
				'data'   => array(
					'status'           => $plugins[ $_POST['xts_plugin'] ]['status'], // phpcs:ignore
					'version'          => $plugins[ $_POST['xts_plugin'] ]['version'], // phpcs:ignore
					'required_plugins' => count( $this->get_required_plugins_to_activate() ) > 0 ? 'has_required' : 'no',
					'is_all_activated' => $this->is_all_activated() ? 'yes' : 'no',
				),
				'status' => 'success',
			)
		);
	}

	/**
	 * Load TGM.
	 *
	 * @since 1.0.0
	 */
	public function tgmpa_load() {
		return is_admin() || current_user_can( 'install_themes' );
	}

	/**
	 * Get plugins list array.
	 *
	 * @since 1.0.0
	 */
	public function get_plugins() {
		$tgmpa             = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		$tgmpa_plugins     = $tgmpa->plugins;
		$installed_plugins = get_plugins();

		$plugins = array();

		foreach ( $tgmpa_plugins as $slug => $plugin ) {
			$plugins[ $slug ]                   = $plugin;
			$plugins[ $slug ]['activate_url']   = $this->get_action_url( $slug, 'activate' );
			$plugins[ $slug ]['update_url']     = $this->get_action_url( $slug, 'update' );
			$plugins[ $slug ]['deactivate_url'] = '';

			if ( isset( $installed_plugins[ $plugin['file_path'] ]['Version'] ) ) {
				$plugins[ $slug ]['version'] = $installed_plugins[ $plugin['file_path'] ]['Version'];
			}

			if ( ! $tgmpa->is_plugin_installed( $slug ) ) {
				$plugins[ $slug ]['status'] = 'install';
			} else {
				if ( $tgmpa->does_plugin_have_update( $slug ) ) {
					$plugins[ $slug ]['status'] = 'update';
				} elseif ( $tgmpa->can_plugin_activate( $slug ) ) {
					$plugins[ $slug ]['status'] = 'activate';
				} elseif ( $tgmpa->does_plugin_require_update( $slug ) ) {
					$plugins[ $slug ]['status'] = 'require_update';
				} else {
					$plugins[ $slug ]['status'] = 'deactivate';
				}
			}
		}

		$builder = 'elementor';

		if ( isset( $_GET['wd_builder'] ) ) { // phpcs:ignore
			$builder = wp_unslash( $_GET['wd_builder'] ); // phpcs:ignore
		}

		if ( isset( $_POST['xts_builder'] ) ) { // phpcs:ignore
			$builder = wp_unslash( $_POST['xts_builder'] ); // phpcs:ignore
		}

		$order = array(
			'elementor',
			'js_composer',
			'woocommerce',
			'revslider',
			'woodmart-core',
			'contact-form-7',
			'mailchimp-for-wp',
			'safe-svg',
		);

		$plugins = array_replace( array_flip( $order ), $plugins );

		if ( 'elementor' === $builder ) {
			unset( $plugins['js_composer'] );
		} else {
			unset( $plugins['elementor'] );
		}

		return $plugins;
	}

	/**
	 * Get required plugins to activate.
	 *
	 * @since 1.0.0
	 */
	public function get_required_plugins_to_activate() {
		$plugins = $this->get_plugins();
		$tgmpa   = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		$output  = array();

		foreach ( $plugins as $slug => $plugin ) {
			if ( ! $tgmpa->is_plugin_active( $slug ) && $plugin['required'] ) {
				$output[] = $plugin;
			}
		}

		return $output;
	}

	/**
	 * Is all plugins activated.
	 *
	 * @since 1.0.0
	 */
	public function is_all_activated() {
		$plugins = $this->get_plugins();
		$tgmpa   = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		$output  = array();

		foreach ( $plugins as $slug => $plugin ) {
			if ( ! $tgmpa->is_plugin_active( $slug ) && $tgmpa->can_plugin_activate( $slug ) ) {
				$output[] = $plugin;
			}
		}

		return count( $output ) === 0;
	}

	/**
	 * Get required plugins to activate.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug   Slug.
	 * @param string $status Status.
	 *
	 * @return string
	 */
	public function get_action_url( $slug, $status ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'plugin'           => rawurlencode( $slug ),
					'tgmpa-' . $status => $status . '-plugin',
				),
				admin_url( 'themes.php?page=tgmpa-install-plugins' )
			),
			'tgmpa-' . $status,
			'tgmpa-nonce'
		);
	}

	/**
	 * Get required plugins to activate.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Status.
	 *
	 * @return string
	 */
	public function get_action_text( $status ) {
		$text = esc_html__( 'Deactivate', 'woodmart' );

		if ( 'install' === $status ) {
			$text = esc_html__( 'Install', 'woodmart' );
		} elseif ( 'update' === $status ) {
			$text = esc_html__( 'Update', 'woodmart' );
		} elseif ( 'activate' === $status ) {
			$text = esc_html__( 'Activate', 'woodmart' );
		}

		return $text;
	}
}

Install_Plugins::get_instance();
