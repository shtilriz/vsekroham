function showPopup(target) {
	$('body').find('.popup').fadeOut(200);

	var popupName = '#' + target;
	if ($('body').find(popupName).parent()[0].tagName != 'body') {
		$('body').find(popupName).show();
	};
	var popupX = 0,
		popupY = 0;

	if ($(popupName).height() > $(window).height()) {
		popupX = $(window).width() / 2 - $(popupName).width() / 2 - $(popupName).offsetParent().offset().left;
		popupY = $(document).scrollTop() + 25 - $(popupName).parent().offset().top;
		$('body').find(popupName).css('left', popupX).css('top', popupY).fadeIn(300);
		// console.log('y ' + popupY);
	} else {
		popupX = $(window).width() / 2 - $(popupName).width() / 2;
		popupY = $(window).height() / 2 - $(popupName).height() / 2;
		$('body').addClass('no-scroll').find(popupName).css('left', popupX).css('top', popupY).fadeIn(300);
	}

	if ($('body > .shadow').length === 0) {
		$('body').append('<div class="shadow"/>');
	}

	if ($(popupName).find('.js-scroll-pane').length) {
		$('.js-scroll-pane').jScrollPane();
	};
}

$(document).ready(function() {
	var $rangeSlider = $(".range-slider");

    $rangeSlider.each(function() {
        var $this = $(this);
        $this.noUiSlider({
            start: [$this.data('start-min'), $this.data('start-max')],
            range: {
                'min': $this.data('min'),
                'max': $this.data('max')
            },
            format: wNumb({
                decimals: 0
            })
        });

        var measuring = $this.data('measuring') || '';

        $this.Link('lower').to($this.parent().find('.range-value-lower'), 'html');
        $this.Link('upper').to($this.parent().find('.range-value-upper'), 'html');

    });

    $(".range-slider").on('change', function() {
        var $this = $(this)
        var values = $this.val();
        $this.parent().find('.range-slider-low').val(values[0]);
        $this.parent().find('.range-slider-high').val(values[1]);
    });

    $('.link-advanced-filter').on('click', function() {
        if ($(this).hasClass('link-hide')) {
            $('.product-filter-2').removeClass('active');
            $('.product-filter-2__body').slideUp();
            $('.link-advanced-filter:not(.link-hide)').delay(400).fadeIn();
            var apis = [];
            apis.push($('.product-filter-2 .js-scroll-pane').jScrollPane().data().jsp);
            $.each(apis, function(i) {
                    $('.js-scroll-pane').destroy();
                }
            )
            apis = [];
        } else {
            $('.product-filter-2').addClass('active');
            // $('.product-filter-1').slideUp();
            $('.product-filter-2__body').slideDown();
            $('.link-advanced-filter:not(.link-hide)').hide();
            setTimeout(function() {
                $('.select_type_multiselect').multiselect({
                    minWidth: 100
                });
                $('.select-chosen').chosen({
                    disable_search: true
                });
                $('.product-filter-2 .js-scroll-pane').jScrollPane();
            }, 400)
        };
    });

	$('.js-b-az-link').on('click', function(e) {
        var link = $(this);
        var lang = link.data('lang');
        $('.js-b-az-link').removeClass('active');
        link.addClass('active');
        $('.js-b-az-lang').hide();
        $('.js-b-az-' + lang).show();

        e.preventDefault();
    });

	if ($('.product-filter-2__body').is(':visible'))
		$('.product-filter-2 .js-scroll-pane').jScrollPane();

	$('.menu_type_leftmenu .menu__item').hover(
		function() {
			$(this).addClass('hovered');

			if ($(this).find('.submenu').length > 0) {
				$(this).addClass('open');
			}
		},
		function() {
			$(this).removeClass('hovered').removeClass('open');
		}
	);

	/*if ($('.menu__list_name_brands').length > 0) {

		var _this = $('.menu__list_name_brands'),
			ulHeightMax = _this.height(),
			ulHeightMin = _this.height(),
			items = _this.find('.menu__item'),
			trigger = _this.find('.show-trigger');

		$('.menu__item', _this).slice(12, -1).hide();

		ulHeightMin = _this.height();

		_this.animate({
			height: ulHeightMin
		}, 1, function() {});

		trigger.on('click', function(event) {
			event.preventDefault();

			if (trigger.hasClass('showall')) {
				items.each(function() {
					if ($(this).is(':hidden')) {
						$(this).show();
					}
				});

				_this.animate({
					height: ulHeightMax
				}, 400, function() {
					trigger.removeClass('showall').addClass('hideall').text('Свернуть список');
				});
			} else if (trigger.hasClass('hideall')) {
				_this.animate({
					height: ulHeightMin
				}, 400, function() {
					$('.menu__item', _this).slice(12, -1).hide();
					trigger.removeClass('hideall').addClass('showall').text('Показать все');
				});
			}
		});
	}*/

	$('body').delegate('a[data-target]', 'click', function(event) {
		event.preventDefault();
		showPopup($(this).data('target'));
	});

	$(document).on('click', '.popup__close, .popup__button__close', function(event) {
		event.preventDefault();
		var el = $(this);
		el.closest('.popup').trigger('hidden.modal');
		if ($('#popupSelectOffers:hidden').length) {
			$('#popupSelectOffers:hidden').remove();
		}
        el.parents('.popup').fadeOut(200, function() {
            $('body').removeClass('no-scroll').find('.shadow').remove();
        });
        if (
            el.closest('.popup').attr('id') == 'add2basketModal' ||
            el.closest('.popup').attr('id') == 'info-modal' ||
            el.closest('.popup').attr('id') == 'popupSelectOffers'
        ) {
            el.closest('.popup').remove();
        }
	});

	if ($('.item-tabs').length > 0) {
		$(this).find('.item-tabs__nav li:first-child .item-tabs__link').addClass('active');
		$(this).find('.tab').hide();
		$(this).find('.tab:first-child').show();

		$('.item-tabs__link').on('click', function(event) {
			event.preventDefault();

			var _this = $(this),
				parentDiv = $('.item-tabs__content'),
				needTab = _this.attr('href');

			if (_this.hasClass('active')) {
				return false;
			} else {
				$('.item-tabs__link').removeClass('active');
				_this.addClass('active');
				parentDiv.find('.tab').hide();
				parentDiv.find('' + needTab).show();
			}
			$('.form-select_whos-review').chosen({
				disable_search: true
			});
			autosize($('.autosize'));
		});
	}

	$(window).scroll(function() {
		var wHeight = $(window).height();
		var dHeight = $(document).height();
		var cbHeight = $('.content__bottom').outerHeight();
		var el = $('.paginator-fixed');
		var correction = 182;
		if ($('.content__bottom').html()) {
			correction = 210;
		};

		if ((($(window).scrollTop() + wHeight) + dHeight / 2) >= dHeight) {
			el.css('display', 'block');
			if ((($(window).scrollTop() + wHeight) + correction + cbHeight) > dHeight && $('#catalogMoreLink').is(':hidden')) {
				el.css('bottom', ($(window).scrollTop() + wHeight + correction + cbHeight - dHeight) + 'px');
			} else {
				el.css('bottom', 0);
			}
		} else {
			el.css({
				'display': 'none',
				'bottom': 0
			});
		}
	});
	$('body').delegate('.paginator-fixed__totop', 'click', function() {
		$('body,html').stop().animate({
			scrollTop: 0
		}, 800);
	});

	autosize($('.autosize:visible'));

	if ($('.stuff-list-container .stuff-list__link span').text().length > 40) {
		$('.stuff-list-container .stuff-list__link span').css({
			'padding' : '0 15px'
		});
	};

	$('body').delegate('.js-dropdown-toggle', 'click', function() {
	    var dropdown = $(this).parents('.js-dropdown');
	    dropdown
	        .toggleClass('active')
	        .find('.js-dropdown-content').toggle();
	});
});

$(function() { // executes when complete page is fully loaded, including all frames, objects and images
	$('body').find('.custom-form').customForm();

	$('.select_type_multiselect').multiselect({
		minWidth: 100,
		checkAllText: "Выбрать все",
		uncheckAllText: "Сбросить",
		noneSelectedText: "Выберите элемент",
		selectedText: "# эл-т выбрано"
	});

	$('.select_type_brand').multiselect({
		noneSelectedText: "Бренд",
        checkAllText: "Выбрать все",
        uncheckAllText: "Отменить выбор",
        selectedText: "Выбрано: #"
	});

	$('.select_type_category, .select_type_color, .select_type_size, .select-chosen').chosen({
		disable_search: true,
		no_results_text: $(this).data('placeholder')
	});

	$('.form-select_city_chooser').chosen({
		no_results_text: "Ничего не найдено по "
	});

	if ($('.slider_type_purchise').length > 0) {
		$('.slider_type_purchise #slider').carouFredSel({
			scroll: {
				items: 1,
				duration: 600,
				timeoutDuration: 12000
			},
			items: {
				visible: 4
			},
			prev: ".slider_type_purchise .slide-prev",
			next: ".slider_type_purchise .slide-next"
		});
	}

	if ($('.item-colors__slider').length > 0 && $('.item-colors__slider .slider__item').size() > 4) {
		$('.item-colors__slider').addClass('active');
		$('.item-colors__slider .slider').carouFredSel({
			scroll: {
				items: 1,
				duration: 600,
				timeoutDuration: 12000
			},
			items: {
				visible: 5
			},
			prev: ".item-colors__slider .slide-prev",
			next: ".item-colors__slider .slide-next"
		});
	}

	/*$('a[data-target="cart-added"]').on('click', function() {
	  if ($('.cart-added .slider_size_mini').length > 0) {
	    $('.cart-added .slider_size_mini #slider').carouFredSel({
	      scroll: {
	        items: 1,
	        duration: 600,
	        timeoutDuration: 12000
	      },
	      items: {
	        visible: 3
	      },
	      prev: ".slider_size_mini .slide-prev",
	      next: ".slider_size_mini .slide-next"
	    });
	  }
	});*/


	$('.item-remove').on('click', function(event) {
		event.preventDefault();
		$(this).parents('tr').fadeOut('400', 'swing', function() {
			this.remove();
		});
	});

	$('.item-colors .get-more').on('click', function(event) {
		event.preventDefault();
		el = $(this);

		if (el.hasClass('open')) {
			el.removeClass('open');
			el.parent().find('.item-colors__list').slideUp('300');
			el.parent().find('.item-colors__slider').slideDown('300');
			el.text(el.data('showtext'));
		} else {
			el.addClass('open');
			el.parent().find('.item-colors__slider').slideUp('300');
			el.parent().find('.item-colors__list').slideDown('300');
			el.text(el.data('hidetext'));
		}
	});

	$('.toggle-link').on('click', function(e) {
		var togglelink = $(this),
			toggle = '.toggle',
			toggleContent = '.toggle-content';
		if (!togglelink.hasClass('active')) {
			togglelink.text('Скрыть детали заказа').addClass('active').parents(toggle).find(toggleContent).slideDown();
		}
		else {
			togglelink.text('Показать детали заказа').removeClass('active').parents(toggle).find(toggleContent).slideUp();
		}
		e.preventDefault();
	});

	$('[data-max-els]').each(function() {
		var el = $(this);
		var n = el.attr('data-max-els');
		var height = (19 + 15) * n;
		el.css('max-height', height +'px');
	});

	$('.js-scroll-pane:visible').jScrollPane();
});

$('.stars a').on('click', function(e) {
	$(this).parent().children('a').attr('class', 'star-gray');
	$(this).parent().children('a').slice(0, $(this).index() + 1).removeClass('star-gray').addClass('star-blue');
	$(this).closest('form').find('input[name=UF_RATE]').val($(this).index() + 1);
	e.preventDefault();
});

$('.stars a').hover(
	function() {
		$(this).parent().children('a').addClass('star-hover');
		$(this).parent().children('a').slice(0, $(this).index() + 1).removeClass('star-hover').addClass('star-hover-active');
	},
	function() {
		$(this).parent().children('a').removeClass('star-hover star-hover-active');
	});



function addHoverClass(item) {
	$(item).hover(
		function() {
			$(this).addClass('hovered');
		},
		function() {
			$(this).removeClass('hovered');
		}
	);
}

function scrollToElement(trigger, element, speed, preventFlag) {
	$(trigger).click(function(event) {
		if (preventFlag == true) {
			event.preventDefault();
		}

		$('html, body').stop(true, true).animate({
			scrollTop: $(element).offset().top
		}, speed);

		return false;
	});
}

function equalHeight(group) {
	var tallest = 0;

	group.each(function() {
		var thisHeight = $(this).height();

		if (thisHeight > tallest) {
			tallest = thisHeight;
		}
	});

	group.height(tallest);
}


/*(function() {
  $("[name=i-sposob-dostavki]").change(function(e) {
    var $this =  $(this);

    if ($this.prop('checked')) {
      $(".toggle-content").hide('slow');
      $this.parents('tr').next(".toggle-content").show('slow');

      var $addressMap = $('.address-map');
      if ($addressMap.length) {
        var addressMap = '.address-map',
            $carousel = $addressMap.find('.carousel'),
            sliderWidth = $carousel.find('.carousel__slide').css('width');
        $carousel.carouFredSel({
          onCreate: function(data) {
            $(data.items[0]).children('script').attr('src', $(data.items[0]).children('script').attr('data-src'));
          },
          scroll: {
            items: 1,
            fx: "crossfade",
            duration: 600,
            onAfter: function(data) {
              $(data.items.visible).children('script').attr('src', $(data.items.visible).children('script').attr('data-src'));
            }
          },
          width: sliderWidth,
          items: {
            visible: 1
          },
          auto: false,
          pagination: {
              container : '.address-list .pager',
              anchorBuilder : function(nr, item) {
                  var street = $(this).data('street');
                  //var detail = $(this).data('detail');

                  //return '<li class="address-list__item"><a href="#'+nr+'">'+ street +'<small>'+ detail +'</small></a></li>';
                  return '<li class="address-list__item"><a href="#'+nr+'">'+ street +'</a></li>';
              }
          },
        });
      }
    }
    e.preventDefault();
  });
}());
*/

if (window.frameCacheVars !== undefined) {
	BX.addCustomEvent("onFrameDataReceived", function(json) {
		initCarouselSimilar();
		initSlider();
	});
} else {
	$(function() {
		initCarouselSimilar();
		initSlider();
	});
}

function initCarouselSimilar() {
	if ($('.slider_type_similar').length > 0) {
		$('.slider_type_similar #slider').carouFredSel({
			scroll: {
				items: 1,
				duration: 600,
				timeoutDuration: 12000
			},
			items: {
				visible: 3
			},
			prev: ".slider_type_similar .slide-prev",
			next: ".slider_type_similar .slide-next",
			auto: false
		});
	}
}

function initSlider() {
	var slider = '.slider-block',
		$slider = $(slider);
	if ($slider.length > 0) {
		$slider.find('.slider').carouFredSel({
			scroll: {
				items: 1,
				fx: "cover-fade",
				duration: 900,
				timeoutDuration: 12000
			},
			items: {
				visible: 1,
				width: 669,
				height: 300
			},
			prev: slider + " .slide-prev",
			next: slider + " .slide-next"
		});
	}
}

$(window).load(function() {
    $('.js-page')
        .addClass('loaded');
	(function() {
	    $('.stuff-list__link-title').each(function() {
	        var el = $(this);
	        var s = el.text();
	        var arr = ['2 в 1', '3 в 1'];
	        for (var i = arr.length - 1; i >= 0; i--) {
	            if(s.indexOf(arr[i]) >= 0) {
	              s = s.replace(arr[i], '<span>'+ arr[i] +'</span>');
	              el.html(s);
	            }
	        };
	    })
	}());
});