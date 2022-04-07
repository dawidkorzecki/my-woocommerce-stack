function pickup_point_change( element ) {
    console.log(element);
    var id = '';
    var val = '';
    if ( jQuery(element).is('select') ) {
        id = jQuery(element).attr('data-id');
        var val = jQuery(element).val();
    }
    if ( typeof id == 'undefined' ) {
        id = 0;
    }
    if ( jQuery(element).is('input') ) {
        id = jQuery(element).attr('data-id');
        var val = jQuery(element).val();
    }
    console.log(id);
    jQuery('#flexible_pickup_point_'+id+'_details').hide();
    var ajax_data = {
        action  		: 'flexible_pickup_point',
        security        : jQuery('#flexible_pickup_ajax_nonce_' + id).val(),
        post_id         : jQuery(element).val(),
        id              : id,
    };
    console.log(ajax_data);
    jQuery.ajax({
        url		: flexible_pickup_checkout.ajax_url,
        data	: ajax_data,
        method	: 'POST',
        dataType: 'JSON',
        success: function( data ) {
            console.log(data);
            jQuery( '#flexible_pickup_point_'+data.id+'_details').replaceWith(data.content);
        },
        error: function ( xhr, ajaxOptions, thrownError ) {
            alert( xhr.status + ': ' + thrownError );
        },
        complete: function() {
        }
    });
}

jQuery(document).ready(function(){
    function flexible_pickup_shipping() {
        jQuery('.shipping_method').each(function( index ){
            if ( jQuery(this).parent().find('.flexible-pickup-select-point').length ) {
                if ( jQuery(this).is(':checked') ) {
                    jQuery(this).parent().find('.flexible-pickup-select-point').show();
                }
                else {
                    jQuery(this).parent().find('.flexible-pickup-select-point').hide();
                }
            }
        })
    }

    jQuery(document).on('change','.shipping_method',function(){
        flexible_pickup_shipping();
    });

    jQuery( document.body ).on( 'updated_checkout', function() {
        flexible_pickup_shipping();
    });

    flexible_pickup_shipping();

    jQuery(document).on('click','a.fp-map-selector', function(event) {
        event.preventDefault();
        var w = screen.width * 0.9;
        if ( w > 900 ) {
            w = 900;
        }
        var h = screen.height * 0.8;
        if ( h > 800 ) {
            h = 800;
        }
        var left = Number((screen.width/2)-(w/2));
        var tops = Number((screen.height/2)-(h/2));
        var select_pickup_point_window = window.open(
            flexible_pickup_checkout.map_url + '&points_group='+flexible_pickup_checkout['points_group_'+jQuery(this).attr('data-id')] + '&select_field=' + jQuery(this).attr('data-select_field'),
            'fp_map',
            'toolbar=no, menubar=no, resizable=yes, width='+w+', height='+h+', top='+tops+', left='+left
        );

        select_pickup_point_window.onbeforeunload = function () {
            jQuery('.flexible-pickup-point select').trigger('change');
        }
    })

    jQuery(document).on('change','.flexible-pickup-point select',function() {
        pickup_point_change(this);
    })

    jQuery(document).on('change','.flexible-pickup-point input[type=radio]',function() {
        pickup_point_change(this);
    })

})
