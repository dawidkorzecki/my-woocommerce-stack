<?php
/**
 * Class ShippingMethodSimple
 *
 * @package WPDesk\FS\TableRate\
 */

namespace WPDesk\FS\TableRate;

use FSVendor\WPDesk\FS\TableRate\Logger\NoticeLogger;
use FSVendor\WPDesk\FS\TableRate\Logger\ShippingMethodLogger;
use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettingsFactory;
use Psr\Log\LoggerInterface;
use WC_Shipping_Method;
use WPDesk\FS\TableRate\Rule\Condition\ConditionsFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleAdditionalCostFactory;
use WPDesk\FS\TableRate\Rule\Cost\RuleCostFieldsFactory;
use WPDesk\FS\TableRate\ShippingMethod\RateCalculatorFactory;
use WPDesk\FS\TableRate\ShippingMethod\SingleMethodSettings;
use WPDesk\FS\TableRate\Tax\TaxCalculator;
use WPDesk_Flexible_Shipping;

/**
 * Shipping method flexible_shipping_single
 */
class ShippingMethodSingle extends WC_Shipping_Method {

	const SHIPPING_METHOD_ID = 'flexible_shipping_single';

	/**
	 * Logger provided by Flexible Shipping plugin.
	 *
	 * @var LoggerInterface
	 */
	protected static $fs_logger;

	/**
	 * @var bool
	 */
	private $is_html_ads_loaded = false;

	/**
	 * ShippingMethodSingle constructor.
	 *
	 * @param int $instance_id .
	 */
	public function __construct( $instance_id = 0 ) {
		$this->instance_id = absint( $instance_id );

		$this->id = self::SHIPPING_METHOD_ID;
		$this->init();
	}

	/**
	 * Init.
	 */
	protected function init() {
		$this->supports = [
			'shipping-zones',
			'instance-settings',
		];
		$this->init_instance_form_fields( false );
		$this->method_title       = __( 'Flexible Shipping', 'flexible-shipping' );
		$this->method_description = __( 'A single Flexible Shipping method.', 'flexible-shipping' );
		if ( $this->instance_id ) {
			$docs_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/' : 'https://docs.flexibleshipping.com/collection/20-fs-table-rate/';
			$docs_link .= '?utm_source=flexible-shipping-settings&utm_medium=link&utm_campaign=flexible-shipping-docs-link';
			// Translators: docs link.
			$this->method_description = sprintf( __( 'A single Flexible Shipping method. Learn %1$show to configure FS shipping method &rarr;%2$s', 'flexible-shipping' ), '<a href="' . $docs_link . '" target="_blank">', '</a>' );
		}
		$this->title                      = $this->get_instance_option( 'method_title', $this->method_title );
		$this->instance_settings['title'] = $this->title;
		$this->tax_status                 = $this->get_instance_option( 'tax_status' );
	}

	/**
	 * @param bool $with_integration_settings .
	 */
	public function init_instance_form_fields( $with_integration_settings = false ) {
		$this->instance_form_fields = ( new SingleMethodSettings() )->get_settings_fields( $this->instance_settings, $with_integration_settings );
	}

	/**
	 * @param array $form_fields .
	 * @param bool  $echo        .
	 *
	 * @return string
	 */
	public function generate_settings_html( $form_fields = [], $echo = true ) {
		$this->init_instance_form_fields( true );
		$form_fields = $this->get_instance_form_fields();
		if ( $echo ) {
			parent::generate_settings_html( $form_fields, $echo );
		} else {
			return parent::generate_settings_html( $form_fields, $echo );
		}
	}


	/**
	 * Generate Title HTML.
	 *
	 * @param string $key  Field key.
	 * @param array  $data Field data.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_title_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = [
			'title' => '',
			'class' => '',
		];

		$data = wp_parse_args( $data, $defaults );

		$ads = '';

		if ( ! $this->is_html_ads_loaded ) {
			ob_start();
			$shipping_method_id = self::SHIPPING_METHOD_ID;
			include __DIR__ . '/../../../../classes/table-rate/views/html-ads.php';
			$ads                      = ob_get_clean();
			$ads                      = apply_filters( 'flexible-shipping/sell-box', is_string( $ads ) ? $ads : '' );
			$this->is_html_ads_loaded = true;
		}

		ob_start();
		?>
		</table>
		<h3 class="wc-settings-sub-title <?php echo esc_attr( $data['class'] ); ?>"
			id="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></h3>
		<?php if ( ! empty( $data['description'] ) ) : ?>
			<p><?php echo wp_kses_post( $data['description'] ); ?></p>
		<?php endif; ?>

		<?php echo wp_kses_post( $ads ); ?>

		<table class="form-table">
		<?php

		return ob_get_clean();
	}

	/**
	 * .
	 */
	public function init_instance_settings() {
		parent::init_instance_settings();
		if ( isset( $this->instance_settings['method_rules'] ) && ! is_array( $this->instance_settings['method_rules'] ) && is_string( $this->instance_settings['method_rules'] ) ) {
			$this->instance_settings['method_rules'] = json_decode( $this->instance_settings['method_rules'], true );
		}
		$this->instance_settings['id_for_shipping'] = $this->get_rate_id();
		$this->instance_settings['title']           = $this->title;
	}

	/**
	 * @return bool
	 */
	public function process_admin_options() {
		$this->init_instance_form_fields( true );
		$filter_name     = 'woocommerce_shipping_' . $this->id . '_instance_settings_values';
		$filter_callback = [ $this, 'process_integrations_settings' ];
		add_filter( $filter_name, $filter_callback );
		$processed = parent::process_admin_options();
		remove_filter( $filter_name, $filter_callback );

		return $processed;
	}

	/**
	 * @param array $settings .
	 *
	 * @return array
	 */
	public function process_integrations_settings( $settings ) {
		$settings = apply_filters( 'flexible_shipping_process_admin_options', $settings );

		return $settings;
	}

	/**
	 * Admin options.
	 */
	public function admin_options() {
		parent::admin_options();
		do_action( 'flexible_shipping_method_script', self::SHIPPING_METHOD_ID, $this->instance_id );
	}

	/**
	 * Renders shipping rules settings.
	 *
	 * @param string $key  .
	 * @param array  $data .
	 *
	 * @return string
	 */
	public function generate_shipping_rules_html( $key, $data ) {
		$field_key             = $this->get_field_key( $key );
		$method_rules_settings = $this->get_option( $key, '[]' );
		$rules_settings        = new RulesSettingsField( $field_key, $field_key, $data['title'], $data, ! is_array( $method_rules_settings ) ? json_decode( $method_rules_settings, true ) : $method_rules_settings );

		return $rules_settings->render();
	}

	/**
	 * @param string $key   .
	 * @param array  $value .
	 *
	 * @return string
	 */
	public function validate_shipping_rules_field( $key, $value ) {
		$rules_settings_processor = new Rule\Settings\SettingsProcessor(
			( null === $value || ! is_array( $value ) ) ? [] : $value,
			( new ConditionsFactory() )->get_conditions(),
			( new RuleCostFieldsFactory() )->get_fields(),
			( new Rule\Cost\RuleAdditionalCostFieldsFactory( ( new RuleAdditionalCostFactory() )->get_additional_costs() ) )->get_fields(),
			( new Rule\SpecialAction\SpecialActionFieldsFactory( ( new Rule\SpecialAction\SpecialActionFactory() )->get_special_actions() ) )->get_fields()
		);

		return json_encode( $rules_settings_processor->prepare_settings(), JSON_PRETTY_PRINT );
	}

	/**
	 * Field key.
	 * For compatibility with integrations scripts.
	 *
	 * @param string $key .
	 *
	 * @return string
	 */
	public function get_field_key( $key ) {
		return $this->plugin_id . WPDesk_Flexible_Shipping::METHOD_ID . '_' . $key;
	}

	/**
	 * Get method rules.
	 *
	 * @return array
	 */
	public function get_method_rules() {
		return $this->get_instance_option( 'method_rules', [] );
	}

	/**
	 * @param string $key   .
	 * @param string $value .
	 *
	 * @return bool
	 */
	public function update_instance_option( $key, $value = '' ) {
		if ( empty( $this->instance_settings ) ) {
			$this->init_instance_settings();
		}

		$this->instance_settings[ $key ] = $value;

		return update_option( $this->get_instance_option_key(), apply_filters( 'woocommerce_shipping_' . $this->id . '_instance_settings_values', $this->instance_settings, $this ), 'yes' );
	}

	/**
	 * @param array $package .
	 */
	public function calculate_shipping( $package = [] ) {
		$rate_calculator = RateCalculatorFactory::create_for_shipping_method( $this, $package );

		$method_settings = MethodSettingsFactory::create_from_array( $this->instance_settings );

		$logger = $this->prepare_shipping_method_calculation_logger( $method_settings );

		$rate_calculator->set_logger( $logger );
		$calculated_rate = $rate_calculator->calculate_rate( $method_settings, $this->get_rate_id(), is_user_logged_in() );

		if ( ! empty( $calculated_rate ) && $this->should_add_rate( $calculated_rate ) ) {
			$calculated_rate = ( new TaxCalculator( $method_settings, \WC_Tax::get_shipping_tax_rates() ) )->append_taxes_to_rate_if_enabled( $calculated_rate, WC()->cart->get_customer()->get_is_vat_exempt() );
			$this->add_rate( $calculated_rate );
			$logger->debug( __( 'Shipping cost added.', 'flexible-shipping' ), $logger->get_results_context() );
		}

		$logger->show_notice_if_enabled();
	}

	/**
	 * @param array $calculated_rate .
	 *
	 * @return bool
	 */
	private function should_add_rate( array $calculated_rate ) {
		return ! ( 'yes' === $this->get_instance_option( 'method_visibility', 'no' ) && ! is_user_logged_in() );
	}

	/**
	 * Set logger. This logger is set by Flexible Shipping plugin.
	 *
	 * @param LoggerInterface $fs_logger .
	 */
	public static function set_fs_logger( LoggerInterface $fs_logger ) {
		static::$fs_logger = $fs_logger;
	}

	/**
	 * @param \FSVendor\WPDesk\FS\TableRate\Settings\MethodSettings $shipping_method_settings .
	 *
	 * @return Logger\ShippingMethodLogger
	 */
	private function prepare_shipping_method_calculation_logger( $shipping_method_settings ) {
		$method_debug_mode     = $shipping_method_settings->get_debug_mode();
		$shipping_method_title = $shipping_method_settings->get_title();
		$shipping_method_url   = admin_url(
			'admin.php?page=wc-settings&tab=shipping&instance_id=' . sanitize_key( $this->instance_id ) . '&action=edit&method_id=' . sanitize_key( $shipping_method_settings->get_id() )
		);
		if ( null !== static::$fs_logger ) {
			$fs_logger = static::$fs_logger;
		} else {
			$fs_logger = NullLogger();
		}

		return new ShippingMethodLogger(
			$fs_logger,
			new NoticeLogger(
				$shipping_method_title,
				$shipping_method_url,
				'yes' === $method_debug_mode && current_user_can( 'manage_woocommerce' )
			)
		);
	}

}
