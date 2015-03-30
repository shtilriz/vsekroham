$(document).ready(function() {// executes when HTML-Document is loaded and DOM is ready
  $('body').removeClass('no-js');

  // динамические линки на деревянные страницы
  $.ajax({
    'url': '/ajax.pages_list.php',
    'type': 'get'
  })
      .fail(function(){
      })
      .success(function(data){
        var $wnd = $('<div style="color: #fff; position: absolute; top: 10px; left: 10px; background: #000; border: 2px solid #ccc; z-index: 101000; padding: 10px; "/>');
        var $close = $('<a href="#" style="color: #fff; font-weight: normal; display: block; margin-bottom: 10px;">Список страниц :: Закрыть</a>')
            .click(function(e){
              e.preventDefault();
              $wnd.hide();
            })
            .appendTo($wnd);
        var even = false;
        $(data.pages).each(function(){
          var page = this;
          var style = even ? 'color: #000; background: #f5f4f4; ' : 'background: #fff; color: #000;'
          $wnd.append('<a href="/' + page + '" style="' + style + ' padding: 8px 10px; font-weight: normal; display: block;">' + page + '</a>');
          even = !even;
        });
        $('body').append($wnd);
      });
  // аж досюда =)

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

  if($('.menu__list_name_brands').length > 0) {

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
      }
      else if (trigger.hasClass('hideall')) {
        _this.animate({
          height: ulHeightMin
        }, 400, function() {
          $('.menu__item', _this).slice(12, -1).hide();
          trigger.removeClass('hideall').addClass('showall').text('Показать все');
        });
      }
    });
  }

  $('a[data-target]').on('click', function (event) {
    event.preventDefault();

    $('body').find('.popup').fadeOut(200);

    var popupName = '#' + $(this).data('target'),
        popupX = $(window).width() / 2 - $(popupName).width() / 2,
        popupY = $(window).height() / 2 + $(document).scrollTop() - $(popupName).height() / 2 - 25;

    if (popupY < 0) {
      popupY = 25;
    }
    else if (popupY <= $(document).scrollTop()) {
      popupY = $(document).scrollTop() + 25;
    }

    if ($(popupName).height() < $(window).height()) {
      $('body').addClass('no-scroll').find(popupName).css('left', popupX).css('top', popupY).fadeIn(300);
    } else {
      $('body').find(popupName).css('left', popupX).css('top', popupY).fadeIn(300);
    }

    if ($('body > .shadow').length === 0) {$('body').append('<div class="shadow"/>');}
  });

  $('.popup__close').on('click', function (event) {
    event.preventDefault();

    $('body').find('.popup').fadeOut(200, function () {
      $('body').removeClass('no-scroll').find('.shadow').remove();
    });
  });

  $('.plus-minus a').on('click', function(event) {
    var needAction = $(this).attr('class'),
        value = $(this).parent().find('input').val();

    event.preventDefault();

    if(needAction == 'increase') {
      value++;
      $(this).parent().find('input').val(value);
    }
    if(needAction == 'decrease') {
      value--;
      if (value >= 0) {
        $(this).parent().find('input').val(value);
      }
    }
  });
});

$(window).load(function() {// executes when complete page is fully loaded, including all frames, objects and images
  $('body').find('.custom-form').customForm();

  $('.select_type_multiselect').multiselect({
    minWidth: 100,
    checkAllText:"Выбрать все",
    uncheckAllText:"Сбросить",
    noneSelectedText:"Выберите элемент",
    selectedText:"# эл-т выбрано"
  });

  $('.select_type_brand').multiselect({
    noneSelectedText: "Бренд"
  });

  $('.select_type_category').chosen({disable_search: true});
  $('.form-select_city_chooser').chosen({no_results_text: "Ничего не найдено по "});

  if ($('.slider-block').length > 0) {
    $('.slider-block .slider').carouFredSel({
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
      prev: ".slide-prev",
      next: ".slide-next"
    });
  }

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
        next: ".slider_type_similar .slide-next"
      });
    }

  $('a[data-target="cart-added"]').on('click', function() {
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
  });

  $('.item-remove').on('click', function(event) {
      event.preventDefault();
      $(this).parents('tr').fadeOut('400', 'swing', function() {this.remove();});
  });

});

function addHoverClass(item) {
  $(item).hover(
    function () {
      $(this).addClass('hovered');
    },
    function () {
      $(this).removeClass('hovered');
    }
  );
}

function scrollToElement(trigger, element, speed, preventFlag) {
  $(trigger).click(function (event) {
    if (preventFlag == true) {
      event.preventDefault();
    }

    $('html, body').stop(true, true).animate({scrollTop: $(element).offset().top}, speed);

    return false;
  });
}

function equalHeight(group) {
  var tallest = 0;

  group.each(function () {
    var thisHeight = $(this).height();

    if (thisHeight > tallest) {
      tallest = thisHeight;
    }
  });

  group.height(tallest);
}
