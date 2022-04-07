<?php

namespace FSVendor;

/**
 * @var $plugin_title string
 * @var $plugin_file string
 * @var $plugin_slug string
 * @var $thickbox_id string
 * @var $ajax_action string
 * @var $ajax_nonce string
 */
if (!\defined('ABSPATH')) {
    exit;
}
?><script type="text/javascript">

    function resize_deactivation_tb_window() {
        let margin_horizontal = 60;
        let margin_vertical = 110;
        let $TB_ajaxContent = jQuery('#TB_ajaxContent').find('.wpdesk_tracker_deactivate');
        let $TB_window = jQuery(document).find('#TB_window');
        let width = $TB_ajaxContent.width();
        let height = $TB_ajaxContent.height();
        $TB_window.width( width + margin_horizontal ).height( height + margin_vertical ).css( 'margin-left', - ( width + margin_horizontal ) / 2 );
        let margin_top = window.innerHeight / 2 - $TB_window.height() / 2;
        if ( margin_top > 0) {
            $TB_window.css( 'margin-top', margin_top );
        }
    }


    jQuery(document).ready(function(){

        jQuery("span.deactivate a").click(function(e){
            if ( jQuery(this).closest('tr').attr('data-plugin') === '<?php 
echo $plugin_file;
?>' ) {
                e.preventDefault();
                let tb_id = '#TB_inline?inlineId=<?php 
echo $thickbox_id;
?>';
                tb_show('<?php 
\_e('Plugin deactivation', 'flexible-shipping');
?>', tb_id);
                resize_deactivation_tb_window();
            }
        });

        jQuery(document).on( 'click', '.<?php 
echo $thickbox_id;
?> .tracker-button-close', function(e) {
            e.preventDefault();
            tb_remove();
        });

        jQuery(document).on( 'click', '.<?php 
echo $thickbox_id;
?> .allow-deactivate', function(e) {
            e.preventDefault();
            let url = jQuery("tr[data-plugin='<?php 
echo $plugin_file;
?>']").find('span.deactivate a').attr('href');
            let reason = jQuery('.<?php 
echo $thickbox_id;
?> input[name=selected-reason]:checked').val();
            let additional_info = jQuery('.<?php 
echo $thickbox_id;
?> input[name=selected-reason]:checked').closest('li').find('input.additional-info').val();
            if ( typeof reason !== 'undefined' ) {
                jQuery('.button').attr('disabled',true);
                jQuery.ajax( '<?php 
echo \admin_url('admin-ajax.php');
?>',
                    {
                        type: 'POST',
                        data: {
                            'action': '<?php 
echo $ajax_action;
?>',
                            '_ajax_nonce': '<?php 
echo $ajax_nonce;
?>',
                            'reason': reason,
                            'additional_info': additional_info,
                        }
                    }
                ).always(function() {
                    window.location.href = url;
                });
            }
            else {
                window.location.href = url;
            }
        });

        jQuery(document).on( 'click', '.<?php 
echo $thickbox_id;
?> input[type=radio]', function(){
            var tracker_deactivate = jQuery(this).closest('.wpdesk_tracker_deactivate');
            tracker_deactivate.find('input[type=radio]').each(function(){
                if ( jQuery(this).data("show") ) {
                    var show_element = tracker_deactivate.find( '.' + jQuery(this).data('show') );
                    if ( jQuery(this).is(':checked') ) {
                        show_element.show();
                    } else {
                        show_element.hide();
                    }
                }
            });
            resize_deactivation_tb_window();
            jQuery('.<?php 
echo $thickbox_id;
?> .button-deactivate').html( '<?php 
\_e('Submit &amp; Deactivate', 'flexible-shipping');
?>' );
        });
    });

    jQuery(window).on( 'load', function() {
        jQuery(window).resize(function(){
            resize_deactivation_tb_window();
        });
    });
</script>
<style>
    #TB_ajaxContent {
        overflow: hidden;
    }
    .<?php 
echo $thickbox_id;
?> input[type=text] {
        margin-left: 25px;
        width: 90%;
    }
</style>
<?php 
