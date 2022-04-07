<?php

namespace FSVendor\WPDesk\Nps\DisplayDecisions;

use FSVendor\WPDesk\Nps\UserMeta;
/**
 * Can determine if display NPS for user.
 */
class UserDisplayNpsDecision implements \FSVendor\WPDesk\Nps\DisplayDecisions\DisplayNpsDecision
{
    /**
     * @var string
     */
    private $user_meta_name;
    /**
     * @var int
     */
    private $max_dismiss_count;
    /**
     * @var int
     */
    private $next_display_time_period;
    /**
     * UserDisplayNpsDecision constructor.
     *
     * @param string $user_meta_name
     * @param int    $max_dismiss_count
     * @param int    $next_display_time_period
     */
    public function __construct($user_meta_name, $max_dismiss_count = 2, $next_display_time_period = WEEK_IN_SECONDS)
    {
        $this->user_meta_name = $user_meta_name;
        $this->max_dismiss_count = $max_dismiss_count;
        $this->next_display_time_period = $next_display_time_period;
    }
    /**
     * @return bool
     */
    public function should_display_nps()
    {
        $current_user = \wp_get_current_user();
        $user_meta = new \FSVendor\WPDesk\Nps\UserMeta($current_user, $this->user_meta_name);
        if (empty($user_meta->get_meta_value())) {
            return \true;
        }
        if ('yes' === $user_meta->get_from_meta_value(\FSVendor\WPDesk\Nps\UserMeta::SENT, 'no')) {
            return \false;
        }
        $dismiss_count = (int) $user_meta->get_from_meta_value(\FSVendor\WPDesk\Nps\UserMeta::DISMISS_COUNT, 0);
        if ($dismiss_count < $this->max_dismiss_count) {
            $dismiss_time = (int) $user_meta->get_from_meta_value(\FSVendor\WPDesk\Nps\UserMeta::DISMISS_TIME, 0);
            if (0 === $dismiss_time || $dismiss_time + $this->next_display_time_period < \current_time('timestamp')) {
                return \true;
            }
        }
        return \false;
    }
}
