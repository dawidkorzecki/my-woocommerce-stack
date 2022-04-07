<?php

namespace FSVendor;

/**
 * @var $thickbox_id string
 * @var $thickbox_title string
 * @var $ajax_action string
 * @var $ajax_nonce string
 * @var $button_send_text string
 * @var $thickbox_all_options int
 */
if (!\defined('ABSPATH')) {
    exit;
}
?><script type="text/javascript">

	jQuery(document).ready(function(){

        function resize_user_feedback_tb_window() {
            let margin_horizontal = 30;
            let $TB_ajaxContent = jQuery('#TB_ajaxContent');
            let $TB_window = jQuery(document).find('#TB_window');
            let width = $TB_ajaxContent.width();
            let height = jQuery('#TB_title').outerHeight(true) + 20;
			$TB_ajaxContent.children().each(function(){
				height += jQuery(this).outerHeight(true);
			});
			$TB_ajaxContent.height( height );
            $TB_window.width( width + margin_horizontal ).height( $TB_ajaxContent.outerHeight(true) ).css( 'margin-left', - ( width + margin_horizontal ) / 2 );
        }

        jQuery(document).bind('<?php 
echo \esc_attr($thickbox_id);
?>', function(e){
            let tb_id = '#TB_inline?inlineId=<?php 
echo $thickbox_id;
?>';
            tb_show('<?php 
echo \esc_html($thickbox_title);
?>', tb_id);
            resize_user_feedback_tb_window();
        });

        jQuery(document).on( 'click', '.<?php 
echo $thickbox_id;
?> .tracker-button-close', function(e) {
            e.preventDefault();
            tb_remove();
        });

		jQuery(document).on( 'click', '.<?php 
echo $thickbox_id;
?> .skip-proceed', function(e) {
			e.preventDefault();
			tb_remove();
			jQuery(document).trigger('<?php 
echo $thickbox_id;
?>_proceed');
		});

        jQuery(document).on( 'click', '.<?php 
echo $thickbox_id;
?> .allow-proceed', function(e) {
            e.preventDefault();
            let selected_option = jQuery('.<?php 
echo $thickbox_id;
?> input[name=selected_option]:checked').val();
            let additional_info = jQuery('.<?php 
echo $thickbox_id;
?> input[name=selected_option]:checked').closest('li').find('.additional-info').val();
            if ( typeof selected_option !== 'undefined' ) {
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
                            'selected_option': selected_option,
                            'additional_info': additional_info,
                        }
                    }
                ).always(function() {
                    jQuery(document).trigger('<?php 
echo $thickbox_id;
?>_proceed');
                });
            }
            else {
				jQuery(document).trigger('<?php 
echo $thickbox_id;
?>_proceed');
            }
        });

        jQuery(document).on( 'click', '.<?php 
echo $thickbox_id;
?> input[type=radio]', function(){
            var tracker_user_feedback = jQuery(this).closest('.<?php 
echo $thickbox_id;
?>');
            tracker_user_feedback.find('input[type=radio]').each(function(){
                if ( jQuery(this).data("show") ) {
                    var show_element = tracker_user_feedback.find( '.' + jQuery(this).data('show') );
                    if ( jQuery(this).is(':checked') ) {
                        show_element.show();
                    } else {
                        show_element.hide();
                    }
                }
            });
            resize_user_feedback_tb_window();
            jQuery('.<?php 
echo $thickbox_id;
?> .skip-proceed').addClass('allow-proceed').removeClass('skip-proceed').html( '<?php 
echo \esc_html($button_send_text);
?>' );
        });

		jQuery(window).on('load', function() {
			jQuery(window).resize(function(){
				resize_user_feedback_tb_window();
			});
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
    .<?php 
echo $thickbox_id;
?> textarea {
        margin-left: 25px;
        width: 90%;
        height: 86px;
        resize: none;
    }
    .<?php 
echo $thickbox_id;
?> textarea.no_question {
        margin-left: 0px;
        width: 90%;
    }
</style>
<?php 
