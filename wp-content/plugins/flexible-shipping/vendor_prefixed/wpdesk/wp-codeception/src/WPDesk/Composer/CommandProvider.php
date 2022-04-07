<?php

namespace FSVendor\WPDesk\Composer\Codeception;

use FSVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests;
use FSVendor\WPDesk\Composer\Codeception\Commands\PrepareCodeceptionDb;
use FSVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTests;
use FSVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTestsWithCoverage;
use FSVendor\WPDesk\Composer\Codeception\Commands\PrepareParallelCodeceptionTests;
use FSVendor\WPDesk\Composer\Codeception\Commands\PrepareWordpressForCodeception;
use FSVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests;
use FSVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTests;
use FSVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTestsWithCoverage;
/**
 * Links plugin commands handlers to composer.
 */
class CommandProvider implements \FSVendor\Composer\Plugin\Capability\CommandProvider
{
    public function getCommands()
    {
        return [new \FSVendor\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests(), new \FSVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests(), new \FSVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTests(), new \FSVendor\WPDesk\Composer\Codeception\Commands\RunLocalCodeceptionTestsWithCoverage(), new \FSVendor\WPDesk\Composer\Codeception\Commands\PrepareCodeceptionDb(), new \FSVendor\WPDesk\Composer\Codeception\Commands\PrepareWordpressForCodeception(), new \FSVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTests(), new \FSVendor\WPDesk\Composer\Codeception\Commands\PrepareLocalCodeceptionTestsWithCoverage(), new \FSVendor\WPDesk\Composer\Codeception\Commands\PrepareParallelCodeceptionTests()];
    }
}
