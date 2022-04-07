<?php

namespace FSVendor\WPDesk\Nps\DisplayDecisions;

/**
 * Can determine if display NPS for location and user.
 */
class DisplayNpsLocationsAndUserDecisions implements \FSVendor\WPDesk\Nps\DisplayDecisions\DisplayNpsDecision
{
    /**
     * @var DisplayNpsDecision[]
     */
    private $location_display_decisions;
    /**
     * @var DisplayNpsDecision[]
     */
    private $user_display_decisions;
    /**
     * DisplayNpsDecisions constructor.
     *
     * @param DisplayNpsDecision[] $location_display_decisions
     * @param DisplayNpsDecision[] $user_display_decisions
     */
    public function __construct(array $location_display_decisions, array $user_display_decisions)
    {
        $this->location_display_decisions = $location_display_decisions;
        $this->user_display_decisions = $user_display_decisions;
    }
    /**
     * @return bool
     */
    public function should_display_nps()
    {
        return $this->should_display_nps_for_location() && $this->should_display_nps_for_user();
    }
    /**
     * @return bool
     */
    private function should_display_nps_for_location()
    {
        foreach ($this->location_display_decisions as $display_nps_decision) {
            if ($display_nps_decision->should_display_nps()) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @return bool
     */
    private function should_display_nps_for_user()
    {
        foreach ($this->user_display_decisions as $display_nps_decision) {
            if ($display_nps_decision->should_display_nps()) {
                return \true;
            }
        }
        return \false;
    }
}
