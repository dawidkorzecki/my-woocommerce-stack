<?php

namespace FSVendor\WPDesk\Nps\DisplayDecisions;

/**
 * Can determine if display NPS.
 */
interface DisplayNpsDecision
{
    /**
     * @return bool
     */
    public function should_display_nps();
}
