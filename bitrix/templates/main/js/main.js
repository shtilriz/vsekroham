/**
 * Форматирование числа.
 * @param val - Значение для форматирования
 * @param thSep - Разделитель разрядов
 * @param dcSep - Десятичный разделитель
 * @returns string
 */
function numeric_format(val, thSep, dcSep) {
	// Проверка указания разделителя разрядов
	if (!thSep) thSep = ' ';
	// Проверка указания десятичного разделителя
	if (!dcSep) dcSep = ',';
	var res = val.toString();
	var lZero = (val < 0); // Признак отрицательного числа
	// Определение длины форматируемой части
	var fLen = res.lastIndexOf('.'); // До десятичной точки
	fLen = (fLen > -1) ? fLen : res.length;
	// Выделение временного буфера
	var tmpRes = res.substring(fLen);
	var cnt = -1;
	for (var ind = fLen; ind > 0; ind--) {
		// Формируем временный буфер
		cnt++;
		if (((cnt % 3) === 0) && (ind !== fLen) && (!lZero || (ind > 1))) {
			tmpRes = thSep + tmpRes;
		}
		tmpRes = res.charAt(ind - 1) + tmpRes;
	}
	return tmpRes.replace('.', dcSep);
}

//проверяет, есть ли значение в массиве
function in_array(needle, haystack, strict) {
	var found = false, key, strict = !!strict;
	for (key in haystack) {
		if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
			found = true;
			break;
		}
	}
	return found;
}

//выводит информационное сообщение
function info_alert(title, msg) {
	var getData = {
		title: title,
		msg: msg
	};
	$.post('/include/modal/info-modal.php', getData, function(data) {
		$('body').append(data);
		showPopup('info-modal');
	});
}

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

$(function() {
	$.validator.addMethod("phoneRU", function(phone_number, element) {
		phone_number = phone_number.replace(/\s+/g, "");
		return this.optional(element) || phone_number.length > 9 &&
			phone_number.match(/^(\+?\d+)?\s*(\(\d+\))?[\s-]*([\d-]*)$/);
	}, "Please specify a valid phone number");

	//подгрузка списка товаров по ajax в списке товаров
	var loading = false;
	$('body').delegate('#catalogMoreLink', 'click', function(e) {
		e.preventDefault();
		var el = $(this),
			ProductList = el.closest('.stuff-list-container').find('ul.stuff-list'),
			NavBlock = el.closest('#catalog-section').find('#paginatorBlock'),
			pagenum = parseInt(el.data('pagenum'),10),
			nextPageNum = pagenum+1,
			NavRecordCount = el.data('navrecordcount'),
			NavPageCount = el.data('navpagecount'),
			NavNum = el.data('navnum'),
			NavPageSize = parseInt(el.closest('.stuff-list-container').data('navpagesize'),10);
			//SECTION_CODE = el.closest('.stuff-list-container').data('code');

		if (nextPageNum <= NavPageCount) {
			$('#preloader').show();
			var getData = '';
			if ($('form[name=catalog-filter] input#send-filter').val()=="Y")
				getData = $('form[name=catalog-filter],form[name=makersForm]').serialize();
			else
				getData = $('form[name=makersForm]').serialize();

			getData += (getData.length>0?'&':'')+'PAGEN_'+NavNum+'='+nextPageNum;
			var licount = ProductList.find('li').length;
			$.getJSON('', getData, function(data) {
				$('#preloader').hide();
				ProductList.append(data.PRODUCTS);
				if (data.NAV) {
					NavBlock.html(data.NAV);
					NavBlock.find('.paginator-fixed').show();
				}

				loading = false;
			});

			el.data('pagenum',nextPageNum);
			if (nextPageNum == NavPageCount) {
				el.hide();
			}
		}
	});

	$(window).scroll(function(){
		if((($(window).scrollTop()+$(window).height())+1300)>=$(document).height()){
			if(loading == false){
				loading = true;
				$( "#catalogMoreLink" ).trigger( "click" );
			}
		}
	});

	$('form[name=catalog-filter] select#catalog-filter').on('change', function() {
		if ($('#searchPage').length <= 0) {
			var select = $(this),
				cat_url = select.find('option:selected').val();
			select.closest('form').attr('action', cat_url);
		}
	});

	//сортировка в каталоге товаров
	$('body').delegate('#catalog-sort a', 'click', function(e) {
		e.preventDefault();
		var el = $(this),
			sort = el.data('sort'),
			order = el.data('order'),
			getData = $('form[name=catalog-filter],form[name=makersForm]').serialize(),
			ProductList = $('.stuff-list-container ul.stuff-list'),
			NavBlock = el.closest('#catalog-section').find('#paginatorBlock'),
			SECTION_CODE = $('.stuff-list-container').data('code');

		getData += (getData.length>0?'&':'?')+'SECTION_CODE='+SECTION_CODE+'&sort='+sort;

		if (!el.hasClass('active')) {
			el.data('order', 'ASC');
			getData += (getData.length>0?'&':'?')+'order=ASC';
		}
		else {
			if (order == "ASC") {
				el.data('order', 'DESC');
				getData += (getData.length>0?'&':'?')+'order=DESC';
			}
			else if (order == "DESC") {
				el.data('order', 'ASC');
				getData += (getData.length>0?'&':'?')+'order=ASC';
			}
		}
		el.addClass('active').siblings().removeClass('active');
		$.getJSON('',getData, function(data) {
			//$('#preloader').hide();
			ProductList.html(data.PRODUCTS);
			if (data.NAV) {
				NavBlock.html(data.NAV);
				//NavBlock.find('.paginator-fixed').show();
			}
			$('#catalogMoreLink').data('pagenum','1');
			if (parseInt($('#catalogMoreLink').data('navpagecount'),10) > 1 && $('#catalogMoreLink').is(':hidden')) {
				$('#catalogMoreLink').show();
			}
			$('html, body').stop().animate({scrollTop: $('#catalog-sort').offset().top}, 800);
		});
	});

	//картинки товаров в лайтбоксе
	$('body').delegate('a[data-target="cart-img-popup"]', 'click', function() {
		var counter_product = $(this).data('index');
		if ($('.cart-img-popup .slider_popup').length > 0) {
			var slider = $('.cart-img-popup .slider_popup #slider');
			slider.carouFredSel({
				onCreate: function (data) {
					var offer = {
						id: data.items.find('img').data('id'),
						title: data.items.find('img').data('title'),
						color: data.items.find('img').data('color'),
						size: data.items.find('img').data('size'),
						price: data.items.find('img').data('price')
					}
					setPropModalSKU(offer);
				},
				scroll: {
					items: 1,
					duration: 600,
					timeoutDuration: 12000,
					onAfter: function(data) {
						var offer = {
							id: data.items.visible.find('img').data('id'),
							title: data.items.visible.find('img').data('title'),
							color: data.items.visible.find('img').data('color'),
							size: data.items.visible.find('img').data('size'),
							price: data.items.visible.find('img').data('price')
						};
						setPropModalSKU(offer);
					}
				},
				items: {
					visible: 1,
					start: slider.find('.slider__item[data-index='+counter_product+']')
				},
				auto: false,
				prev: {
					button: '.slider_popup .slide-prev',
					key: 37
				},
				next: {
					button: '.slider_popup .slide-next',
					key: 39
				},
				swipe: {
					onTouch: true
				},
				mousewheel: {
					items: 1,
					duration: 600
				}
			});
		};
	});
	function setPropModalSKU(offer) {
		console.log(offer);
		$('#cart-img-popup .cart-img-popup__title').text(offer.title);
		$('#cart-img-popup .popup__right .price').text(offer.price);
		//вывести выбор размеров, если для данного цвета их больше одного
		var count = 0;
		if (SKU.SIZE_IN_COLOR[offer.color] != undefined) {
			count = Object.getOwnPropertyNames(SKU.SIZE_IN_COLOR[offer.color]).length;
		}
		var insertHTML = '';
		if (count > 1) {
			var strTemp = '';
			var i = 0;
			for (var key in SKU.SIZE_IN_COLOR[offer.color]) {
				var thisSize = SKU.SIZE_IN_COLOR[offer.color][key];
				strTemp += '<option value="'+key+'"'+(i==0?' selected':'')+'>'+thisSize+'</option>';
				i++;
			}
			insertHTML += (offer.color?'Цвет: '+offer.color+'<br/>':'') + 'Размер: <select class="select-chosen" name="offer-size" data-placeholder=" ">'+strTemp+'</select>';
		}
		else {
			insertHTML = (offer.color?'Цвет: '+offer.color+'<br/>':'') + (offer.size?'Размер: '+offer.size:'');
		}
		$('#cart-img-popup .popup__right__opt__inner').html(insertHTML);
		setTimeout(function() {
			$('#cart-img-popup select').chosen({disable_search: true});
		}, 1);
		//скрыть кнопку "В корзину", если это не ТП
		if (offer.id == undefined) {
			$('#add2bsk_popup').hide();
		}
		else {
			$('#add2bsk_popup').show().data('id', offer.id);
		}
	}
	$('#cart-img-popup #slider .slider__item').on('click', function(e) {
		$('.cart-img-popup .slider_popup #slider').trigger('next');
	});

	//смена картинки при смене ТП из селекта
	$('body').delegate('select[name=COLOR]', 'change', function() {
		var select = $(this),
			opSelected = select.find('option:selected'),
			id = opSelected.val(),
			image = opSelected.data('image'),
			index = opSelected.index();
		$('#productImg').data('index', index);
		$('#productImg img').fadeTo(300,0.5,function() {
			$(this).attr('src',image).fadeTo(600,1);
		});
		$('#item__info .add-to-basket, #popupSelectOffers .add-to-basket').data('id', id);
	});

	//кладем товар в корзину
	//console.log(SKU);
	function add2basket(id) {
		if (parseInt(id,10) > 0) {
			var getData = {
				action: 'ADD2BASKET',
				ajax_basket: 'Y',
				id: id
			}
			$.getJSON('', getData, function (response) {
				if (response.STATUS == "OK") {
					bskSmallRefresh();
					$.get('/include/modal/add2basketModal.php', {id:id}, function(data) {
						$('body').append(data);
						showPopup('add2basketModal');
					});
				}
				else {
					info_alert('Ошибка', response.MESSAGE);
				}
			});
		}
		else {
			info_alert('Ошибка', 'Возникла ошибка при добавлении товара в корзину. Повторите попытку позже.');
		}
	}

	function bskSmallRefresh() {
		$.post('/include/basket_small.php',
			function(data) {
				$('#basket_small').html(data);
			}
		);
	}
	$('body').delegate('#item__info .add-to-basket, #popupSelectOffers .add-to-basket', 'click',function(e) {
		e.preventDefault();
		var el = $(this),
			form = el.closest('form');
		//form.find('#addBskErr').slideUp();
		//если товар с торговыми предложениями
		if (window.SKU.B_OFFERS) {
			var setVal = {}
			var bAllowed = true; //флаг разрешено ли добавлять в корзину. true, если выбраны все параметры ТП
			var msg = '';
			for (var cell in window.SKU.SKU_PROPS) {
				var prop = window.SKU.SKU_PROPS[cell];
				if (form.find('select[name='+prop+']').length)
					setVal[prop] = form.find('select[name='+prop+'] option:selected').val();
				if (form.find('input[name='+prop+']:radio').length) {
					setVal[prop] = form.find('input[name='+prop+']:checked').val();
					if (setVal[prop] == undefined)
						setVal[prop] = '';
				}
				if (setVal[prop].length == 0) {
					bAllowed = false;
					if (prop == "COLOR")
						msg += 'цвет, ';
					if (prop == "SIZE")
						msg += 'размер, ';
				}
			}

			if (bAllowed) {
				//найти id ТП по его параметрам
				var skuID = 0;
				for (var key in window.SKU.TREE) {
					var bSelect = true;
					for (var cell in window.SKU.SKU_PROPS) {
						var prop = window.SKU.SKU_PROPS[cell];
						if (window.SKU.TREE[key][prop] == setVal[prop]) {
							bSelect = true;
						}
						else {
							bSelect = false;
							break;
						}
					}
					if (bSelect) {
						skuID = key;
						break;
					}
				}
				add2basket(skuID);
			}
			else {
				if ($('#popupSelectOffers').length) {
					$('#popupSelectOffers #message').text('Выберите '+msg+'чтобы добавить товар в корзину');
				}
				else {
					info_alert('Ошибка', 'Выберите '+msg+'чтобы добавить товар в корзину');
				}
			}
		}
		//иначе товар без ТП
		else {
			var product_id = window.SKU.PRODUCT_ID;
			add2basket(product_id);
		}
	});
	//кладем в корзину во всплывающем окне
	$('body').delegate('#add2bsk_popup', 'click', function(e) {
		e.preventDefault();
		var el = $(this);
		//если есть выбор размера
		if (el.closest('#cart-img-popup').find('select[name=offer-size]').length) {
			var product_id = el.closest('#cart-img-popup').find('select[name=offer-size] option:selected').val();
			if (parseInt(product_id, 10) > 0) {
				add2basket(product_id);
			}
			else {
				info_alert('Ошибка','Выберите размер');
			}
		}
		else {
			//иначе просто добавляем ТП
			var product_id = el.data('id');
			add2basket(product_id);
		}
	});

	//выбрать доступные цвета или размеры в зависимости от текущего выбора
	$('body').delegate('#item__info select, #popupSelectOffers select, #item__info input:radio', 'change', function() {
		if ($('#popupSelectOffers').length) {
			$('#popupSelectOffers #message').empty();
		}
		var form = $(this).closest('form'),
			skuCount = window.SKU.SKU_PROPS.length,
			thisPropCode = $(this).attr('name');
		if (thisPropCode == 'COLOR' && parseInt(window.SKU.CNT_SIZE,10) > 1) {
			form.find('input[name=SIZE]:radio').each(function() {
				$(this).prop('checked',false).removeAttr('checked');
			});
		}
		if (skuCount > 1) {
			var setVal = {};
			var bAllUnselected = true; //флаг того, что не выбран ни один из параметров
			var bAllSelected = true; //флаг того, что выбраны все параметры
			for (var key in window.SKU.SKU_PROPS) {
				var prop = window.SKU.SKU_PROPS[key];
				if (form.find('select[name='+prop+']').length)
					setVal[prop] = form.find('select[name='+prop+'] option:selected').val();
				if (form.find('input[name='+prop+']:radio').length) {
					setVal[prop] = form.find('input[name='+prop+']:checked').val();
					if (setVal[prop] == undefined)
						setVal[prop] = '';
				}
				if (setVal[prop].length > 0) {
					bAllUnselected = false;
				}
				if (setVal[prop].length == 0) {
					bAllSelected = false;
				}
			}
			//если не выбран ни один из параметров
			if (bAllUnselected) {
				form.find('select option, input:radio').prop('disabled', false);
			}
			//если выбран один из параметров
			//else if (!bAllSelected) {
				var setPropArray = {};
				for (var cell in window.SKU.SKU_PROPS) {
					if (window.SKU.SKU_PROPS[cell] != thisPropCode) {
						setPropArray[window.SKU.SKU_PROPS[cell]] = [];
					}
				}
				for (var key in window.SKU.TREE) {
					if (window.SKU.TREE[key][thisPropCode] == setVal[thisPropCode]) {
						for (var cell in window.SKU.SKU_PROPS) {
							var thisProp = window.SKU.SKU_PROPS[cell];
							if (thisProp != thisPropCode) {
								setPropArray[thisProp][setPropArray[thisProp].length] = window.SKU.TREE[key][thisProp];
							}
						}
					}
				}
				for (var key in setPropArray) {
					/*form.find('select[name='+key+'] option').each(function() {
						var el = $(this);
						if (el.val() != 0)
							el.prop('disabled',true);
						var thisProp = el.val();
						if (in_array(thisProp,setPropArray[key])) {
							el.prop('disabled',false);
						}
					});*/
					form.find('input[name='+key+']:radio').each(function() {
						var el = $(this);
						if (el.val() != 0)
							el.prop('disabled',true).attr('disabled','disabled');
						var thisProp = el.val();
						if (in_array(thisProp,setPropArray[key])) {
							el.prop('disabled',false).removeAttr('disabled');
						}
					});
				}
			//}

			setTimeout(function() {
				form.find('select').trigger('chosen:updated');
				setSizeChecked(form);
			}, 1);
		}
		setSkuPrice();
	});
	setSizeChecked($('#item__info form'));

	function setSizeChecked(form) {
		if (form.find('input[name=SIZE]:not(:disabled)').length == 1) {
			form.find('input[name=SIZE]:not(:disabled)').prop('checked',true).attr('checked', 'checked');
		}
	}

	function setSkuPrice() {
		var setVal = {};
		for (var cell in window.SKU.SKU_PROPS) {
			var prop = window.SKU.SKU_PROPS[cell];
			setVal[prop] = $('#item__info select[name='+prop+'] option:selected, #item__info input[name='+prop+']:radio:checked, #popupSelectOffers select[name='+prop+'] option:selected').val();
		}
		var skuID = 0;
		for (var key in window.SKU.TREE) {
			var bSelect = true;
			for (var cell in window.SKU.SKU_PROPS) {
				var prop = window.SKU.SKU_PROPS[cell];
				if (window.SKU.TREE[key][prop] == setVal[prop]) {
					bSelect = true;
				}
				else {
					bSelect = false;
					break;
				}
			}
			if (bSelect) {
				skuID = key;
				break;
			}
		}
		if (skuID > 0) {
			//ищем цену ТП
			var skuPriceDiscount = window.SKU.PRICE_DISCOUNT[skuID],
				discountDiff = parseInt(window.SKU.DISCOUNT_DIFF[skuID],10);
				skuPrice = window.SKU.PRICE[skuID],
				skuPriceMargin = window.SKU.PRICE_MARGIN[skuID];
			//меняем цену ТП
			$('#item__info .price #prPrice, #popupSelectOffers #prPrice').text(skuPriceDiscount);
			if (discountDiff > 0) {
				$('#item__info .price .price__old, #popupSelectOffers .price__old').text(skuPrice);
			}
			else if (skuPriceMargin) {
				$('#item__info .price .price__old, #popupSelectOffers .price__old').text(skuPriceMargin);
			}
			//меняем артикул
			//$('#item__info articul').text(skuID);
		}
	}
	/*** корзина ***/
	function basketRefresh() {
		var form = $('#basket-list form[name=basket_form]'),
			getData = form.serialize();
		$('#basket-list').append('<div class="loader"></div>');
		$.post('/basket/',getData,function(data) {
			//$('#basket-list').remove('.loader');
			$('#basket-list').html(data);
			bskSmallRefresh();
		});
	}
	//удаление товара из корзины
	$('body').delegate('form[name=basket_form] .item-remove', 'click', function(e) {
		e.preventDefault();
		var el = $(this);
		el.next('input').attr({checked:'checked'});
		basketRefresh();
	});
	//плюс-минус
	$('body').delegate('.plus-minus a', 'click', function(e) {
		e.preventDefault();
		var el = $(this),
			inputQuantity = el.closest('.plus-minus').find('input'),
			value = el.closest('.plus-minus').find('input.input-quan').val();

		if (el.hasClass('increase')) {
			value++;
			inputQuantity.val(value);
			basketRefresh();
		}
		if(el.hasClass('decrease')) {
			value--;
			if (value >= 1) {
				inputQuantity.val(value);
				basketRefresh();
			}
		}
	});
	//очищаем корзину
	$('body').delegate('form[name=basket_form] a.clear-cart', 'click',function(e) {
		e.preventDefault();
		$('form[name=basket_form] .productDel').attr({checked:'checked'});
		setTimeout(function() {
			basketRefresh();
		},1);
	});

	//заказ обратного звонка
	$('form[name=callbackModal]').on('submit', function(e) {
		e.preventDefault();
		var form = $(this);
		form.validate({
			rules: {
				PHONE: {
					required: true,
					phoneRU: true
				}
			},
			messages: {
				PHONE: {
					required: 'Введите ваш номер телефона',
					phoneRU: 'Номер телефона введен некорректно'
				}
			}
		});
		if (form.valid()) {
			var getData = form.serialize();
			$.getJSON('/include/callbackSend.php', getData, function(data) {
				info_alert(data.TITLE, data.MESSAGE);
			});
		};
	});

	//вызов сборщика
	$('form[name=collector]').on('submit', function(e) {
		e.preventDefault();
		var form = $(this);
		form.validate({
			rules: {
				FIO: 'required',
				PHONE: {
					required: true,
					phoneRU: true
				}
			},
			messages: {
				FIO: 'Введите ваше имя',
				PHONE: {
					required: 'Введите ваш номер телефона',
					phoneRU: 'Номер телефона введен некорректно'
				}
			},
			errorLabelContainer: '#messageBox',
			errorElement: 'div'
		});
		if (form.valid()) {
			var getData = form.serialize(),
				url = form.attr('action');
			$.getJSON(url, getData, function(data) {
				form.find('input[type=text],textarea').val("");
				info_alert(data.TITLE, data.MESSAGE);
			});
		};
	});

	//возврат товара
	$('form[name=returnPolicy]').validate({
		rules: {
			FIO: 'required',
			'prop[CITY]': 'required',
			'prop[PHONE]': {
				required: true,
				phoneRU: true
			},
			'prop[EMAIL]': {
				required: true,
				email: true
			},
			'prop[PRODUCT]': 'required',
			'prop[ORDER_ID]': 'required'
		},
		/*messages: {
			FIO: 'Введите ваше имя',
			'prop[CITY]': 'Введите ваш город',
			'prop[PHONE]': {
				required: 'Введите ваш номер телефона',
				phoneRU: 'Номер телефона введен некорректно'
			},
			'prop[EMAIL]': {
				required: 'Введите ваш E-mail',
				email: 'E-mail введен некорректно'
			},
			'prop[PRODUCT]': 'Введите название товара',
			'prop[ORDER_ID]': 'Введите номер вашего заказа',
		},*/
		messages: {
			FIO: '<span class="validation-message validation-error"></span>',
			'prop[CITY]': '<span class="validation-message validation-error"></span>',
			'prop[PHONE]': {
				required: '<span class="validation-message validation-error"></span>',
				phoneRU: '<span class="validation-message validation-error"></span>'
			},
			'prop[EMAIL]': {
				required: '<span class="validation-message validation-error"></span>',
				email: '<span class="validation-message validation-error"></span>'
			},
			'prop[PRODUCT]': '<span class="validation-message validation-error"></span>',
			'prop[ORDER_ID]': '<span class="validation-message validation-error"></span>',
		},
		//errorLabelContainer: '#messageBox',
		//errorElement: 'div'
		errorClass: 'has-error',
		errorPlacement: function(error, element) {
			error.appendTo(element.closest('tr').find('td:last'));
		}
	});

	//подписка оптовиков
	$('form[name=opt-subscribe]').on('submit', function(e) {
		e.preventDefault();
		var form = $(this);
		form.validate({
			rules: {
				EMAIL: {
					required: true,
					email: true
				}
			},
			messages: {
				EMAIL: {
					required: 'Введите ваш E-mail',
					email: 'E-mail введен некорректно'
				}
			},
			errorLabelContainer: '#messageBox',
			errorElement: 'div'
		});
		if (form.valid()) {
			var getData = form.serialize(),
				url = form.attr('action');
			$.getJSON(url, getData, function(data) {
				form.find('input[type=text],textarea').val("");
				info_alert(data.TITLE, data.MESSAGE);
			});
		};
	});

	//ajax подгрузка отзывов в карточке товара
	/*$('.item-tabs__link').on('click', function() {
		var tab = $(this);
		if (tab.attr('href') == '#reviews' && 0 <= $('#pr-reviews .review__item').length) {
			getData = {
				id: $('#pr-reviews').data('id'),
				iNumPage: 1
			}
			$.get('/include/getReviews.php', getData, function(data) {
				$('#pr-reviews').html(data);
			});
		}
	});

	$('body').delegate('#pr-reviews .paginator__list a', 'click', function(e) {
		e.preventDefault();
		var el = $(this),
			iNumPage = el.data('page');
		var getData = {
			id: $('#pr-reviews').data('id'),
			iNumPage: iNumPage
		}
		$.get('/include/getReviews.php', getData, function(data) {
			$('#pr-reviews').html(data);
			$('html,body').stop().animate({scrollTop: $('#pr-reviews').offset().top}, 800)
		});
	});*/

	//отправить отзыв
	$('body').delegate('form[name=send-reviews]', 'submit', function(e) {
		e.preventDefault();
		var form = $(this);
		form.validate({
			ignore: [],
			rules: {
				UF_RATE: {
					required: true,
					range: [1, 5]
				},
				UF_COMMENT: {
					required: true,
					minlength: 20
				},
				UF_NAME: 'required'
			},
			messages: {
				UF_RATE: {
					required: 'Поставьте оценку',
					range: 'Поставьте оценку'
				},
				UF_COMMENT: {
					required: 'Введите комментарий к товару',
					minlength: 'Комментарий долже быть минимум 20 символов'
				},
				UF_NAME: 'Введите ваше имя'
			},
			//errorLabelContainer: '#messageBox',
			errorElement: 'div'
		});
		if (form.valid()) {
			form.find('.my-review').append('<div class="loader"></div>');
			form.find('button[type=submit]').prop('disabled',true);
			var getData = form.serialize();
			form.find('input[type=text],input[type=email],textarea').val("");
			$.post('/include/reviewSend.php', getData, function(data) {
				info_alert(data.TITLE, data.MESSAGE);
				form.find('.loader').remove();
				form.find('button[type=submit]').prop('disabled',false);
			},'json');
		};
	});

	//голосование за отзыв
	$('body').delegate('#reviews .review__item .review__bottom a', 'click', function (e) {
		e.preventDefault();
		var el = $(this);
		var getData = {
			reviev_id: el.closest('.review__item').data('review_id'),
			vote: (el.hasClass('review-plus')?'Y':'N')
		}
		el.closest('.review__item').css('position','relative').append('<div class="loader"></div>');
		$.getJSON('/include/setReviewVote.php', getData, function(response) {
			el.closest('.review__item').find('.loader').remove();
			if (response.STATUS == 'OK') {
				info_alert('Сообщение', response.MESSAGE);
				el.closest('.review__bottom').find('.review-plus-count').text(response.UF_LIKE);
				el.closest('.review__bottom').find('.review-minus-count').text(response.UF_DIZLIKE);
			}
			else if (response.STATUS == 'ERROR') {
				info_alert('Ошибка', response.MESSAGE);
			}
		});
	});

	$('#moreLinkReview').on('click', function(e) {
		e.preventDefault();
		var el = $(this);
		setTimeout(function() {
			$('a.item-tabs__link[href=#reviews]').click();
			var scrollingBlock = $('.item-tabs');
			if (el.data('mode') == 'add')
				scrollingBlock = $('form[name=send-reviews]');

			$('html,body').stop().animate({scrollTop: scrollingBlock.offset().top}, 800);
		}, 200);
	});
	if (window.location.hash == '#reviews') {
		$('#moreLinkReview').click();
	}
	//сохрание временных введенных данных в форме отзыва
	$('form[name=send-reviews] input[type=text], form[name=send-reviews] textarea').on('blur', function() {
		var form = $(this).closest('form'),
			getData = form.serialize();
		$.post('/include/saveTempReview.php', getData);
	});

	/*var getQuery = getUrlVar();
	if (getQuery.code != undefined) {
		$('#moreLinkReview').data('mode', 'add').click();
		$.ajax({
			url: '/include/vk.php',
			dataType: 'json',
			success: onSetVkData
		});
	}*/
	if (window.location.hash == '#add-review') {
		$('#moreLinkReview').data('mode', 'add').click();
		$.ajax({
			url: '/include/ulogin_user.php',
			dataType: 'json',
			success: onSetVkData
		});
	}

    function onSetVkData(json) {
    	var form = $('form[name=send-reviews]');
    	form.find('input[name=UF_NAME]').val((json.first_name?json.first_name+' ':'')+(json.last_name?json.last_name:''));
    	if (json.email != undefined && json.email != '') {
    		form.find('input[name=UF_EMAIL]').val(json.email)
    	}
    	else if (json.UF_EMAIL) {
    		form.find('input[name=UF_EMAIL]').val(json.UF_EMAIL);
    	}
    	if (json.first_name != undefined) {
    		$('#auth2vk').hide();
    	}
    	form.find('textarea[name=UF_WORTH]').val(json.UF_WORTH);
    	form.find('textarea[name=UF_LACK]').val(json.UF_LACK);
    	form.find('textarea[name=UF_COMMENT]').val(json.UF_COMMENT);
		if (parseInt(json.UF_RATE,10) > 0) {
			form.find('input[name=UF_RATE]').val(json.UF_RATE);
			form.find('.stars a:eq('+(json.UF_RATE-1)+')').click();
		}
    }

	$('input.date-delivery').datepicker({
		format: "dd.mm.yyyy",
		startDate: '+1d',
		language: "ru",
		//todayHighlight: true,
		autoclose: true,
		daysOfWeekDisabled: [0]
	});

	$('input[name=delivery]:radio').on('change', function(e) {
		var el = $(this),
			deliveryID = el.val();
		getAvailablePay(deliveryID);
		if (el.prop('checked')) {
			$('.toggle-content').hide('slow');
			el.parents('tr').next('.toggle-content').show('slow');
		}
	});
	function getAvailablePay(deliv_id) {
		if (parseInt(deliv_id, 10) > 0) {
			var paySystems = window.D2P[deliv_id];
			$('input[name=PAY_SYSTEM_ID]').each(function() {
				var el = $(this),
					thisPayment = el.val();
				if (!in_array(thisPayment,paySystems)) {
					el.prop('checked',false);
					el.closest('tr').hide();
				}
				else {
					el.closest('tr').show();
				}
			});
		}
	}
	if ($('#payments-block').length) {
		var deliveryID = $('input[name=delivery]:radio:checked').val();
		getAvailablePay(deliveryID);
	}

	$('#payments-block input[name=PAY_SYSTEM_ID]').on('change', function() {
		$('#payments-block #has-err').removeClass('has-error').find('.text-danger').hide();
	});

	$('form[name=orderForm] button[type=submit]').on('click', function(e) {
		e.preventDefault();
		$('#payments-block #has-err').removeClass('has-error').find('.text-danger').hide();
		var form = $(this).closest('form');
		form.validate({
			rules: {
				'USER_PROP[FIO]': {
					required: true,
					minlength: 3
				},
				'USER_PROP[FAMILY]': {
					required: true,
					minlength: 3
				},
				'USER_PROP[EMAIL]': {
					required: true,
					email: true
				},
				'USER_PROP[PHONE]': {
					required: true,
					phoneRU: true
				},
				'USER_PROP[CITY]': 'required',
				'USER_PROP[ADDRESS]': 'required',
				PAY_SYSTEM_ID: 'required'
			},
			messages: {
				'USER_PROP[FIO]': {
					required: 'Введите ваше имя',
					minlength: 'Имя должно быть не менее 3 символов'
				},
				'USER_PROP[FAMILY]': {
					required: 'Введите вашу фамилию',
					minlength: 'Фамилия должна быть не менее 3 символов'
				},
				'USER_PROP[EMAIL]': {
					required: 'Введите ваш E-mail',
					email: 'E-mail введен некорректно'
				},
				'USER_PROP[PHONE]': {
					required: 'Введите ваш номер телефона',
					phoneRU: 'Номер телефона введен некорректно'
				},
				'USER_PROP[CITY]': 'Введите ваш город',
				'USER_PROP[ADDRESS]': 'Введите ваш адрес для доставки',
				PAY_SYSTEM_ID: 'Выберите способ оплаты'
			},
			//errorLabelContainer: '#messageBox',
			errorElement: 'span',
			errorClass: 'validation-message validation-error',
			errorPlacement: function(error, element) {
				element.closest('tr').find('.error-cont').empty().append(error);
			}
		});
		var currentPay = $('#payments-block input[name=PAY_SYSTEM_ID]:radio:checked').val();
		if (currentPay == undefined) {
			$('#payments-block #has-err').addClass('has-error').find('.text-danger').show();
		}
		if (form.valid()) {
			form.submit();
		}
		else {
			if ($('#user-props span.validation-error:visible').length) {
				$('html, body').stop().animate({scrollTop: form.offset().top}, 800);
			}
			else {
				$('html, body').stop().animate({scrollTop: $('#payments-block').prev('.box-heading').offset().top}, 800);
			}
		}
	});

	$('body').delegate('.address-search__title', 'click', function() {
		$('.address-search__dropdown').slideToggle();
		$('.address-dropdown__input').val('').focus();
	});
	$('body').delegate('input[name=YOUR_CITY]', 'keypress', function (e) {
		var el = $(this);
		var $listItem = el.parents('.address-dropdown').find('#cityBlock li');
			console.log(e);
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
	$('body').delegate('#cityBlock a', 'click', function (e) {
		e.preventDefault()
		var el = $(this);
		$('input[name=YOUR_CITY]').val(el.text());
		setCity(el.text());
		getDCV2P('','');
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

	//окно выбора расцветок в списке товаров
	$('body').delegate('.showSelectOffers', 'click', function (e) {
		e.preventDefault();
		var el = $(this);
		if ($('body > .shadow').length === 0) {
			$('body').append('<div class="shadow"/>');
		}
		$.get('/include/modal/select-offers.php', {id:el.data('id')}, function (data) {
			$('body').append(data);
			showPopup('popupSelectOffers');
		});
	});
	//положить в корзину товар в списке товаров, если у него нет ТП
	$('body').delegate('.add2basket', 'click', function (e) {
		e.preventDefault();
		var el = $(this);
		add2basket(el.data('id'));
	});

	//выбор города в карточке товара
	function getDCV2P (mode, city) {
		if ($('#deliveryCalc2Product').length) {
			var dcv2p = $('#deliveryCalc2Product');
			var getData = {
				weight: dcv2p.data('weight'),
				length: dcv2p.data('length'),
				width: dcv2p.data('width'),
				height: dcv2p.data('height'),
				price:  dcv2p.data('price')
			};
			if (city === undefined) {
				city = '';
			}
			if (city.length > 0) {
				getData.city = city;
			}
			if (mode.length) {
				getData.mode = mode;
			}
			if (parseInt(getData.weight, 10) > 0) {
				$.ajax({
					type: 'GET',
					url: '/include/deliveryCalc2Product.php',
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
					}
				});
			}
		}
	}
	getDCV2P('','');

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
	$('body').delegate('#cityBlock2Product a', 'click', function (e) {
		e.preventDefault()
		var el = $(this);
		el.parent().parent().find('input.sity-change-input').val(el.text()).removeClass('active');
		//setCity(el.text());
		setSessionCity(el.text());
		getDCV2P(el.text());
		el.parent('.address-dropdown').hide();
	});
	$('body').delegate('#cityBlock2ProductModal a', 'click', function (e) {
		e.preventDefault()
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
			cityProduct = $('#deliveryCalc2Product .bl-calc input[name=YOUR_CITY_PRODUCT]').val();
		if (cityModal != cityProduct) {
			getDCV2P(cityModal);
		}
	});

	/*function getCookie(name) {
		var matches = document.cookie.match(new RegExp(
			"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
		));
		return matches ? decodeURIComponent(matches[1]) : undefined;
	}
	$('.auth2vk').on('click', function(e) {
		e.preventDefault();
		var el = $(this);
		window.open('https://oauth.vk.com/authorize?client_id=4433979&scope=photos,emailS&redirect_uri='+el.data('redirect')+'&API_VERSION=5.28&SESSION_STATE='+getCookie('PHPSESSID'),'vk',"width=420,height=230,resizable=yes,scrollbars=yes,status=yes");
	});*/

	if ($('#payment-block input[name=payment]').length) {
		function setPayHref() {
			var activePayHref = $('#payment-block input[name=payment]:radio:checked').val();
			$('#payment-block .payment-method__footer a.btn').attr('href', activePayHref);
		}
		setPayHref();

		$('#payment-block .payment-method__item').on('click', function(e) {
			$(this).find('.payment-method__checkbox').prop("checked", true);
			setPayHref();
		});
	}
});
