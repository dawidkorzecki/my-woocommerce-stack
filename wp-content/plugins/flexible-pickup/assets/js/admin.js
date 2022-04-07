
jQuery(document).ready(function() {
    jQuery('.button-save-pickup-points').click(function(event) {
        event.preventDefault();
        jQuery( '#flexible_pickup_message').hide();
        jQuery('.button-save-pickup-points').attr('disabled','disabled');
        jQuery('.flexible-pickup-spinner').css({visibility: 'visible'});

        var data = [];
        jQuery('.pickup-point').each(function(){
            console.log(jQuery(this));
            data[jQuery(this).attr('data-key')] = jQuery(this).val();
        })

        var ajax_data = {
            action  		: 'flexible_pickup',
            security        : jQuery('#flexible_pickup_ajax_nonce').val(),
            order_id        : jQuery('#flexible_pickup_order_id').val(),
            data            : data,
        };

        jQuery.ajax({
            url		: flexible_pickup.ajax_url,
            data	: ajax_data,
            method	: 'POST',
            dataType: 'JSON',
            success: function( data ) {
                console.log(data);
                jQuery( '#flexible_pickup_message' ).html(data.message);
                jQuery( '#flexible_pickup_message' ).show();
                setTimeout(function(){ jQuery( '#flexible_pickup_message').hide(); }, 5000 );
            },
            error: function ( xhr, ajaxOptions, thrownError ) {
                alert( xhr.status + ': ' + thrownError );
            },
            complete: function() {
                jQuery('.button-save-pickup-points').removeAttr('disabled','disabled');
                jQuery('.flexible-pickup-spinner').css({visibility: 'hidden'});
            }
        });

    });
})

jQuery(document).on('change','.flexible_shipping_shipment .pickup-point',function(){
    var id = jQuery(this).attr('data-key');
    console.log(id);
    jQuery( '#flexible_pickup_point_' + id + '_details' ).hide();

    var ajax_data = {
        action  		: 'flexible_pickup_order_point',
        security        : jQuery('#flexible_pickup_ajax_nonce_' + id).val(),
        shipping_id     : id,
        pickup_point    : jQuery(this).val(),
    };

    jQuery.ajax({
        url		: flexible_pickup.ajax_url,
        data	: ajax_data,
        method	: 'POST',
        dataType: 'JSON',
        success: function( data ) {
            console.log(data);
            jQuery('#flexible_pickup_point_' + data.id + '_details').replaceWith(data.content);
        },
        error: function ( xhr, ajaxOptions, thrownError ) {
            alert( xhr.status + ': ' + thrownError );
        },
        complete: function() {
        }
    });

})

jQuery(document).on('click','.flexible-pickup-button-save', function(e) {
    var id = jQuery(this).attr('data-id');
    if ( typeof id != 'undefined' ) {
        e.stopImmediatePropagation();
        e.preventDefault();
        var id = fs_id(this);
        var fs_action = 'save';
        fs_ajax( this, id, fs_action );
    }
})

jQuery(document).on('click','.flexible-pickup-button-create', function(e) {
    var id = jQuery(this).attr('data-id');
    if ( typeof id != 'undefined' ) {
        e.stopImmediatePropagation();
        e.preventDefault();
        var id = fs_id(this);
        var fs_action = 'send';
        fs_ajax( this, id, fs_action );
    }
})

jQuery(document).on('click','.flexible-pickup-button-delete-created', function( e ) {
    var id = jQuery(this).attr('data-id');
    if ( typeof id != 'undefined' ) {
        e.stopImmediatePropagation();
        e.preventDefault();
        var id = fs_id(this);
        var fs_action = 'cancel';
        fs_ajax( this, id, fs_action );
    }
})

function flexible_pickup_init() {
    jQuery('.flexible-pickup-disabled').each(function(){
        jQuery(this).prop('disabled',true);
    })
}

flexible_pickup_init();