;(function() {

}());


var ua = navigator.userAgent.toLowerCase();
var isAndroid = ua.indexOf("android") > -1; //&& ua.indexOf("mobile");

var wWidth = $(window).width();
var wHeight = $(window).height();

;(function() {
    if(isAndroid) {
        function orientation() {
            if(window.orientation == 0) {
                return 'portrait'
            } else {
                return 'landscape'
            }
        }
        function changeMetaViewport() {
            var mvp = document.getElementById('meta-viewport');
            if (orientation() == 'portrait') {
                mvp.setAttribute('content', 'width=600');
            } else {
                mvp.setAttribute('content', 'width=980');
            }
        }
        changeMetaViewport();

        $(window).on("orientationchange", function() {
            changeMetaViewport();
        });
    }
}());


// $('#main')
//     .width($(window).width()*2)
//     // .css('display', 'none');
// alert($(window).width());



;(function() {
    $('.js-scrollbar').perfectScrollbar();
}());

;(function() {
    $('.js-side-menu .deeper > a').on('click', function(e) {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).parent().children('ul').slideUp();
        } else {
            $(this).addClass('active');
            $(this).parent().siblings().children('ul').slideUp();
            $(this).parent().children('ul').slideDown();
        };
        e.preventDefault();
    })
}());

;(function() {
    $('.js-b-basket-list-item-close').on('click', function(e) {
        $(this).parents('.js-b-basket-list-item').slideUp();
        e.preventDefault();
    })
}());


;(function() {
    $('.js-b-stars-1 a').on('click', function(e) {
        var el = $(this);
        var els = $(this).parent().children('a');
            els.removeClass('active');
            els.slice(0, el.index() + 1).stop().addClass('active');
            el.parent().children('input').val(el.parent().children('a.active').size());

        e.preventDefault();
    });

    // $('.js-b-stars-1 a').hover(
    //     function() {
    //         var el = $(this);
    //         var els = el.parent().children('a');
    //             els.addClass('star-hover');
    //             els.slice(0, el.index() + 1).removeClass('star-hover').addClass('star-hover-active');
    //     },
    //     function() {
    //         var el = $(this);
    //         var els = el.parent().children('a');
    //             els.removeClass('star-hover star-hover-active');
    // });
}());


;(function() {
    $('.js-show-more-button').on('click', function(e) {
        $('.spin-loader').show();
        $.ajax({
        url: "ajax-goods.html",
        })
        .done(function( html ) {
            setTimeout(function() {
                $('.spin-loader').hide();
                $('html, body').stop().animate({scrollTop: $('.b-g:last').offset().top  + $('.b-g:last').height()}, 800);
                $('.b-goods').append(html);
            }, 800);
        });
        e.preventDefault();
    });
}());


;(function() {
    $('.js-b-checker').on('click', function() {
        var input = $(this).find('.checkbox-styled');
        if (input.is(':radio')) {
            input.prop('checked', true);
        } else {
            if (input.prop( 'checked' )) {
                input.prop('checked', false)
            } else {
                input.prop('checked', true)
            };
        };
    });
}());

(function() {
    $('.payment-method__item').on('click', function(e) {
        $(this).find('.payment-method__checkbox').prop("checked", true);
    });
}());

;(function() {
    (function(el) {
        if ($(el).hasClass('active')) {
            $('.js-dropdown').slideDown();
        } else {
            $('.js-dropdown').slideUp();
        };
    }('.js-toogle'));
    $('.js-toogle').on('click', function() {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).parent().find('.js-dropdown').slideUp();
        } else {
            $(this).addClass('active');
            $(this).parent().find('.js-dropdown').slideDown();
        };
    });
}());

// ;(function() {
//     $('.selectpicker').selectpicker({
//         mobile: true,
//         noneResultsText: 'Не найдено',
//         style: 'dropdown-toggle_lg btn-default'
//     });
// }());


;(function() {
    // $(document).foundation();
    $('.js-side-menu-link').on('click', function(e) {
        $('body').addClass('is-menu-show');
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
        };
        e.preventDefault();
    });

    $('.hide-sidebar-link').on('click', function(e) {
        $('body').removeClass('is-menu-show');
        e.preventDefault();
    });
}());

;(function() {
    $('.slider-default').slick({
        arrows: false,
        dots: true
    });
    $('.slider-big').slick({
        arrows: true,
        dots: false
    });
    $('.slider-b').slick({
        arrows: false,
        dots: true,
        vertical: true,
        autoplay: false,
        autoplaySpeed: 8000
    });
}());

$('#modal-pr').on('shown.bs.modal', function (event) {
    $(this).find('.slider-big').slick('unslick');
    $(this).find('.slider-big').slick();
});

$('#modal-pr-added-to-cart').on('shown.bs.modal', function (event) {
    $(this).find('.slider-big').slick('unslick');
    $(this).find('.slider-related').slick({
        // slidesToShow: 3,
        // slidesToScroll: 3
    });
});

// $('.slider-related').slick();

$('.js-change-delivery').on('click', function (e) {
    e.preventDefault();
    $('#modal-deliver').modal('hide');
});


$('.js-icon-clear-control').on('click', function (e) {
    $(this).parent().children('input').val('').focus();
    e.preventDefault();
});

;(function() {
    $('.has-dropdown > a').on('click', function() {
        var $this = $(this).parent();
        $this
            .siblings()
                .removeClass('active')
                .find('.js-dropdown-content').hide();
        if ($this.hasClass('active')) {
            $this
                .removeClass('active')
                .find('.js-dropdown-content').hide();
        } else {
            $this
                .addClass('active')
                .find('.js-dropdown-content').show();
        };
    });
    $('.js-dropdown-close').on('click', function(e) {
        $(this).parents('.js-dropdown-content').hide()
        $('.has-dropdown > a').removeClass('active');
        e.preventDefault()
    });
}());

// ;(function() {
//     $(document).bind('mouseup touchend', function (e) {
//         if ($(".js-dropdown-content").has(e.target).length === 0){
//             $(".js-dropdown-content").hide();
//             $('.has-dropdown').removeClass('active');
//         }
//     });
// }());


;(function() {
    $('.js-sity-change-change').on('click', function(e) {
        e.preventDefault();
        var link = $(this);
        var parent = link.parents('.sity-change');
        parent.addClass('active');
        // parent.find('.js-sity-change-control').show();
        parent.find(".js-sity-change-control").select2({
            language: "ru",
          ajax: {
            url: "https://api.github.com/search/repositories",
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                q: params.term, // search term
                page: params.page
              };
            },
            processResults: function (data, page) {
              // parse the results into the format expected by Select2.
              // since we are using custom formatting functions we do not need to
              // alter the remote JSON data
              return {
                results: data.items
              };
            },
            cache: true
          },
          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
          minimumInputLength: 1,
          templateResult: formatRepo, // omitted for brevity, see the source of this page
          templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        }).select2("open");
    })
}());

function formatRepo (repo) {
   if (repo.loading) return repo.text;

   var markup = '<div class="clearfix">' +
   '<div class="col-sm-1">' +
   '<img src="' + repo.owner.avatar_url + '" style="max-width: 100%" />' +
   '</div>' +
   '<div clas="col-sm-10">' +
   '<div class="clearfix">' +
   '<div class="col-sm-6">' + repo.full_name + '</div>' +
   '<div class="col-sm-3"><i class="fa fa-code-fork"></i> ' + repo.forks_count + '</div>' +
   '<div class="col-sm-2"><i class="fa fa-star"></i> ' + repo.stargazers_count + '</div>' +
   '</div>';

   if (repo.description) {
     markup += '<div>' + repo.description + '</div>';
   }

   markup += '</div></div>';

   return markup;
 }

 function formatRepoSelection (repo) {
   return repo.full_name || repo.text;
 }

;(function() {
    $('.js-b-header-view-link').on('click', function(e) {
        var el = $(this);
        el.siblings().removeClass('active');
        el.addClass('active');

        e.preventDefault();
    })
}());

;(function() {
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


        $this.Link('lower').to('-inline-<div class="tooltip"></div>', function ( value ) {
            $(this).html(
                '<span>' + value + '</span> ' + measuring
            );
        });
        $this.Link('upper').to('-inline-<div class="tooltip"></div>', function ( value ) {
            $(this).html(
                '<span>' + value + '</span> ' + measuring
            );
        });
        // console.log($this.val());
    });
    $(".range-slider").on('change', function() {
        var $this = $(this)
        var values = $this.val();
        $this.parent().find('.range-slider-low').val(values[0]);
        $this.parent().find('.range-slider-high').val(values[1]);
    });
}());
