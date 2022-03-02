<?php
/**
 * Install child theme class.
 *
 * @package woodmart
 */

namespace XTS;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Install child theme class.
 */
class Install_Child_Theme extends Singleton {
	/**
	 * Constructor.
	 */
	public function init() {
		add_action( 'wp_ajax_woodmart_install_child_theme', array( $this, 'install_child_theme' ) );
		add_action( 'wp_ajax_nopriv_woodmart_install_child_theme', array( $this, 'install_child_theme' ) );
	}

	/**
	 * Install child theme.
	 */
	public function install_child_theme() {
		$parent_theme_name = 'woodmart';
		$child_theme_name  = $parent_theme_name . '-child';
		$theme_root        = get_theme_root();
		$child_theme_path  = $theme_root . '/' . $child_theme_name;

		if ( ! file_exists( $child_theme_path ) ) {
			$dir = wp_mkdir_p( $child_theme_path );

			if ( ! $dir ) {
				echo wp_json_encode( array( 'status' => 'dir_not_exists' ) );
				die();
			}

			$child_theme_resource_folder = get_parent_theme_file_path( 'inc/' . $child_theme_name );

			copy( $child_theme_resource_folder . '/functions.php', $child_theme_path . '/functions.php' );
			copy( $child_theme_resource_folder . '/screenshot.png', $child_theme_path . '/screenshot.png' );
			copy( $child_theme_resource_folder . '/style.css', $child_theme_path . '/style.css' );

			$allowed_themes                      = get_site_option( 'allowedthemes' );
			$allowed_themes[ $child_theme_name ] = true;
			update_site_option( 'allowedthemes', $allowed_themes );
		}

		if ( $parent_theme_name !== $child_theme_name ) {
			switch_theme( $child_theme_name );
			echo wp_json_encode( array( 'status' => 'success' ) );
			die();
		}
	}
}

Install_Child_Theme::get_instance();
