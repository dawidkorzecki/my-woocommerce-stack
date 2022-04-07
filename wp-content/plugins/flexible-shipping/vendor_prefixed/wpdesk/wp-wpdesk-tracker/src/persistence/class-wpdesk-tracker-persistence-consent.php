<?php

namespace FSVendor;

/**
 * Can handle tracker consent settings.
 */
class WPDesk_Tracker_Persistence_Consent
{
    /**
     * Option name with settings.
     * @var string
     */
    private $option_name = 'wpdesk_helper_options';
    /**
     * Checks if consent of tracking is active.
     *
     * @return bool Consent status.
     */
    public function is_active()
    {
        $options = $this->get_helper_options();
        return isset($options['wpdesk_tracker_agree']) && $options['wpdesk_tracker_agree'] === '1';
    }
    /**
     * @param bool $active
     */
    public function set_active($active)
    {
        $options = $this->get_helper_options();
        $options['wpdesk_tracker_agree'] = $active ? '1' : '0';
        \update_option('wpdesk_helper_options', $options);
    }
    /**
     * @return array
     */
    private function get_helper_options()
    {
        $options = \get_option($this->option_name, array());
        if (!\is_array($options)) {
            $options = array();
        }
        return $options;
    }
}
