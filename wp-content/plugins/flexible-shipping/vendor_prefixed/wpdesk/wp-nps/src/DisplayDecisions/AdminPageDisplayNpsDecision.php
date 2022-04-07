<?php

namespace FSVendor\WPDesk\Nps\DisplayDecisions;

/**
 * Can determine if display NPS from request parameters.
 */
class AdminPageDisplayNpsDecision implements \FSVendor\WPDesk\Nps\DisplayDecisions\DisplayNpsDecision
{
    /**
     * @var array<string, string>
     */
    private $get_parameters;
    /**
     * AdminPageDetector constructor.
     *
     * @param string[] $get_parameters
     */
    public function __construct(array $get_parameters)
    {
        $this->get_parameters = $get_parameters;
    }
    /**
     * @return bool
     */
    public function should_display_nps()
    {
        foreach ($this->get_parameters as $parameter => $value) {
            if (!isset($_GET[$parameter]) || $_GET[$parameter] !== $value) {
                return \false;
            }
        }
        return \true;
    }
}
