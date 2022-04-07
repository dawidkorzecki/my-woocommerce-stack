<?php

namespace FSVendor;

/**
 * @var $thickbox_id string
 * @var $thickbox_title string
 * @var $thickbox_heading string
 * @var $thickbox_question string
 * @var $thickbox_feedback_options \WPDesk\Tracker\UserFeedback\UserFeedbackOption[]
 * @var $ajax_action string
 * @var $ajax_nonce string
 * @var $thickbox_all_options int
 */
if (!\defined('ABSPATH')) {
    exit;
}
?><div id="<?php 
echo $thickbox_id;
?>" style="display:none;">
	<?php 
if ($thickbox_heading) {
    ?>
        <h4><?php 
    echo \esc_html($thickbox_heading);
    ?></h4>
    <?php 
}
?>
	<div class="wpdesk_tracker_user_feedback <?php 
echo $thickbox_id;
?>">
		<div class="body">
			<div class="panel active" data-panel-id="options">
				<h2><strong><?php 
echo \esc_html($thickbox_question);
?></strong></h2>
				<ul class="reasons-list">
                    <?php 
foreach ($thickbox_feedback_options as $feedback_option) {
    ?>
                        <li class="reason <?php 
    echo \esc_attr($feedback_option->has_additional_info() ? 'has-input' : '');
    ?>">
                            <label style="<?php 
    echo \esc_attr($feedback_option->get_option_text() === '' ? 'display: none' : '');
    ?>">
                                <span>
                                    <input
                                        type="radio"
                                        name="selected_option"
                                        value="<?php 
    echo \esc_attr($feedback_option->get_option_name());
    ?>"
                                        data-show="<?php 
    echo \esc_attr($feedback_option->get_option_name());
    ?>"
                                        <?php 
    echo \esc_attr(1 === $thickbox_all_options ? 'checked' : '');
    ?>
                                    />
                                </span>
                                <span><?php 
    echo \esc_html($feedback_option->get_option_text());
    ?></span>
                            </label>
                            <?php 
    if ($feedback_option->has_additional_info()) {
        ?>
                                <div class="<?php 
        echo \esc_attr($feedback_option->get_option_name());
        ?>" class="option-input" style="<?php 
        echo \esc_attr(1 !== $thickbox_all_options ? 'display: none' : '');
        ?>">
                                    <textarea
                                        class="additional-info <?php 
        echo \esc_attr($feedback_option->get_option_text() === '' ? 'no_question' : '');
        ?>"
                                        name="<?php 
        echo \esc_attr($feedback_option->get_option_name());
        ?>_input"
                                        placeholder="<?php 
        echo \esc_attr($feedback_option->get_additional_info_placeholder());
        ?>"></textarea>
                                </div>
                            <?php 
    }
    ?>
                        </li>
                    <?php 
}
?>
				</ul>
			</div>
		</div>
		<div class="footer">
			<a href="#" class="button button-secondary button-close tracker-button-close"><?php 
\_e('Cancel', 'flexible-shipping');
?></a>
			<a href="#" class="button button-primary <?php 
echo \esc_attr(1 === $thickbox_all_options ? 'allow-proceed' : 'skip-proceed');
?>"><?php 
echo \esc_html(1 === $thickbox_all_options ? $button_send_text : \__('Skip', 'flexible-shipping'));
?></a>
		</div>
	</div>
</div><?php 
