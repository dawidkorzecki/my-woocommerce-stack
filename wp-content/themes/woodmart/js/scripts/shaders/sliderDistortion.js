/* global woodmart_settings */
(function($) {
	woodmartThemeModule.$document.on('wdCarouselFlickityInited', function () {
		woodmartThemeModule.sliderDistortion();
	});

	woodmartThemeModule.sliderDistortion = function() {
		var $elements = $('.wd-slider-wrapper.anim-distortion');

		if ('undefined' === typeof ShaderX || woodmartThemeModule.$body.hasClass('single-woodmart_slide')) {
			return;
		}

		$elements.each(function() {
			var $slider = $(this),
			    $slides = $slider.find('.wd-slide'),
			    imgSrc  = $slides.eq(0).data('image-url'),
			    imgSrc2 = $slides.eq(1).data('image-url');

			if ($slider.hasClass('webgl-inited') || !imgSrc || !imgSrc2) {
				return;
			}

			$slider.addClass('webgl-inited');

			var shaderX = new ShaderX({
				container     : $slider.find('.flickity-viewport'),
				sizeContainer : $slider,
				vertexShader  : woodmartThemeModule.shaders.matrixVertex,
				fragmentShader: woodmartThemeModule.shaders[woodmart_settings.slider_distortion_effect] ? woodmartThemeModule.shaders[woodmart_settings.slider_distortion_effect] : woodmartThemeModule.shaders.sliderWithWave,
				width         : $slider.outerWidth(),
				height        : $slider.outerHeight(),
				distImage     : woodmart_settings.slider_distortion_effect === 'sliderPattern' ? woodmart_settings.theme_url + '/images/dist11.jpg' : false
			});

			shaderX.loadImage(imgSrc, 0, function() {
				$slider.addClass('wd-canvas-image-loaded');
			});
			shaderX.loadImage(imgSrc, 1);
			shaderX.loadImage(imgSrc2, 0, undefined, true);

			$slider.on('change.flickity', function(event, index) {
				imgSrc = $slides.eq(index).data('image-url');

				if (!imgSrc) {
					return;
				}

				shaderX.replaceImage(imgSrc);

				if ($slides.eq(index + 1).length > 0) {
					imgSrc2 = $slides.eq(index + 1).data('image-url');
					if ( imgSrc2 ) {
						shaderX.loadImage(imgSrc2, 0, undefined, true);
					}
				}
			});
		});
	};
})(jQuery);
