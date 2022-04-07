<?php

namespace FSVendor\WPDesk\Composer\Codeception\Commands;

use FSVendor\Symfony\Component\Console\Input\InputArgument;
use FSVendor\Symfony\Component\Console\Input\InputInterface;
use FSVendor\Symfony\Component\Console\Output\OutputInterface;
use FSVendor\Symfony\Component\Yaml\Exception\ParseException;
use FSVendor\Symfony\Component\Yaml\Yaml;
/**
 * Codeception tests run command.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
class RunLocalCodeceptionTests extends \FSVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests
{
    use LocalCodeceptionTrait;
    /**
     * Configure command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('run-local-codeception-tests')->setDescription('Run local codeception tests.')->setDefinition(array(new \FSVendor\Symfony\Component\Console\Input\InputArgument(self::SINGLE, \FSVendor\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'Name of Single test to run.', ' ')));
    }
    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(\FSVendor\Symfony\Component\Console\Input\InputInterface $input, \FSVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $configuration = $this->getWpDeskConfiguration();
        $this->prepareWpConfig($output, $configuration);
        $singleTest = $input->getArgument(self::SINGLE);
        $sep = \DIRECTORY_SEPARATOR;
        $codecept = "vendor{$sep}bin{$sep}codecept";
        $cleanOutput = $codecept . ' clean';
        $this->execAndOutput($cleanOutput, $output);
        $runLocalTests = $codecept . ' run -f --steps --html --verbose acceptance ' . $singleTest;
        $this->execAndOutput($runLocalTests, $output);
    }
}
