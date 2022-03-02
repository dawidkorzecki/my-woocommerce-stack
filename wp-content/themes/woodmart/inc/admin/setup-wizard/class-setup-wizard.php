<?php
/**
 * Setup wizard class.
 *
 * @package woodmart
 */

namespace XTS;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Setup wizard class.
 */
class Setup_Wizard extends Singleton {
	/**
	 * Available pages.
	 *
	 * @var array
	 */
	public $available_pages = array();

	/**
	 * Constructor.
	 */
	public function init() {
		$this->available_pages = array(
			'welcome'       => esc_html__( 'Welcome', 'woodmart' ),
			'activation'    => esc_html__( 'Activation', 'woodmart' ),
			'child-theme'   => esc_html__( 'Child theme', 'woodmart' ),
			'page-builder'  => esc_html__( 'Page builder', 'woodmart' ),
			'plugins'       => esc_html__( 'Plugins', 'woodmart' ),
			'dummy-content' => esc_html__( 'Dummy content', 'woodmart' ),
			'done'          => esc_html__( 'Done', 'woodmart' ),
		);

		if ( isset( $_GET['skip_setup'] ) ) {
			update_option( 'woodmart_setup_status', 'done' );
        }

		if ( 'done' !== get_option( 'woodmart_setup_status' ) ) { // phpcs:ignore
			add_action( 'admin_init', array( $this, 'prevent_plugins_redirect' ), 1 );
			do_action( 'woodmart_setup_wizard' );
		}

		add_action( 'admin_init', array( $this, 'theme_activation_redirect' ) );
	}

	/**
	 * Prevent plugins redirect.
	 */
	public function prevent_plugins_redirect() {
		delete_transient( 'elementor_activation_redirect' );
		add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );
		remove_action( 'admin_menu', 'vc_menu_page_build' );
		remove_action( 'network_admin_menu', 'vc_network_menu_page_build' );
		remove_action( 'vc_activation_hook', 'vc_page_welcome_set_redirect' );
		remove_action( 'admin_init', 'vc_page_welcome_redirect' );
	}

	/**
	 * Redirect to setup wizard after theme activated.
	 */
	public function theme_activation_redirect() {
		global $pagenow;

		$args = array(
			'page' => 'woodmart_dashboard',
		);

		if ( 'done' !== get_option( 'woodmart_setup_status' ) ) {
			$args['tab'] = 'wizard';
		}

		if ( 'themes.php' === $pagenow && is_admin() && isset( $_GET['activated'] ) ) { // phpcs:ignore
			wp_safe_redirect( esc_url_raw( add_query_arg( $args, admin_url( 'admin.php' ) ) ) );
		}
	}

	/**
	 * Template.
	 */
	public function setup_wizard_template() {
		if ( 'done' === get_option( 'woodmart_setup_status' ) ) {
			return;
		}

		$page = 'welcome';

		if ( isset( $_GET['step'] ) && ! empty( $_GET['step'] ) ) { // phpcs:ignore
			$page = trim( wp_unslash( $_GET['step'] ) ); // phpcs:ignore
		}

		$this->show_page( $page );
	}

	/**
	 * Show page.
	 *
	 * @param string $name Template file name.
	 */
	public function show_page( $name ) {
		?>
		<div class="xts-setup-wizard-wrap">
			<div class="xts-setup-wizard">
				<div class="xts-wizard-nav">
					<?php $this->show_part( 'sidebar' ); ?>
				</div>

				<div class="xts-wizard-content">
					<?php $this->show_part( $name ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get previous page button.
	 *
	 * @param string $page Page slug.
	 */
	public function get_prev_button( $page ) {
		?>
		<a class="xts-inline-btn xts-prev" href="<?php echo esc_url( $this->get_page_url( $page ) ); ?>">
			<?php esc_html_e( 'Previous step', 'woodmart' ); ?>
		</a>
		<?php
	}

	/**
	 * Get previous page button.
	 *
	 * @param string  $page Page slug.
	 * @param string  $builder Builder name.
	 * @param boolean $disabled Is button disabled.
	 */
	public function get_next_button( $page, $builder = '', $disabled = false ) {
		$classes = '';
		$url     = $this->get_page_url( $page );

		if ( 'elementor' === $builder ) {
			$classes .= ' xts-elementor xts-shown';
			$url     .= '&wd_builder=elementor';
		} elseif ( 'wpb' === $builder ) {
			$classes .= ' xts-wpb xts-hidden';
			$url     .= '&wd_builder=wpb';
		}

		if ( $disabled ) {
			$classes .= ' xts-disabled';
		}

		?>
		<a class="xts-btn xts-btn-primary xts-next<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $url ); ?>">
			<?php esc_html_e( 'Next step', 'woodmart' ); ?>
		</a>
		<?php
	}

	/**
	 * Get skip page button.
	 *
	 * @param string $page Page slug.
	 */
	public function get_skip_button( $page ) {
		?>
		<a class="xts-inline-btn xts-inline-btn-alt xts-skip" href="<?php echo esc_url( $this->get_page_url( $page ) ); ?>">
			<?php esc_html_e( 'Skip', 'woodmart' ); ?>
		</a>
		<?php
	}

	/**
	 * Show template part.
	 *
	 * @param string $name Template file name.
	 */
	public function show_part( $name ) {
		include_once get_parent_theme_file_path( WOODMART_FRAMEWORK . '/admin/setup-wizard/templates/' . $name . '.php' );
	}

	/**
	 * Is active page.
	 *
	 * @param string $name Page name.
	 */
	public function is_active_page( $name ) {
		$page = 'welcome';

		if ( isset( $_GET['step'] ) && ! empty( $_GET['step'] ) ) { // phpcs:ignore
			$page = trim( wp_unslash( $_GET['step'] ) ); // phpcs:ignore
		}

		return $name === $page; // phpcs:ignore
	}

	/**
	 * Get page url.
	 *
	 * @param string $name Page name.
	 */
	public function get_page_url( $name ) {
		return admin_url( 'admin.php?page=woodmart_dashboard&tab=wizard&step=' . $name ); // phpcs:ignore
	}

	/**
	 * Get image url.
	 *
	 * @param string $name Image name.
	 */
	public function get_image_url( $name ) {
		return WOODMART_THEME_DIR . '/inc/admin/setup-wizard/images/' . $name;
	}
}

Setup_Wizard::get_instance();
