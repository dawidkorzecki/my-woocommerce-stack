<?php

/**
 * Class Env
 * @package WPDesk\Composer\Codeception\Commands
 */
namespace FSVendor\WPDesk\Composer\Codeception\Commands;

use FSVendor\Dotenv\Dotenv;
/**
 * Env.
 */
class Configuration
{
    const MYSQL_IP = 'MYSQL_IP';
    const MYSQL_DBNAME = 'MYSQL_DBNAME';
    const MYSQL_DBUSER = 'MYSQL_DBUSER';
    const MYSQL_DBPASSWORD = 'MYSQL_DBPASSWORD';
    const APACHE_DOCUMENT_ROOT = 'APACHE_DOCUMENT_ROOT';
    const WOOTESTS_IP = 'WOOTESTS_IP';
    const DEPENDENT_PLUGINS_DIR = 'DEPENDENT_PLUGINS_DIR';
    /**
     * @var string
     */
    private $apache_document_root;
    /**
     * @var string
     */
    private $wptests_ip;
    /**
     * @var string
     */
    private $dbhost;
    /**
     * @var string
     */
    private $dbname;
    /**
     * @var string
     */
    private $dbuser;
    /**
     * @var string
     */
    private $dbpassword;
    /**
     * @var string
     */
    private $dependent_plugins_dir;
    /**
     * @var string
     */
    private $plugin_slug;
    /**
     * @var string
     */
    private $plugin_dir;
    /**
     * @var string
     */
    private $plugin_file;
    /**
     * @var string
     */
    private $plugin_title;
    /**
     * @var string
     */
    private $plugin_product_id;
    /**
     * @var array
     */
    private $repository_plugins;
    /**
     * @var array
     */
    private $local_plugins;
    /**
     * @var array
     */
    private $activate_plugins;
    /**
     * @var array
     */
    private $prepare_database;
    /**
     * @var array
     */
    private $theme_files;
    /**
     * Configuration constructor.
     *
     * @param $apache_document_root
     * @param $wptests_ip
     * @param $dbhost
     * @param $dbname
     * @param $dbuser
     * @param $dbpassword
     * @param $dependent_plugins_dir
     * @param $plugin_slug
     * @param $plugin_dir
     * @param $plugin_file
     * @param $plugin_title
     * @param $plugin_product_id
     * @param $repository_plugins
     * @param $local_plugins
     * @param $activate_plugins
     * @param $prepare_database
     * @param $theme_files
     */
    public function __construct($apache_document_root, $wptests_ip, $dbhost, $dbname, $dbuser, $dbpassword, $dependent_plugins_dir, $plugin_slug, $plugin_dir, $plugin_file, $plugin_title, $plugin_product_id, $repository_plugins, $local_plugins, $activate_plugins, $prepare_database, $theme_files)
    {
        $this->apache_document_root = $apache_document_root;
        $this->wptests_ip = $wptests_ip;
        $this->dbhost = $dbhost;
        $this->dbname = $dbname;
        $this->dbuser = $dbuser;
        $this->dbpassword = $dbpassword;
        $this->dependent_plugins_dir = $dependent_plugins_dir;
        $this->plugin_slug = $plugin_slug;
        $this->plugin_dir = $plugin_dir;
        $this->plugin_file = $plugin_file;
        $this->plugin_title = $plugin_title;
        $this->plugin_product_id = $plugin_product_id;
        $this->repository_plugins = $repository_plugins;
        $this->local_plugins = $local_plugins;
        $this->activate_plugins = $activate_plugins;
        $this->prepare_database = $prepare_database;
        $this->theme_files = $theme_files;
    }
    /**
     * @return string
     */
    public function getApacheDocumentRoot()
    {
        return $this->apache_document_root;
    }
    /**
     * @return string
     */
    public function getWptestsIp()
    {
        return $this->wptests_ip;
    }
    /**
     * @return string
     */
    public function getDbhost()
    {
        return $this->dbhost;
    }
    /**
     * @return string
     */
    public function getDbname()
    {
        return $this->dbname;
    }
    /**
     * @return string
     */
    public function getDbuser()
    {
        return $this->dbuser;
    }
    /**
     * @return string
     */
    public function getDbpassword()
    {
        return $this->dbpassword;
    }
    /**
     * @return string
     */
    public function getDependentPluginsDir()
    {
        return $this->dependent_plugins_dir;
    }
    /**
     * @return string
     */
    public function getPluginSlug()
    {
        return $this->plugin_slug;
    }
    /**
     * @return string
     */
    public function getPluginDir()
    {
        return $this->plugin_dir;
    }
    /**
     * @return string
     */
    public function getPluginFile()
    {
        return $this->plugin_file;
    }
    /**
     * @return string
     */
    public function getPluginTitle()
    {
        return $this->plugin_title;
    }
    /**
     * @return string
     */
    public function getPluginProductId()
    {
        return $this->plugin_product_id;
    }
    /**
     * @return array
     */
    public function getRepositoryPlugins()
    {
        return $this->repository_plugins;
    }
    /**
     * @return array
     */
    public function getLocalPlugins()
    {
        return $this->local_plugins;
    }
    /**
     * @return array
     */
    public function getActivatePlugins()
    {
        return $this->activate_plugins;
    }
    /**
     * @return array
     */
    public function getPrepareDatabase()
    {
        return $this->prepare_database;
    }
    /**
     * @return array
     */
    public function getThemeFiles()
    {
        return $this->theme_files;
    }
    /**
     * Set env variables from configuration.
     */
    public function prepareEnvForConfiguration()
    {
        $this->putEnv('WPDESK_PLUGIN_SLUG', $this->getPluginSlug());
        $this->putEnv('WPDESK_PLUGIN_FILE', $this->getPluginFile());
        $this->putEnv('WPDESK_PLUGIN_TITLE', $this->getPluginTitle());
        $this->putEnv('WPDESK_PLUGIN_PRODUCT_ID', $this->getPluginProductId());
    }
    /**
     * @param string $env_variable
     * @param string $value
     *
     * @return string
     */
    private function putEnv($env_variable, $value)
    {
        \putenv($env_variable . '=' . $value);
    }
    /**
     * @param array $configuration .
     *
     * @return Configuration
     */
    public static function createFromEnvAndConfiguration(array $configuration)
    {
        $dotenv = \FSVendor\Dotenv\Dotenv::createImmutable(\getcwd() . '/../');
        $dotenv->safeLoad();
        $apache_document_root = self::prepareFromEnv(self::APACHE_DOCUMENT_ROOT, self::prepareApacheDocumentRoot());
        $wptests_ip = self::prepareFromEnv(self::WOOTESTS_IP, 'wptests.lh');
        $dbhost = self::prepareFromEnv(self::MYSQL_IP, 'mysqltests');
        $dbname = self::prepareFromEnv(self::MYSQL_DBNAME, 'wptest');
        $dbuser = self::prepareFromEnv(self::MYSQL_DBUSER, 'mysql');
        $dbpassword = self::prepareFromEnv(self::MYSQL_DBPASSWORD, 'mysql');
        $dependent_plugins_dir = self::prepareFromEnv(self::DEPENDENT_PLUGINS_DIR, '../');
        if (isset($configuration['plugin-slug'])) {
            $plugin_slug = $configuration['plugin-slug'];
        } else {
            throw new \FSVendor\WPDesk\Composer\Codeception\Commands\SettingsException('Missing plugin-slug setting!');
        }
        if (isset($configuration['plugin-file'])) {
            $plugin_file = $configuration['plugin-file'];
        } else {
            throw new \FSVendor\WPDesk\Composer\Codeception\Commands\SettingsException('Missing plugin-file setting!');
        }
        $plugin_file_exploded = \explode('/', $plugin_file);
        $plugin_dir = $plugin_file_exploded[0];
        if (isset($configuration['plugin-title'])) {
            $plugin_title = $configuration['plugin-title'];
        } else {
            throw new \FSVendor\WPDesk\Composer\Codeception\Commands\SettingsException('Missing plugin-title setting!');
        }
        if (isset($configuration['plugin-product-id'])) {
            $plugin_product_id = $configuration['plugin-product-id'];
        } else {
            $plugin_product_id = '';
        }
        $prepare_database = array();
        if (isset($configuration['prepare-database']) && \is_array($configuration['prepare-database'])) {
            $prepare_database = $configuration['prepare-database'];
        }
        $theme_files = array();
        if (isset($configuration['theme-files']) && \is_array($configuration['theme-files'])) {
            $theme_files = $configuration['theme-files'];
        }
        $repository_plugins = self::getPluginsSettings($configuration, 'repository');
        $local_plugins = self::getPluginsSettings($configuration, 'local');
        $activate_plugins = self::getPluginsSettings($configuration, 'activate');
        return new self($apache_document_root, $wptests_ip, $dbhost, $dbname, $dbuser, $dbpassword, $dependent_plugins_dir, $plugin_slug, $plugin_dir, $plugin_file, $plugin_title, $plugin_product_id, $repository_plugins, $local_plugins, $activate_plugins, $prepare_database, $theme_files);
    }
    /**
     * @param string $env_variable .
     * @param string $default_value .
     *
     * @return string
     */
    private static function prepareFromEnv($env_variable, $default_value)
    {
        $value = \getenv($env_variable);
        $value = $value ? $value : $default_value;
        return $value;
    }
    /**
     * @return string
     */
    private static function prepareApacheDocumentRoot()
    {
        return self::isWindows() ? 'c:\\xampp\\htdocs\\wptests' : '/tmp/wptests';
    }
    /**
     * @return bool
     */
    public static function isWindows()
    {
        return \false !== \stristr(\PHP_OS, 'WIN') && \false === \stristr(\PHP_OS, 'DARWIN');
    }
    /**
     * @param array  $configuration .
     * @param string $plugins_section .
     *
     * @return array
     */
    private static function getPluginsSettings(array $configuration, $plugins_section)
    {
        if (\is_array($configuration) && isset($configuration['plugins'], $configuration['plugins'][$plugins_section]) && \is_array($configuration['plugins'][$plugins_section])) {
            return $configuration['plugins'][$plugins_section];
        }
        return array();
    }
}
