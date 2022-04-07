<?php

namespace FSVendor\WPDesk\Composer\Codeception\Commands;

use FSVendor\Symfony\Component\Console\Input\InputArgument;
use FSVendor\Symfony\Component\Console\Input\InputInterface;
use FSVendor\Symfony\Component\Console\Output\OutputInterface;
use FSVendor\Symfony\Component\Yaml\Exception\ParseException;
use FSVendor\Symfony\Component\Yaml\Yaml;
/**
 * Prepare Database for Codeception tests command.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
class PrepareWordpressForCodeception extends \FSVendor\WPDesk\Composer\Codeception\Commands\BaseCommand
{
    use LocalCodeceptionTrait;
    /**
     * Configure command.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('prepare-wordpress-for-codeception')->setDescription('Prepare wordpress installation for codeception tests.');
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
        $this->installWP($output, $configuration);
    }
    /**
     * @param OutputInterface $output
     * @param Configuration   $configuration
     */
    private function installWP(\FSVendor\Symfony\Component\Console\Output\OutputInterface $output, \FSVendor\WPDesk\Composer\Codeception\Commands\Configuration $configuration)
    {
        if (!\file_exists($configuration->getApacheDocumentRoot())) {
            \mkdir($configuration->getApacheDocumentRoot(), 0777, \true);
        }
        $dbhost = $configuration->getDbhost();
        $dbname = $configuration->getDbname();
        $dbuser = $configuration->getDbuser();
        $dbpassword = $configuration->getDbpassword();
        $url = $configuration->getWptestsIp();
        $apache_document_root = $configuration->getApacheDocumentRoot();
        $this->executeWpCliAndOutput('core download --force', $output, $apache_document_root);
        $this->executeWpCliAndOutput("core config --dbhost={$dbhost} --dbname={$dbname} --dbuser={$dbuser} --dbpass={$dbpassword}", $output, $apache_document_root);
        $this->executeWpCliAndOutput("core install --url={$url} --title=Woo-tests --admin_user=admin --admin_password=admin --admin_email=tests@wpdesk.dev --skip-email", $output, $apache_document_root);
        \copy('./vendor/wpdesk/wp-codeception/wordpress/htaccess', $apache_document_root . '/.htaccess');
        $this->executeWpCliAndOutput('rewrite structure \'/%postname%/\'', $output, $apache_document_root);
        $this->executeWpCliAndOutput('plugin install woocommerce', $output, $apache_document_root);
        $this->executeWpCliAndOutput('theme activate storefront', $output, $apache_document_root);
        $this->executeWpCliAndOutput('theme delete storefront-wpdesk-tests', $output, $apache_document_root);
        $this->executeWpCliAndOutput('theme install storefront', $output, $apache_document_root);
        $this->executeWpCliAndOutput('theme install https://gitlab.com/wpdesk/storefront-wpdesk-tests/-/archive/master/storefront-wpdesk-tests-master.zip', $output, $apache_document_root);
        \rename($apache_document_root . '/wp-content/themes/storefront-wpdesk-tests-master', $apache_document_root . '/wp-content/themes/storefront-wpdesk-tests');
        $this->executeWpCliAndOutput('theme activate storefront-wpdesk-tests', $output, $apache_document_root);
    }
}
