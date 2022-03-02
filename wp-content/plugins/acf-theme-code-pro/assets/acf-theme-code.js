// jQuery doc ready
(function($) {
	$(document).ready(function() {

		// set the new block code
		var newblockcode = $('.acf-block-code-hidden').html();
		$('#acftc-block-code-output-code').html(newblockcode);

		// set the new options code
		var newoptionscode = $('.acf-option-code-hidden').html();
		$('#acftc-option-code-output-code').html(newoptionscode);

		// prevent # linking to top of page
		$( "a.acftc-field__copy" ).click(function( event ) {
			event.preventDefault();
		});

		// add the copy all button
		$( "#acftc-meta-box .inside").prepend('<a href="#" class="acftc-copy-all  acf-js-tooltip" title="Copy all to clipboard"></a>');
		$( "#acftc-meta-box .toggle-indicator").hide();

		// add active to the first location
		$('#acftc-group-0').addClass('location-wrap--active');

		// On toggle of the location
		$( "#acftc-group-option" ).change(function( event ) {

			// get the selected value
			var activediv = $(this).val();

			// hide all the divs
			$('.location-wrap').slideUp();

			// remove the active class from all the divs
			$('.location-wrap').removeClass('location-wrap--active');

			// slide down the one we want
			$('#' + activediv ).slideDown();

			// add the active class to the active div
			$('#' + activediv ).addClass('location-wrap--active');

		});

		// On toggle of the registration location
		$( "#acftc-registration-option" ).change(function( event ) {

			// get the selected value
			var activediv = $(this).val();

			// hide all the divs
			$('.registration-wrap').slideUp();

			// remove the active class from all the divs
			$('.registration-wrap').removeClass('registration-wrap--active');

			// slide down the one we want
			$('#' + activediv ).slideDown();

			// add the active class to the active div
			$('#' + activediv ).addClass('registration-wrap--active');

		});

		// watch the block name input for changes
		$("#acf_block_name").bind("change paste keyup", function() {

			// set some vars to the value of the input
			var block_name_raw = $(this).val().replace(/[^a-zA-Z0-9 _-]/g, '');
			var block_name_lower_dashes = block_name_raw.toLowerCase().replace(/[ _]/g, '-');
			var block_name_lower_underscores = block_name_raw.toLowerCase().replace(/[ -]/g, '_');
			var block_name_lower_keywords = block_name_raw.toLowerCase().replace(/([_ -])+/g, "', '");

			// as long as the block isn't empty
			if(block_name_raw != ''){
				$('.acf-tc-block-name--raw').text(block_name_raw);
				$('.acf-tc-block-name--lower-dashes').text(block_name_lower_dashes);
				$('.acf-tc-block-name--lower-underscores').text(block_name_lower_underscores);
				$('.acf-tc-block-name--lower-keywords').text(block_name_lower_keywords);
			} else {
				$('.acf-tc-block-name--raw').text('Example');
				$('.acf-tc-block-name--lower-dashes').text('example');
				$('.acf-tc-block-name--lower-underscores').text('blocks');
				$('.acf-tc-block-name--lower-keywords').text('example');
			}

			$('#acftc-block-code-output-code').empty();
			var newcontent = $('.acf-block-code-hidden').html();
			$('#acftc-block-code-output-code').html(newcontent);
			Prism.highlightElement($('#acftc-block-code-output-code')[0]);

		});

		// watch the option name input for changes
		$("#acf_option_name").bind("change paste keyup", function() {

			// set some vars to the value of the input
			var option_name_raw = $(this).val().replace(/[^a-zA-Z0-9 ]/g,'');
			var option_name_lower_dashes = option_name_raw.toLowerCase().split(' ').join('-');

			// as long as the block isn't empty
			if(option_name_raw != ''){
				$('.acf-tc-option-name--raw').text(option_name_raw);
				$('.acf-tc-option-name--lower-dashes').text(option_name_lower_dashes);
			} else {
				$('.acf-tc-option-name--raw').text('Options');
				$('.acf-tc-option-name--lower-dashes').text('options');
			}

			$('#acftc-option-code-output-code').empty();
			var newcontent = $('.acf-option-code-hidden').html();
			$('#acftc-option-code-output-code').html(newcontent);
			Prism.highlightElement($('#acftc-option-code-output-code')[0]);

		});

		// ACF 4 - add anchor link to each field object
		$( "div.field" ).each(function( index ) {
			var field_key = $(this).attr("data-id");
			var data_type = $(this).attr("data-type");

			// find the parent class - this is to prevent mulitple links added to a repeater
			var parent_class = $(this).parent().parent().prop('className');
			if ( ( data_type != 'tab' ) && ( data_type != 'message') && ( parent_class == 'inside' ) ) {
				  $(this).find('.row_options').first().append( '<span>| <a class="acftc-scroll__link" href="#acftc-' + field_key + '">Code</a></span>' );
			}

		});

		// ACF 5 - add anchor link to each field object
		var fieldsV5 = $('#acf-field-group-fields .acf-field-object')

			// exclude nested fields
			.filter( function() {
				return $(this).parentsUntil('#acf-field-group-fields', '.acf-field-object').length === 0;
			});

		fieldsV5.each(function( index ) {

			var field_key = $(this).attr("data-id");
			var data_type = $(this).attr("data-type");

			// no code is generated for tab and message fields
			if ( ( data_type != 'tab' ) && ( data_type != 'message') ) {

				$(this).find( '.row-options' ).eq( 0 ).append( '<a class="acftc-scroll__link" href="#acftc-' + field_key + '">Code</a>' );

			}

		});

		// smooth scroll - with offset for title and WP admin bar
		$('a.acftc-scroll__link').click(function(event) {

			// prevent default
			event.preventDefault();

			// find the location that's selected
			var location = $( "#acftc-group-option option:selected" ).val();

			// if there is nothing selected
			if( location == null ) {
				var location = 'acftc-meta-box .inside';
			}

			// find the field that we want to scroll to (from the hash)
			var hash = $(this).attr("href");

			// define a target field
			var target = $('#' + location + ' ' + hash);

			// find the offset from the top of our target field
			var target_offset = target.offset().top;

			// after the large bp, the header is fixed
			if (window.matchMedia("(max-width: 782px)").matches) {
				var customoffset = 80; // increase offset for small screens
			} else {
				var customoffset = 60; // default offset for large screens
			}

			$('html,body').animate({
				scrollTop: target_offset - customoffset
			}, 1000);

			return false;

		});

	});

	var copyCode = new Clipboard('.acftc-field__copy', {
		target: function(trigger) {
			return trigger.nextElementSibling;
		}
	});

	copyCode.on('success', function(e) {
		e.clearSelection();
	});

	// copy all
	var copyAll = new Clipboard('.acftc-copy-all', {
		text: function(trigger) {
			var $allCodeBlocks = $('#acftc-meta-box .location-wrap--active .acftc-field-code pre');
			return $allCodeBlocks.text();
		}
	});

	copyAll.on('success', function(e) {
		e.clearSelection();
	});

} )( jQuery );
