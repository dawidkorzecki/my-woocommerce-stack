<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Flexible_Pickup_Post_Type {

	private $plugin = null;

	public function __construct( Flexible_Pickup_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	public function hooks() {
		add_action( 'init', array( $this, 'register_post_types' ), 20 );

		//add_action( 'manage_posts_extra_tablenav', array( $this, 'manage_posts_extra_tablenav' ), 999 );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

		add_action( 'save_post', array( $this, 'save_post' ), 2 );

		add_filter( 'manage_edit-pickup_point_columns', array( $this, 'manage_edit_pickup_point_columns' ), 99 );

		add_action( 'manage_pickup_point_posts_custom_column', array( $this, 'manage_pickup_point_posts_custom_column' ), 10, 2 );

		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

	/**
	 * Register post types.
	 */
	public function register_post_types() {

		if ( post_type_exists( 'pickup_point' ) ) {
			return;
		}

		register_post_type( 'pickup_point',
			array(
				'labels'              => array(
					'name'               => __( 'Pickup Points', 'flexible-pickup' ),
					'singular_name'      => __( 'Pickup Point', 'flexible-pickup' ),
					'menu_name'          => __( 'Flexible Pickup', 'flexible-pickup' ),
					'parent_item_colon'  => '',
					'all_items'          => __( 'Pickup Points', 'flexible-pickup' ),
					'view_item'          => __( 'View Pickup Points', 'flexible-pickup' ),
					'add_new_item'       => __( 'Add new Pickup Point', 'flexible-pickup' ),
					'add_new'            => __( 'Add new Pickup Point', 'flexible-pickup' ),
					'edit_item'          => __( 'Edit Pickup Point', 'flexible-pickup' ),
					'update_item'        => __( 'Save Pickup Point', 'flexible-pickup' ),
					'search_items'       => __( 'Seach Pickup Points', 'flexible-pickup' ),
					'not_found'          => __( 'Pickup Point not found', 'flexible-pickup' ),
					'not_found_in_trash' => __( 'Pickup Point not found in trash', 'flexible-pickup' )
				),
				'description'         => __( 'Pickup Points.', 'flexible-pickup' ),
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => 'post',
				'capabilities'        => array(),
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'hierarchical'        => false,
				'query_var'           => true,
				'supports'            => array( 'title', 'editor', 'points_group' ),
				'has_archive'         => false,
				'show_in_nav_menus'   => true,
				'menu_icon'           => 'dashicons-upload',
//                'show_in_menu'		  => 'flexible-pickup',
			)
		);


		$labels = array(
			'name'              => _x( 'Groups', 'taxonomy general name', 'flexible-pickup' ),
			'singular_name'     => _x( 'Group', 'taxonomy singular name', 'flexible-pickup' ),
			'search_items'      => __( 'Search Groups', 'flexible-pickup' ),
			'all_items'         => __( 'All Groups', 'flexible-pickup' ),
			'parent_item'       => __( 'Parent Group', 'flexible-pickup' ),
			'parent_item_colon' => __( 'Parent Group:', 'flexible-pickup' ),
			'edit_item'         => __( 'Edit Group', 'flexible-pickup' ),
			'update_item'       => __( 'Update Group', 'flexible-pickup' ),
			'add_new_item'      => __( 'Add New Group', 'flexible-pickup' ),
			'new_item_name'     => __( 'New Group Name', 'flexible-pickup' ),
			'menu_name'         => __( 'Pickup Points Groups', 'flexible-pickup' ),
		);

		register_taxonomy(
			'fp_points_group',
			array( 'pickup_point' ),
			array(
				'labels'            => $labels,
				'public'            => false,
				'show_admin_column' => true,
				'show_ui'           => true,
				'rewrite'           => false,
				'hierarchical'      => true,
				'meta_box_cb'       => 'post_categories_meta_box'
			)
		);

	}

	public function manage_posts_extra_tablenav() {
		?>
        <div class="flexible-pickup-import-link">
            <span class=""><a
                        href="<?php echo admin_url( 'admin.php?import=flexible-pickup' ); ?>"><?php _e( 'Import Pickup Points from CSV' ); ?></a></span>
        </div>
		<?php
	}

	public function add_meta_boxes() {
		add_meta_box(
			'pickup_point', __( 'Point details', 'flexible-pickup' ),
			array( $this, 'pickup_point_meta_box_output' ),
			'pickup_point',
			'advanced',
			'default'
		);
	}


	public function pickup_point_meta_box_output( $post ) {
		wp_nonce_field( 'pickup_point_meta_box', 'pickup_point_meta_box' );
		include 'views/pickup-point-settings.php';
	}

	public function save_post( $post_id ) {

		if ( ! isset( $_POST['pickup_point_meta_box'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['pickup_point_meta_box'], 'pickup_point_meta_box' ) ) {
			return;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['_company'] ) ) {
			$_company = sanitize_text_field( $_POST['_company'] );
			update_post_meta( $post_id, '_company', $_company );
		}

		if ( isset( $_POST['_address'] ) ) {
			$_address = sanitize_text_field( $_POST['_address'] );
			update_post_meta( $post_id, '_address', $_address );
		}

		if ( isset( $_POST['_address_2'] ) ) {
			$_address_2 = sanitize_text_field( $_POST['_address_2'] );
			update_post_meta( $post_id, '_address_2', $_address_2 );
		}

		if ( isset( $_POST['_postal_code'] ) ) {
			$_postal_code = sanitize_text_field( $_POST['_postal_code'] );
			update_post_meta( $post_id, '_postal_code', $_postal_code );
		}

		if ( isset( $_POST['_city'] ) ) {
			$_city = sanitize_text_field( $_POST['_city'] );
			update_post_meta( $post_id, '_city', $_city );
		}

		if ( isset( $_POST['_cost'] ) ) {
			$_cost = sanitize_text_field( $_POST['_cost'] );
			update_post_meta( $post_id, '_cost', $_cost );
		}

		do_action( 'flexible_pickup_save_post', $post_id  );

	}

	public function manage_edit_pickup_point_columns( $columns ) {
		$ret = array();
		foreach ( $columns as $key => $column ) {
			if ( $key == 'taxonomy-fp_points_group' ) {
				$ret['address'] = __( 'Address', 'flexible-pickup' );
			}
			$ret[ $key ] = $column;
		}
		unset( $ret['date'] );

		return apply_filters( 'flexible_pickup_pickup_point_columns', $ret );
	}

	public function manage_pickup_point_posts_custom_column( $column, $post_id ) {
		global $post;
		switch ( $column ) {
			case 'address' :
				$post_meta = get_post_meta( $post_id );
				if ( !empty( $post_meta['_company'] ) && !empty( $post_meta['_company'][0] ) ) {
					echo $post_meta['_company'][0];
					echo '<br/>';
				}
			    if ( !empty( $post_meta['_address'] ) && !empty( $post_meta['_address'][0] ) ) {
			        echo $post_meta['_address'][0];
				    echo '<br/>';
                }
				if ( !empty( $post_meta['_address_2'] ) && !empty( $post_meta['_address_2'][0] ) ) {
					echo $post_meta['_address_2'][0];
					echo '<br/>';
				}
				$city = '';
				if ( !empty( $post_meta['_postal_code'] ) && !empty( $post_meta['_postal_code'][0] ) ) {
					$city .= $post_meta['_postal_code'][0];
				}
				if ( $city != '' ) {
				    $city .= ' ';
                }
				if ( !empty( $post_meta['_city'] ) && !empty( $post_meta['_city'][0] ) ) {
					$city .= $post_meta['_city'][0];
				}
				if ( $city != '' ) {
					echo $city;
					echo '<br/>';
                }
				break;
			case 'lat_lng' :
				$post_meta = get_post_meta( $post_id );
				if ( !empty( $post_meta['_lat'] ) && !empty( $post_meta['_lat'][0] ) ) {
					echo $post_meta['_lat'][0];
					echo '<br/>';
				}
				if ( !empty( $post_meta['_lng'] ) && !empty( $post_meta['_lng'][0] ) ) {
					echo $post_meta['_lng'][0];
					echo '<br/>';
				}
			    break;
			case 'cod' :
				$post_meta = get_post_meta( $post_id, '_cod', true );
				if ( $post_meta == '' ) {
					echo __( 'No', 'flexible-pickup' );
				}
				else {
				    if ( $post_meta == 'no' ) {
					    echo __( 'No', 'flexible-pickup' );
                    }
                    else {
	                    echo __( 'Yes', 'flexible-pickup' );
                    }
                }
				break;
			default :
				break;
		}
	}

	public function pre_get_posts( WP_Query $query ) {
	    if ( is_admin() && isset( $query->query['post_type'] ) && $query->query['post_type'] == 'pickup_point' && !empty( $query->query['s'] ) ) {
		    add_filter( 'posts_where', array( $this, 'posts_where' ), 10, 2 );
        }
        return $query;
    }

    public function posts_where( $where, $query ) {
	    global $wpdb;
	    remove_filter( 'posts_where', array( $this, 'posts_where' ), 10, 2 );
	    $where = str_replace( "({$wpdb->posts}.post_title LIKE ", "({$wpdb->posts}.ID in (SELECT ID FROM {$wpdb->postmeta} pms where pms.post_id = {$wpdb->posts}.ID AND pms.meta_value LIKE '%" . $query->query['s'] . "%')) OR ({$wpdb->posts}.post_title LIKE", $where );
	    return $where;
    }

}