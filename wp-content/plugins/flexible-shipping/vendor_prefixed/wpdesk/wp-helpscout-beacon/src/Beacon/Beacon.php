<?php

namespace FSVendor\WPDesk\Beacon;

/**
 * Can display HelpScout Beacon. For more info check https://secure.helpscout.net/settings/beacons/
 */
class Beacon
{
    /**
     * Beacon UUID from HelpScout.
     *
     * @var string
     */
    private $beacon_id;
    /**
     * When to display beacon.
     *
     * @var BeaconShouldShowStrategy
     */
    private $activation_strategy;
    /**
     * @var string
     */
    private $assets_url;
    /**
     * @var string
     */
    private $beacon_search_elements_class;
    /**
     * @var string
     */
    protected $confirmation_message;
    /**
     * @var string
     */
    private $beacon_image_content;
    /**
     * Beacon constructor.
     *
     * @param string $beacon_id .
     * @param BeaconShouldShowStrategy $strategy When to display beacon.
     * @param string $assets_url With ending /
     */
    public function __construct($beacon_id, \FSVendor\WPDesk\Beacon\BeaconShouldShowStrategy $strategy, $assets_url, $beacon_search_elements_class = 'hs-beacon-search', $beacon_image_content = '')
    {
        $this->beacon_id = $beacon_id;
        $this->activation_strategy = $strategy;
        $this->assets_url = $assets_url;
        $this->beacon_search_elements_class = $beacon_search_elements_class;
        $this->confirmation_message = \__('When you click OK we will open our HelpScout beacon where you can find answers to your questions. This beacon will load our help articles and also potentially set cookies.', 'wp-helpscout-beacon');
        $this->beacon_image_content = $beacon_image_content;
    }
    /**
     * @return string
     */
    public function get_beacon_id()
    {
        return $this->beacon_id;
    }
    /**
     * Hooks.
     */
    public function hooks()
    {
        \add_action('admin_footer', [$this, 'add_beacon_to_footer']);
        \add_action('admin_enqueue_scripts', [$this, 'add_beacon_js']);
    }
    /**
     * Should display beacon?
     *
     * @return bool
     */
    protected function should_display_beacon()
    {
        return $this->activation_strategy->shouldDisplay();
    }
    public function add_beacon_js()
    {
        if ($this->should_display_beacon()) {
            \wp_register_script('hs-beacon', $this->assets_url . 'js/hs-bc.js', []);
            \wp_enqueue_script('hs-beacon');
        }
    }
    /**
     * Display Beacon script.
     */
    public function add_beacon_to_footer()
    {
        if ($this->should_display_beacon()) {
            $beacon_id = $this->beacon_id;
            $confirmation_message = \__('When you click OK we will open our HelpScout beacon where you can find answers to your questions. This beacon will load our help articles and also potentially set cookies.', 'wp-helpscout-beacon');
            $beacon_search_elements_class = $this->beacon_search_elements_class;
            $confirmation_message = $this->confirmation_message;
            $beacon_image_content = $this->beacon_image_content;
            include __DIR__ . '/templates/html-beacon-script.php';
        }
    }
}
