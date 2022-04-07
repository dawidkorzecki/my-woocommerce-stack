<?php

use FSVendor\WPDesk\Beacon\Beacon\WooCommerceSettingsFieldsModifier;
use FSVendor\WPDesk\FS\TableRate\Logger\NoticeLogger;
use FSVendor\WPDesk\FS\TableRate\Logger\ShippingMethodLogger;
use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettings;
use FSVendor\WPDesk\FS\TableRate\Settings\MethodSettingsFactory;
use Psr\Log\LoggerInterface;
use WPDesk\FS\TableRate\Rates\FlexibleShippingRates;
use WPDesk\FS\TableRate\RulesSettingsField;
use WPDesk\FS\TableRate\ShippingMethod\CommonMethodSettings;
use WPDesk\FS\TableRate\ShippingMethod\RateCalculatorFactory;
use WPDesk\FS\TableRate\ShippingMethod\SettingsDisplayPreparer;
use WPDesk\FS\TableRate\ShippingMethod\SettingsProcessor;

class WPDesk_Flexible_Shipping extends WC_Shipping_Method {

	const METHOD_ID = 'flexible_shipping';

    const FIELD_METHOD_FREE_SHIPPING = 'method_free_shipping';

	const META_DEFAULT = '_default';

	const WEIGHT_ROUNDING_PRECISION = 6;

	const SETTING_METHOD_RULES = 'method_rules';

	const SETTING_METHOD_FREE_SHIPPING_NOTICE = 'method_free_shipping_cart_notice';

	/**
	 * Logger provided by Flexible Shipping plugin.
	 *
	 * @var LoggerInterface
	 */
	protected static $fs_logger;

	/**
	 * Message added.
	 *
	 * @var bool
	 */
	private $message_added = false;

	/**
	 * @var string
	 *
	 * See Active Payments - must be public
	 */
	public $shipping_methods_option;

	/**
	 * @var string
	 */
	private $shipping_method_order_option;

	/**
	 * @var string
	 */
	private $section_name;

	/**
	 * Constructor for your shipment class
	 *
	 * @access public
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {
		$this->instance_id 			     	= absint( $instance_id );
		$this->id                 			= self::METHOD_ID;
		$this->shipping_methods_option 		= 'flexible_shipping_methods_' . $this->instance_id;
		$this->shipping_method_order_option = 'flexible_shipping_method_order_' . $this->instance_id;
		$this->section_name 				= 'flexible_shipping';
		$this->method_title       			= __( 'Flexible Shipping Group', 'flexible-shipping' );
		$this->method_description 			= __( 'A group of Flexible Shipping methods - useful to organize numerous shipping methods.', 'flexible-shipping' );

		$this->supports = array(
			'instance-settings',
		);

		if ( $this->is_allowed_support_shipping_zones() ) {
			$this->supports[] = 'shipping-zones';
		}

		$this->instance_form_fields = array(
				'enabled' => array(
						'title' 		=> __( 'Enable/Disable', 'flexible-shipping' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Enable this shipment method', 'flexible-shipping' ),
						'default' 		=> 'yes',
				),
				'title' => array(
						'title' 		=> __( 'Shipping Title', 'flexible-shipping' ),
						'type' 			=> 'text',
						'description' 	=> __( 'This controls the title which the user sees during checkout.', 'flexible-shipping' ),
						'default'		=> __( 'Flexible Shipping', 'flexible-shipping' ),
						'desc_tip'		=> true
				)
		);

		if ( version_compare( WC()->version, '2.6' ) < 0  && $this->get_option( 'enabled', 'yes' ) == 'no' ) {
			$this->enabled		    = $this->get_option( 'enabled' );
		}

		$this->title            = $this->get_option( 'title' );

		$this->init();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
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
     * @param MethodSettings $shipping_method_settings
     *
	 * @return \WPDesk\FS\TableRate\Logger\ShippingMethodLogger
	 */
	private function prepare_shipping_method_calculation_logger( $shipping_method_settings ) {
		$method_debug_mode = $shipping_method_settings->get_debug_mode();
		$shipping_method_title = $shipping_method_settings->get_title();
		$shipping_method_url = admin_url(
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

	/**
	 * Init your settings
	 *
	 * @access public
	 * @return void
	 */
	function init() {
		$this->instance_form_fields = include( 'settings/flexible-shipping.php' );
		// Load the settings API
		$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
		$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

		// Define user set variables
		$this->title        		= $this->get_option( 'title' );
		$this->tax_status   		= $this->get_option( 'tax_status' );

		$this->availability         = $this->get_option( 'availability' );

		$this->type                 = $this->get_option( 'type', 'class' );
	}

	/**
	 * Initialise Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = include( 'settings/flexible-shipping.php' );
		$this->form_fields = $this->add_beacon_search_data_to_fields( $this->form_fields );
	}

	public function generate_title_shipping_methods_html( $key, $data ) {
		$field    = $this->get_field_key( $key );
		$defaults = array(
			'title'             => '',
			'class'             => ''
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();

		?>
			</table>
            <?php
				$shipping_method_id = self::METHOD_ID;
                include __DIR__ . '/views/html-ads.php';
            ?>
			<h3 class="wc-settings-sub-title <?php echo esc_attr( $data['class'] ); ?>" id="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?>
		<?php if ( $this->is_allowed_editing() ): ?>
			<a href="<?php echo remove_query_arg( 'added', add_query_arg( 'action', 'new' ) ); ?>"
			   class="add-new-h2"><?php echo __( 'Add New', 'flexible-shipping' ); ?></a></h3>
		<?php endif; ?>

			<?php if ( ! empty( $data['description'] ) ) : ?>
				<p><?php echo wp_kses_post( $data['description'] ); ?></p>
			<?php endif; ?>
			</div><table class="form-table">
		<?php

		return ob_get_clean();
	}

	/**
	 * @param unknown $key
	 *
	 * @return string
	 *
	 * Dodane w WooCommerce 2.4
	 * Dodane w celu zachowania kompatybilności z WooCommerce 2.3
	 * Przetestowane na WooCommerce 2.3.9
	 */
	public function get_field_key( $key ) {
		return $this->plugin_id . $this->id . '_' . $key;
	}

	public function generate_shipping_methods_html( $key, $data ) {
		$shipping_methods = $this->get_shipping_methods();
		$shipping_method_order = $this->get_shipping_method_order();
		ob_start();
		include ( 'views/html-shipping-method-settings.php' );
		return ob_get_clean();
	}

	public function get_shipping_methods( $enabled = false ) {
		$shipping_methods = $this->get_option_shipping_methods();
		$shipping_method_order = get_option( $this->shipping_method_order_option, array() );
		$ret = array();
		if ( is_array( $shipping_method_order ) ) {
			foreach ( $shipping_method_order as $method_id ) {
				if ( isset( $shipping_methods[$method_id] ) ) {$ret[$method_id] = $shipping_methods[$method_id];}
			}
		}
		foreach ( $shipping_methods as $shipping_method ) {
			if ( !isset( $ret[$shipping_method['id']] ) ) {$ret[$shipping_method['id']] = $shipping_method;}
		}
		if ( $enabled )	{
			foreach ( $ret as $key => $shipping_method ) {
				if ( isset( $shipping_method['method_enabled'] ) && 'yes' != $shipping_method['method_enabled'] ) {unset($ret[$key]);}
			}
		}
		return $ret;
	}

	private function get_shipping_method_order() {
		$shipping_methods = $this->get_option_shipping_methods();
		$shipping_method_order = get_option( $this->shipping_method_order_option, array() );
		$ret = array();
		if ( is_array( $shipping_method_order ) ) {
			foreach ( $shipping_method_order as $method_id ) {
				if ( isset( $shipping_methods[$method_id] ) ) {$ret[$method_id] = $method_id;}
			}
		}
		foreach ( $shipping_methods as $shipping_method ) {
			if ( !isset( $ret[$shipping_method['id']] ) ) {$ret[$shipping_method['id']] = $shipping_method['id'];}
		}
		return $ret;
	}

	/**
     * Generates method rules field.
     *
	 * @param string $key .
	 * @param array $data .
	 *
	 * @return string
	 */
	/**
     * Renders shipping rules settings.
     *
	 * @param string $key .
	 * @param array $data .
	 *
	 * @return false|string
	 */
	public function generate_shipping_rules_html( $key, $data ) {
		$rules_settings = new RulesSettingsField( $key, self::SETTING_METHOD_RULES, $data['title'], $data );

		return $rules_settings->render();
	}

	public function shipping_method_title_used( $title, $shipping_methods ) {
		foreach ( $shipping_methods as $shipping_method ) {
			if ( $title == $shipping_method['method_title'] ) {
				return true;
			}
		}
		return false;
	}

	public function shipping_method_next_id( $shipping_methods ) {
		$next_id = 0;
		foreach ( $shipping_methods as $shipping_method ) {
			if ( intval($shipping_method['id'] ) > $next_id ) {
				$next_id = intval($shipping_method['id'] );
			}
		}
		$next_id++;
		return $next_id;
	}

	public function process_admin_options()	{
		$action = false;
		if ( isset( $_POST['method_action'] ) ) {
			$action = sanitize_key( $_POST['method_action'] );
		}

		if ( isset( $_POST['import_action'] ) && $_POST['import_action'] == '1' ) {
			$this->process_import_action();
		} elseif ( $action === 'new' || $action === 'edit' ) {
			$save_rules = new SettingsProcessor(
				$this->id, $this->instance_id, $this->shipping_methods_option, $this->shipping_method_order_option
			);

			try {
				$shipping_method_settings = $save_rules->process_and_save_settings( $action, $_POST );
				if ( $action === 'new' ) {
				    $this->redirect_new_method( $shipping_method_settings['id'] );
				}
			} catch ( Exception $e ) {
				$this->add_error( $e->getMessage() );
			}

		} else {
			parent::process_admin_options();

			if ( isset( $_POST['method_order'] ) ) {
				$this->process_order_method();
			}
		}
	}

	/**
	 * @param string $method_id .
	 */
	private function redirect_new_method( $method_id ) {
		$redirect = add_query_arg( array('added' => $method_id, 'action' => false, 'method_id' => false ));
		$redirect .= '#method_' . $method_id;
		$redirect = add_query_arg( array('added' => $method_id, 'action' => 'edit', 'method_id' => $method_id ));
		wpdesk_redirect( $redirect );
	}

	private function process_order_method() {
		$method_order                = $_POST['method_order'];
		$method_order_security_alert = false;
		foreach ( $method_order as $method_order_key => $method_id ) {
			if ( strval( $method_order_key ) !== strval( sanitize_key( $method_order_key ) ) || strval( $method_id ) !== strval( sanitize_key( $method_id ) ) ) {
				$method_order_security_alert = true;
			}
		}
		if ( $method_order_security_alert ) {
			WC_Admin_Settings::add_error( __( 'Flexible Shipping: security check error. Shipping method order not saved!', 'flexible-shipping' ) );
			WC_Admin_Settings::show_messages();
		} else {
			update_option( $this->shipping_method_order_option, $method_order );
		}
	}

	private function process_import_action() {
		$shipping_methods = $this->get_option_shipping_methods();

		if ( ! is_array( $shipping_methods ) ) {
			$shipping_methods = array();
		}

		try {
			$importer = ( new \WPDesk\FS\TableRate\ImporterExporter\Importer\ImporterFactory( $_FILES['import_file'], $this, $shipping_methods ) )->get_importer();

			$importer->import();

			$imported_shipping_methods = $importer->get_shipping_methods();

			update_option( $this->shipping_methods_option, $imported_shipping_methods );
		} catch ( Exception $e ) {
			WC_Admin_Settings::add_error( $e->getMessage() );
		}

		WC_Admin_Settings::show_messages();
	}

	public function admin_options()	{
		$action = false;
		if ( isset( $_GET['action'] ) )
		{
			$action = sanitize_key( $_GET['action'] );
		}
	    $settings_div_class = in_array( $action, array( 'new', 'edit' ), true ) ? '' : 'fs-settings-div';
		?>
        <div class="<?php echo esc_html( $settings_div_class ) ; ?>"><table class="form-table">
		<?php
			if ( $action == 'new' || $action == 'edit' ) {
				$shipping_methods = $this->get_option_shipping_methods();
				$shipping_method = array(
						'method_title' 				=> '',
						'method_description'		=> '',
						'method_enabled' 			=> 'no',
						'method_shipping_zone' 		=> '',
						'method_calculation_method'	=> 'sum',
						self::FIELD_METHOD_FREE_SHIPPING		=> '',
						'method_free_shipping_label'=> '',
						'method_visibility'			=> 'no',
						'method_default'			=> 'no',
						'method_integration'		=> '',
				);
				$method_id = '';
				if ( $action == 'edit' ) {
					$method_id = sanitize_key( $_GET['method_id'] );
					$shipping_method = $shipping_methods[$method_id];
					$method_id_for_shipping = $this->id . '_' . $this->instance_id . '_' . sanitize_title( $shipping_method['method_title'] );
					$method_id_for_shipping = apply_filters( 'flexible_shipping_method_rate_id', $method_id_for_shipping, $shipping_method );
				}
				else {
					$method_id_for_shipping = '';
				}
				?>
				<input type="hidden" name="method_action" value="<?php echo $action; ?>" />
				<input type="hidden" name="method_id" value="<?php echo $method_id; ?>" />
				<input type="hidden" name="method_id_for_shipping" value="<?php echo $method_id_for_shipping; ?>" />
				<?php if ( $action == 'new' ) : ?>
					<h2><?php _e('New Shipping Method', 'flexible-shipping' ); ?></h2>
				<?php endif; ?>
				<?php if ( $action == 'edit' ) : ?>
					<h2><?php _e('Edit Shipping Method', 'flexible-shipping' ); ?></h2>
				<?php endif; ?>
				<?php
				if ( isset( $_GET['added'] ) ) {
					$method_id = sanitize_key( $_GET['added'] );
					$shipping_methods = $this->get_option_shipping_methods();
					if ( isset( $shipping_methods[$method_id] ) )
					{
						if ( ! $this->message_added ) {
							$shipping_method = $shipping_methods[$method_id];
							WC_Admin_Settings::add_message( sprintf(__( 'Shipping method %s added.', 'flexible-shipping' ), $shipping_method['method_title'] ) );
							$this->message_added = true;
						}
					}
					WC_Admin_Settings::show_messages();
				}
				$shipping_method['woocommerce_method_instance_id'] = $this->instance_id;
				$this->get_shipping_method_form($shipping_method);
				$this->generate_settings_html();
			}
			else if ( $action == 'delete' ) {
				$methods_id = '';
				if ( isset( $_GET['methods_id'] ) ) {
					$methods_id = explode( ',' , sanitize_text_field( $_GET['methods_id'] ) );
				}
				$shipping_methods = $this->get_option_shipping_methods();
				$shipping_method_order = get_option( $this->shipping_method_order_option, array() );
				foreach ( $methods_id as $method_id ) {
					if ( isset( $shipping_methods[$method_id] ) ) {
						$shipping_method = $shipping_methods[$method_id];
						unset(	$shipping_methods[$method_id] );
						if ( isset( $shipping_method_order[$method_id] ) ) {
							unset(	$shipping_method_order[$method_id] );
						}
						update_option( $this->shipping_methods_option, $shipping_methods );
						update_option( $this->shipping_method_order_option, $shipping_method_order );
						WC_Admin_Settings::add_message( sprintf(__('Shipping method %s deleted.', 'flexible-shipping' ), $shipping_method['method_title'] ) );
					}
					else {
						WC_Admin_Settings::add_error( __( 'Shipping method not found.', 'flexible-shipping' ) );
					}
				}
				WC_Admin_Settings::show_messages();
				$this->generate_settings_html();
			}
			else {
				if ( isset( $_GET['added'] ) ) {
					$method_id = sanitize_key( $_GET['added'] );
					$shipping_methods = $this->get_option_shipping_methods();
					if ( isset( $shipping_methods[$method_id] ) )
					{
						if ( ! $this->message_added ) {
							$shipping_method = $shipping_methods[$method_id];
							WC_Admin_Settings::add_message( sprintf(__( 'Shipping method %s added.', 'flexible-shipping' ), $shipping_method['method_title'] ) );
							$this->message_added = true;
						}
					}
					WC_Admin_Settings::show_messages();
				}
				if ( isset( $_GET['updated'] ) ) {
					$method_id = sanitize_key( $_GET['updated'] );
					$shipping_methods = $this->get_option_shipping_methods();
					if ( isset( $shipping_methods[$method_id] ) )
					{
						$shipping_method = $shipping_methods[$method_id];
						WC_Admin_Settings::add_message( sprintf(__( 'Shipping method %s updated.', 'flexible-shipping' ), $shipping_method['method_title'] ) );
					}
					WC_Admin_Settings::show_messages();
				}

				// General Settings
				$this->generate_settings_html();
			}
		?>
		</table>
        <?php include __DIR__ . '/views/html-shipping-method-scripts.php'; ?>
		<?php do_action( 'flexible_shipping_method_script', self::METHOD_ID, $this->instance_id ); ?>
		<?php
	}

	private function get_shipping_method_form( $shipping_method ) {
		$this->form_fields = ( new CommonMethodSettings() )->get_settings_fields( $shipping_method, true );
		$this->form_fields = ( new SettingsDisplayPreparer() )->prepare_settings_for_display( $this->form_fields );
	}

	private function package_weight( $items ) {
		$weight = 0;
		foreach( $items as $item ) {
			$weight += $item['data']->weight * $item['quantity'];
		}
		return $weight;
	}

	private function woocommerce_product_weight( $weight ) {
		if ( $weight === '' ) {
			return 0;
		}
		return $weight;
	}

	private function package_item_count( $items ) {
		$item_count = 0;

		foreach( $items as $item ) {
			$item_count += $item['quantity'];
		}
		return $item_count;
	}

	private function cart_item_count() {
		$item_count = 0;

		$cart = WC()->cart;
		foreach( $cart->cart_contents as $item ) {
			$item_count += $item['quantity'];
		}

		return $item_count;
	}

	/**
	 * @param array $package
	 */
	public function calculate_shipping( $package = array() ) {
		$shipping_methods = $this->get_shipping_methods( true );

		$rate_calculator = RateCalculatorFactory::create_for_shipping_method( $this, $package );

		foreach ( $shipping_methods as $shipping_method_settings ) {
			$method_settings = MethodSettingsFactory::create_from_array_and_tax_status( $shipping_method_settings, $this->tax_status );
			$logger = $this->prepare_shipping_method_calculation_logger( $method_settings );
			$rate_calculator->set_logger( $logger );
			$calculated_rate = $rate_calculator->calculate_rate( $method_settings, $this->prepare_rate_id( $method_settings->get_raw_settings() ), is_user_logged_in() );
			if ( ! empty( $calculated_rate ) ) {
				$this->add_rate( $calculated_rate );
				$logger->debug( __( 'Shipping cost added.', 'flexible-shipping' ), $logger->get_results_context() );
			}
			$logger->show_notice_if_enabled();
		}
	}

	/**
	 * @param string $suffix
	 *
	 * @return string
	 */
	public function prepare_rate_id( $shipping_method_settings ) {
		$id = $this->id . '_' . $this->instance_id . '_' . sanitize_title( $shipping_method_settings['method_title'] );
		$id = apply_filters( 'flexible_shipping_method_rate_id', $id, $shipping_method_settings );

		return $id;
	}

	public function set_as_converted() {
		$shipping_method_key     = $this->get_instance_option_key();
		$shipping_method_options = $this->get_instance_options();

		$shipping_method_options['converted'] = 'yes';

		update_option( $shipping_method_key, $shipping_method_options );
	}

	/**
	 * @return bool
	 */
	public function is_converted() {
		$shipping_method_options = $this->get_instance_options();

		return isset( $shipping_method_options['converted'] ) ? $shipping_method_options['converted'] === 'yes' : false;
	}

	/**
	 * @return array
	 */
	private function get_instance_options() {
		$options = get_option( $this->get_instance_option_key(), array() );

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		return $options;
	}

	/**
	 * @return bool
	 */
	private function prices_include_tax() {
		return (bool) apply_filters( 'flexible_shipping_prices_include_tax', WC()->cart->display_prices_including_tax() );
	}

	/**
	 * @return array
	 */
	public function get_all_rates() {
		return FlexibleShippingRates::get_flexible_shipping_rates();
	}

	/**
	 * Add beacon search data to fields.
	 *
	 * @param array $form_fields .
	 *
	 * @return array
	 */
	private function add_beacon_search_data_to_fields( array $form_fields ) {
		$modifier = new WooCommerceSettingsFieldsModifier();

		return $modifier->append_beacon_search_data_to_fields( $form_fields );
	}

	/**
	 * @return array
	 */
	private function get_option_shipping_methods() {
		$shipping_methods = get_option( $this->shipping_methods_option, array() );

		if ( ! is_array( $shipping_methods ) ) {
			$shipping_methods = array();
		}

		return $shipping_methods;
	}

	/**
	 * @return bool
	 */
	private function is_allowed_support_shipping_zones() {
		return ! is_admin() || (bool) apply_filters( 'flexible-shipping/group-method/supports/shipping-zones', false );
	}

	/**
	 * @return bool
	 */
	private function is_allowed_editing() {
		return (bool) apply_filters( 'flexible-shipping/group-method/supports/edit', false );
	}

}
