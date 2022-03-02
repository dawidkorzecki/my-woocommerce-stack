jQuery(document).ready(function( $ ){
    $('#wpmc-main_color').wpColorPicker();
    $('[data-toggle="tooltip"]').tooltip();

    toggle_wpml();
    $('#t_wpml').on( 'change', toggle_wpml );

    function toggle_wpml() {
        var all_text = '#t_login, #t_billing, #t_shipping, #t_order, #t_payment, #t_back_to_cart, #t_skip_login, #t_previous, #t_next'; 
        if ($('#t_wpml').is(':checked') ) {
            $(all_text).prop('disabled', true);
        } else {
            $(all_text).prop('disabled', false);
        }
    }
});
