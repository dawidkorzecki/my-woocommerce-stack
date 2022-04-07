<?php

namespace FSVendor;

/**
 * @var string $element_id
 * @var string $worst_label
 * @var string $best_label
 * @var string $question
 * @var string $feedback_question
 * @var array  $feedback_options
 * @var string $disclaimer
 * @var string $send_button
 * @var string $send_button_sending
 * @var string $write_key
 * @var string $campaign_id
 * @var string $feedback_id
 * @var string $thank_you_message
 * @var string $user_id
 * @var string $ajax_url
 */
?><div
    id="<?php 
echo \esc_attr($element_id);
?>"
    worstLabel="<?php 
echo \esc_attr($worst_label);
?>"
    bestLabel="<?php 
echo \esc_attr($best_label);
?>"
    question="<?php 
echo \esc_attr($question);
?>"
    feedbackQuestion="<?php 
echo \esc_attr($feedback_question);
?>"
    feedbackOptions="<?php 
echo \esc_attr(\json_encode($feedback_options));
?>"
    disclaimer="<?php 
echo \esc_attr($disclaimer);
?>"
    sendButton="<?php 
echo \esc_attr($send_button);
?>"
    sendButtonSending="<?php 
echo \esc_attr($send_button_sending);
?>"
    writeKey="<?php 
echo \esc_attr($write_key);
?>"
    campaignId="<?php 
echo \esc_attr($campaign_id);
?>"
    feedbackId="<?php 
echo \esc_attr($feedback_id);
?>"
    thankYouMessage="<?php 
echo \esc_attr($thank_you_message);
?>"
    userId="<?php 
echo \esc_attr($user_id);
?>"
    ajaxUrl="<?php 
echo \esc_attr($ajax_url);
?>"
></div>
<?php 
