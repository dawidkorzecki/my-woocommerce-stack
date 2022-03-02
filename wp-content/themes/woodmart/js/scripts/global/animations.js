/* global xts_settings */
(function($) {
	woodmartThemeModule.$document.on('wdElementorSectionReady wdElementorColumnReady wdElementorGlobalReady', function() {
		woodmartThemeModule.animations();
	});

	woodmartThemeModule.animations = function() {
		if (typeof $.fn.waypoint === 'undefined') {
			return;
		}

		$('[class*="wd-animation"]').each(function() {
			var $element = $(this);

			if ('inited' === $element.data('wd-waypoint') || $element.parents('.wd-autoplay-animations-off').length > 0) {
				return;
			}

			$element.data('wd-waypoint', 'inited');

			$element.waypoint(function() {
				var $this = $($(this)[0].element);

				var classes = $this.attr('class').split(' ');
				var delay = 0;

				for (var index = 0; index < classes.length; index++) {
					if (classes[index].indexOf('wd_delay_') >= 0) {
						delay = classes[index].split('_')[2];
					}
				}

				$this.addClass('wd-animation-ready');

				setTimeout(function() {
					$this.addClass('wd-animated');
				}, delay);
			}, {
				offset: '90%'
			});
		});
	};

	$(document).ready(function() {
		woodmartThemeModule.animations();
	});
})(jQuery);