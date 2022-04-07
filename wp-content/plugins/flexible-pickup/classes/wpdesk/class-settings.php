<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPDesk_Settings_1_6' ) ) {

	if ( ! class_exists( 'WPDesk_S214_Settings_1_6' ) ) {
		require_once 'settings-api/class.s214-settings.php';
	}

	/**
	 * Base plugin class for WP Desk plugins settings
	 *
	 * @author Grzegorz
	 *
	 */
     class WPDesk_Settings_1_6 extends WPDesk_S214_Settings_1_6 {

     	private $slug;

     	protected $version = '1.0';

        protected $plugin_text_domain = 'wpdesk-plugin';

        protected $plugin = null;

        protected $func = 'wpdesk_plugin';

        public function __construct( WPDesk_Plugin_1_6 $plugin, $slug = 'wpdesk-settings', $default_tab = 'general' ) {
            parent::__construct( $slug, $default_tab );
            $this->slug = $slug;
            $this->plugin = $plugin;
            $this->func = str_replace( '-', '_', $slug );
            global ${$this->func . '_options'};
            ${$this->func . '_options'} = $this->get_settings();
            $this->hooks();
        }

        public function hooks() {
        }

        public function render_settings_page() {
            settings_errors();
            parent::render_settings_page();
        }

        public function enqueue_scripts( $hook ) {
            if( ! apply_filters( $this->func . '_load_admin_scripts', $this->load_scripts( $hook ), $hook ) ) {
                return;
            }

            global $wp_scripts;

            // Use minified libraries if SCRIPT_DEBUG is turned off
            $suffix         = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
            //$url_path    = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname( __FILE__ ) );
            $url_path    = $this->plugin->get_plugin_url() . 'classes/wpdesk';
            $select2_cdn    = 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/';
            $cm_cdn         = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.14.2/';
            $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';

            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_script( 'jquery-ui-tooltip' );
            wp_enqueue_media();
            wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui' . $suffix . '.css', array(), $jquery_version );

            wp_enqueue_script( 'media-upload' );
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_script( 'thickbox' );
            wp_enqueue_style( 's14-select2', $select2_cdn . 'css/select2' . $suffix . '.css', array(), '4.0.3' );
            wp_enqueue_script( 's14-select2', $select2_cdn . 'js/select2' . $suffix . '.js', array( 'jquery' ), '4.0.3' );

            wp_enqueue_style( $this->slug . '-cm', $cm_cdn . 'codemirror.css', array(), '5.10' );
            wp_enqueue_script( $this->slug . '-cm', $cm_cdn . 'codemirror.js', array( 'jquery' ), '5.14.2' );
            wp_enqueue_script( $this->slug . '-cm-html', $cm_cdn . 'mode/htmlmixed/htmlmixed.js', array( 'jquery', $this->slug . '-cm' ), '5.14.2' );
            wp_enqueue_script( $this->slug . '-cm-xml', $cm_cdn . 'mode/xml/xml.js', array( 'jquery', $this->slug . '-cm' ), '5.14.2' );
            wp_enqueue_script( $this->slug . '-cm-js', $cm_cdn . 'mode/javascript/javascript.js', array( 'jquery', $this->slug . '-cm' ), '5.14.2' );
            wp_enqueue_script( $this->slug . '-cm-css', $cm_cdn . 'mode/css/css.js', array( 'jquery', $this->slug . '-cm' ), '5.14.2' );
            wp_enqueue_script( $this->slug . '-cm-php', $cm_cdn . 'mode/php/php.js', array( 'jquery', $this->slug . '-cm' ), '5.14.2' );
            wp_enqueue_script( $this->slug . '-cm-clike', $cm_cdn . 'mode/clike/clike.js', array( 'jquery', $this->slug . '-cm' ), '5.14.2' );

            wp_enqueue_style( $this->slug, $url_path . '/assets/css/admin-settings' . $suffix . '.css', array(), $this->version );
            wp_enqueue_script( $this->slug . '-js', $url_path . '/assets/js/admin-settings' . $suffix . '.js', array( 'jquery' ), $this->version );
            wp_localize_script( $this->slug . '-js', 's214_settings_vars', apply_filters( $this->func . 'localize_script', array(
                	'func'               => $this->func,
                	'image_media_button' => __( 'Insert Image', 'wpdesk-plugin' ),
                	'image_media_title'  => __( 'Select Image', 'wpdesk-plugin' ),
            ) ) );
       	}

     	/**
		 * Add settings sections and fields
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		function register_settings() {
			if( get_option( $this->func . '_settings' ) == false ) {
				add_option( $this->func . '_settings' );
			}

			foreach( $this->get_registered_settings() as $tab => $sections ) {
				foreach( $sections as $section => $settings ) {
					// Check for backwards compatibility
					$section_tabs = $this->get_settings_tab_sections( $tab );

					if( ! is_array( $section_tabs ) || ! array_key_exists( $section, $section_tabs ) ) {
						$section  = 'main';
						$settings = $sections;
					}

					add_settings_section(
						$this->func . '_settings_' . $tab . '_' . $section,
						__return_null(),
						'__return_false',
						$this->func . '_settings_' . $tab . '_' . $section
					);

					foreach( $settings as $option ) {
						// For backwards compatibility
						if( empty( $option['id'] ) ) {
							continue;
						}

						$name = isset( $option['name'] ) ? $option['name'] : '';

						add_settings_field(
							$this->func . '_settings[' . $option['id'] . ']',
							$name,
							function_exists( $this->func . '_' . $option['type'] . '_callback' ) ? $this->func . '_' . $option['type'] . '_callback' : ( method_exists( $this, $option['type'] . '_callback' ) ? array( $this, $option['type'] . '_callback' ) : array( $this, 'missing_callback' ) ),
							$this->func . '_settings_' . $tab . '_' . $section,
							$this->func . '_settings_' . $tab . '_' . $section,
							array(
								'section'       => $section,
								'id'            => isset( $option['id'] )            ? $option['id']             : null,
								'desc'          => ! empty( $option['desc'] )        ? $option['desc']           : '',
								'name'          => isset( $option['name'] )          ? $option['name']           : null,
								'size'          => isset( $option['size'] )          ? $option['size']           : null,
								'options'       => isset( $option['options'] )       ? $option['options']        : '',
								'std'           => isset( $option['std'] )           ? $option['std']            : '',
								'min'           => isset( $option['min'] )           ? $option['min']            : null,
								'max'           => isset( $option['max'] )           ? $option['max']            : null,
								'step'          => isset( $option['step'] )          ? $option['step']           : null,
								'select2'       => isset( $option['select2'] )       ? $option['select2']        : null,
								'placeholder'   => isset( $option['placeholder'] )   ? $option['placeholder']    : null,
								'multiple'      => isset( $option['multiple'] )      ? $option['multiple']       : null,
								'allow_blank'   => isset( $option['allow_blank'] )   ? $option['allow_blank']    : true,
								'readonly'      => isset( $option['readonly'] )      ? $option['readonly']       : false,
								'buttons'       => isset( $option['buttons'] )       ? $option['buttons']        : null,
								'wpautop'       => isset( $option['wpautop'] )       ? $option['wpautop']        : null,
								'teeny'         => isset( $option['teeny'] )         ? $option['teeny']          : null,
								'tab'           => isset( $option['tab'] )           ? $option['tab']            : null,
								'tooltip_title' => isset( $option['tooltip_title'] ) ? $option['tooltip_title']  : false,
								'tooltip_desc'  => isset( $option['tooltip_desc'] )  ? $option['tooltip_desc']   : false,

								'available_header'	=> isset( $option['available_header'] )     ? $option['available_header']      : null,
								'selected_header'	=> isset( $option['selected_header'] )      ? $option['selected_header']       : null,

								'class'         => isset( $option['class'] )         ? $option['class']          : '',

							)
						);
					}
				}
			}

			register_setting( $this->func . '_settings', $this->func . '_settings', array( $this, 'settings_sanitize' ) );
		}

     	/**
		 * Retrieve the plugin settings sections
		 *
		 * @access      private
		 * @since       1.0.1
		 * @return      array $sections The registered sections
		 */
		private function get_registered_settings_sections() {
			global ${$this->func . '_sections'};

			if ( !empty( $sections ) ) {
				return $sections;
			}

			$sections = apply_filters( $this->func . '_registered_settings_sections', array() );

			return $sections;
		}

	     /**
	      * Checkbox callback
	      *
	      * @access      public
	      * @since       1.0.0
	      * @param       array $args Arguments passed by the setting
	      * @global      array ${$this->func . '_options'} The plugin options
	      * @return      void
	      */
	     public function checkbox_callback( $args ) {
		     global ${$this->func . '_options'};

		     $name    = ' name="' . $this->func . '_settings[' . $args['id'] . ']"';

		     if( isset( ${$this->func . '_options'}[$args['id']] ) ) {
			     $value = ${$this->func . '_options'}[$args['id']];
		     } else {
			     $value = isset( $args['std'] ) ? $args['std'] : '';
		     }

		     $checked = checked( 1, $value, false );

		     $html  = '<input type="hidden"' . $name . ' value="0" />';
		     $html .= '<input type="checkbox" id="' . $this->func . '_settings[' . $args['id'] . ']"' . $name . ' value="1" ' . $checked . '/>&nbsp;';
		     $html .= '<span class="description"><label for="' . $this->func . '_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';

		     echo apply_filters( $this->func . '_after_setting_output', $html, $args );
	     }


		/**
       	 * Date callback
       	 *
       	 * @access      public
       	 * @since       1.0.0
       	 * @param       array $args Arguments passed by the setting
       	 * @global      array ${$this->func . '_options'} The Beacon options
       	 * @return      void
       	 */
       	public function date_callback( $args ) {
       		global ${$this->func . '_options'};

       		if( isset( ${$this->func . '_options'}[$args['id']] ) ) {
       			$value = ${$this->func . '_options'}[$args['id']];
       		} else {
       			$value = isset( $args['std'] ) ? $args['std'] : '';
       		}

       		$name     = ' name="' . $this->func . '_settings[' . $args['id'] . ']"';
       		$max      = isset( $args['max'] ) ? $args['max'] : 999999;
       		$min      = isset( $args['min'] ) ? $args['min'] : 0;
       		$step     = isset( $args['step'] ) ? $args['step'] : 1;
       		$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'date';
       		$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';

       		$html  = '<input type="date" ' . '" class="' . $size . '-text" id="' . $this->func . '_settings[' . $args['id'] . ']"' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . '/>&nbsp;';
       		$html .= '<span class="description"><label for="' . $this->func . '_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';

       		echo apply_filters( $this->func . '_after_setting_output', $html, $args );
       	}


        /**
       	 * select 2 columns
       	 *
       	 * @access      public
       	 * @since       1.0.0
       	 * @param       array $args Arguments passed by the setting
       	 * @global      array ${$this->func . '_options'} The Beacon options
       	 * @return      void
       	 */
       	public function select_2_columns_callback( $args ) {
			global ${$this->func . '_options'};

			if( isset( ${$this->func . '_options'}[$args['id']] ) ) {
				$value = ${$this->func . '_options'}[$args['id']];
			} else {
				$value = isset( $args['std'] ) ? $args['std'] : '';
			}

			$value_array = explode( ',', $value );

			$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
	        $width       = isset( $args['size'] ) ? ' style="width: ' . $args['size'] . '"' : '';

	        $available_header = isset( $args['available_header'] ) ? $args['available_header'] : '';

	        $selected_header = isset( $args['selected_header'] ) ? $args['selected_header'] : '';

			$html = '<input type="hidden" value="' . esc_attr( $value ) . '" id="' . $this->func . '_settings[' . $args['id'] . ']" name="' . $this->func . '_settings[' . $args['id'] . ']"' . ' />';

			$html_list_available = '<div class="available-column">';
			$html_list_available .= '<strong>' . $available_header . '</strong>';
			$html_list_available .= '<ul id="' . $this->func . '_settings[' . $args['id'] . '][available]" class="available connectedSortable">';

			$html_list_selected = '<div class="selected-column">';
			$html_list_selected .= '<strong>' . $selected_header . '</strong>';
			$html_list_selected .= '<ul id="' . $this->func . '_settings[' . $args['id'] . '][selected]" class="selected connectedSortable">';

			foreach( $args['options'] as $option => $name ) {
				if( is_array( $value_array ) && in_array( $option, $value_array ) ) {
					$html_list_selected .= '<li data-value="' . esc_attr( $option ) . '">' . $name . '</li>';
				}
				else {
					$html_list_available .= '<li data-value="' . esc_attr( $option ) . '">' . $name . '</li>';
				}
			}

			$html_list_available .= '</ul></div>';
			$html_list_selected .= '</ul></div>';

			$html .= '<div class="select-2-columns">';
			$html .= $html_list_available;
			$html .= $html_list_selected;

			$html .= '<div style="clear:both;">';

			$html .= '<script type="text/javascript">';
			$html .= "\n";
			$html .= '
					 jQuery( function() {
					 	jQuery( "#'. $this->func . '_settings\\\\[' . $args['id'] . '\\\\]\\\\[available\\\\], #' . $this->func . '_settings\\\\[' . $args['id'] . '\\\\]\\\\[selected\\\\]" ).sortable({
							connectWith: ".connectedSortable",
					 		deactivate: function( event, ui ) {
					 			jQuery("#' . $this->func . '_settings\\\\[' . $args['id'] . '\\\\]").val("");
					 			var val = "";
					 			jQuery("#' . $this->func . '_settings\\\\[' . $args['id'] . '\\\\]\\\\[selected\\\\] > li").each( function () {
					 				if ( val != "" ) {
					 					val = val + ",";
					 				}
					 				val = val + jQuery(this).attr("data-value");
					 			});
					 			jQuery("#' . $this->func . '_settings\\\\[' . $args['id'] . '\\\\]").val(val);
       						}
       					}).disableSelection();
       				 } );
					 ';
			$html .= "\n";
			$html .= '</script>';

			$html .= '</div>';

			$html .= '<span class="description"><label for="' . $this->func . '_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';

			echo apply_filters( $this->func . '_after_setting_output', $html, $args );
       	}

       	public function set_text_domain( $plugin_text_domain ) {
     		$this->plugin_text_domain = $plugin_text_domain;
     	}

        public function get_text_domain() {
     		return $this->plugin_text_domain;
     	}

     }
}
