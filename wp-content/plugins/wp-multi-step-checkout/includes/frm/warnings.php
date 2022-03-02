<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists('SilkyPress_Warnings') ) {
/**
 * SilkyPress_Warnings 
 */
class SilkyPress_Warnings {

    var $allowed_actions = array();

    var $notices = array();

    /**
     * Constructor
     */
    public function __construct($allowed_actions) {
        $this->allowed_actions = $allowed_actions;
        add_action( 'wp_ajax_sk_dismiss_warning', array( $this, 'notice_dismiss' ) );
    }


    /**
     * Check if we are on the right page
     */
    function is_url( $url_part = '' ) {
        if ( !isset( $_SERVER ) || !isset( $_SERVER['REQUEST_URI'] ) ) {
            return false;
        }
        if ( strpos( $_SERVER['REQUEST_URI'], $url_part ) !== false ) { 
            return true;
        }
        return false;
    }


    /**
     * Add this message to the $this->notices array
     */
    function add_notice($id, $message, $class = '') {
        if ( get_option($id) != false ) return false;

        $notice = array(
            'id'        => $id,
            'message'   => $message,
        );
        if ( !empty($class) ) $notice['class'] = $class;

        $this->notices[] = $notice;
    }


    function show_warnings() {
        add_action( 'admin_notices', array($this, 'show_admin_notice') );
    }


    /**
     * Show the admin notices
     * */
    function show_admin_notice() {
        if ( !is_array($this->notices) || count($this->notices) == 0 ) return;

        foreach( $this->notices as $_n ) {
            $nonce =  wp_create_nonce( $_n['id'] );
            if ( !isset($_n['class'])) $_n['class'] = 'notice notice-warning is-dismissible';
            $_n['class'] .= ' sk-notice-dismiss';
            printf( '<div class="%1$s" id="%2$s" data-nonce="%3$s"><p>%4$s</p></div>', $_n['class'], $_n['id'], $nonce, $_n['message'] );
        }
            ?>
                <script type='text/javascript'>
                jQuery(function($){
                    $(document).on( 'click', '.sk-notice-dismiss', function() {
                        var id = $(this).attr('id');
                        var data = {
                            action: 'sk_dismiss_warning',
                            option: id, 
                            nonce: $(this).data('nonce'),
                        };
                        $.post(ajaxurl, data, function(response ) {
                            $('#'+id).fadeOut('slow');
                        });
                    });
                });
                </script>
            <?php
    }


    /**
     * Ajax response for `notice_dismiss` action
     */
    function notice_dismiss() {

        $option = $_POST['option'];

        if ( ! in_array($option, $this->allowed_actions ) ) return; 

        check_ajax_referer( $option, 'nonce' );

        update_option( $option, 1 );


        wp_die();
    }
}
}
