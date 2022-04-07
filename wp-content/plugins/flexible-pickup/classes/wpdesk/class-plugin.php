<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once 'functions.php';

if ( ! class_exists( 'WPDesk_Plugin_1_6' ) ) {

	require_once 'class-settings.php';

	/**
	 * Base plugin class for WP Desk plugins
	 *
	 * @author Grzegorz
	 *
	 */
    class WPDesk_Plugin_1_6 {

    	const VERSION = '1.6';

    	protected $plugin_is_active = true;

    	//protected $plugin_data = false;

    	protected $default_view_args;

    	public $plugin_namespace = 'wpdesk_plugin';
    	public $plugin_text_domain = 'wpdesk-plugin';
    	public $plugin_has_settings = false;

    	public $plugin_path;
	    public $template_path;
	    public $plugin_file_path;
	    public $plugin_url;

	    public $settings_url = false;
	    public $docs_url = false;

	    public $default_settings_tab = 'general';

	    public $settings = null;
	    public $options = null;

	    protected function __construct( $plugin_data = false ) {
	    	$this->init_base_variables();
		    if ( $this->plugin_is_active ) {
			    if ( $this->plugin_has_settings ) {
				    $this->settings = new WPDesk_Settings_1_6( $this, $this->get_namespace(), $this->default_settings_tab );
				    $this->options = $this->settings->get_settings();
			    }
		    }
		    $this->hooks();
    	}

	    public function plugin_is_active() {
	    	return $this->plugin_is_active;
	    }

	    public function get_settings() {
	    	return $this->settings;
	    }

	    public function get_option( $key, $default ) {
	    	return $this->settings->get_option( $key, $default );
	    }

    	/**
    	 *
    	 * @return WPDesk_Plugin_1_6
    	 */
    	public function get_plugin() {
    		return $this;
    	}

    	public function get_text_domain() {
    		return $this->plugin_text_domain;
    	}

    	public function load_plugin_text_domain() {
    	    $wpdesk_translation = load_plugin_textdomain( 'wpdesk-plugin', FALSE, $this->get_namespace() . '/classes/wpdesk/lang/' );
    	    $plugin_translation = load_plugin_textdomain( $this->get_text_domain(), FALSE, $this->get_namespace() . '/lang/' );
    	}

    	/**
    	 *
    	 */
    	public function init_base_variables() {
    		$reflection = new ReflectionClass( $this );

    		// Set Plugin Path
    		$this->plugin_path = dirname( $reflection->getFileName() );

    		// Set Plugin URL
    		$this->plugin_url = plugin_dir_url( $reflection->getFileName() );

    		$this->plugin_file_path = $reflection->getFileName();

    		$this->template_path = $this->get_namespace();

    		$this->default_view_args = array(
    			'plugin_url' => $this->get_plugin_url()
    		);

    	}

        	/**
    	 *
    	 */
    	public function hooks() {

    		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

    		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

    		add_action( 'plugins_loaded', array( $this, 'load_plugin_text_domain') );

    		add_filter( 'plugin_action_links_' . plugin_basename( $this->get_plugin_file_path() ), array( $this, 'links_filter' ) );

    	}

        /**
         *
         * @return string
         */
        public function get_plugin_url() {
        	return esc_url( trailingslashit( $this->plugin_url ) );
        }

        public function get_plugin_assets_url() {
            return esc_url( trailingslashit( $this->get_plugin_url() . 'assets' ) );
        }

        /**
         * @return string
         */
        public function get_template_path() {
        	return trailingslashit( $this->template_path );
        }

        public function get_plugin_file_path() {
        	 return $this->plugin_file_path;
        }

        public function get_namespace() {
        	return $this->plugin_namespace;
        }


        /**
		 * Renders end returns selected template
		 *
		 * @param string $name name of the template
		 * @param string $path additional inner path to the template
		 * @param array $args args accesible from template
		 * @return string
		 */
		public function load_template( $name, $path = '', $args = array() ) {
			$path = trim( $path, '/' );

			$template_name = implode( '/', array( get_template_directory(), $this->get_template_path(), $path, $name . '.php' ) );

			if ( !file_exists( $template_name ) ) {
				$template_name = implode( '/', array( untrailingslashit( $this->plugin_path ), 'templates', $path, $name . '.php' ) );
			}
			extract( $args );
			ob_start();
			include( $template_name );
			return ob_get_clean();
		}

		public function admin_enqueue_scripts( $hooq ) {
		}

    	public function wp_enqueue_scripts() {
		}

		/**
         * action_links function.
         *
         * @access public
         * @param mixed $links
         * @return void
         */
        public function links_filter( $links ) {

            $support_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/support/' : 'https://www.wpdesk.net/support';

        	$plugin_links = array(
        			'<a href="' . $support_link . '">' . __( 'Support', 'wpdesk-plugin' ) . '</a>',
        	);
        	$links = array_merge( $plugin_links, $links );

        	if ( $this->docs_url ) {
        		$plugin_links = array(
        				'<a href="' . $this->docs_url . '">' . __( 'Docs', 'wpdesk-plugin' ) . '</a>',
        		);
        		$links = array_merge( $plugin_links, $links );
        	}

        	if ( $this->settings_url ) {
        		$plugin_links = array(
        				'<a href="' . $this->settings_url . '">' . __( 'Settings', 'wpdesk-plugin' ) . '</a>',
        		);
        		$links = array_merge( $plugin_links, $links );
        	}

        	return $links;
        }

    }

}
