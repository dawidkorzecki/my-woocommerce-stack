<?php

namespace FSVendor\WPDesk\Codeception\Command;

use FSVendor\Codeception\Lib\Generator\Test;
/**
 * Class code for codeception example test for WP Desk plugin activation.
 *
 * @package WPDesk\Codeception\Command
 */
class AcceptanceTestGenerator extends \FSVendor\Codeception\Lib\Generator\Test
{
    protected $template = <<<EOF
<?php {{namespace}}

use WPDesk\\Codeception\\Tests\\Acceptance\\Cest\\AbstractCestForPluginActivation;

class {{name}} extends AbstractCestForPluginActivation {

\t/**
\t * Deactivate plugins before tests.
\t *
\t * @param AcceptanceTester \$i .
\t *
\t * @throws \\Codeception\\Exception\\ModuleException .
\t */
\tpublic function _before( \$i ) {
\t\t\$i->loginAsAdministrator();
\t\t\$i->amOnPluginsPage();
\t\t\$i->deactivatePlugin( \$this->getPluginSlug() );
\t\t\$i->amOnPluginsPage();
\t\t\$i->seePluginDeactivated( \$this->getPluginSlug() );
\t\t\$i->amOnPluginsPage();
\t\t\$i->deactivatePlugin( self::WOOCOMMERCE_PLUGIN_SLUG );
\t\t\$i->amOnPluginsPage();
\t\t\$i->seePluginDeactivated( self::WOOCOMMERCE_PLUGIN_SLUG );
\t}

\t/**
\t * Plugin activation.
\t *
\t * @param AcceptanceTester \$i .
\t *
\t * @throws \\Codeception\\Exception\\ModuleException .
\t */
\tpublic function pluginActivation( \$i ) {

\t\t\$i->loginAsAdministrator();

\t\t\$i->amOnPluginsPage();
\t\t\$i->seePluginDeactivated( \$this->getPluginSlug() );

\t\t// This is an example and you should change it to current plugin.
\t\t\$i->activateWPDeskPlugin(
\t\t\t\$this->getPluginSlug(),
\t\t\tarray( 'woocommerce' ),
\t\t\tarray( 'The “WooCommerce Fakturownia” plugin cannot run without WooCommerce active. Please install and activate WooCommerce plugin.' )
\t\t);

\t}
}
EOF;
}
