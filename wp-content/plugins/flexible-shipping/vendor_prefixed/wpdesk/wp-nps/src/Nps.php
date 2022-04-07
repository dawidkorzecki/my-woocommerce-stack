<?php

namespace FSVendor\WPDesk\Nps;

use FSVendor\WPDesk\Nps\DisplayDecisions\DisplayNpsDecision;
use FSVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use FSVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
/**
 * Handles NPS functionality.
 */
class Nps implements \FSVendor\WPDesk\PluginBuilder\Plugin\HookableCollection
{
    use HookableParent;
    /**
     * @var string
     */
    private $nps_id;
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
    private $feedback_options = [];
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
    private $satismeter_write_key;
    /**
     * @var string
     */
    private $satismeter_campaign_id;
    /**
     * @var string
     */
    private $satismeter_feedback_id;
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
    private $assets_url;
    /**
     * @var string
     */
    private $scrips_version;
    /**
     * @var DisplayNpsDecision
     */
    private $display_nps_decision;
    /**
     * @var string
     */
    private $user_meta_name;
    /**
     * @var string
     */
    private $ajax_url;
    /**
     * Nps constructor.
     *
     * @param string $nps_id
     * @param string $satismeter_write_key
     * @param string $satismeter_campaign_id
     * @param string $satismeter_feedback_id
     * @param UserId $user_id_provider
     * @param string $assets_url
     * @param string $scrips_version
     * @param DisplayNpsDecision $display_nps_decision
     * @param string $user_meta_name
     * @param string $ajax_url
     */
    public function __construct($nps_id, $satismeter_write_key, $satismeter_campaign_id, $satismeter_feedback_id, $user_id_provider, $assets_url, $scrips_version, $display_nps_decision, $user_meta_name, $ajax_url)
    {
        $this->nps_id = $nps_id;
        $this->satismeter_write_key = $satismeter_write_key;
        $this->satismeter_campaign_id = $satismeter_campaign_id;
        $this->satismeter_feedback_id = $satismeter_feedback_id;
        $this->user_id_provider = $user_id_provider;
        $this->assets_url = $assets_url;
        $this->scrips_version = $scrips_version;
        $this->display_nps_decision = $display_nps_decision;
        $this->user_meta_name = $user_meta_name;
        $this->ajax_url = $ajax_url;
        $this->element_id = 'wpdesk-nps';
        $this->worst_label = \__('Not likely at all', 'flexible-shipping');
        $this->best_label = \__('Extremely likely', 'flexible-shipping');
        $this->question = \__('How likely are you to recommend our plugin to your friends?', 'flexible-shipping');
        $this->feedback_question = \__('What could we do to improve it?', 'flexible-shipping');
        $this->disclaimer = \sprintf(\__('By using the \'Send feedback\' button I hereby agree and consent to the terms of %1$sPrivacy Policy%2$s.', 'flexible-shipping'), '<a target="_blank" href="https://flexibleshipping.com/terms/privacy/">', '</a>');
        $this->send_button = \__('Send feedback', 'flexible-shipping');
        $this->send_button_sending = \__('Sending feedback', 'flexible-shipping');
        $this->thank_you_message = \__('Thank you!', 'flexible-shipping');
    }
    public function hooks()
    {
        $this->add_hookable(new \FSVendor\WPDesk\Nps\AjaxHandler($this->user_meta_name));
        $this->add_hookable(new \FSVendor\WPDesk\Nps\Assets($this->assets_url, $this->scrips_version, $this->display_nps_decision));
        $element = new \FSVendor\WPDesk\Nps\Element($this->nps_id, $this->display_nps_decision, $this->element_id, $this->worst_label, $this->best_label, $this->question, $this->feedback_question, $this->feedback_options, $this->disclaimer, $this->send_button, $this->send_button_sending, $this->satismeter_write_key, $this->satismeter_campaign_id, $this->satismeter_feedback_id, $this->thank_you_message, $this->user_id_provider, $this->ajax_url);
        $this->add_hookable($element);
        $this->hooks_on_hookable_objects();
    }
    /**
     * @param string $element_id
     *
     * @return Nps
     */
    public function set_element_id($element_id)
    {
        $this->element_id = $element_id;
        return $this;
    }
    /**
     * @param string $worst_label
     *
     * @return Nps
     */
    public function set_worst_label($worst_label)
    {
        $this->worst_label = $worst_label;
        return $this;
    }
    /**
     * @param string $best_label
     *
     * @return Nps
     */
    public function set_best_label($best_label)
    {
        $this->best_label = $best_label;
        return $this;
    }
    /**
     * @param string $question
     *
     * @return Nps
     */
    public function set_question($question)
    {
        $this->question = $question;
        return $this;
    }
    /**
     * @param string $feedback_question
     *
     * @return Nps
     */
    public function set_feedback_question($feedback_question)
    {
        $this->feedback_question = $feedback_question;
        return $this;
    }
    /**
     * @param string $disclaimer
     *
     * @return Nps
     */
    public function set_disclaimer($disclaimer)
    {
        $this->disclaimer = $disclaimer;
        return $this;
    }
    /**
     * @param string $send_button
     *
     * @return Nps
     */
    public function set_send_button($send_button)
    {
        $this->send_button = $send_button;
        return $this;
    }
    /**
     * @param string $send_button_sending
     *
     * @return Nps
     */
    public function set_send_button_sending($send_button_sending)
    {
        $this->send_button_sending = $send_button_sending;
        return $this;
    }
    /**
     * @param string $thank_you_message
     *
     * @return Nps
     */
    public function set_thank_you_message($thank_you_message)
    {
        $this->thank_you_message = $thank_you_message;
        return $this;
    }
    /**
     * @return Nps
     */
    public function clear_feedback_options()
    {
        $this->feedback_options = [];
        return $this;
    }
    /**
     * @param FeedbackOption $feedback_option
     *
     * @return Nps
     */
    public function add_feedback_option(\FSVendor\WPDesk\Nps\FeedbackOption $feedback_option)
    {
        $this->feedback_options[] = $feedback_option;
        return $this;
    }
}
