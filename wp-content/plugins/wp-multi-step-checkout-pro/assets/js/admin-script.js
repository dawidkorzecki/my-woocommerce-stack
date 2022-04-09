jQuery(document).ready(function( $ ){
    "use strict";


	var wpmc_checkout_admin = {

		init: function() {
			var self = this;

		    $('[data-toggle="tooltip"]').tooltip();

		    // When changing the color, adjust the color's hex next to the color field
		    $('input[type=color]').on('change', function() {
		        $(this).siblings('span').text($(this).val());
		    });

    		// Enable/disable text fields when WPML is toggled
			self.toggle_wpml();
    		$('#t_wpml').on( 'change', self.toggle_wpml );

			// Breadcrumb design options
			// $( '#breadcrumbs_numbers, #breadcrumbs_full ' ).parent().parent().hide();
			// self.breadcrumb_options();
			// $('input[name=template').change( self.breadcrumb_options );


    		// Design preview
		    self.change_preview_template();
		    $('input[name=template]').on( 'change', self.change_preview_template );
    		self.change_preview_color();
		    $('#main_color').on( 'change', self.change_preview_color );


    		// Arrange the settings form
		    $('label[for="registration_with_login"]').parent().css('margin-bottom', '0');
		    $('label[for="registration_with_login"]').css({'padding-bottom': '0', 'height' : '30px'});
		},

		breadcrumb_options: function() {
			var template = $('input[name=template]:checked').val();

			if ( template !== 'breadcrumb' ) {
				$( '#breadcrumbs_numbers, #breadcrumbs_full ' ).parent().parent().hide( 300, 'linear' );
			} else {
				$( '#breadcrumbs_numbers, #breadcrumbs_full ' ).parent().parent().show( 300, 'linear' );
			}
		},

		toggle_wpml: function() {
			var all_text = '#t_login, #t_billing, #t_shipping, #t_order, #t_payment, #t_back_to_cart, #t_skip_login, #t_previous, #t_billing_review, #t_shipping_review, #t_next, #t_error'; 
			if ($('#t_wpml').is(':checked') ) {
				$(all_text).prop('disabled', true);
			} else {
				$(all_text).prop('disabled', false);
			}
		},

   		change_preview_template: function() {
	        var template = $('input[name=template]:checked').val();

    	    // Change stylesheet
        	if ( typeof $('#wmsc-style-css').attr('href') !== 'undefined' ) {
	            var stylesheet = $('#wmsc-style-css').attr('href').replace(/style-(.*?)\.css/g, 'style-'+template+'.css');
    	        $('#wmsc-style-css').attr('href', stylesheet);
        	}

	        // Change .wpmc-wrapper class
    	    $('.wpmc-preview .wpmc-tabs-wrapper')
        	    .removeClass('wpmc-tabs-wrapper-md')
            	.removeClass('wpmc-tabs-wrapper-default')
	            .removeClass('wpmc-tabs-wrapper-breadcrumb')
    	        .addClass('wpmc-tabs-wrapper-'+template);
	    },

   		change_preview_color: function() {
	        var color = $('#main_color').val();
    	    var css_text = $('#wpmc-preview-css').text();

        	if ( css_text.length ) {
            	var old_css_color = css_text.match(/: #(.*?)\}/)[1];
	            $('#wpmc-preview-css').text( css_text.replace(new RegExp('#'+old_css_color, 'g'), color));
    	    }
    	}
	}
	wpmc_checkout_admin.init();
});
