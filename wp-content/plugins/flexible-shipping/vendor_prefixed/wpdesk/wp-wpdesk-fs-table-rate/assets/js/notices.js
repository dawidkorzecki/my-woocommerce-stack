jQuery( document ).ready( function () {
    jQuery( document ).on( 'click', '.flexible-shipping-log>button.show', function(event){
        event.preventDefault();
        let $parent = jQuery(this).parent();
        $parent.find('.hide').show();
        $parent.find('pre').show();
        jQuery(this).hide();
    });
    jQuery( document ).on( 'click', '.flexible-shipping-log>button.hide', function(event){
        event.preventDefault();
        let $parent = jQuery(this).parent();
        $parent.find('.show').show();
        $parent.find('pre').hide();
        jQuery(this).hide();
    });

    jQuery( document ).on( 'click', '.flexible-shipping-log>button.clipboard', function(event){
        event.preventDefault();
        let $temp = jQuery("<textarea>");
        jQuery('body').append( $temp );
        $temp.val(jQuery(this).parent().find('pre').text()).select();
        document.execCommand('copy');
        $temp.remove();
    });

    jQuery( document ).on( 'click', 'button.flexible-shipping-log-clipboard-all', function(event){
        event.preventDefault();
        let $temp = jQuery("<textarea>");
        jQuery('body').append( $temp );
        jQuery(this).parent().find('.flexible-shipping-log').find('pre').each(function(){
            $temp.val( $temp.val() + "\n\n" + jQuery( this ).text() );
        });
        $temp.select();

        document.execCommand('copy');
        $temp.remove();
    });

});