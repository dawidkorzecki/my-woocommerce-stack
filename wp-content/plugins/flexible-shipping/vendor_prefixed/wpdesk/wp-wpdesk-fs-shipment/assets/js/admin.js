function fs_select2() {
	let elements = jQuery( '.fs_select2' );
	if ( elements.length ) {
		if (jQuery.fn.selectWoo) {
			elements.selectWoo();
		} else {
			elements.select2();
		}
	}
}

function fs_shipment_removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

function fs_shipment_trimChar(string, charToRemove) {
    while(string.charAt(0)==charToRemove) {
        string = string.substring(1);
    }

    while(string.charAt(string.length-1)==charToRemove) {
        string = string.substring(0,string.length-1);
    }

    return string;
}


// Order functions
function fs_id( element ) {
    return jQuery(element).closest('.flexible_shipping_shipment').attr('data-id');
}

function fs_data_set_val( data, name, val ) {
    if ( typeof name == 'undefined' ) {
        return data;
    }
    if ( name.indexOf("[") == -1 ) {
        data[name] = val;
    }
    else {
        var names = name.split("[");
        var data2 = data;
        var data3 = data;
        var name2 = '';
        jQuery.each(names,function(index,name) {
            name2 = name.replace("]","");
            if ( typeof data2[name2] == 'undefined' ) {
                data2[name2] = {};
            }
            data3 = data2;
            data2 = data2[name2];
        });
        data3[name2] = val;
    }
    return data;
}

function fs_ajax(button, id, fs_action) {
    jQuery('.button-shipping').attr('disabled', true);
    jQuery(button).parent().find('.spinner').css({visibility: 'visible'});
    var data = {};

    jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_content input, #flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_content select, #flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_content textarea').each(function () {
        if (jQuery(this).attr('type') == 'radio') {
            data = fs_data_set_val( data, jQuery(this).attr('name'), jQuery('#flexible_shipping_shipment_' + id + ' input[name=' + jQuery(this).attr('name') + ']:checked').val() );
        }
        else if (jQuery(this).attr('type') == 'checkbox') {
            if (jQuery(this).is(':checked')) {
                data = fs_data_set_val( data, jQuery(this).attr('name'), jQuery(this).val() );
            }
            else {
                data = fs_data_set_val( data, jQuery(this).attr('name'), '' );
            }
        }
        else {
            data = fs_data_set_val( data, jQuery(this).attr('name'), jQuery(this).val() );
        }
    });

    var nonce = jQuery('#flexible_shipping_shipment_nonce_' + id).val();

    jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').hide();
    jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').removeClass("flexible_shipping_shipment_message_error");

    jQuery.ajax({
        url: fs_shipment_admin.ajax_url,
        type: 'POST',
        data: {
            fs_action: fs_action,
            action: 'flexible_shipping',
            nonce: nonce,
            shipment_id: id,
            data: data,
        },
        dataType: 'json',
    }).done(function (response) {
        if (response) {
            if (response == '0') {
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').show();
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').html("Invalid response: 0");
            }
            else if (response.status == 'success') {
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_content').html(response.content);
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').hide();
                if ( typeof response.message != 'undefined' ) {
                    jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').show();
                    jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').html(response.message);
                }
            }
            else {
            	if ( typeof response.content !== 'undefined' ) {
					jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_content').html(response.content);
				}
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').addClass("flexible_shipping_shipment_message_error");
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').show();
                jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').html(response.message);
            }
        }
        else {
            jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').addClass("flexible_shipping_shipment_message_error");
            jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').show();
            jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').html("Request failed: invalid method?");
        }
    }).always(function () {
        jQuery('.button-shipping').attr('disabled', false);
        jQuery('.shipping-spinner').parent().find('.spinner').css({visibility: 'hidden'});
		fs_select2();
		jQuery('#flexible_shipping_shipment_' + id).trigger( "flexible_shipping_ajax_fs_action_after" );
    }).fail(function (jqXHR, textStatus) {
		jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').addClass("flexible_shipping_shipment_message_error");
        jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').show();
        jQuery('#flexible_shipping_shipment_' + id + ' .flexible_shipping_shipment_message').html("Request failed: " + textStatus + " " + jqXHR.status);
    })
}

jQuery(document).ready(function(){
    if ( jQuery('#flexible_shipping_labels_url').length ) {
        window.location.href = jQuery('#flexible_shipping_labels_url').attr('href');
    }

    if ( jQuery('a.shipping_manifest_download').length == 1 ) {
        window.location.href = jQuery('a.shipping_manifest_download').attr('href');
    }

    if ( typeof window.history.pushState == 'function' ) {
        var url = document.location.href;
        var url2 = document.location.href;
        url = fs_shipment_removeParam('bulk_flexible_shipping_labels', url);
        url = fs_shipment_removeParam('bulk_flexible_shipping_send', url);
        url = fs_shipment_removeParam('bulk_flexible_shipping_manifests', url);
        url = fs_shipment_removeParam('bulk_flexible_shipping_no_labels_created', url);
        url = fs_shipment_trimChar(url,'?');
        if ( url != url2 ) {
            window.history.pushState({}, "", url);
        }
    }
});

