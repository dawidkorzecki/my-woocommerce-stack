<?php

namespace FSVendor\WPDesk\Composer\Codeception;

use FSVendor\Composer\Composer;
use FSVendor\Composer\IO\IOInterface;
use FSVendor\Composer\Plugin\Capable;
use FSVendor\Composer\Plugin\PluginInterface;
/**
 * Composer plugin.
 *
 * @package WPDesk\Composer\Codeception
 */
class Plugin implements \FSVendor\Composer\Plugin\PluginInterface, \FSVendor\Composer\Plugin\Capable
{
    /**
     * @var Composer
     */
    private $composer;
    /**
     * @var IOInterface
     */
    private $io;
    public function activate(\FSVendor\Composer\Composer $composer, \FSVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    /**
     * @inheritDoc
     */
    public function deactivate(\FSVendor\Composer\Composer $composer, \FSVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    /**
     * @inheritDoc
     */
    public function uninstall(\FSVendor\Composer\Composer $composer, \FSVendor\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    public function getCapabilities()
    {
        return [\FSVendor\Composer\Plugin\Capability\CommandProvider::class => \FSVendor\WPDesk\Composer\Codeception\CommandProvider::class];
    }
}
