<?php

namespace FSVendor\WPDesk\Beacon;

/**
 * Can display HelpScout Beacon without confirmation. For more info check https://secure.helpscout.net/settings/beacons/
 */
class BeaconPro extends \FSVendor\WPDesk\Beacon\Beacon
{
    /**
     * Beacon constructor.
     *
     * @param string $beacon_id .
     * @param BeaconShouldShowStrategy $strategy When to display beacon.
     * @param string $assets_url With ending /
     */
    public function __construct($beacon_id, \FSVendor\WPDesk\Beacon\BeaconShouldShowStrategy $strategy, $assets_url, $beacon_search_elements_class = 'hs-beacon-search')
    {
        parent::__construct($beacon_id, $strategy, $assets_url, $beacon_search_elements_class);
        $this->confirmation_message = '';
    }
}
