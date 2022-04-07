<?php

namespace FSVendor\WPDesk\Nps;

use FSVendor\WPDesk\Nps\DisplayDecisions\DisplayNpsDecision;
use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
/**
 * Can render NPS element.
 */
class Element implements \FSVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    /**
     * @var string
     */
    private $nps_id;
    /**
     * @var DisplayNpsDecision
     */
    private $display_nps_decision;
    /**
     * @var string
     */
    private $element_id;
    /**
     * @var string
     */
    private $worst_label;
    /**
     * @var string
     */
    private $best_label;
    /**
     * @var string
     */
    private $question;
    /**
     * @var string
     */
    private $feedback_question;
    /**
     * @var array
     */
    private $feedback_options;
    /**
     * @var string
     */
    private $disclaimer;
    /**
     * @var string
     */
    private $send_button;
    /**
     * @var string
     */
    private $send_button_sending;
    /**
     * @var string
     */
    private $write_key;
    /**
     * @var string
     */
    private $campaign_id;
    /**
     * @var string
     */
    private $feedback_id;
    /**
     * @var string
     */
    private $thank_you_message;
    /**
     * @var UserId
     */
    private $user_id_provider;
    /**
     * @var string
     */
    private $ajax_url;
    /**
     * Element constructor.
     *
     * @param string $nps_id
     * @param DisplayNpsDecision $display_nps_decision
     * @param string $element_id
     * @param string $worst_label
     * @param string $best_label
     * @param string $question
     * @param string $feedback_question
     * @param array  $feedback_options
     * @param string $disclaimer
     * @param string $send_button
     * @param string $send_button_sending
     * @param string $write_key
     * @param string $campaign_id
     * @param string $feedback_id
     * @param string $thank_you_message
     * @param UserId $user_id_provider
     * @param string $ajax_url
     */
    public function __construct($nps_id, $display_nps_decision, $element_id, $worst_label, $best_label, $question, $feedback_question, $feedback_options, $disclaimer, $send_button, $send_button_sending, $write_key, $campaign_id, $feedback_id, $thank_you_message, $user_id_provider, $ajax_url)
    {
        $this->nps_id = $nps_id;
        $this->display_nps_decision = $display_nps_decision;
        $this->element_id = $element_id;
        $this->worst_label = $worst_label;
        $this->best_label = $best_label;
        $this->question = $question;
        $this->feedback_question = $feedback_question;
        $this->feedback_options = $feedback_options;
        $this->disclaimer = $disclaimer;
        $this->send_button = $send_button;
        $this->send_button_sending = $send_button_sending;
        $this->write_key = $write_key;
        $this->campaign_id = $campaign_id;
        $this->feedback_id = $feedback_id;
        $this->thank_you_message = $thank_you_message;
        $this->user_id_provider = $user_id_provider;
        $this->ajax_url = $ajax_url;
    }
    public function hooks()
    {
        \add_action('admin_footer', array($this, 'display_nps_element'));
    }
    /**
     * @internal
     */
    public function display_nps_element()
    {
        if ($this->display_nps_decision->should_display_nps()) {
            $nps_id = $this->nps_id;
            $element_id = $this->element_id;
            $worst_label = $this->worst_label;
            $best_label = $this->best_label;
            $question = $this->question;
            $feedback_question = $this->feedback_question;
            $feedback_options = $this->feedback_options;
            $disclaimer = $this->disclaimer;
            $send_button = $this->send_button;
            $send_button_sending = $this->send_button_sending;
            $write_key = $this->write_key;
            $campaign_id = $this->campaign_id;
            $feedback_id = $this->feedback_id;
            $thank_you_message = $this->thank_you_message;
            $user_id = $this->user_id_provider->get_id();
            $ajax_url = $this->ajax_url . '?security=' . \wp_create_nonce(\FSVendor\WPDesk\Nps\AjaxHandler::NONCE_ACTION);
            include __DIR__ . '/views/element.php';
        }
    }
}
