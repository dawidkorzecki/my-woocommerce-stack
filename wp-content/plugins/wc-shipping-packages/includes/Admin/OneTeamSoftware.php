<?php
/*********************************************************************/
/* PROGRAM    (C) 2021 FlexRC                                        */
/* PROPERTY   3-7170 Ash Cres                                        */
/* OF         Vancouver, BC V6P3K7                                   */
/*            CANADA                                                 */
/*            Voice (604) 800-7879                                   */
/*********************************************************************/

namespace OneTeamSoftware\WooCommerce\Admin;

//declare(strict_types=1);

defined('ABSPATH') || exit;

// make sure that we will include shared class only once
if (!class_exists(__NAMESPACE__ . '\\OneTeamSoftware')):

class OneTeamSoftware
{
	protected static $instance = null;
	protected $mainMenuId;
	protected $author;
	protected $freePluginsApiUrl;
	protected $paidPluginsApiUrl;
	protected $isRegistered;

	static public function instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new OneTeamSoftware();
		}

		return self::$instance;
	}

	public function __construct()
	{
		$this->mainMenuId = 'oneteamsoftware';
		$this->author = 'oneteamsoftware';
		$this->freePluginsApiUrl = 'http://api.wordpress.org/plugins/info/1.0/';
		// NOT IN USE, LEFT FOR THE REFERENCE ONLY
		$this->paidPluginsApiUrl = 'https://1teamsoftware.com/wp-json/wp-plugin-life-server/plugins';
		$this->isRegistered = false;
	}

	public function register()
	{
		if ($this->isRegistered) {
			return;
		}

		$this->isRegistered = true;
		
		add_action('admin_menu', array($this, 'onAdminMenu'), 1);
		add_action('admin_init', array($this, 'onEnqueueScripts'));
	}

	public function onAdminMenu()
	{
		add_menu_page(
			'1TeamSoftware',
			'1TeamSoftware',
			'manage_options',
			$this->mainMenuId,
			array(&$this, 'display'),
			plugins_url('assets/images/1TeamSoftware-icon.png', dirname(dirname(str_replace('phar://', '', __FILE__)))),
			26
		);

		add_submenu_page($this->mainMenuId, 'About', 'About', 'manage_options', $this->mainMenuId);
	}

	public function onEnqueueScripts()
	{
		$styles = "
			.{$this->mainMenuId} .card {
				max-width: none;
			}

			.{$this->mainMenuId} .item {
				border-bottom: 1px solid #eee;
				margin: 0;
				padding: 10px 0;
				display: inline-block;
				width: 100%;
			}

			.{$this->mainMenuId} .card ul {
				list-style-type: inherit;
				padding: inherit;
			}

			.{$this->mainMenuId} .item:last-child {
				border-bottom: none;
			}

			.{$this->mainMenuId} .item a {
				display: inline-block;
				width: 100%;
				color: #23282d;
				text-decoration: none;
				outline: none;
				box-shadow: none;
			}

			.{$this->mainMenuId} .item .num {
				width: 40px;
				height: 40px;
				margin-bottom: 30px;
				float: left;
				margin-right: 10px;
				border-radius: 20px;
				background-color: #0079c6;
				text-align: center;
				line-height: 40px;
				color: #ffffff;
				font-weight: bold;
				font-size: 20px;
			}

			.{$this->mainMenuId} .item p {
				margin: 5px 0;
			}

			.{$this->mainMenuId} .item .title {
				font-weight: bold;
			}

			.{$this->mainMenuId} .item .extra {
				opacity: .5;
			}
		";

		$styleId = $this->mainMenuId . '_custom_css';
		wp_register_style($styleId, false);
    	wp_enqueue_style($styleId);
		wp_add_inline_style($styleId, $styles);
	}

	protected function getPlugins()
	{
		$plugins = get_transient($this->mainMenuId . '_plugins');
		if (!empty($plugins)) {
			return $plugins;
		}

		$plugins = array();
		$plugins = $this->getFreePlugins();
		//$plugins += $this->getPaidPlugins();

		set_transient($cacheKey, $plugins, 24 * HOUR_IN_SECONDS);

		return $plugins;
	}

	protected function getFreePlugins()
	{
		$args = (object)array(
			'author' => $this->author,
			'per_page' => '120',
			'page' => '1',
			'fields' => array('slug', 'name', 'version', 'downloaded', 'active_installs', 'homepage'),
		);

		$request = array(
			'action' => 'query_plugins',
			'timeout' => 15,
			'request' => serialize($args),
		);

		$response = wp_remote_post($this->freePluginsApiUrl, array('body' => $request));
		if (is_wp_error($response)) {
			return array();
		}

		$plugins = array();

		$data = unserialize($response['body']);
		if (isset($data->plugins) && (count($data->plugins) > 0)) {
			foreach ($data->plugins as $pluginData) {
				$pluginData = json_decode(json_encode($pluginData), true);
				$plugins[$pluginData['slug']] = $pluginData;
			}
		}
		
		return $plugins;
	}

	// NOT IN USE, LEFT FOR THE REFERENCE ONLY
	protected function getPaidPlugins()
	{
		$request = array(
			'timeout' => 15,
		);

		$response = wp_remote_get($this->paidPluginsApiUrl, array('body' => $request));
		if (is_wp_error($response)) {
			return array();
		}

		$response = json_decode($response['body'], true);
		if (!empty($response['code'])) {
			return array();
		}

		$plugins = $response;

		return $plugins;
	}

	protected function displayPlugins()
	{
		$plugins = $this->getPlugins();

		if (empty($plugins)) {
			return;
		}
		?>
			<div class="card">
				<h2 class="title"><?php echo __('Our Plugins');?></h2>
				<?php
				$idx = 1;
				foreach ($plugins as $plugin) {
					if (!empty($plugin['homepage']) && !empty($plugin['name']) && !empty($plugin['short_description']) && !empty($plugin['version'])) {
						echo sprintf('<div class="item"><a href="%s" target="_blank"><span class="num">%d</span><span class="title">%s</span><p>%s</p><span class="extra">Version %s</span></a></div>',
							esc_url($plugin['homepage']),
							$idx,
							esc_html($plugin['name']),
							esc_html($plugin['short_description']),
							esc_html($plugin['version'])
						);
						$idx++;
					}
				}
				?>
			</div>
		<?php
	}

	public function display()
	{
		?>
		<div class="wrap <?php echo $this->mainMenuId; ?>">
			<img width="190" height="80" src="<?php echo plugins_url('assets/images/1TeamSoftware-logo.png', dirname(dirname(str_replace('phar://', '', __FILE__)))); ?>" class="header_logo header-logo" alt="1 Team Software">
			<div class="card">
				<h2 class="title"><?php echo __('About Us'); ?></h2>
				<p><strong>1TeamSoftware</strong> specializes in providing elegant solutions for complex e-commerce challenges. We have over 15 years of software development experience as well as many years of hands on e-commerce experience, which makes us an ideal solutions provider for your e-commerce needs.</p>
				<p>We develop and publish unique and helpful free and premium <a href="https://1teamsoftware.com/woocommerce-extensions/" target="_blank">WooCommerce plugins</a> that will help to boost your sales while reducing your operation expenses.</p>
				<p>Customer satisfaction is our top priority, so we are always working hard to address any customers’ requirements and provide <a href="https://1teamsoftware.com/contact-us" target="_blank">rapid support</a> at every step.</p>
				<p>We are continually listening to the needs of our customers, so our <a href="https://1teamsoftware.com/woocommerce-extensions/" target="_blank">WooCommerce plugins</a> are constantly evolving into more feature-rich and solid products.</p>
				<p><strong>1TeamSoftware</strong> strives to provide best quality useful products and support, so we guarantee your satisfaction by offering 30-day money back guarantee and hassle-free updates.</p>
			</div>
			<div class="card">
				<h2 class="title"><?php echo __('Contact Us'); ?></h2>
				<p>We are always here to help you with any of our great WooCommerce extensions, as well as with any customization or project you have in mind!</p>
				<span>Contact us using any of the following ways:</span>
				<ul>
					<li><a href="https://1teamsoftware.com/contact-us" target="_blank">Website Contact Form</a></li>
					<li><a href="https://www.facebook.com/1teamsoftware/" target="_blank">Facebook Page</a></li>
				</ul>
			</div>
			<?php $this->displayPlugins(); ?>
	    </div>
		<?php
	}
}

endif;