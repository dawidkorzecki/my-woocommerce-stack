jQuery(window).ready(function($){
    "use strict";

	var wpmc_checkout = {
		$tabs 			: $( '.wpmc-tab-item' ),
		$sections 		: $( '.wpmc-step-item' ),
		$buttons 		: $( '.wpmc-nav-button' ),
		$checkout_form 	: $( 'form.woocommerce-checkout' ),
		$coupon_form 	: $( '#checkout_coupon' ),
		$before_form 	: $( '#woocommerce_before_checkout_form' ),
		current_step	: $( 'ul.wpmc-tabs-list' ).data( 'current-title' ),
		init: function() {
			var self = this;

			// add the "wpmc_switch_tab" trigger
			$( '.woocommerce-checkout' ).on( 'wpmc_switch_tab', function( event, theIndex) {
				self.switch_tab( theIndex );
			});

			$( '.wpmc-step-item:first').addClass( 'current');

			// Click on "next" button
			$( '#wpmc-next, #wpmc-skip-login').on( 'click', function() {
				self.validate_this_step();
			}); 

			$( '.woocommerce-checkout' ).on( 'wpmc_validate', function( event ) {
				return self.validate_this_step_JS();
			});

			// Click on "previous" button
			$( '#wpmc-prev' ).on( 'click', function() {
				self.switch_tab( self.current_index() - 1);
				self.scroll_top();
			});

			// After submit, switch tabs where the invalid fields are
			$( document ).on( 'checkout_error', function() {

				if ( ! $( '#createaccount' ).is( ':checked') ) {
					$( '#account_password_field, #account_username_field, #account_confirm_password_field' ).removeClass( 'woocommerce-invalid-required-field' );
				}

				if ( ! $( '#ship-to-different-address-checkbox' ).is( ':checked' ) ) {
					$( '.woocommerce-shipping-fields__field-wrapper p' ).removeClass( 'woocommerce-invalid-required-field' );
				}

				var section_class = $('.woocommerce-invalid-required-field')
							.filter( function(i) {
								return $(this).css('display') != 'none';
							} )
							.closest( '.wpmc-step-item' )
							.attr( 'class' );

				$( '.wpmc-step-item' ).each( function( i ) {
					if ( $( this ).attr( 'class' ) === section_class ) {
						self.switch_tab(i)
						self.scroll_top();
					}
				})
			});

		 
			// Click on a step
			if ( WPMC.clickable_steps === '1' ) {
				this.set_clickability();

				$( '.wpmc-tab-item' ).on( 'click' , function() {

					if ( $( this ).hasClass( 'current' ) ) {
						return; 
					}

					if ( self.current_index() < $( this ).index() - 1 && ! $( this ).hasClass( 'visited' ) ) {
						return;
					}

					if ( self.current_index() < $( this ).index() ) {
						// Go to a next step 
						self.validate_this_step(); 
					} else {
						// Go to a previous step
						self.switch_tab( $( this ).index() ); 
						self.scroll_top();
					}

				});
			}

			// Compatibility with Super Socializer
			if ( $( '.the_champ_sharing_container' ).length > 0 ) {
				$( '.the_champ_sharing_container' ).insertAfter( $( this ).parent().find( '#checkout_coupon' ) );
			}

			// Prevent form submission on Enter
			$( '.woocommerce-checkout' ).on( 'keydown', function( e ) {
				if ( e.which === 13 ) {
					e.preventDefault();
					return false;
				}
			});

			// "Back to Cart" button
			$( '#wpmc-back-to-cart' ).on( 'click', function() {
				window.location.href = $( this ).data( 'href' ); 
			});

			// Switch tabs with <- and -> keyboard arrows
			if ( WPMC.keyboard_nav === '1' ) {
				$( document ).on( 'keydown', function ( e ) {
				  var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
				  if ( key === 39 ) {
					  self.validate_this_step(); 
				  }
				  if ( key === 37 ) {
					  self.switch_tab( self.current_index() - 1 );
					  self.scroll_top();
				  }
				});
			}

			// Show address review, if the option is enabled
			if ( $( '#address_review' ).length > 0 ) {
				$( '.woocommerce-checkout' ).on( 'wpmc_after_switching_tab', function() {
					self.fill_address_review();
					return false;
				});
			}

			// Change tab if the hash #step-0 is present in the URL
			if ( typeof window.location.hash != 'undefined' && window.location.hash ) {
				changeTabOnHash( window.location.hash );
			}
			$( window ).on( 'hashchange', function() { 
				changeTabOnHash( window.location.hash ) 
			} ); 
			function changeTabOnHash( hash ) {
				if ( /step-[0-9]/.test( hash ) ) {
					var step = hash.match( /step-([0-9])/ )[1];
					self.switch_tab( step );
					self.scroll_top();
				}
				var hash_tab = '.wpmc-tab-item.wpmc-' + hash.replace( '#', '' );
				if ( $( hash_tab ).length > 0 && $( hash_tab + '.current' ).length === 0 ) {
					self.switch_tab( $( '.wpmc-tab-item' ).index( $( hash_tab ) ) );
					self.scroll_top();
				} 
			}


			// select2
			if ( typeof $(this).selectWoo !== 'undefined' ) {
				self.wc_country_select_select2();
				$( document.body ).on( 'country_to_state_changed', function() {
					self.wc_country_select_select2();
				});
			}


			// Ripple effect on an element 
			$( '.wpmc-ripple' ).on( 'click', function ( event ) {
				  var elem = $( this );
				  
				  var ripple = $( '<div/>' )
					.addClass( 'wpmc-ripple-effect' )
					.css({
					  height    : elem.height(),
					  width     : elem.width(),
					  top       : event.pageY - elem.offset().top - elem.height()/2, 
					  left      : event.pageX - elem.offset().left - elem.width()/2,
					}) 
					.appendTo( elem );

				  window.setTimeout( function() {
					ripple.remove();
				  }, 2000);
			});

			// Add the URL hash 
			if ( WPMC.url_hash && typeof window.location.hash != 'undefined' && ! window.location.hash ) {
				window.location = '#' + self.$tabs.eq( 0 ).data( 'step-title' );
			}
		},
		current_index: function() {

			return this.$sections.index( this.$sections.filter( '.current'));
		},
		validate_this_step: function() {
			var self = this;

			// validation per step isn't enabled
			if ( WPMC.validation_per_step !== '1' ) {
				this.switch_tab( this.current_index() + 1);
				return;
			}

			// don't validate the login step
			if ( $( '.wpmc-step-login.current' ).length > 0 ) {
				$( '.woocommerce-NoticeGroup-checkout' ).remove();
				this.switch_tab( this.current_index() + 1);
				return;
			}

			$( '.woocommerce-NoticeGroup' ).remove();

			// gather the input values
			var input_values = {};
			$( '.wpmc-step-item.current input, .wpmc-step-item.current select, .wpmc-step-item.current textarea' ).each( function() {
				if ( ! $( this ).is( ':hidden' ) && typeof $( this ).attr( 'name' ) !== 'undefined' ) {
					input_values[ $( this ).attr( 'name' ).replace(/\[/g, '').replace(/\]/g, '') ] = $( this ).val();
					if ( $( this ).parents( '.form-row' ).hasClass( 'validate-required' ) ) {
						$( this ).parents( '.form-row' ).removeClass( 'woocommerce-validated' ).removeClass( 'woocommerce-invalid' );
					} 
				}
			} );
			/*
			$( '.wpmc-step-item.current input[type=checkbox]' ).each( function() {
				if ( ! $( this ).prop( 'checked' ) && ! $( this ).parents('.validate-required') || typeof $( this ).attr( 'name' ) !== 'undefined' ) {
					delete input_values[ $( this ).attr( 'name' ) ];
				}
			} );
			*/

			if ( typeof window.location.href != 'undefined' && window.location.href.search('ajax_sent=1') !== - 1 ) {
				console.log( input_values );
			}

			// send the input values through an AJAX call
			input_values[ 'action' ] = 'wpms_checkout_errors';
			input_values[ '_ajax_nonce' ] = WPMC.nonce;
			$.ajax({
				type		: "POST",
				url			: WPMC.ajax_url,
				data		: input_values,
				beforeSend	: function() {
					$( '.wpmc-footer-right.wpmc-nav-buttons button' ).prop( 'disabled', true );
					block( $(".wpmc-step-item.current") );
				},
				complete	: function() {
					$( '.wpmc-footer-right.wpmc-nav-buttons button' ).prop( 'disabled', false );
					unblock( $(".wpmc-step-item.current") );
				},
				success		: ajax_callback,
				error		: function( XMLHttpRequest, textStatus, errorThrown ) {
					console.log( 'Status: ' + textStatus, 'Error: ' + errorThrown ); 
				},
				dataType	: 'json',
			});

			var additional_errors = $( '.woocommerce-checkout' ).triggerHandler( 'wpmc_validate' );

        	function ajax_callback( data ) {
				var number_errors = 0;
				var errors_container = $( '<ul>' ).addClass( 'woocommerce-error' ).attr( 'role', 'alert' );
				
				if ( window.location.href.search('ajax_return=1') !== -1 ) {
					console.log( data );
				}

				// filter the error messages to the inputs for this step
				Object.keys( input_values ).forEach( function( i ) {
					if ( typeof data[i] === 'undefined' && typeof additional_errors[i] !== 'undefined' ) {
						data[i] = additional_errors[i];
					}
					if ( typeof data[i] !== 'undefined' ) {
						number_errors ++;
						errors_container.append( '<li>' + data[i] + '</li>' );
						$( '#' + i + '_field' ).addClass( 'woocommerce-invalid' );
					}
				});


				// add "woocommerce-validated" class to the validated fields
				$( '.wpmc-step-item.current input, .wpmc-step-item.current select, .wpmc-step-item.current textarea' ).each( function() {
					if ( $( this ).parents( '.form-row' ).hasClass( 'validate-required' ) 
						&& ! $( this ).is( ':hidden' ) 
						&& typeof data[ $(this).attr( 'id' ) ] === 'undefined' ) {
						$( this ).parents( '.form-row' ).addClass( 'woocommerce-validated' );
					}
				} );

				// show errors or switch tab
				if ( number_errors > 0 ) {
					self.scroll_top(); 
					$( 'form.woocommerce-checkout' ).prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout wpmc-error" data-for-step="' + self.current_step + '">' );
					$( '.woocommerce-NoticeGroup-checkout' ).prepend( errors_container );
				} else {
					self.switch_tab( self.current_index() + 1 );
				}
			}
		},
		set_clickability: function() {
			// Make the step tabs clickable
			var self = this;
        	$( '.wpmc-tab-item' ).each( function() {
            	$( this ).removeClass( 'wpmc-not-clickable' );
            	if ( self.current_index() < $( this ).index() - 1 && ! $( this ).hasClass( 'visited' )) { 
                	$( this ).addClass( 'wpmc-not-clickable' );
            	}
        	});
    	},
		validate_this_step_JS: function() { 
			var errors = {};

			// Empty required fields
			$( '.wpmc-step-item.current .validate-required input, .wpmc-step-item.current .validate-required select' ).each( function( i ) {
				if ( $( this ).parents( '.shipping_address' ).length > 0 && $( '.shipping_address' ).is( ':hidden' ) ) {
					return errors;
				}
				if ( $( this ).parents( '.create-account' ).length > 0 && $( '.create-account' ).is( ':hidden' ) ) {
					return errors;
				}
				if ( $( this ).is( ':hidden' ) ) {
					return errors;
				}
				if ( typeof $( this ).attr( 'name' ) === 'undefined' ) {
					return errors;
				}

				var v = parseFloat( $( this ).val() );

				if ( ( ! $( this ).is( ':radio' ) && $( this ).val().length === 0 ) 
					|| ($( this ).is( ':radio' ) && ! $( '.wpmc-step-item.current .validate-required input[name=' + $( this ).attr( 'name' ) + ']' ).is( ':checked' ) )
					|| ($( this ).is( ':checkbox' ) && $( this ).parents( '.wpmc-step-login' ).length == 0 && ! $( this ).is( ':checked' ) )
					|| ($( this ).is( '[type=number]' ) && ( v < $( this ).attr( 'min' ) || v > $( this ).attr( 'max' ) ) ) ) {						
						$( this ).parents( '.validate-required' ).removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
						var field_id = $( this ).attr('id');
						var field_label = $( this ).closest('.form-row').find('label').first().clone().find('abbr').remove().end().text();
						errors[field_id] = WPMC.validation_strings.required.replace( '{0}', field_label.trim() );
						if ( $( this ).is( ':radio' ) ) {
							errors[$( this ).attr('name')] = WPMC.validation_strings.required.replace( '{0}', field_label.trim() );
						}
					} else {
						$( this ).parents( '.validate-required' ).addClass( 'woocommerce-validated' ).removeClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
					}
			});

			// Email
			$( '.wpmc-step-item.current .validate-email input' ).each( function( i ) {
				if ( $( this ).parents( '.shipping_address' ).length > 0 && $( '.shipping_address' ).is( ':hidden' ) ) {
					return errors;
				}
				// the pattern from the woocommerce/assets/js/frontend/checkout.js file
				var pattern = new RegExp( /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i );

				var this_field = $( this ).parent().parent();

				if ( ! pattern.test( $( this ).val() ) ) {
					var field_id = $( this ).attr('id');
					var field_label = $( this ).closest('.form-row').children('label').clone().find('abbr').remove().end().text();
					errors[field_id] = WPMC.validation_strings.email.replace( '{0}', field_label.trim() );
					this_field.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-email' );
				} else {
					this_field.addClass( 'woocommerce-validated' ).removeClass( 'woocommerce-invalid woocommerce-invalid-email' );
				}
			});
									
			return errors; 
		},
		scroll_top: function() {
			// scroll to top
			if ( $( '.wpmc-tabs-wrapper' ).length === 0 ) {
				return;
			}

			var diff = $( '.wpmc-tabs-wrapper' ).offset().top - $( window ).scrollTop();
			var scroll_offset = 70;
			if ( typeof WPMC.scroll_top !== 'undefined' ) {
				scroll_offset = WPMC.scroll_top;
			}
			if ( diff < -40 ) {
				$( 'html, body' ).animate({
					scrollTop: $( '.wpmc-tabs-wrapper' ).offset().top - scroll_offset, 
				}, 800);
			}
		},
		switch_tab: function( theIndex ) {
			var self = this;

			$( '.woocommerce-checkout' ).trigger( 'wpmc_before_switching_tab' );

			if ( theIndex < 0 || theIndex > this.$sections.length - 1 ) {
				return false;
			}

			this.scroll_top(); 

			$( 'html, body' ).promise().done( function() {

				self.$tabs.removeClass( 'previous' ).filter( '.current' ).addClass( 'previous' ).addClass( 'visited' );
				self.$sections.removeClass( 'previous' ).filter( '.current' ).addClass( 'previous' ).addClass( 'visited' );
				// $( '.woocommerce-NoticeGroup-checkout:not(.wpmc-error)' ).show();

				// Change the tab
				self.$tabs.removeClass( 'current' );
				self.$tabs.eq( theIndex ).addClass( 'current' );
				self.current_step = self.$tabs.eq( theIndex ).data( 'step-title' );
				$( '.wpmc-tabs-list' ).data( 'current-title', self.current_step );
			 
				// Change the section
				self.$sections.removeClass( 'current' );
				self.$sections.eq( theIndex ).addClass( 'current' ).unblock();

				// Which buttons to show?
				self.$buttons.removeClass( 'current' );
				self.$coupon_form.hide();
				self.$before_form.hide();

				// Remove errors from previous steps
				if ( typeof $( '.woocommerce-NoticeGroup-checkout' ).data( 'for-step' ) !== 'undefined' && $( '.woocommerce-NoticeGroup-checkout' ).data( 'for-step' ) !== self.current_step ) {
					$( '.woocommerce-NoticeGroup-checkout' ).remove();
				}

				// Show "next" button 
				if ( theIndex < self.$sections.length - 1 ) {
					$( '#wpmc-next' ).addClass( 'current' );
				}

				// Show "skip login" button
				if ( theIndex === 0 && $( '.wpmc-step-login' ).length > 0 ) {
					$( '#wpmc-skip-login').addClass( 'current' );
					$( '#wpmc-next' ).removeClass( 'current' );
				//	$( '.woocommerce-NoticeGroup-checkout:not(.wpmc-error)' ).hide();
				}
				// Last section
				if ( theIndex === self.$sections.length - 1 ) {
					$( '#wpmc-prev' ).addClass( 'current' );
					$( '#wpmc-submit' ).addClass( 'current' );
				}
				// Show "previous" button 
				if ( theIndex != 0 ) {
					$( '#wpmc-prev' ).addClass( 'current' );
				}

				if ( $( '.wpmc-step-review.current' ).length > 0 ) {
					self.$coupon_form.show();
				}

				if ( $( '.wpmc-' + self.$before_form.data( 'step' ) + '.current' ).length > 0 ) {
					self.$before_form.show();
				}

				if ( WPMC.url_hash ) {
					window.location = '#' + self.$tabs.eq( theIndex ).data( 'step-title' );
				}

				$( '.woocommerce-checkout' ).trigger( 'wpmc_after_switching_tab' );

			});

			this.set_clickability();
		},
    	toggle_error: function( showError ) {
			// This is a helper function for the old JS validation "validate_this_step" that was replaced with an AJAX validation
			if ( showError ) {
				var errorMsg = ( WPMC.error_msg.length > 0 ) ? WPMC.error_msg : 'Please fix the errors on this step before moving to the next step';
				var errorStyle = 'width: ' + $( '.wpmc-tabs-wrapper' ).width() + 'px; left: ' + Math.round( $( '.wpmc-tabs-wrapper' ).offset().left ) + 'px;';
				$( '.wpmc-error' ).remove();
				var thisError = '<div style="clear: both;"></div>'
					+ '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout wpmc-error" style="' + errorStyle + '"><ul class="woocommerce-error" role="alert">'
					+ '   <li>' + errorMsg + '</li>'
					+ '</ul></div>';
				$( thisError ).insertAfter( '.wpmc-tabs-wrapper' ).delay( 3000 ).fadeOut(); 
			} else {
				$( '.wpmc-error' ).remove();
			}
		},

		fill_address_review: function( index ) {
			var self = this;
			if ( self.current_step === 'review' ) {
				$('#address_review .address_review_1, #address_review .address_review_2').empty();

				// Billing fields
				$('#address_review .address_review_1').append( '<h3>' + WPMC.translation_strings.t_billing_review + '</h3>' );
				$('#address_review .address_review_1').append( self.address_review_rows( '.woocommerce-billing-fields' ) );


				// Shipping fields
				if ( $( '#ship-to-different-address-checkbox' ).length > 0 && $( '#ship-to-different-address-checkbox' ).is( ':checked' ) ) {
					$('#address_review .address_review_2').append( '<h3>' + WPMC.translation_strings.t_shipping_review + '</h3>' );
					$('#address_review .address_review_2').append( self.address_review_rows( '.woocommerce-shipping-fields__field-wrapper' ) );
					$('#address_review .address_review_2').append( self.address_review_rows( '.woocommerce-additional-fields__field-wrapper' ) );
				} else {
					$('#address_review .address_review_1').append( self.address_review_rows( '.woocommerce-additional-fields__field-wrapper' ) );
				}
			}
			return false;
		},

		address_review_rows: function( section ) {
			var output = '';
			$( section + ' .form-row').each( function() {
				var input = $(this).find(':input');
				var label = $(this).find('label').clone().children().remove().end();
				if ( input.val() ) {
					var val = input.val();
					if ( input.is(':checkbox') && input.is(':checked') ) val = '&#9745;';
					if ( input.is(':checkbox') && ! input.is(':checked') ) val = '&#9744;';
					if ( input.is('select') ) val = $('#' + $(this).attr('id') + ' option:selected').text();
					if ( ( input.attr('id') === 'billing_country' || input.attr('id') === 'shipping_country' ) && typeof input.attr('type') !== 'undefined' && input.attr('type') === 'hidden') val = input.siblings().text();
					output += '<p id="' + label.attr('for') + '_addr_rev"><span>' + label.text() + '</span> ' + val + '</p>';
				}
			});
			return output;
		},
		wc_country_select_select2: function() {
			var self = this;
			$( 'select.country_select:not(visible), select.state_select:not(visible)' ).each( function() {
				var $this = $( this );

				var select2_args = $.extend({
					placeholder: $this.attr( 'data-placeholder' ) || $this.attr( 'placeholder' ) || '',
					label: $this.attr( 'data-label' ) || null,
					width: '100%'
				}, self.getEnhancedSelectFormatString() );

				$( this )
					.on( 'select2:select', function() {
						$( this ).trigger( 'focus' ); // Maintain focus after select https://github.com/select2/select2/issues/4384
					} )
					.selectWoo( select2_args );
			});
		},
		getEnhancedSelectFormatString: function() {
			return {
				'language': {
					errorLoading: function() {
						// Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
						return wc_country_select_params.i18n_searching;
					},
					inputTooLong: function( args ) {
						var overChars = args.input.length - args.maximum;

						if ( 1 === overChars ) {
							return wc_country_select_params.i18n_input_too_long_1;
						}

						return wc_country_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
					},
					inputTooShort: function( args ) {
						var remainingChars = args.minimum - args.input.length;

						if ( 1 === remainingChars ) {
							return wc_country_select_params.i18n_input_too_short_1;
						}

						return wc_country_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
					},
					loadingMore: function() {
						return wc_country_select_params.i18n_load_more;
					},
					maximumSelected: function( args ) {
						if ( args.maximum === 1 ) {
							return wc_country_select_params.i18n_selection_too_long_1;
						}

						return wc_country_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
					},
					noResults: function() {
						return wc_country_select_params.i18n_no_matches;
					},
					searching: function() {
						return wc_country_select_params.i18n_searching;
					}
				}
			};
		}

	};

	/**
	 * Check if a node is blocked for processing.
	 *
	 * @param {JQuery Object} $node
	 * @return {bool} True if the DOM Element is UI Blocked, false if not.
	 */
	var is_blocked = function( $node ) {
		return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
	};

	/**
	 * Block a node visually for processing.
	 *
	 * @param {JQuery Object} $node
	 */
	var block = function( $node ) {
		if ( ! is_blocked( $node ) ) {
			$node.addClass( 'processing' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		}
	};

	/**
	 * Unblock a node after processing is complete.
	 *
	 * @param {JQuery Object} $node
	 */
	var unblock = function( $node ) {
		$node.removeClass( 'processing' ).unblock();
	};


	wpmc_checkout.init();
});
