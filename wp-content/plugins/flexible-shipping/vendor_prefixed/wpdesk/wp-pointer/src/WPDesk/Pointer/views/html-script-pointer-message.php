<?php

namespace FSVendor;

/**
 * @var string $pointerAnchor
 * @var string $pointerContent
 * @var \WPDesk\Pointer\PointerPosition $pointerPosition
 * @var string $pointerId
 * @var string $pointerContentId
 * @var int $pointerWidth
 * @var array $pointerCss
 */
?>
<script type="text/javascript">
    jQuery(document).ready( function($) {
        if(typeof(jQuery().pointer) != 'undefined') {
            $('<?php 
echo $pointerAnchor;
?>').pointer({
                pointerWidth: <?php 
echo $pointerWidth;
?>,
                content: <?php 
echo \json_encode($pointerContent);
?>,
                position: <?php 
echo $pointerPosition->render();
?>,
                <?php 
if ($pointerCss) {
    ?>
				    show: function(event, t){
					    t.pointer.css(<?php 
    echo \json_encode($pointerCss);
    ?>);
                        $('<?php 
    echo $pointerAnchor;
    ?>').css('position', 'relative');
                        t.pointer.appendTo('<?php 
    echo $pointerAnchor;
    ?>');
				    },
                <?php 
}
?>
                close: function() {
                    $('#<?php 
echo $pointerContentId;
?>').remove();
                    $.post(ajaxurl, {
                        pointer: '<?php 
echo $pointerId;
?>',
                        action: 'dismiss-wp-pointer'
                    });
                },
            }).pointer('open');
        }
    });
</script>
<?php 
