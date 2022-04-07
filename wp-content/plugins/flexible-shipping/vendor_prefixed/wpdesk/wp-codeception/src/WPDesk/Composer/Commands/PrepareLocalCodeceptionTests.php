<?php

namespace FSVendor\WPDesk\Composer\Codeception\Commands;

use FSVendor\Composer\Downloader\FilesystemException;
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
class PrepareLocalCodeceptionTests extends \FSVendor\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests
{
    use LocalCodeceptionTrait;
    /**
     * Configure command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('prepare-local-codeception-tests')->setDescription('Prepare local codeception tests.');
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
        $this->prepareLocalCodeceptionTests($input, $output, \false);
    }
    /**
     * @param array $theme_files
     * @param $theme_folder
     *
     * @throws FilesystemException
     */
    private function copyThemeFiles(array $theme_files, $theme_folder)
    {
        foreach ($theme_files as $theme_file) {
            if (!\copy($theme_file, $this->trailingslashit($theme_folder) . \basename($theme_file))) {
                throw new \FSVendor\Composer\Downloader\FilesystemException('Error copying theme file: ' . $theme_file);
            }
        }
    }
}
