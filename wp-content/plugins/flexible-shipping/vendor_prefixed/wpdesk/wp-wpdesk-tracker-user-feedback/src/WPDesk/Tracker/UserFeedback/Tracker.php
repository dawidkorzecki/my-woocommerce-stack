<?php

namespace FSVendor\WPDesk\Tracker\UserFeedback;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can track user feedback.
 */
class Tracker implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var UserFeedbackData
     */
    private $user_feedback_data;
    /**
     * @var Scripts
     */
    private $scripts;
    /**
     * @var Thickbox
     */
    private $thickbox;
    /**
     * @var AjaxUserFeedbackDataHandler
     */
    private $ajax;
    /**
     * Tracker constructor.
     *
     * @param UserFeedbackData $user_feedback_data .
     * @param Scripts $scripts .
     * @param Thickbox $thickbox .
     * @param AjaxUserFeedbackDataHandler $ajax
     */
    public function __construct(\FSVendor\WPDesk\Tracker\UserFeedback\UserFeedbackData $user_feedback_data, \FSVendor\WPDesk\Tracker\UserFeedback\Scripts $scripts, \FSVendor\WPDesk\Tracker\UserFeedback\Thickbox $thickbox, \FSVendor\WPDesk\Tracker\UserFeedback\AjaxUserFeedbackDataHandler $ajax)
    {
        $this->user_feedback_data = $user_feedback_data;
        $this->scripts = $scripts;
        $this->thickbox = $thickbox;
        $this->ajax = $ajax;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('admin_print_footer_scripts-' . $this->user_feedback_data->get_hook_suffix(), [$this, 'print_user_feedback_scripts']);
        \add_action('admin_footer-' . $this->user_feedback_data->get_hook_suffix(), [$this, 'print_user_feedback_thickbox']);
        \add_action('admin_enqueue_scripts', [$this, 'enqueue_thickbox']);
        $this->ajax->hooks();
    }
    /**
     * Enqueue thickbox script and styles.
     */
    public function enqueue_thickbox()
    {
        \wp_enqueue_script('thickbox');
        \wp_enqueue_style('thickbox');
    }
    /**
     * Print user feedback scripts.
     */
    public function print_user_feedback_scripts()
    {
        echo $this->scripts->get_content();
    }
    /**
     * Print user feedback thickbox.
     */
    public function print_user_feedback_thickbox()
    {
        echo $this->thickbox->get_content();
    }
}
