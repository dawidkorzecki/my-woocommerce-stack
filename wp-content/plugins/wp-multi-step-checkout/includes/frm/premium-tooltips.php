<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists('SilkyPress_PremiumTooltips') ) {
/**
 * SilkyPress_PremiumTooltips 
 */
class SilkyPress_PremiumTooltips {


    /**
     * Constructor
     */
    public function __construct($message) {
        $this->html($message);
        $this->css();
        $this->javascript();
    }



    function html($message) {
    ?>
    <div id="skp-premium-tooltip">
        <div class="skp-premium-tooltip--arrow">
            <div style=""></div>
        </div>
        <div class="skp-premium-tooltip--msg">
            <label><?php echo $message; ?></label>
        </div>
    </div>
    <?php
    }


    function css() {
    ?>
    <style type="text/css">
        #skp-premium-tooltip {
            display:none;
            width: 230px; 
            height: 60px; 
            position: absolute; 
            margin-left: 354px; 
            margin-top: 112px; 
            color: white;
        }
        #skp-premium-tooltip .skp-premium-tooltip--arrow {
            float:left;
            width:13px;
        }
        #skp-premium-tooltip .skp-premium-tooltip--arrow div {
            width: 0px; 
            height: 0px;
            border-top: 6px solid transparent; 
            border-right: 6px solid #333333; 
            border-bottom: 6px solid transparent; 
            float: right; 
            margin-right: 0px; 
            margin-top: 16px;
        }
        #skp-premium-tooltip .skp-premium-tooltip--msg {
            font-family:sans-serif;
            font-size:13px;
            text-align: center; 
            border-radius: 5px; 
            float: left; 
            background-color: rgb(51, 51, 51); 
            color: white; 
            width: 210px; 
            padding: 10px 0px;
        }
    </style>
    <?php
    }

    function javascript() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $(".form-group.disabled-short, .form-group.disabled").on('click',function(e){
                if(typeof window.tooltip != "undefined"){
                    clearTimeout(window.tooltip);
                }

                var inputCon = $(e.currentTarget).find(".input-group");
                var left = 30;


                $(e.currentTarget).children().each(function(i, child){
                    left += $(child).width(); 
                });

                var offsetTop = $(e.currentTarget).offset().top - 38;
                offsetTop -= $('h2').offset().top - 52;

                $("#skp-premium-tooltip").css({"margin-left" : left + "px", "margin-top" : offsetTop + "px"});
                $("#skp-premium-tooltip").fadeIn( "slow", function() {
                    window.tooltip = setTimeout(function(){ $("#skp-premium-tooltip").hide(); }, 1000);
                });
                return;
            });
        });
    </script>
    <?php
    }
}
}
