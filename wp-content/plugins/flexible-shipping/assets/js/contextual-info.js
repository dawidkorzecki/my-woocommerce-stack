/**
 * Displays contextual info after input element.
 *
 * @package Contextual Info
 */

( function( $ ) {

	/**
	 * Contextual info jQuery plugin
	 *
	 * @param settings
	 * @returns {*}
	 */
	$.fn.contextualInfo = function( settings ) {
		let config = $.extend(
			{
				'id': '',
				'phrases_in': [],
				'info_html': '',
				'phrases_not_in': [],
				'is_limited_width': false,
			},
			settings
		);

		return this.each(
			function() {
				let $element = $( this );
				createInfoHTMLElement( $element );
				toggleInfoElement( $element );
				$element.keyup(
					function() {
						toggleInfoElement( $element );
					}
				);
			}
		);

		/**
		 * .
		 *
		 * @param $element
		 * @returns {string}
		 */
		function prepareInfoElementId( $element ) {
			return $element.attr( 'id' ) + "_" + config.id;
		}

		/**
		 * Create HTML info element.
		 *
		 * @param $element
		 */
		function createInfoHTMLElement( $element ) {
			$( $element ).after(
				function() {
					let classes = ['description'];

					if ( config.is_limited_width ) {
						classes.push( 'limited-width' )
					}

					return '<p class="' + classes.join( ' ' ) + '" id="' + prepareInfoElementId( $element ) + '" style="display: none;">' + config.info_html + "</p>";
				}
			);
		}

		/**
		 * Toggle info element: show when element contains one or more phrases.
		 *
		 * @param $element
		 */
		function toggleInfoElement($element) {
			let element_value = $element.val().toLowerCase();
			let show_info     = false;
			if ( config.phrases_in.length === 0 ) {
				show_info = true;
			}
			$( config.phrases_in ).each(
				function( index, value ) {
					let phrase_value = value.toLowerCase();
					show_info        = show_info || element_value.indexOf( phrase_value ) !== -1;
				}
			);
			$( config.phrases_not_in ).each(
				function( index, value ) {
					let phrase_value = value.toLowerCase();
					show_info        = show_info && element_value.indexOf( phrase_value ) === -1;
				}
			);
			$( '#' + $element.attr( 'id' ) + "_" + config.id ).toggle( show_info );
		}
	}
})( jQuery );
