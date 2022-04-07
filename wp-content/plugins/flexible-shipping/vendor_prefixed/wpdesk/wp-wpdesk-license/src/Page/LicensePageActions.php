<?php

namespace FSVendor\WPDesk\License\Page;

use FSVendor\WPDesk\License\Page\License\Action\LicenseActivation;
use FSVendor\WPDesk\License\Page\License\Action\LicenseDeactivation;
use FSVendor\WPDesk\License\Page\License\Action\Nothing;
/**
 * Action factory.
 *
 * @package WPDesk\License\Page\License
 */
class LicensePageActions
{
    /**
     * Creates action object according to given param
     *
     * @param string $action .
     * @param bool   $add_settings_error .
     *
     * @return Action
     */
    public function create_action($action, $add_settings_error = \true)
    {
        if ('activate' === $action) {
            return new \FSVendor\WPDesk\License\Page\License\Action\LicenseActivation($add_settings_error);
        }
        if ('deactivate' === $action) {
            return new \FSVendor\WPDesk\License\Page\License\Action\LicenseDeactivation($add_settings_error);
        }
        return new \FSVendor\WPDesk\License\Page\License\Action\Nothing();
    }
}
