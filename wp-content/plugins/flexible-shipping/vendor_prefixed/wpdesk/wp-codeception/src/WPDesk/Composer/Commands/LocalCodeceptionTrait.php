<?php

namespace FSVendor\WPDesk\Composer\Codeception\Commands;

use FSVendor\Composer\Downloader\FilesystemException;
use FSVendor\Symfony\Component\Console\Input\InputInterface;
use FSVendor\Symfony\Component\Console\Output\OutputInterface;
use FSVendor\Symfony\Component\Yaml\Exception\ParseException;
use FSVendor\Symfony\Component\Yaml\Yaml;
/**
 * Common methods for local Codeception tests.
 */
trait LocalCodeceptionTrait
{
    private function getWpDeskConfiguration()
    {
        try {
            $wpdesk_configuration = \FSVendor\Symfony\Component\Yaml\Yaml::parseFile(\getcwd() . '/tests/codeception/wpdesk.yml');
        } catch (\FSVendor\Symfony\Component\Yaml\Exception\ParseException $e) {
            $wpdesk_configuration = array();
        }
        return \FSVendor\WPDesk\Composer\Codeception\Commands\Configuration::createFromEnvAndConfiguration($wpdesk_configuration);
    }
    /**
     * @param OutputInterface $output
     * @param Configuration   $configuration
     */
    private function prepareWpConfig(\FSVendor\Symfony\Component\Console\Output\OutputInterface $output, \FSVendor\WPDesk\Composer\Codeception\Commands\Configuration $configuration)
    {
        $apache_document_root = $configuration->getApacheDocumentRoot();
        $this->executeWpCliAndOutput('config set WP_DEBUG true --raw', $output, $apache_document_root);
        $this->executeWpCliAndOutput('config set WP_DEBUG_LOG true --raw', $output, $apache_document_root);
        $this->executeWpCliAndOutput('config set WP_DEBUG_DISPLAY false --raw', $output, $apache_document_root);
        $this->executeWpCliAndOutput('config set WP_HOME http://' . $configuration->getWptestsIp(), $output, $apache_document_root);
        $this->executeWpCliAndOutput('config set WP_SITEURL http://' . $configuration->getWptestsIp(), $output, $apache_document_root);
        $this->executeWpCliAndOutput('config set WP_AUTO_UPDATE_CORE false --raw', $output, $apache_document_root);
        $this->executeWpCliAndOutput('config set AUTOMATIC_UPDATER_DISABLED false --raw', $output, $apache_document_root);
        $this->executeWpCliAndOutput('rewrite structure \'/%postname%/\'', $output, $apache_document_root);
    }
    /**
     * @param OutputInterface $output
     * @param Configuration   $configuration
     */
    private function activatePlugins(\FSVendor\Symfony\Component\Console\Output\OutputInterface $output, \FSVendor\WPDesk\Composer\Codeception\Commands\Configuration $configuration)
    {
        $this->executeWpCliAndOutput('plugin deactivate --all', $output, $configuration->getApacheDocumentRoot());
        $plugins = '';
        foreach ($configuration->getRepositoryPlugins() as $plugin) {
            $plugins .= ' ' . $plugin;
        }
        if ($plugins) {
            $this->executeWpCliAndOutput('plugin install ' . $plugins, $output, $configuration->getApacheDocumentRoot());
        }
        foreach ($configuration->getLocalPlugins() as $plugin) {
            $source = $this->preparePathForRsync($this->prepareLocalPluginDir($plugin, $configuration->getDependentPluginsDir()) . '/*', $configuration::isWindows());
            $target = $this->preparePathForRsync($this->prepareTargetDir($plugin, $configuration) . '/', $configuration::isWindows());
            $rsync = 'rsync -a ' . $source . ' ' . $target . ' --exclude=node_modules --exclude=.git --exclude=tests --exclude=.idea';
            $output->writeln($rsync);
            $this->execAndOutput($rsync, $output);
        }
        foreach ($configuration->getActivatePlugins() as $plugin) {
            $this->executeWpCliAndOutput('plugin activate ' . $plugin, $output, $configuration->getApacheDocumentRoot());
        }
        $this->executeWpCliAndOutput('plugin activate ' . $configuration->getPluginDir(), $output, $configuration->getApacheDocumentRoot());
    }
    /**
     * @param string $plugin .
     */
    private function prepareLocalPluginDir($plugin, $local_plugins_dir = \false)
    {
        if (!$local_plugins_dir) {
            $local_plugins_dir = \dirname(\getcwd());
        }
        return $this->trailingslashit($local_plugins_dir) . $plugin;
    }
    /**
     * @param $string
     *
     * @return string
     */
    private function trailingslashit($string)
    {
        return \rtrim($string, '/\\') . '/';
    }
    /**
     * @param string          $command
     * @param OutputInterface $output
     * @param string          $apache_document_root
     */
    private function executeWpCliAndOutput($command, \FSVendor\Symfony\Component\Console\Output\OutputInterface $output, $apache_document_root)
    {
        $output->write("WPCLI: {$command}\n");
        $sep = \DIRECTORY_SEPARATOR;
        $wp = "php vendor{$sep}wp-cli{$sep}wp-cli{$sep}php{$sep}boot-fs.php";
        $command = $wp . ' ' . $command . ' --allow-root --path=' . $apache_document_root;
        $this->execAndOutput($command, $output);
    }
    /**
     * @param string          $plugin_dir
     * @param OutputInterface $output
     * @param Configuration   $configuration
     * @param bool            $coverage
     */
    private function installPlugin(string $plugin_dir, \FSVendor\Symfony\Component\Console\Output\OutputInterface $output, \FSVendor\WPDesk\Composer\Codeception\Commands\Configuration $configuration, bool $coverage = \true)
    {
        $source = $this->preparePathForRsync(\getcwd() . '/*', $configuration::isWindows());
        $target = $this->preparePathForRsync($this->prepareTargetDir($plugin_dir, $configuration) . '/', $configuration::isWindows());
        $rsync = 'rsync -av ' . $source . ' ' . $target . ' --exclude=node_modules --exclude=.git --exclude=.idea --exclude=vendor --exclude=vendor_prefixed --exclude=tests/wordpress ';
        $this->execAndOutput($rsync, $output);
        \copy(\getcwd() . '/.env.testing', $this->prepareTargetDir($plugin_dir, $configuration) . '/.env.testing');
        if (!$coverage) {
            $this->execAndOutput('composer install --working-dir=' . $configuration->getApacheDocumentRoot() . '/wp-content/plugins/' . $plugin_dir, $output);
            $this->execAndOutput('composer install --no-dev --working-dir=' . $configuration->getApacheDocumentRoot() . '/wp-content/plugins/' . $plugin_dir, $output);
        } else {
            $this->execAndOutput('composer require --dev codeception/c3 --working-dir=' . $configuration->getApacheDocumentRoot() . '/wp-content/plugins/' . $plugin_dir, $output);
        }
    }
    /**
     * @param string $path
     * @param bool   $is_windows
     *
     * @return string
     */
    private function preparePathForRsync($path, $is_windows)
    {
        if ($is_windows) {
            $path = '/cygdrive/' . $path;
            $path = \str_replace(':', '', $path);
            $path = \str_replace('\\', '/', $path);
        }
        return $path;
    }
    /**
     * @param string $plugin_slug
     * @param Configuration $configuration
     *
     * @return string
     */
    private function prepareTargetDir($plugin_slug, \FSVendor\WPDesk\Composer\Codeception\Commands\Configuration $configuration)
    {
        return $configuration->getApacheDocumentRoot() . '/wp-content/plugins/' . $plugin_slug;
    }
    /**
     * @param Configuration $configuration
     * @param OutputInterface $output
     */
    private function prepareCommonWpWcConfiguration(\FSVendor\WPDesk\Composer\Codeception\Commands\Configuration $configuration, \FSVendor\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->executeWpCliAndOutput('db reset --yes', $output, $configuration->getApacheDocumentRoot());
        $this->executeWpCliAndOutput('core install --url=' . $configuration->getWptestsIp() . ' --title=Woo-tests --admin_user=admin --admin_password=admin --admin_email=grola@seostudio.pl --skip-email', $output, $configuration->getApacheDocumentRoot());
        $commands = array('theme activate storefront-wpdesk-tests', 'plugin activate woocommerce');
        $commands = \array_merge($commands, $this->prepareWcOptionsCommands(), $this->prepareTaxes(), $this->prepareShippingMethods(), $this->prepareWooCommercePages(), $this->prepareCustomer(), $this->prepareDisableRESTApiPermissions(), $this->prepareCreateProductsCommands(), $configuration->getPrepareDatabase());
        foreach ($commands as $command) {
            $this->executeWpCliAndOutput($command, $output, $configuration->getApacheDocumentRoot());
        }
    }
    /**
     * @return array
     */
    private function prepareWcOptionsCommands()
    {
        return array('option update woocommerce_admin_notices \'{}\'', 'option update storefront_nux_dismissed 1', 'option set woocommerce_store_address "al. Jana Pawła 12"', 'option set woocommerce_store_address_2 ""', 'option set woocommerce_store_city "Warszawa"', 'option set woocommerce_default_country "PL"', 'option set woocommerce_store_postalcode "22-100"', 'option set woocommerce_currency "PLN"', 'option set woocommerce_currency_pos "right_space"', 'option set woocommerce_product_type "physical"', 'option set woocommerce_allow_tracking "no"', 'option set --format=json woocommerce_stripe_settings \'{"enabled":"no","create_account":false,"email":false}\'', 'option set --format=json woocommerce_ppec_paypal_settings \'{"reroute_requests":false,"email":false}\'', 'option set --format=json woocommerce_cheque_settings \'{"enabled":"no"}\'', 'option set --format=json woocommerce_bacs_settings \'{"enabled":"no"}\'', 'option set --format=json woocommerce_cod_settings \'{"enabled":"yes"}\'', 'option set --format=json woocommerce_onboarding_profile \'{"skipped":true}\'', 'option set --format=json wc-admin-onboarding-profiler-reminder \'{"skipped":true}\'', 'option get  woocommerce_onboarding_profile', 'option set woocommerce_task_list_hidden "yes"');
    }
    /**
     * @return string[]
     */
    private function prepareTaxes()
    {
        return array('wc tax create --country="PL" --rate=23 --name=VAT --shipping=true --user=admin', 'option set woocommerce_calc_taxes "yes"');
    }
    /**
     * @return string[]
     */
    private function prepareShippingMethods()
    {
        return array('wc shipping_zone_method create 0 --method_id="flat_rate" --settings=\'{"title": "Flat rate", "cost":1, "tax_status": "taxable"}\' --enabled=true --user=admin');
    }
    /**
     * @return string[]
     */
    private function prepareWooCommercePages()
    {
        return array('wc --user=admin tool run install_pages');
    }
    /**
     * @return string[]
     */
    private function prepareCustomer()
    {
        return array('wc customer create --email=\'customer@woo.local\' --username="customer" --billing=\'{"first_name":"First","last_name":"Last","company":"WPDesk","address_1":"Street 1","city":"City","postcode": "53-030", "country": "PL", "phone": "012345678"}\' --password=\'customer\' --user=admin');
    }
    /**
     * @return string[]
     */
    private function prepareDisableRESTApiPermissions()
    {
        return array('option set wpdesk_rest_api_disable_permissions "1"');
    }
    /**
     * @return array
     */
    private function prepareCreateProductsCommands()
    {
        return array($this->prepareCreateProductCommand('100', '100'), $this->prepareCreateProductCommand('10', '10'), $this->prepareCreateProductCommand('9', '9'), $this->prepareCreateProductCommand('1', '1'), $this->prepareCreateProductCommand('09', '0.9'), $this->prepareCreateProductCommand('009', '0.09'), $this->prepareCreateProductCommand('01', '0.1'), $this->prepareCreateProductCommand('001', '0.01'), $this->prepareCreateProductCommand('0001', '0.001'), $this->prepareCreateProductCommand('00001', '0.0001'));
    }
    /**
     * @param string $name
     * @param string $price
     * @param null|string $weight
     * @param null|string $sku
     * @param null|string $dimensions
     *
     * @return string
     */
    private function prepareCreateProductCommand($name, $price, $weight = null, $sku = null, $dimensions = null)
    {
        $product_name = "Product {$name}";
        $weight = $weight ? $weight : $price;
        $sku = $sku ? $sku : 'product-' . $name;
        $dimensions = $dimensions ? $dimensions : '{"width":"' . $price . '","length":"' . $price . '","height":"' . $price . '"}';
        return "wc product create --name=\"{$product_name}\" --virtual=false --downloadable=false --type=simple --sku={$sku} --regular_price={$price} --weight={$weight} --dimensions='{$dimensions}' --user=admin";
    }
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param bool $coverage
     *
     * @throws \Composer\Downloader\FilesystemException
     */
    private function prepareLocalCodeceptionTests(\FSVendor\Symfony\Component\Console\Input\InputInterface $input, \FSVendor\Symfony\Component\Console\Output\OutputInterface $output, bool $coverage = \false)
    {
        $configuration = $this->getWpDeskConfiguration();
        $this->installPlugin($configuration->getPluginDir(), $output, $configuration, $coverage);
        $this->activatePlugins($output, $configuration);
        $this->prepareWpConfig($output, $configuration);
        $this->copyThemeFiles($configuration->getThemeFiles(), $configuration->getApacheDocumentRoot() . '/wp-content/themes/storefront-wpdesk-tests');
        $sep = \DIRECTORY_SEPARATOR;
        $codecept = "vendor{$sep}bin{$sep}codecept";
        $cleanOutput = $codecept . ' clean';
        $this->execAndOutput($cleanOutput, $output);
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
