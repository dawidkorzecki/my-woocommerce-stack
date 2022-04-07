<?php

/**
 * Class WPDesk_Flexible_Shipping_Shipment
 */
abstract class WPDesk_Flexible_Shipping_Manifest {

    /**
     * @var int
     */
    private $id;

    /**
     * @var WP_Post
     * Post assigned to manifest
     */
    private $post;

    /**
     * @var bool
     * True if assigned post ich changed. Used when saving post
     */
    private $save_post = false;

    /**
     * @var null
     * Holds old status when manifest status is changed
     */
    private $old_status = null;

    /**
     * @var bool
     * True when status changing
     */
    private $status_changed = false;

    /**
     * @var array
     * Manifest metadata (from postmeta table)
     */
    private $meta_data = array();

    /**
     * @var bool
     * True when manifest metadata loaded
     */
    private $meta_data_loaded = false;

    /**
     * @var array
     * Holds changed metadata keys. Used when saving manifest
     */
    private $meta_data_save_keys = array();

	/**
	 * WPDesk_Flexible_Shipping_Manifest constructor.
	 *
	 * @param int|WPDesk_Flexible_Shipping_Manifest|WP_Post $manifest Manifest.
	 * @param WC_Order|null                                 $order Order.
	 */
	public function __construct( $manifest, $order = null ) {
		if ( is_numeric( $manifest ) ) {
			$this->id   = absint( $manifest );
			$this->post = get_post( $this->id );
		} elseif ( $manifest instanceof WPDesk_Flexible_Shipping_Manifest ) {
			$this->id   = absint( $manifest->get_id() );
			$this->post = $manifest->get_post();
		} elseif ( isset( $manifest->ID ) ) {
			$this->id   = absint( $manifest->ID );
			$this->post = $manifest;
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
     * @param string $meta_key
     * @param null|string $default
     * @return array|string|null
     */
    public function get_meta( $meta_key = '', $default = null ) {
        $this->load_meta_data();
        if ( $meta_key == '' ) {
            return $this->meta_data;
        }
        if ( isset( $this->meta_data[$meta_key] ) ) {
            return maybe_unserialize( $this->meta_data[$meta_key][0] );
        }
        else {
            return $default;
        }
    }

    /**
     * @param string $meta_key
     * @param int|string|array|object|null $value
     */
    public function set_meta( $meta_key, $value ) {
        $this->load_meta_data();
        if ( !isset( $this->meta_data[$meta_key] ) ) {
            $this->meta_data[$meta_key] = array();
        }
        $this->meta_data[$meta_key][0] = $value;
        $this->meta_data_save_keys[$meta_key] = $meta_key;
    }

    /**
     * @param string $meta_key
     */
    public function delete_meta( $meta_key ) {
        unset( $this->meta_data[$meta_key] );
        $this->meta_data_save_keys[$meta_key] = $meta_key;
    }

    /**
     * Saves manifest data to database.
     */
    public function save() {
        if ( $this->save_post ) {
            wp_update_post($this->post);
            $this->save_post = false;
        }
        foreach ( $this->meta_data_save_keys as $key ) {
            if ( isset( $this->meta_data[$key] ) ) {
                update_post_meta( $this->id, $key, $this->meta_data[$key][0] );
            }
            else {
                delete_post_meta( $this->id, $key );
            }
            unset( $this->meta_data_save_keys[$key] );
        }
        if ( $this->status_changed ) {
            do_action( 'flexible_shipping_manifest_status_updated', $this->old_status, $this->post->post_status, $this );
            $this->status_changed = false;
            $this->old_status = null;
        }
    }

    /**
     * Loads all meta data from postmeta
     */
    public function load_meta_data() {
        if ( !$this->meta_data_loaded ) {
            $this->meta_data = get_post_meta( $this->id );
            $this->meta_data_loaded = true;
        }
    }

    /**
     * @return array|null
     * Returns integration assigned to manifest
     */
    public function get_integration() {
        return $this->get_meta( '_integration' );
    }

    /**
     * @return string
     */
    public function get_status() {
        return $this->post->post_status;
    }

    public function get_date() {
        return $this->post->post_date;
    }

    /**
     * @param string $new_status
     */
    public function update_status( $new_status ) {
        $this->old_status = $this->post->post_status;
        $this->post->post_status = $new_status;
        $this->save_post = true;
        $this->status_changed = true;
    }


    /**
     * @param mixed $shipments
     */
    public function add_shipments( $shipments ) {
        if ( !is_array( $shipments ) ) {
            $shipments = array( $shipments );
        }
        $shipments_ids = $this->get_meta( '_shipments', array() );
        foreach ( $shipments as $shipment ) {
            /* @var WPDesk_Flexible_Shipping_Shipment $shipment */
            $shipment->add_to_manifest( $this );
            $shipment->save();
            $shipments_ids[] = $shipment->get_id();
        }
        $this->set_meta( '_shipments', $shipments_ids );
        $this->save();
    }

}
