<?php

namespace FSVendor\WPDesk\Composer\Codeception\Commands;

use FSVendor\Composer\Downloader\FilesystemException;
use FSVendor\Symfony\Component\Console\Input\InputInterface;
use FSVendor\Symfony\Component\Console\Output\OutputInterface;
/**
 * Codeception tests creator command.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
class CreateCodeceptionTests extends \FSVendor\WPDesk\Composer\Codeception\Commands\BaseCommand
{
    use SedTrait;
    /**
     * Configure command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('create-codeception-tests')->setDescription('Create codeception tests directories and files.');
    }
    /**
     * Copy file.
     *
     * @param string $source
     * @param string $dest
     * @param string $exceptionMessage
     * @throws FilesystemException
     */
    private function copy($source, $dest, $exceptionMessage)
    {
        if (!\copy($source, $dest)) {
            throw new \FSVendor\Composer\Downloader\FilesystemException($exceptionMessage);
        }
    }
    /**
     * Copy configuration files.
     *
     * @param $codeceptionDir
     * @param $testsDir
     * @param $codeceptionYml
     * @param $envConfig
     * @param $acceptanceYml
     * @param $bootstrapScript
     * @return void
     * @throws FilesystemException
     */
    private function copyConfigurationFiles($codeceptionDir, $testsDir, $codeceptionYml, $envConfig, $acceptanceYml, $bootstrapScript)
    {
        if (!\file_exists('./' . $codeceptionYml)) {
            $this->copy('./vendor/wpdesk/wp-codeception/configuration/' . $codeceptionYml, './' . $codeceptionYml, 'Error copying codeception configuration file!');
        }
        if (!\file_exists('./' . $envConfig)) {
            $this->copy('./vendor/wpdesk/wp-codeception/configuration/' . $envConfig, './' . $envConfig, 'Error copying codeception env configuration file!');
        }
        if (\file_exists($testsDir . '/' . $acceptanceYml)) {
            \unlink($testsDir . '/' . $acceptanceYml);
        }
        $this->copy('./vendor/wpdesk/wp-codeception/configuration/' . $acceptanceYml, $testsDir . '/' . $acceptanceYml, 'Error copying codeception acceptance configuration file!');
        if (!\file_exists($codeceptionDir . '/' . $bootstrapScript)) {
            $this->copy('./vendor/wpdesk/wp-codeception/scripts/' . $bootstrapScript, $codeceptionDir . '/' . $bootstrapScript, 'Error copying codeception bootstrap script file!');
        }
        if (!@\file_exists($testsDir . '/_output')) {
            \mkdir($testsDir . '/_output', 0777, \true);
        }
        if (!\file_exists($testsDir . '/_output/.gitignore')) {
            $this->copy('./vendor/wpdesk/wp-codeception/configuration/_output.gitignore', $testsDir . '/_output/.gitignore', 'Error copying codeception acceptance output .gitignore file!');
        }
        if (!@\file_exists($testsDir . '/_support/_generated')) {
            \mkdir($testsDir . '/_support/_generated', 0777, \true);
        }
        if (!\file_exists($testsDir . '/_support/_generated/.gitignore')) {
            $this->copy('./vendor/wpdesk/wp-codeception/configuration/_generated.gitignore', $testsDir . '/_support/_generated/.gitignore', 'Error copying codeception acceptance output .gitignore file!');
        }
    }
    /**
     * Inject traits into tester class.
     *
     * @param string $testsDir
     * @return void
     */
    private function injectTraitsIntoTesterClass($testsDir)
    {
        $file_pattern = $testsDir . '/_support/AcceptanceTester.php';
        $pattern = "/use _generated\\\\AcceptanceTesterActions;/";
        $replace = "use _generated\\AcceptanceTesterActions;\n" . "\n\tuse \\WPDesk\\Codeception\\Tests\\Acceptance\\Tester\\TesterWordpressActions;" . "\n\tuse \\WPDesk\\Codeception\\Tests\\Acceptance\\Tester\\TesterWooCommerceActions;" . "\n\tuse \\WPDesk\\Codeception\\Tests\\Acceptance\\Tester\\TesterWPDeskActions;";
        $this->wpdeskSed($file_pattern, $pattern, $replace);
    }
    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws FilesystemException
     */
    protected function execute(\FSVendor\Symfony\Component\Console\Input\InputInterface $input, \FSVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $codeceptionDir = './tests/codeception';
        $testsDir = $codeceptionDir . '/tests';
        $codeceptionYml = 'codeception.dist.yml';
        $envConfig = '.env.testing';
        $acceptanceYml = 'acceptance.suite.yml';
        $bootstrapScript = 'bootstrap.sh';
        if (!@\file_exists($testsDir)) {
            \mkdir($testsDir, 0777, \true);
        }
        $this->copyConfigurationFiles($codeceptionDir, $testsDir, $codeceptionYml, $envConfig, $acceptanceYml, $bootstrapScript);
        $this->execAndOutput('./vendor/bin/codecept bootstrap ' . $codeceptionDir, $output);
        $this->execAndOutput('./vendor/bin/codecept generate:activation acceptance ActivationCest', $output);
        $this->execAndOutput('./vendor/bin/codecept generate:woocommerce acceptance WooCommerceCest', $output);
        $this->injectTraitsIntoTesterClass($testsDir);
    }
}
