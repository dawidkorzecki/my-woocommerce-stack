<?php

namespace FSVendor\WPDesk\Nps;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can handle AJAX requests for NPS.
 */
class AjaxHandler implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    const NONCE_ACTION = 'wpdesk-nps';
    /**
     * @var string
     */
    private $user_meta_name;
    /**
     * AjaxHandler constructor.
     *
     * @param string $user_meta_name
     */
    public function __construct($user_meta_name)
    {
        $this->user_meta_name = $user_meta_name;
    }
    public function hooks()
    {
        \add_action('wp_ajax_wpdesk_nps_dismiss', array($this, 'handle_ajax_dismiss'));
        \add_action('wp_ajax_wpdesk_nps_sent', array($this, 'handle_ajax_sent'));
    }
    /**
     *
     */
    public function handle_ajax_dismiss()
    {
        \check_ajax_referer(self::NONCE_ACTION, 'security');
        $user_meta = new \FSVendor\WPDesk\Nps\UserMeta(\wp_get_current_user(), $this->user_meta_name);
        $user_meta->update_dismiss_meta();
    }
    /**
     *
     */
    public function handle_ajax_sent()
    {
        \check_ajax_referer(self::NONCE_ACTION, 'security');
        $user_meta = new \FSVendor\WPDesk\Nps\UserMeta(\wp_get_current_user(), $this->user_meta_name);
        $user_meta->update_sent_meta();
    }
}
