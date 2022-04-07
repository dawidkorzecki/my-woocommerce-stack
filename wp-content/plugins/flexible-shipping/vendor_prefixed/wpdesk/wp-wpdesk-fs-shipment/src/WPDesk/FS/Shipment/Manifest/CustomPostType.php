<?php

namespace FSVendor\WPDesk\FS\Shipment\Manifest;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
class ManifestCustomPostType implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    public function hooks()
    {
        \add_action('init', array($this, 'register_post_types'), 20);
        \add_action('admin_init', array($this, 'cancel_manifest'), 20);
        \add_action('admin_init', array($this, 'download_manifest'), 20);
        \add_action('admin_menu', array($this, 'admin_menu'), 199);
        \add_action('add_meta_boxes', array($this, 'add_meta_boxes'), 20, 2);
        \add_action('trash_shipping_manifest', array($this, 'trash_shipping_manifest'));
        \add_filter('manage_edit-shipping_manifest_columns', array($this, 'manage_edit_shipping_manifest_columns'), 11);
        \add_action('manage_shipping_manifest_posts_custom_column', array($this, 'manage_shipping_manifest_posts_custom_column'), 11);
        \add_filter('post_row_actions', array($this, 'shipping_manifest_row_actions'), 10, 2);
        \add_action('do_meta_boxes', array($this, 'hide_publish_metabox'));
        \add_filter('woocommerce_screen_ids', array($this, 'woocommerce_screen_ids'));
        \add_filter('bulk_actions-edit-shipping_manifest', array($this, 'bulk_actions_edit_shipping_manifest'));
        \add_action('restrict_manage_posts', array($this, 'restrict_manage_posts'), 9999);
        \add_filter('parse_query', array($this, 'parse_query'), 999);
    }
    /**
     * Register post types.
     */
    public function register_post_types()
    {
        if (\post_type_exists('shipping_manifest')) {
            return;
        }
        \register_post_type('shipping_manifest', array('labels' => array('name' => \__('Shipping Manifests', 'flexible-shipping'), 'singular_name' => \__('Shipping Manifest', 'flexible-shipping'), 'menu_name' => \__('Shipping Manifests', 'flexible-shipping'), 'parent_item_colon' => '', 'all_items' => \__('Shipping Manifests', 'flexible-shipping'), 'view_item' => \__('View Shipping Manifests', 'flexible-shipping'), 'add_new_item' => \__('Add new Shipping Manifest', 'flexible-shipping'), 'add_new' => \__('Add new Shipping Manifests', 'flexible-shipping'), 'edit_item' => \__('Edit Shipping Manifest', 'flexible-shipping'), 'update_item' => \__('Save Shipping Manifest', 'flexible-shipping'), 'search_items' => \__('Search Shipping Manifests', 'flexible-shipping'), 'not_found' => \__('Shipping Manifests not found', 'flexible-shipping'), 'not_found_in_trash' => \__('Shipping Manifests not found in trash', 'flexible-shipping')), 'description' => \__('Shipping Manifests.', 'flexible-shipping'), 'public' => \false, 'show_ui' => \true, 'capability_type' => 'post', 'capabilities' => array('create_posts' => \false), 'map_meta_cap' => \true, 'publicly_queryable' => \false, 'exclude_from_search' => \true, 'hierarchical' => \false, 'query_var' => \true, 'supports' => array('title'), 'has_archive' => \false, 'show_in_nav_menus' => \false, 'show_in_menu' => 'edit.php?post_type=shop_order', 'menu_icon' => 'dashicons-upload'));
    }
    public function admin_menu()
    {
        $show_in_menu = \current_user_can('manage_woocommerce') ? 'woocommerce' : \false;
        if (\apply_filters('flexible_shipping_has_manifests', \false)) {
            $slug = \add_submenu_page($show_in_menu, \__('Shipping Manifests', 'flexible-shipping'), \__('Shipping Manifests', 'flexible-shipping'), 'manage_woocommerce', 'edit.php?post_type=shipping_manifest');
        }
    }
    public function add_meta_boxes($post_type, $post)
    {
        if ($post_type == 'shipping_manifest') {
            /*
            add_meta_box(
                'shipping_manifest_meta_box',
                __('Shipping manifest data', 'flexible-shipping'),
                array( $this, 'metabox' ),
                'shipping_manifest',
                'normal',
                'high'
            );
            */
            \add_meta_box('shipping_manifest_shipments', \__('Shipments', 'flexible-shipping'), array($this, 'shipments_metabox'), 'shipping_manifest', 'normal', 'high');
        }
    }
    public function metabox()
    {
        global $post;
        echo '<pre>';
        \print_r($post);
        echo '</pre>';
        $meta_data = \get_post_meta($post->ID);
        foreach ($meta_data as $key => $val) {
            echo '<pre>';
            echo $key;
            echo ' = ';
            \print_r(\maybe_unserialize($val[0]));
            echo '</pre>';
        }
    }
    public function shipments_metabox()
    {
        global $post;
        $manifest = fs_get_manifest($post->ID);
        $shipments_array = $manifest->get_meta('_shipments', array());
        $shipments = array();
        foreach ($shipments_array as $shipment_id) {
            $shipments[] = fs_get_shipment($shipment_id);
        }
        include 'views/manifest-metabox.php';
        /*
        echo "<pre>";
        print_r($shipments);
        echo "</pre>";
        */
    }
    public function manage_edit_shipping_manifest_columns($columns)
    {
        unset($columns['title']);
        unset($columns['date']);
        unset($columns['cb']);
        $columns['manifest_date'] = \__('Date', 'flexible-shipping');
        $columns['integration'] = \__('Integration', 'flexible-shipping');
        $columns['external_number'] = \__('Number', 'flexible-shipping');
        $columns['shipment_count'] = \__('Shipments count', 'flexible-shipping');
        $columns['actions'] = \__('Actions', 'flexible-shipping');
        return $columns;
    }
    public function shipping_manifest_row_actions($actions, $post)
    {
        if ($post->post_type == 'shipping_manifest') {
            $actions = array();
        }
        return $actions;
    }
    public function manage_shipping_manifest_posts_custom_column($column)
    {
        global $post;
        global $manifest;
        $integrations = \apply_filters('flexible_shipping_integration_options', array());
        if (empty($manifest) || $manifest->get_id() != $post->ID) {
            $manifest = fs_get_manifest($post->ID);
        }
        if ($column == 'manifest_date') {
            echo $manifest->get_date();
        }
        if ($column == 'integration') {
            echo $integrations[$manifest->get_integration()];
        }
        if ($column == 'external_number') {
            $download_manifest_url = \admin_url('edit.php?post_type=shipping_manifest&flexible_shipping_download_manifest=' . $manifest->get_id() . '&nonce=' . \wp_create_nonce('flexible_shipping_download_manifest'));
            include 'views/column-number.php';
        }
        if ($column == 'shipment_count') {
            echo \count($manifest->get_meta('_shipments', array()));
        }
        if ($column == 'actions') {
            if ($manifest->get_status() != 'trash') {
                $download_manifest_url = \admin_url('edit.php?post_type=shipping_manifest&flexible_shipping_download_manifest=' . $manifest->get_id() . '&nonce=' . \wp_create_nonce('flexible_shipping_download_manifest'));
                $cancel_url = \admin_url('edit.php?post_type=shipping_manifest&flexible_shipping_cancel_manifest=' . $manifest->get_id() . '&nonce=' . \wp_create_nonce('flexible_shipping_cancel_manifest'));
                include 'views/column-actions.php';
            }
        }
    }
    public function woocommerce_screen_ids($screen_ids)
    {
        $screen_ids[] = 'edit-shipping_manifest';
        $screen_ids[] = 'shipping_manifest';
        return $screen_ids;
    }
    public function bulk_actions_edit_shipping_manifest($bulk_actions)
    {
        $bulk_actions = array();
        return $bulk_actions;
    }
    public function cancel_manifest()
    {
        if (!empty($_GET['flexible_shipping_cancel_manifest']) && !empty($_GET['nonce'])) {
            $nonce = \sanitize_text_field($_GET['nonce']);
            if (!\wp_verify_nonce($nonce, 'flexible_shipping_cancel_manifest')) {
                echo \__('Invalid nonce!', 'flexible-shipping');
                exit;
            }
            $sendback = \admin_url('edit.php?post_type=shipping_manifest');
            try {
                $shipping_manifest_id = \sanitize_key($_GET['flexible_shipping_cancel_manifest']);
                $shipping_manifest = fs_get_manifest($shipping_manifest_id);
                $shipping_manifest->cancel();
                fs_delete_manifest($shipping_manifest);
                \wp_redirect($sendback);
                exit;
            } catch (\FSVendor\WPDesk\FS\Shipment\Manifest\Exception $e) {
                \wp_redirect($sendback);
                exit;
            }
        }
    }
    public function download_manifest()
    {
        if (!empty($_GET['flexible_shipping_download_manifest']) && !empty($_GET['nonce'])) {
            $nonce = \sanitize_text_field($_GET['nonce']);
            if (!\wp_verify_nonce($nonce, 'flexible_shipping_download_manifest')) {
                echo \__('Invalid nonce!', 'flexible-shipping');
            }
            try {
                $shipping_manifest_id = \sanitize_key($_GET['flexible_shipping_download_manifest']);
                $shipping_manifest = fs_get_manifest($shipping_manifest_id);
                $manifest = $shipping_manifest->get_manifest();
                \header("Content-type: application/octet-stream");
                \header("Content-Disposition: attachment; filename=" . $manifest['file_name']);
                echo $manifest['content'];
            } catch (\FSVendor\WPDesk\FS\Shipment\Manifest\Exception $e) {
                echo $e->getMessage();
            }
            exit;
        }
    }
    public function hide_publish_metabox()
    {
        \remove_meta_box('submitdiv', 'shipping_manifest', 'side');
    }
    public function trash_shipping_manifest($post_id)
    {
        $manifest = fs_get_manifest($post_id);
        $shipments_posts = \get_posts(array('posts_per_page' => -1, 'post_type' => 'shipment', 'post_status' => 'any', 'meta_key' => '_manifest', 'meta_value' => $post_id));
        foreach ($shipments_posts as $shipment_post) {
            $shipment = fs_get_shipment($shipment_post->ID);
            $shipment->delete_meta('_manifest');
            $shipment->update_status('fs-confirmed');
            $shipment->save();
        }
        $manifest->delete_meta('_shipments');
        $manifest->save();
    }
    public function restrict_manage_posts()
    {
        global $typenow;
        if ('shipping_manifest' == $typenow) {
            $integrations = \apply_filters('flexible_shipping_integration_options', array());
            foreach ($integrations as $key => $integration) {
                if (!\class_exists('WPDesk_Flexible_Shipping_Manifest_' . $key)) {
                    unset($integrations[$key]);
                }
            }
            $integration = '';
            if (isset($_GET['flexible_shipping_integration_filter'])) {
                $integration = \sanitize_key($_GET['flexible_shipping_integration_filter']);
            }
            include 'views/filter-form.php';
        }
    }
    /**
     * @param WP_Query $query .
     */
    public function parse_query($query)
    {
        global $pagenow;
        if ('edit.php' == $pagenow && \is_admin() && isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'shipping_manifest') {
            $integration = '';
            if (isset($_GET['flexible_shipping_integration_filter'])) {
                $integration = \sanitize_key($_GET['flexible_shipping_integration_filter']);
            }
            if ($integration != '') {
                if ($integration != '') {
                    if (!isset($query->query_vars['meta_query'])) {
                        $query->query_vars['meta_query'] = array();
                    }
                    $meta_query = array();
                    $meta_query['key'] = '_integration';
                    $meta_query['value'] = $integration;
                    $query->query_vars['meta_query'][] = $meta_query;
                }
            }
        }
    }
}
