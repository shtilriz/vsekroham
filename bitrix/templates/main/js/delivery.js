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
	$.cookie('MY_CITY', city, {
		expires: 7,
		path: '/',
	});

}
function setSessionCity (city) {
	if (city.length > 0) {
		$.get('/include/setSessionCity.php', {city: city});
		return true;
	}
	else
		return false;
}

//выбор города в карточке товара
function getDCV2P (mode, city, deliveryID) {
	if ($('#deliveryCalc2Product').length) {
		var dcv2p = $('#deliveryCalc2Product');
		var getData = {
			weight: dcv2p.data('weight'),
			length: dcv2p.data('length'),
			width: dcv2p.data('width'),
			height: dcv2p.data('height'),
			price: dcv2p.data('price'),
			page: dcv2p.data('page'),
			deliveryID: (deliveryID ? deliveryID : 0)
		};
		if (city === undefined) {
			city = '';
		}
		city = city.trim();
		if (city.length > 0) {
			getData.city = city;
		}
		if (mode.length) {
			getData.mode = mode;
		}
		if (parseInt(getData.weight, 10) > 0) {
			$.ajax({
				type: 'GET',
				url: '/include/delivery/deliveryCalc2Product.php',
				data: getData,
				success: function (data) {
					if (mode == 'modal') {
						$('#cityModalList').html(data);
						var pane = $('#cityModalList .js-scroll-pane').jScrollPane({
							mouseWheelSpeed: 200
						});

					}
					else {
						dcv2p.html(data);
					}
					if ($('#edostDeliveryPrice').length) {
						var eDelivPrice = $('#edostDeliveryPrice').data('price');
						if (eDelivPrice && eDelivPrice != undefined) {
							$('#eDeliveryPrice').text(numeric_format(eDelivPrice)+' р.');
							$('#eDeliveryTotalPrice').show().find('.item-price').text(numeric_format(eDelivPrice)+' р.');
							$('.cart__list tr[data-role=delivery-row]').show();
						}
						else {
							$('#eDeliveryPrice').empty();
							$('#eDeliveryTotalPrice').hide().find('.item-price').empty();
							$('.cart__list tr[data-role=delivery-row]').hide();
						}
					}
					else {
						$('#eDeliveryPrice').empty();
						$('#eDeliveryTotalPrice').hide().find('.item-price').empty();
						$('.cart__list tr[data-role=delivery-row]').hide();
					}

					if ($('#infoAboutDelivery').length && (in_array(city, cityobj.CITY_MOSKOW_REGION) || !in_array(city, cityobj.CITY_ALL))) {
						$('#infoAboutDelivery').hide();
					}
					else if ($('#infoAboutDelivery').length && !in_array(city, cityobj.CITY_MOSKOW_REGION)) {
						$('#infoAboutDelivery').show();
					}
				}
			});
		}
	}
}

$(function () {
	/*$('body').delegate('input[name=YOUR_CITY]', 'keypress', function (e) {
		var el = $(this);
		var $listItem = el.parents('.address-dropdown').find('#cityBlock li');
		if (e.keyCode == 13) {
			var city = el.val();
			//$('input[name=YOUR_CITY]').val(el.text());
			getDCV2P((blockName=='cityBlock2ProductModal'?'modal':''),city);
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
	});*/
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

	getDCV2P('',(cityobj.YOUR_CITY != undefined ? cityobj.YOUR_CITY : ''));

	$('body').delegate('.js-sity-change', 'click', function (e) {
		e.preventDefault();
		var el = $(this),
			input = el.parent().parent().find('.sity-change-input');
		input.prop('disabled', false).focus().val('').addClass('active');
		el.hide();
		//input.parent().find('.address-dropdown').slideDown();
	});
	$('body').delegate('input.sity-change-input', 'keyup', function (e) {
		var el = $(this),
			blockName = '',
			$listItem = el.parent().find('.address-dropdown li');
		if (el.closest('#popup-calc').length)
			blockName = 'cityBlock2ProductModal';
		else
			blockName = 'cityBlock2Product';
		if (e.which == 13) {
			var city = el.val();
			el.removeClass('active').data('city', city);
			setCity(city);
			getDCV2P((blockName=='cityBlock2ProductModal'?'modal':''),city);
			el.parent().find('.address-dropdown').hide();
		}
		else if (e.which == 27) {
			el.removeClass('active').val(el.data('city'));
			el.parent().find('.address-dropdown').hide();
		}
		else if (e.which == 40) {
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
		else if (e.which == 38) {
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
			var addressBlock = el.parent().find('.address-dropdown');
			getCity(el.val(), blockName);
		}
	});
	$('body').delegate('.address-dropdown__list a', 'click', function (e) {
		e.preventDefault()
		var el = $(this);
		$('input[name=YOUR_CITY]').val(el.text());
		setCity(el.text());
		setSelectCityBlock ();
		//getDCV2P('','');
	});
	$('body').delegate('#cityBlock a', 'click', function (e) {
		e.preventDefault()
		var el = $(this);
		$('input[name=YOUR_CITY]').val(el.text());
		setCity(el.text());
		getDCV2P('',el.text());
	});
	$('body').delegate('#cityBlock2Product a', 'click', function (e) {
		e.preventDefault()
		var el = $(this);
		el.parent().parent().find('input.sity-change-input').val(el.text()).removeClass('active');
		//setCity(el.text());
		setSessionCity(el.text());
		getDCV2P('', el.text());
		el.parent('.address-dropdown').hide();
	});
	$('body').delegate('#cityBlock2ProductModal a', 'click', function (e) {
		e.preventDefault();
		var el = $(this);
		el.closest('.b-calc').find('input.sity-change-input').val(el.text()).removeClass('active');
		//setCity(el.text());
		setSessionCity(el.text());
		getDCV2P('modal',el.text());
		el.closest('.address-dropdown').hide();
		el.parents('.b-calc').find('.js-sity-change').show();
	});
	$('body').delegate('#popup-calc', 'hidden.modal', function () {
		var cityModal = $('#popup-calc input.sity-change-input').val(),
			cityProduct = $('#deliveryCalc2Product input[name=YOUR_CITY_PRODUCT]').val();
		if (cityModal != cityProduct) {
			getDCV2P(cityModal, '');
		}
	});
	$('body').delegate('#popup-calc a.b-calc__list-item', 'click', function (e) {
		e.preventDefault();
		var el = $(this),
			dCurrentID = el.data('id');
		el.closest('.popup').find('.popup__close').click();
		getDCV2P('','',dCurrentID);
	});

	/* выбор города*/
	function setSelectCityBlock () {
		$('.address-search__dropdown .address-dropdown-s-top').hide();
		$('.address-search__dropdown .address-search__dropdown-content').show();
	}
	var myCity = $.cookie('MY_CITY');
	if (cityobj.YOUR_CITY) {
		//если не установлена кука с городом и пользователь не из московского региона
		if (!myCity && !in_array(cityobj.YOUR_CITY, cityobj.CITY_MOSKOW_REGION)) {
			//спросить верно ли выбран город
			$('.address-search__dropdown').show();
			$('.address-search__dropdown .address-dropdown-s-top').show();
			$('.address-search__dropdown .address-search__dropdown-content').hide();
		}
		//если не установлена кука с городом и пользователь из московского региона или город не определен
		else if (!myCity && in_array(cityobj.YOUR_CITY, cityobj.CITY_MOSKOW_REGION)) {
			//скрыть блок с вопросом "верно ли выбран город", показать блок с выбором города
			setSelectCityBlock ();
		}
		//если установлена кука с городом и пользователь не из московского региона
		else if (myCity && !in_array(myCity, cityobj.CITY_MOSKOW_REGION)) {
			//скрыть блок с вопросом "верно ли выбран город", показать блок с выбором города
			setSelectCityBlock ();
		}
		//если установлена кука с городом и пользователь из московского региона
		else if (myCity && in_array(myCity, cityobj.CITY_MOSKOW_REGION)) {
			setSelectCityBlock ();
		}
	}
	else if (myCity) {
		setCity(myCity);
		setSelectCityBlock ();
	}
	else {
		setSelectCityBlock ();
	}

	$('body').delegate('.address-search__title', 'click', function() {
		$('.address-search__dropdown').slideToggle();
		$('.address-dropdown__input').val('').focus();
		$('#cityBlock').empty();
	});

	$('.js-address-dropdown-link-content').on('click', function(e) {
	    $('.js-address-search-dropdown-content').slideDown();
	    $('.address-dropdown__list-wrapper').jScrollPane({
	        showArrows: true
	    });
	    e.preventDefault();
	});

	$('.js-address-dropdown-close').on('click', function(e) {
		e.preventDefault();
		$('.address-search__dropdown .address-dropdown-s-top').hide();
		$('.address-search__dropdown .address-search__dropdown-content').show();
		//$('.js-address-search-dropdown-content').slideUp();
		$('.address-search__dropdown').slideUp();
		var selectCity = $('#selectYourCity .address-search__title').text();
		setCity(selectCity);
	});

	$('body').delegate('input[name=YOUR_CITY]', 'keypress', function (e) {
		var el = $(this);
		var $listItem = el.parents('.address-dropdown').find('#cityBlock li');
		if (e.keyCode == 13) {
			var city = el.val();
			//$('input[name=YOUR_CITY]').val(el.text());
			setCity(city);
			getDCV2P('',city);
			setSelectCityBlock ();
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

	/*setTimeout(function () {
		if ($('#infoAboutDelivery').length && in_array(cityobj.YOUR_CITY, cityobj.CITY_MOSKOW_REGION) && cityobj.YOUR_CITY != undefined) {
			$('#infoAboutDelivery').hide();
		}
		else if ($('#infoAboutDelivery').length && !in_array(cityobj.YOUR_CITY, cityobj.CITY_MOSKOW_REGION) && cityobj.YOUR_CITY != undefined) {
			$('#infoAboutDelivery').show();
		}
	}, 100);*/
});