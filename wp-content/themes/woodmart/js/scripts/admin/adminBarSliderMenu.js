/* global woodmart_settings */
(function($) {
	woodmartThemeModule.adminBarSliderMenu = function() {
		var $sliderWrapper = $('.wd-slider-wrapper');
		var $adminBar = $('#wpadminbar');

		if ($sliderWrapper.length > 0 && $adminBar.length > 0) {
			$adminBar.find('#wp-admin-bar-theme-dashboard #wp-admin-bar-theme-dashboard-default').append('<li id="wp-admin-bar-woodmart_slider" class="menupop"><a class="ab-item" href="/wp-admin/edit.php?post_type=woodmart_slide">Slider<span class="wp-admin-bar-arrow" aria-hidden="true"></span></a><div class="ab-sub-wrapper"><ul class="ab-submenu"></ul></div></li>');

			$sliderWrapper.each(function() {
				var $slider = $(this);
				var sliderId = $slider.data('id');
				var sliderData = woodmart_admin_bar_sliders[sliderId];
				var $sliderSubMenu = $('#wp-admin-bar-woodmart_slider > .ab-sub-wrapper > .ab-submenu');
				if (!sliderData) {
					return;
				}

				$sliderSubMenu.append('<li id="' + sliderId + '" class="menupop"><a href="' + sliderData.url + '" class="ab-item" target="_blank">' + sliderData.title + '<span class="wp-admin-bar-arrow" aria-hidden="true"></span></a><div class="ab-sub-wrapper"><ul class="ab-submenu"></ul></div></li>');

				$slider.find('.wd-slide').each(function() {
					var $slide = $(this);
					var slideId = $slide.data('id');
					var slideData = sliderData.slides[slideId];

					$sliderSubMenu.find('#' + sliderId + ' > .ab-sub-wrapper > .ab-submenu').append('<li><a href="' + slideData.url + '" class="ab-item" target="_blank">' + slideData.title + '</a></li>');
				});
			});
		}
	};

	woodmartThemeModule.adminBarSliderMenu();
})(jQuery);
