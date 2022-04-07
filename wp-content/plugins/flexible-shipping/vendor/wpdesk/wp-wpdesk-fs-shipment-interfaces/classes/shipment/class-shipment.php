<?php
/**
 * Shipment.
 *
 * @package Flexible Shipping
 */

use Psr\Log\LoggerInterface;

/**
 * Can handle shipment functionality.
 */
abstract class WPDesk_Flexible_Shipping_Shipment {

	const STATUS_FS_NEW = 'fs-new';
	const STATUS_FS_CREATED = 'fs-created';
	const STATUS_FS_CONFIRMED = 'fs-confirmed';
	const STATUS_FS_FAILED = 'fs-failed';
	const STATUS_FS_MANIFEST = 'fs-manifest';

	const CREATED_VIA = 'created_via';

	const SENT_VIA_METABOX = 'metabox';
	const SENT_VIA_BULK = 'bulk';
	const SENT_VIA_AUTO = 'auto';

	const SENT_VIA = '_sent_via';

	const NOT_SET = 'not_set';

	const LABEL_ACTION_DOWNLOAD = 'download';
	const LABEL_ACTION_OPEN = 'open';

	/**
	 * Logger provided by Flexible Shipping plugin.
	 *
	 * @var LoggerInterface
	 */
	protected static $fs_logger;

	/**
	 * @var int
	 */
	private $id;
	/**
	 * @var WP_Post
	 * Post assigned to shipment
	 */
	private $post;
	/**
	 * @var null|WC_Order
	 * WC_Order assigned to shipment
	 */
	private $order = null;
	/**
	 * @var bool
	 * True if assigned post ich changed. Used when saving post
	 */
	private $save_post = false;
	/**
	 * @var null
	 * Holds old status when shipment status is changed
	 */
	private $old_status = null;
	/**
	 * @var bool
	 * True when status changing
	 */
	private $status_changed = false;
	/**
	 * @var array
	 * Shipment metadata (from postmeta table)
	 */
	private $meta_data = array();
	/**
	 * @var bool
	 * True when shipment metadata loaded
	 */
	private $meta_data_loaded = false;
	/**
	 * @var array
	 * Holds changed metadata keys. Used when saving shipment
	 */
	private $meta_data_save_keys = array();
	/**
	 * @var string
	 * Context for order metabox
	 */
	private $order_metabox_context = 'side';

	/**
	 * WPDesk_Flexible_Shipping_Shipment constructor.
	 *
	 * @param int|WP_Post|WPDesk_Flexible_Shipping_Shipment $shipment Shipment or shipment ID.
	 * @param WC_Order|null                                 $order    Order.
	 */
	public function __construct( $shipment, $order = null ) {
		if ( is_numeric( $shipment ) ) {
			$this->id   = absint( $shipment );
			$this->post = get_post( $this->id );
		} elseif ( $shipment instanceof WPDesk_Flexible_Shipping_Shipment ) {
			$this->id   = absint( $shipment->get_id() );
			$this->post = $shipment->get_post();
		} elseif ( isset( $shipment->ID ) ) {
			$this->id   = absint( $shipment->ID );
			$this->post = $shipment;
		}
		$this->order = $order;
	}

	/**
	 * @return mixed
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function get_post() {
		return $this->post;
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
	 * @return string
	 */
	public function get_order_metabox_context() {
		return $this->order_metabox_context;
	}

	/**
	 * @param string $order_metabox_context .
	 */
	public function set_order_metabox_context( $order_metabox_context ) {
		$this->order_metabox_context = $order_metabox_context;
	}

	/**
	 * @param string $meta_key .
	 */
	public function delete_meta( $meta_key ) {
		unset( $this->meta_data[ $meta_key ] );
		$this->meta_data_save_keys[ $meta_key ] = $meta_key;
	}

	/**
	 * Saves shipment data to database.
	 */
	public function save() {
		if ( $this->save_post ) {
			wp_update_post( $this->post );
			$this->fs_log( 'debug', 'Shipment post saved', array( 'post' => $this->post ) );
			$this->save_post = false;
		}
		foreach ( $this->meta_data_save_keys as $key ) {
			if ( isset( $this->meta_data[ $key ] ) ) {
				update_post_meta( $this->id, $key, $this->meta_data[ $key ][0] );
				$this->fs_log(
					'debug',
					'Shipment meta data saved',
					array(
						'key'   => $key,
						'value' => $this->meta_data[ $key ][0],
					)
				);
			} else {
				delete_post_meta( $this->id, $key );
				$this->fs_log( 'debug', 'Shipment meta data deleted', array( 'key' => $key ) );
			}
			unset( $this->meta_data_save_keys[ $key ] );
		}
		if ( $this->status_changed ) {
			do_action( 'flexible_shipping_shipment_status_updated', $this->old_status, $this->post->post_status, $this );
			$this->status_changed = false;
			$this->old_status     = null;
		}
	}

	/**
	 * Writes log message to log provided by Flexible Shipping plugin.
	 *
	 * @param mixed  $level   .
	 * @param string $message .
	 * @param array  $context .
	 */
	private function fs_log( $level, $message, array $context = array() ) {
		if ( static::$fs_logger ) {
			$context['order_id']    = $this->get_order()->get_id();
			$context['shipment_id'] = $this->get_id();
			$current_user           = wp_get_current_user();
			$context['user_id']     = $current_user->ID;
			if ( isset( $_SERVER['REQUEST_URI'] ) ) {
				$context['request_uri'] = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			}
			static::$fs_logger->log( $level, $message, $context );
		}
	}

	/**
	 * @return WC_Order
	 */
	public function get_order() {
		if ( null === $this->order ) {
			$this->order = wc_get_order( $this->post->post_parent );
		}

		return $this->order;
	}

	/**
	 * @return string|null
	 * Returns integration assigned to shipment
	 */
	public function get_integration() {
		return $this->get_meta( '_integration' );
	}

	/**
	 * @param string      $meta_key .
	 * @param null|string $default  .
	 *
	 * @return array|string|null
	 */
	public function get_meta( $meta_key = '', $default = null ) {
		$this->load_meta_data();
		if ( '' === $meta_key ) {
			return $this->meta_data;
		}
		if ( isset( $this->meta_data[ $meta_key ] ) ) {
			return maybe_unserialize( $this->meta_data[ $meta_key ][0] );
		} else {
			return $default;
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function get_meta_data() {
		$this->load_meta_data();

		return $this->meta_data;
	}

	/**
	 * Loads all meta data from postmeta
	 */
	public function load_meta_data() {
		if ( ! $this->meta_data_loaded ) {
			$this->meta_data        = get_post_meta( $this->id );
			$this->meta_data_loaded = true;
		}
	}

	/**
	 * @return string
	 * Returns URL for admin metabox for this shipment
	 */
	public function get_order_metabox_url() {
		return admin_url( 'post.php?post=' . $this->get_order_id() . '&action=edit#shipment_meta_box_' . $this->get_id() );
	}

	/**
	 * @return int
	 */
	public function get_order_id() {
		return $this->post->post_parent;
	}

	/**
	 * @return string
	 */
	public function get_status_for_shipping_column() {
		$statuses = array(
			self::STATUS_FS_NEW       => 'new',
			self::STATUS_FS_CREATED   => 'created',
			self::STATUS_FS_CONFIRMED => 'confirmed',
			self::STATUS_FS_FAILED    => 'error',
			self::STATUS_FS_MANIFEST  => 'manifest',
		);

		return $statuses[ $this->get_status() ];
	}

	/**
	 * @return string
	 */
	public function get_status() {
		return $this->post->post_status;
	}

	/**
	 * @return null|string
	 * Returns URL for label
	 */
	public function get_label_url() {
		if ( in_array(
			$this->get_status(),
			array(
				self::STATUS_FS_NEW,
				self::STATUS_FS_CREATED,
				self::STATUS_FS_FAILED,
			)
		) ) {
			return null;
		}

		$label_url_args = array(
			'action'       => 'flexible_shipping_get_label',
			'label_action' => $this->get_label_action(),
			'shipment_id'  => $this->get_id(),
		);

		$label_url = add_query_arg( $label_url_args, admin_url( 'admin-post.php' ) );

		return wp_nonce_url( $label_url, 'flexible_shipping_get_label' );
	}

	/**
	 * Should open label?
	 * By default label should be downloaded. Integration can override this method when label should be opened in browser.
	 *
	 * @return bool
	 */
	protected function get_label_action() {
		return self::LABEL_ACTION_DOWNLOAD;
	}

	/**
	 * @param string $new_status .
	 */
	public function update_status( $new_status ) {
		$this->old_status        = $this->post->post_status;
		$this->post->post_status = $new_status;
		$this->save_post         = true;
		$this->status_changed    = true;
	}

	/**
	 * @param WPDesk_Flexible_Shipping_Manifest $manifest .
	 */
	public function add_to_manifest( WPDesk_Flexible_Shipping_Manifest $manifest ) {
		$this->set_meta( '_manifest', $manifest->get_id() );
	}

	/**
	 * @param string                       $meta_key .
	 * @param int|string|array|object|null $value    .
	 */
	public function set_meta( $meta_key, $value ) {
		$this->load_meta_data();
		if ( ! isset( $this->meta_data[ $meta_key ] ) ) {
			$this->meta_data[ $meta_key ] = array();
		}
		$this->meta_data[ $meta_key ][0]        = $value;
		$this->meta_data_save_keys[ $meta_key ] = $meta_key;
	}

	/**
	 * @return bool
	 */
	public function label_avaliable() {
		if ( in_array( $this->get_status(), array( self::STATUS_FS_CONFIRMED, self::STATUS_FS_MANIFEST ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Displays shipping column in orders list view.
	 * Must be overwritten!
	 */
	public function shipping_column() {
		echo esc_html( __( 'Please override shipping_column method!', 'flexible-shipping' ) );
		echo '<pre>';
		print_r( $this->post );
		echo '</pre>';
		echo '<pre>';
		print_r( $this->meta_data );
		echo '</pre>';
	}

	/**
	 * Is status fs-new?
	 *
	 * @return bool
	 */
	public function is_status_fs_new() {
		return self::STATUS_FS_NEW === $this->get_status();
	}

	/**
	 * Is status fs-created?
	 *
	 * @return bool
	 */
	public function is_status_fs_created() {
		return self::STATUS_FS_CREATED === $this->get_status();
	}

	/**
	 * Is status fs-confirmed?
	 *
	 * @return bool
	 */
	public function is_status_fs_confirmed() {
		return self::STATUS_FS_CONFIRMED === $this->get_status();
	}

	/**
	 * Is status fs-failed?
	 *
	 * @return bool
	 */
	public function is_status_fs_failed() {
		return self::STATUS_FS_FAILED === $this->get_status();
	}

	/**
	 * Is status fs-manifest?
	 *
	 * @return bool
	 */
	public function is_status_fs_manifest() {
		return self::STATUS_FS_MANIFEST === $this->get_status();
	}

	/**
	 * Set created via checkout.
	 */
	public function set_created_via_checkout() {
		$this->set_created_via( 'checkout' );
	}

	/**
	 * Set created via.
	 *
	 * @param string $created_via Created via.
	 */
	public function set_created_via( $created_via ) {
		$this->set_meta( self::CREATED_VIA, $created_via );
	}

	/**
	 * Set created via add shipment.
	 */
	public function set_created_via_add_shipment() {
		$this->set_created_via( 'add_shipment' );
	}

	/**
	 * Get created via.
	 *
	 * @return string
	 */
	public function get_created_via() {
		return $this->get_meta( self::CREATED_VIA, self::NOT_SET );
	}

	/**
	 * Set sent via bulk.
	 */
	public function set_sent_via_bulk() {
		$this->set_sent_via( self::SENT_VIA_BULK );
	}

	/**
	 * Set sent via.
	 *
	 * @param string $sent_via .
	 */
	public function set_sent_via( $sent_via ) {
		$this->set_meta( self::SENT_VIA, $sent_via );
	}

	/**
	 * Set sent via auto.
	 */
	public function set_sent_via_auto() {
		$this->set_sent_via( self::SENT_VIA_AUTO );
	}

	/**
	 * Set sent via metabox.
	 */
	public function set_sent_via_metabox() {
		$this->set_sent_via( self::SENT_VIA_METABOX );
	}

	/**
	 * Get sent via.
	 *
	 * @return string
	 */
	public function get_sent_via() {
		return $this->get_meta( self::SENT_VIA, self::NOT_SET );
	}

	/**
	 * Get meta shipping method.
	 *
	 * @return WC_Order_Item_Shipping
	 */
	protected function get_meta_shipping_method() {
		return $this->get_meta( '_shipping_method' );
	}

}
