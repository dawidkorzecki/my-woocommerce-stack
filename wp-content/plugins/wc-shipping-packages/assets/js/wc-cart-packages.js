jQuery(window).on('load', function() {
    jQuery(".cart .product-price,.cart .product-quantity, .cart .product-subtotal").each(function() {
        var target = jQuery(this);

        var originalHtml = target.html();
        var trimmedHtml = jQuery.trim(originalHtml);

        if (trimmedHtml.length === 0 && originalHtml.length > trimmedHtml.length) {
            target.html("");
        }
    });
});