<?php

namespace FSVendor\WPDesk\Tracker;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\View\Renderer\SimplePhpRenderer;
use FSVendor\WPDesk\View\Resolver\DirResolver;
class OptInPage implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var string
     */
    private $plugin_file;
    /**
     * @var string
     */
    private $plugin_slug;
    /**
     * @param string $plugin_file
     * @param string $plugin_slug
     */
    public function __construct($plugin_file, $plugin_slug)
    {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = $plugin_slug;
    }
    public function hooks()
    {
        \add_action('admin_menu', [$this, 'add_submenu_page']);
        \add_action('admin_init', [$this, 'admin_init']);
    }
    public function add_submenu_page()
    {
        \add_submenu_page(null, 'WP Desk Tracker', 'WP Desk Tracker', 'manage_options', 'wpdesk_tracker_' . $this->plugin_slug, array($this, 'wpdesk_tracker_page'));
    }
    public function wpdesk_tracker_page()
    {
        $user = \wp_get_current_user();
        $shop_url = \sanitize_text_field($_GET['shop_url']);
        $username = $user->first_name ? $user->first_name : $user->user_login;
        $allow_url = \admin_url('admin.php?wpdesk_tracker=' . $this->plugin_slug);
        $allow_url = \add_query_arg('security', \wp_create_nonce($this->plugin_slug), $allow_url);
        $skip_url = $allow_url;
        $allow_url = \add_query_arg('allow', '1', $allow_url);
        $skip_url = \add_query_arg('allow', '0', $skip_url);
        $shop = new \FSVendor\WPDesk\Tracker\Shop($shop_url);
        $terms_url = $shop->get_usage_tracking_page();
        $logo = $shop->get_shop_logo_file();
        $logo_url = \plugin_dir_url(__FILE__) . '../../../assets/images/' . $logo;
        $renderer = new \FSVendor\WPDesk\View\Renderer\SimplePhpRenderer(new \FSVendor\WPDesk\View\Resolver\DirResolver(__DIR__ . '/views'));
        echo $renderer->render('tracker-connect', ['logo_url' => \apply_filters('wpdesk/tracker/logo_url', $logo_url, $this->plugin_slug), 'username' => $username, 'allow_url' => $allow_url, 'skip_url' => $skip_url, 'terms_url' => $terms_url]);
    }
    /**
     *
     */
    public function admin_init()
    {
        if (isset($_GET['wpdesk_tracker']) && $_GET['wpdesk_tracker'] === $this->plugin_slug) {
            if (isset($_GET['allow']) && isset($_GET['security']) && \wp_verify_nonce($_GET['security'], $this->plugin_slug)) {
                $persistence = new \FSVendor\WPDesk_Tracker_Persistence_Consent();
                $persistence->set_active(\true);
                \delete_option('wpdesk_tracker_notice');
                \update_option('wpdesk_tracker_agree', '1');
                if (\wp_safe_redirect(\admin_url('plugins.php'))) {
                    exit;
                }
            }
        }
    }
}
