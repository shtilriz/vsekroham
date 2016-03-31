/*
делает запрос городов при вводе подстроки
q - поисковый запрос
insertBlock - id блока, в который подставлять результат
*/
function getCity (q, insertBlock) {
	var bStatus = false;
	if (q.length > 1) {
		$.ajax({
			type: 'GET',
			url: '/include/getCity.php',
			data: {q: q},
			dataType: 'json',
			//async: false,
			success: function (data){
				if (data.length > 0) {
					var html = '';
					for (var i = 0; i < data.length; i++) {
						data[i]
						html += '<li><a href="">'+data[i]+'</a></li>';
					};
					html = '<ul class="address-dropdown__list">'+html+'</ul>';
					$('#'+insertBlock).html(html);
					if (insertBlock == 'cityBlock2Product' || insertBlock == 'cityBlock2ProductModal') {
						var addressBlock = $('#'+insertBlock).parent('.address-dropdown');
						if (addressBlock.is(':hidden')) {
							addressBlock.show();
						}
					}
				}
				else {
					$('#'+insertBlock).empty();
					if (insertBlock == 'cityBlock2Product' || insertBlock == 'cityBlock2ProductModal') {
						var addressBlock = $('#'+insertBlock).parent('.address-dropdown');
						if (addressBlock.is(':visible')) {
							addressBlock.hide();
						}
					}
				}
			}
		});
	}
	else {
		$('#'+insertBlock).empty();
	}
	return bStatus;
}
//подставляет выбранный город в строку поиска
function setCity (city) {
	$('.address-search__title').html(city+' <span class="address-search__spinner"></span>');
	$('.address-search__dropdown').hide();
	setSessionCity(city);
}
function setSessionCity (city) {
	if (city.length > 0) {
		$.get('/include/setSessionCity.php', {city: city});
		return true;
	}
	else
		return false;
}
$(function () {
	/* выбор города*/
	/*$('body').delegate('.address-search__title', 'click', function() {
		$('.address-search__dropdown').slideToggle();
		$('.address-dropdown__input').val('').focus();
	});*/
	$('body').delegate('input[name=YOUR_CITY]', 'keypress', function (e) {
		var el = $(this);
		var $listItem = el.parents('.address-dropdown').find('#cityBlock li');
		if (e.keyCode == 13) {
			var city = el.val();
			//$('input[name=YOUR_CITY]').val(el.text());
			setCity(city);
			getDCV2P('',city);
		}
		else if (e.keyCode == 40) {
			if (!$listItem.hasClass('active')) {
				$listItem.first().addClass('active');
				el.val($listItem.first().children('a').text());
			} else {
				var $listItemActive = $listItem.filter('.active');
				el.val($listItemActive.next().children('a').text());
				$listItemActive
				.removeClass('active')
				.next()
				.addClass('active');
			};
		}
		else if (e.keyCode == 38) {
			if (!$listItem.hasClass('active')) {
				$listItem.first().addClass('active');
				el.val($listItem.first().children('a').text());
			} else {
				var $listItemActive = $listItem.filter('.active');
				el.val($listItemActive.prev().children('a').text());
				$listItemActive
				.removeClass('active')
				.prev()
				.addClass('active');
			};
		}
		else {
			getCity(el.val(), 'cityBlock');
		}
	});
	$('form[name=selectCity]').on('submit', function (e) {
		e.preventDefault();
		var city = $(this).find('input[name=YOUR_CITY]').val();
		setCity(city);
	});
	$(document).on('click', function (e) {
		e.stopPropagation();
		if ($(e.target).closest('.address-search').length)
			return;
		$('.address-search .address-dropdown').slideUp();
	});

	$('.dropdown-menu__link').on('click', function(e) {
		var $this = $(this),
			text = '';

		text = $this.data('choose');
		$this.parents('.dropdown').children('.dropdown__link').text(text);

		e.preventDefault();
	});

	$('.js-address-dropdown-link-content').on('click', function(e) {
	    $('.js-address-search-dropdown-content').slideDown();
	    $('.address-dropdown__list-wrapper').jScrollPane({
	        showArrows: true
	    });
	    e.preventDefault();
	});

	$('.js-address-dropdown-close').on('click', function(e) {
	    $('.js-address-search-dropdown-content').slideUp();
	    $(".address-dropdown").slideUp();
	    e.preventDefault();
	});
});