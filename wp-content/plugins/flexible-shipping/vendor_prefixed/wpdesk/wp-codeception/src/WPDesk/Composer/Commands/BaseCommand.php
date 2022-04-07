<?php

namespace FSVendor\WPDesk\Composer\Codeception\Commands;

use FSVendor\Composer\Command\BaseCommand as CodeceptionBaseCommand;
use FSVendor\Symfony\Component\Console\Output\OutputInterface;
/**
 * Base for commands - declares common methods.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
abstract class BaseCommand extends \FSVendor\Composer\Command\BaseCommand
{
    /**
     * @param string $command
     * @param OutputInterface $output
     */
    protected function execAndOutput($command, \FSVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        \passthru($command);
    }
}
