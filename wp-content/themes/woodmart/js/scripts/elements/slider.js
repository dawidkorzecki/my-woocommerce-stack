/* global woodmart_settings */
(function($) {
	$.each([
		'frontend/element_ready/wd_slider.default'
	], function(index, value) {
		woodmartThemeModule.wdElementorAddAction(value, function() {
			woodmartThemeModule.sliderAnimations();
			woodmartThemeModule.carouselInitFlickity();
			woodmartThemeModule.sliderLazyLoad();
		});
	});

	woodmartThemeModule.sliderClearAnimations = function($active, firstLoad) {
		// WPB clear on first load first slide.
		if (firstLoad) {
			$active.find('[class*="wpb_animate"]').each(function() {
				var $this = $(this);
				var classes = $this.attr('class').split(' ');
				var name;

				for (var index = 0; index < classes.length; index++) {
					if (classes[index].indexOf('wd-anim-name_') >= 0) {
						name = classes[index].split('_')[1];
					}
				}

				$this.removeClass('wpb_start_animation animated').removeClass(name);
			});
		}

		// WPB clear all siblings slides.
		$active.siblings().find('[class*="wpb_animate"]').each(function() {
			var $this = $(this);
			var classes = $this.attr('class').split(' ');
			var delay = 0;
			var name;

			for (var index = 0; index < classes.length; index++) {
				if (classes[index].indexOf('wd-anim-delay_') >= 0) {
					delay = parseInt(classes[index].split('_')[1]);
				}

				if (classes[index].indexOf('wd-anim-name_') >= 0) {
					name = classes[index].split('_')[1];
				}
			}

			setTimeout(function() {
				$this.removeClass('wpb_start_animation animated').removeClass(name);
			}, delay);
		});
	};

	woodmartThemeModule.sliderAnimations = function() {
		$('.wd-slider').each(function() {
			var $carousel = $(this);

			$carousel.find('[class*="wd-animation"]').each(function() {
				$(this).addClass('wd-animation-ready');
			});

			runAnimations(0, true);

			$carousel.on('change.flickity', function(event, index) {
				runAnimations(index, false);
			});

			function runAnimations(slideIndex, firstLoad) {
				var $active = $carousel.find('.wd-slide').eq(slideIndex);

				woodmartThemeModule.sliderClearAnimations($active, firstLoad);
				woodmartThemeModule.runAnimations($active, firstLoad);
			}
		});
	};

	woodmartThemeModule.runAnimations = function($active, firstLoad) {
		// Elementor.
		$active.siblings().find('[class*="wd-animation"]').removeClass('wd-animated');

		$active.find('[class*="wd-animation"]').each(function() {
			var $this = $(this);
			var classes = $this.attr('class').split(' ');
			var delay = 0;

			for (var index = 0; index < classes.length; index++) {
				if (classes[index].indexOf('wd_delay_') >= 0) {
					delay = parseInt(classes[index].split('_')[2]);
				}
			}

			if (firstLoad) {
				delay += 500;
			}

			setTimeout(function() {
				$this.addClass('wd-animated');
			}, delay);
		});

		// WPB.
		$active.find('[class*="wpb_animate"]').each(function() {
			var $this = $(this);
			var classes = $this.attr('class').split(' ');
			var delay = 0;
			var name;

			for (var index = 0; index < classes.length; index++) {
				if (classes[index].indexOf('wd-anim-delay_') >= 0) {
					delay = parseInt(classes[index].split('_')[1]);
				}

				if (classes[index].indexOf('wd-anim-name_') >= 0) {
					name = classes[index].split('_')[1];
				}
			}

			if (firstLoad) {
				delay += 500;
			}

			setTimeout(function() {
				$this.removeClass('wd-off-anim').addClass('wpb_start_animation animated').addClass(name);
			}, delay);
		});
	};

	woodmartThemeModule.sliderLazyLoad = function() {
		woodmartThemeModule.$window.on('wdEventStarted', function() {
			$('.wd-slider').each(function() {
				load(0, $(this));
			});
		});

		$('.wd-slider').on('change.flickity', function(event, index) {
			load(index, $(this));
		});

		function load(index, $slider) {
			var active = $slider.find('.wd-slide').eq(index);
			var $els = $slider.find('[id="' + active.attr('id') + '"]');

			$slider.find('.wd-slide').eq(index + 1).addClass('woodmart-loaded');
			active.addClass('woodmart-loaded');

			$els.each(function() {
				$(this).addClass('woodmart-loaded');
			});
		}
	};

	woodmartThemeModule.carouselInitFlickity = function() {
		if (woodmartThemeModule.$body.hasClass('single-woodmart_slide')) {
			return;
		}

		$('.wd-slider-wrapper:not(.scroll-init)').each(function() {
			init($(this));
		});

		if (typeof ($.fn.waypoint) != 'undefined') {
			$('.wd-slider-wrapper.scroll-init').waypoint(function() {
				var $this = $($(this)[0].element);
				init($this);
			}, {
				offset: '100%'
			});
		}

		function init($carousel) {
			if (woodmartThemeModule.windowWidth <= 1024 && $carousel.hasClass('disable-owl-mobile')) {
				return;
			}

			var loop = true;

			// Fix fade glitch with two slide.
			if (($carousel.hasClass('anim-fade') || $carousel.hasClass('anim-parallax')) && $carousel.find('.wd-slide').length <= 2) {
				loop = false;
			}

			var config = {
				contain             : true,
				percentPosition     : true,
				cellAlign           : 'left',
				rightToLeft         : woodmartThemeModule.$body.hasClass('rtl'),
				pageDots            : 'yes' !== $carousel.data('hide_pagination_control'),
				prevNextButtons     : 'yes' !== $carousel.data('hide_prev_next_buttons'),
				autoPlay            : 'yes' !== $carousel.data('autoplay') ? false : parseInt($carousel.data('speed')),
				pauseAutoPlayOnHover: 'yes' !== $carousel.data('autoplay'),
				adaptiveHeight      : true,
				imagesLoaded        : true,
				wrapAround          : loop,
				fade                : $carousel.hasClass('anim-fade'),
				on                  : {
					ready: function() {
						setTimeout(function() {
							woodmartThemeModule.$document.trigger('wdCarouselFlickityInited');
						}, 100);
					}
				}
			};

			$carousel.find('.wd-slider').flickity(config);

			setTimeout(function() {
				$carousel.find('.wd-slider').addClass('wd-enabled');
			}, 100);

			$carousel.find('.wd-slider').on('dragStart.flickity', function() {
				$carousel.find('.wd-slider').addClass('wd-dragging');
			});

			$carousel.find('.wd-slider').on('dragEnd.flickity', function() {
				$carousel.find('.wd-slider').removeClass('wd-dragging');
			});

			if ($carousel.hasClass('anim-parallax')) {
				var flkty = $carousel.find('.wd-slider').data('flickity');
				var $imgs = $('.wd-slide .wd-slide-bg');

				$carousel.find('.wd-slider').on('scroll.flickity', function() {
					flkty.slides.forEach(function(e, i) {
						var img = $imgs[i];

						var x = 0 === i
							? Math.abs(flkty.x) > flkty.slidesWidth
								? flkty.slidesWidth + flkty.x + flkty.slides[flkty.slides.length - 1].outerWidth + e.target
								: e.target + flkty.x
							: i === flkty.slides.length - 1 && Math.abs(flkty.x) + flkty.slides[i].outerWidth < flkty.slidesWidth
								? e.target - flkty.slidesWidth + flkty.x - flkty.slides[i].outerWidth
								: e.target + flkty.x;

						img.style.transform = 'translateX( ' + -.5 * x + 'px)';
					});
				});
			}
		}
	};

	$(document).ready(function() {
		woodmartThemeModule.sliderAnimations();
		woodmartThemeModule.carouselInitFlickity();
		woodmartThemeModule.sliderLazyLoad();
	});
})(jQuery);
