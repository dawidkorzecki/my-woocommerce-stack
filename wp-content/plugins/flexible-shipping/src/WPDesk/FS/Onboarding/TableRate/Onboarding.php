<?php
/**
 * Assets.
 *
 * @package WPDesk\FS\Onboarding
 */

namespace WPDesk\FS\Onboarding\TableRate;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WPDesk\FS\Helpers\FlexibleShippingMethodsChecker;
use WPDesk\FS\Helpers\WooSettingsPageChecker;

/**
 * Onboarding hooks.
 */
class Onboarding implements Hookable {

	/**
	 * @var FinishOption .
	 */
	private $finish_option;

	/**
	 * @var string .
	 */
	private $scripts_version;

	/**
	 * @var string .
	 */
	private $plugin_assets_url;

	/**
	 * @var WooSettingsPageChecker
	 */
	private $setting_page_checker;

	/**
	 * @var FlexibleShippingMethodsChecker
	 */
	private $fs_methods_checker;

	/**
	 * @var array
	 */
	private $popups;

	/**
	 * @param FinishOption           $finish_option        .
	 * @param string                 $scripts_version      .
	 * @param string                 $plugin_assets_url    .
	 * @param WooSettingsPageChecker $setting_page_checker .
	 */
	public function __construct(
		FinishOption $finish_option,
		string $scripts_version,
		string $plugin_assets_url,
		WooSettingsPageChecker $setting_page_checker,
		FlexibleShippingMethodsChecker $fs_methods_checker,
		array $popups
	) {
		$this->finish_option        = $finish_option;
		$this->scripts_version      = $scripts_version;
		$this->plugin_assets_url    = $plugin_assets_url;
		$this->setting_page_checker = $setting_page_checker;
		$this->fs_methods_checker   = $fs_methods_checker;
		$this->popups               = $popups;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'flexible-shipping/admin/enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'flexible-shipping/method-rules-settings/table/before', [ $this, 'add_onboarding_container' ] );
	}

	/**
	 * Add onboarding container.
	 */
	public function add_onboarding_container() {
		include wp_normalize_path( __DIR__ . '/views/before-table-method-rules-settings.php' );
	}

	public function register_scripts() {
		if ( ! $this->setting_page_checker->is_fs_instance_method_edit() ) {
			return;
		}

		wp_enqueue_style( 'wpdesk_onboarding', sprintf( '%scss/onboarding.css', $this->plugin_assets_url ), [], $this->scripts_version );

		wp_enqueue_script(
			'wpdesk_onboarding',
			sprintf( '%sjs/onboarding.js', $this->plugin_assets_url ),
			[ 'jquery' ],
			$this->scripts_version,
			true
		);

		wp_localize_script(
			'wpdesk_onboarding',
			'fs_onboarding_details',
			[
				'ajax'       => [
					'url'    => admin_url( 'admin-ajax.php' ),
					'nonce'  => wp_create_nonce( OptionAjaxUpdater::NONCE_ACTION ),
					'action' => [
						'event'           => OptionAjaxUpdater::AJAX_ACTION_EVENT,
						'click'           => OptionAjaxUpdater::AJAX_ACTION_CLICK,
						'auto_show_popup' => OptionAjaxUpdater::AJAX_ACTION_AUTO_SHOP_POPUP,
					],
				],
				'assets_url' => untrailingslashit( $this->plugin_assets_url ),
				'label_step' => __( 'Step #', 'flexible-shipping' ),
				'logo_img'   => 'logo-fs.svg',
				'steps'      => 4,
				'locale'     => get_user_locale(),
				'open_auto'  => $this->should_auto_load(),
				'popups'     => $this->popups,
			]
		);
	}

	/**
	 * @return bool
	 */
	private function should_auto_load(): bool {
		return ! $this->finish_option->is_option_set() && $this->fs_methods_checker->is_new_shipping_method();
	}
}
