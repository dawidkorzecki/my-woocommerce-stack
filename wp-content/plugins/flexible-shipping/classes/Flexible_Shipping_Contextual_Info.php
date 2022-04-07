<?php
/**
 * Contextual info.
 *
 * @package Contextual Info
 */

/**
 * Can add contextual info script to admin footer.
 */
class Flexible_Shipping_Contextual_Info implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable {

	/**
	 * .
	 *
	 * @var string
	 */
	private $html_elements_ids;

	/**
	 * .
	 *
	 * @var string
	 */
	private $info_id;

	/**
	 * .
	 *
	 * @var string[]
	 */
	private $phrases_in;

	/**
	 * .
	 *
	 * @var string
	 */
	private $info_html;

	/**
	 * @var string[]
	 */
	private $phrases_not_in;

	/**
	 * Flexible_Shipping_Contextual_Info constructor.
	 *
	 * @param string $html_elements_ids Comma separated HTML element IDs to add contextual info.
	 * @param string $info_id Info element ID.
	 * @param array  $phrases_in Phrases to display contextual info.
	 * @param string $info_html HTML code to display as info.
	 * @param array  $phrases_not_in Phrases to not display contextual info.
	 */
	public function __construct(
		$html_elements_ids,
		$info_id,
		array $phrases_in,
		$info_html,
		array $phrases_not_in = array()
	) {
		$this->html_elements_ids = $html_elements_ids;
		$this->info_id           = $info_id;
		$this->phrases_in        = $phrases_in;
		$this->info_html         = $info_html;
		$this->phrases_not_in    = $phrases_not_in;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'admin_footer', array( $this, 'add_contextual_info_script' ) );
	}

	/**
	 * Add contextual info script.
	 */
	public function add_contextual_info_script() {
		$current_screen = get_current_screen();
		if ( 'shop_order' === $current_screen->post_type || 'woocommerce_page_wc-settings' === $current_screen->id ) {
			$html_elements_ids = '#' . implode( ',#', explode( ',', $this->html_elements_ids ) );
			$info_id           = $this->info_id;
			$phrases_in        = $this->phrases_in;
			$info_html         = $this->info_html;
			$phrases_not_in    = $this->phrases_not_in;
			include __DIR__ . '/views/contextual-info-script.php';
		}
	}

}
