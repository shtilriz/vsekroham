//выводит информационное сообщение
function info_alert (title, msg) {
	$('#info-modal .modal-title').text(title);
	$('#info-modal .modal-body').html(msg);
	$('#info-modal').modal('show');
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
//перегружает малую корзину
function bskSmallRefresh() {
	$.post('/m_include/basket_small.php',
		function(data) {
			$('#basket_small').html(data);
		}
	);
}
//отслеживаем добавление/удаление товара в корзине электронной коммерцией яндекса
function ecommerce (id, actionType) {
	if (!id || !actionType)
		return false;
	$.getJSON('/include/ecommerce.php', {id: id}, function (response) {
		if (response.ID) {
			if (actionType == 'add') {
				dataLayer.push({
					"ecommerce": {
						"add": {
							"products": [
								{
									"id": response.ID,
									"name": response.NAME,
									"price": response.PRICE,
									"brand": response.MAKER,
									"category": response.SECTIONS,
									"quantity": response.QUANTITY
								}
							]
						}
					}
				});
			}
			else if (actionType == 'remove') {
				dataLayer.push({
					"ecommerce": {
						"remove": {
							"products": [
								{
									"id": response.ID,
									"name": response.NAME,
									"price": response.PRICE,
									"brand": response.MAKER,
									"category": response.SECTIONS,
									"quantity": response.QUANTITY
								}
							]
						}
					}
				});
			}
		}
	});
}

$.validator.addMethod("phoneRU", function(phone_number, element) {
	phone_number = phone_number.replace(/\s+/g, "");
	return this.optional(element) || phone_number.length > 9 &&
		phone_number.match(/^(\+?\d+)?\s*(\(\d+\))?[\s-]*([\d-]*)$/);
}, "Please specify a valid phone number");

$(function () {
	//console.log(SKU);
	//подгрузка списка товаров по ajax в списке товаров
	var loading = false;
	$('body').delegate('#catalogMoreLink', 'click', function(e) {
		e.preventDefault();
		var el = $(this),
			ProductList = $('#catalog-section'),
			//NavBlock = el.closest('#catalog-section').find('#paginatorBlock'),
			pagenum = parseInt(el.data('pagenum'),10),
			nextPageNum = pagenum+1,
			NavRecordCount = el.data('navrecordcount'),
			NavPageCount = el.data('navpagecount'),
			NavNum = el.data('navnum'),
			NavPageSize = parseInt($('#catalog-section').data('navpagesize'),10);

		if (nextPageNum <= NavPageCount) {
			$('#preloader').show();
			var getData = '';
			if ($('form[name=catalog-filter] input#send-filter').val()=="Y")
				getData = $('form[name=catalog-filter],form[name=makersForm]').serialize();
			else
				getData = $('form[name=makersForm]').serialize();

			getData += (getData.length>0?'&':'')+'PAGEN_'+NavNum+'='+nextPageNum;

			$.getJSON('', getData, function(response) {
				console.log(response.PRODUCTS);
				$('#preloader').hide();
				ProductList.append(response.PRODUCTS);
				loading = false;
			});

			el.data('pagenum',nextPageNum);
			if (nextPageNum == NavPageCount) {
				el.hide();
			}
		}
	});

	/*$(window).scroll(function(){
		if((($(window).scrollTop()+$(window).height())+1300)>=$(document).height()){
			if(loading == false){
				loading = true;
				$( "#catalogMoreLink" ).trigger( "click" );
			}
		}
	});*/

	//сортировка в каталоге товаров
	$('body').delegate('#catalog-sort button', 'click', function(e) {
		e.preventDefault();
		var el = $(this),
			sort = el.data('sort'),
			order = el.data('order'),
			getData = $('form[name=catalog-filter],form[name=makersForm]').serialize(),
			ProductList = $('#catalog-section'),
			//NavBlock = el.closest('#catalog-section').find('#paginatorBlock'),
			SECTION_CODE = ProductList.data('code');
		getData += (getData.length>0?'&':'')+'SECTION_CODE='+SECTION_CODE+'&sort='+sort;

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
			/*if (data.NAV) {
				NavBlock.html(data.NAV);
				//NavBlock.find('.paginator-fixed').show();
			}*/
			$('#catalogMoreLink').data('pagenum','1');
			if (parseInt($('#catalogMoreLink').data('navpagecount'),10) > 1 && $('#catalogMoreLink').is(':hidden')) {
				$('#catalogMoreLink').show();
			}
			$('html, body').stop().animate({scrollTop: $('#catalog-sort').offset().top}, 800);
		});
	});

	$('form[name=catalog-filter] select#catalog-filter').on('change', function() {
		if ($('#searchPage').length <= 0) {
			var select = $(this),
				cat_url = select.find('option:selected').val();
			select.closest('form').attr('action', cat_url);
		}
	});

	$('#back_page').on('click', function (e) {
		e.preventDefault();
		history.back();
	});

	//переключение шаблона вывода списка товаров
	$('#cat_template a').on('click', function (e) {
		e.preventDefault();
		var el = $(this);
		//if (!el.hasClass('active')) {
			el.addClass('active').siblings().removeClass('active');
			var getData = $('form[name=catalog-filter],form[name=makersForm]').serializeArray();
			var i = getData.length?getData.length:0;
			getData[i] = {
				name: 'template',
				value: el.data('template')
			};
			var ProductList = $('#catalog-section');
			$.getJSON('',getData, function(data) {
				//$('#preloader').hide();
				ProductList.html(data.PRODUCTS);
				/*if (data.NAV) {
					NavBlock.html(data.NAV);
					//NavBlock.find('.paginator-fixed').show();
				}*/
				$('#catalogMoreLink').data('pagenum','1');
				if (parseInt($('#catalogMoreLink').data('navpagecount'),10) > 1 && $('#catalogMoreLink').is(':hidden')) {
					$('#catalogMoreLink').show();
				}
				$('html, body').stop().animate({scrollTop: $('#catalog-section').offset().top}, 800);
			});
		//}
	});

	$('#moreLinkReview').on('click', function(e) {
		e.preventDefault();
		var el = $(this);
		setTimeout(function() {
			$('#product-tabs a[href=#profile]').click();
			var scrollingBlock = $('.product__b-tabs');
			if (el.data('mode') == 'add')
				scrollingBlock = $('form[name=send-reviews]');

			$('html,body').stop().animate({scrollTop: scrollingBlock.offset().top}, 800);
		}, 200);
	});
	if (window.location.hash == '#reviews') {
		$('#moreLinkReview').click();
	}

	//голосование за отзыв
	$('body').delegate('#pr-reviews .review__item .review__utility a', 'click', function (e) {
		e.preventDefault();
		var el = $(this);
		var getData = {
			reviev_id: el.closest('.review__item').data('review_id'),
			vote: (el.hasClass('review-utility__plus')?'Y':'N')
		}
		el.closest('.review__item').css('position','relative').append('<div class="loader"></div>');
		$.getJSON('/include/setReviewVote.php', getData, function(response) {
			el.closest('.review__item').find('.loader').remove();
			if (response.STATUS == 'OK') {
				info_alert('Сообщение', '<p>'+response.MESSAGE+'</p>');
				el.closest('.review__utility').find('span[data-role=cnt_plus]').text(response.UF_LIKE);
				el.closest('.review__utility').find('span[data-role=cnt_minus]').text(response.UF_DIZLIKE);
			}
			else if (response.STATUS == 'ERROR') {
				info_alert('Ошибка', '<p>'+response.MESSAGE+'</p>');
			}
		});
	});

	$('body').delegate('#skuSelect select', 'change', function () {
		var form = $(this).closest('form'),
			skuCount = window.SKU.SKU_PROPS.length,
			thisPropCode = $(this).attr('name');
		if (thisPropCode == 'COLOR' && parseInt(window.SKU.CNT_SIZE,10) > 1) {
			form.find('select[name=SIZE] option').prop('selected',false).removeAttr('selected')
		}
		if (skuCount > 1) {
			var setVal = {};
			var bAllUnselected = true; //флаг того, что не выбран ни один из параметров
			for (var key in window.SKU.SKU_PROPS) {
				var prop = window.SKU.SKU_PROPS[key];
				if (form.find('select[name='+prop+']').length)
					setVal[prop] = form.find('select[name='+prop+'] option:selected').val();
				if (setVal[prop].length > 0) {
					bAllUnselected = false;
				}
			}
			//если не выбран ни один из параметров
			if (bAllUnselected) {
				form.find('select option').prop('disabled', false);
			}

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
				form.find('select[name='+key+'] option').each(function () {
					var el = $(this),
						thisProp = el.val();
					if (thisProp != 0)
						el.prop('disabled', true).attr('disabled', 'disabled');
					if (in_array(thisProp,setPropArray[key]))
						el.prop('disabled',false).removeAttr('disabled');
				});
			}

			setTimeout(function() {
				form.find('select').selectpicker('refresh');
				//setSizeChecked(form);
			}, 1);
		}
		setSkuPrice();
	});

	function setSkuPrice() {
		var setVal = {};
		for (var cell in window.SKU.SKU_PROPS) {
			var prop = window.SKU.SKU_PROPS[cell];
			setVal[prop] = $('#skuSelect select[name='+prop+'] option:selected').val();
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
			console.log(skuID);
			//меняем цену ТП
			$('.product__order #prPrice').text(skuPriceDiscount);
			if (discountDiff > 0) {
				$('.product__order .product__price-old, #popupSelectOffers .price__old').text(skuPrice);
			}
			else if (skuPriceMargin) {
				$('.product__order .product__price-old').text(skuPriceMargin);
			}
		}
	}

	//кладем товар в корзину
	function add2basket (id) {
		if (parseInt(id, 10) > 0) {
			var getData = {
				action: 'ADD2BASKET',
				ajax_basket: 'Y',
				id: id
			}
			var modal = $('#modal-pr-added-to-cart');
			$.getJSON('', getData, function (response) {
				if (response.STATUS == "OK") {
					bskSmallRefresh();
					$.get('/m_include/modal/add2basketModal.php', {id:id}, function(response) {
						$('#modal-pr').modal('hide');
						modal.find('.modal-body').html(response);
						modal.modal('show');
						ecommerce (id, 'add');
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

	function add2basketSimple(id) {
		if (parseInt(id,10) > 0) {
			var getData = {
				action: 'ADD2BASKET',
				ajax_basket: 'Y',
				id: id
			}
			$.getJSON('', getData, function (response) {
				if (response.STATUS == "OK") {
					bskSmallRefresh();
					ecommerce (id, 'add');
				}
			});
		}
	}

	$('body').delegate('#product .add2basket', 'click',function (e) {
		e.preventDefault();
		var el = $(this),
			form = el.closest('form'),
			gift = parseInt(el.data('gift'), 10);
		//если товар с торговыми предложениями
		if (window.SKU.B_OFFERS) {
			var setVal = {},
				bAllowed = true; //флаг разрешено ли добавлять в корзину. true, если выбраны все параметры ТП
			for (var cell in window.SKU.SKU_PROPS) {
				var prop = window.SKU.SKU_PROPS[cell];
				if (form.find('select[name='+prop+']').length)
					setVal[prop] = form.find('select[name='+prop+'] option:selected').val();
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
				info_alert('Ошибка', 'Выберите '+msg+'чтобы добавить товар в корзину');
			}
		}
		//иначе товар без ТП
		else {
			var product_id = window.SKU.PRODUCT_ID;
			add2basket(product_id);
		}
	});


	$('#modal-pr').on('shown.bs.modal', function (e) {
		var modal = $(this),
			index = $(e.relatedTarget).index();
		modal.find('.slider-big').slick('unslick');
		modal.find('.slider-big').slick({
			initialSlide: index
		});
	});

	function setPropModalSKU (offer) {
		var modal = $('#modal-pr');
		modal.find('.pr-properties').empty();
		modal.find('.modal-header .modal-title').text(offer.title);
		if (offer.id)
			modal.find('.modal-footer').show().find('button').data('id', offer.id);
		else
			modal.find('.modal-footer').hide().find('button').data('id', '');
		if (offer.price)
			modal.find('.modal-pr__price').show().text(offer.price);
		else
			modal.find('.modal-pr__price').hide().empty();
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
			insertHTML += (offer.color?'<dl class="modal-pr__opts-list"><dt>Цвет:</dt><dd>'+offer.color+'</dd></dl>':'') + '<dl class="modal-pr__opts-list"><dt>Размер:</dt><dd><select name="offer-size">'+strTemp+'</select></dd></dl>';
		}
		else {
			insertHTML = (offer.color?'<dl class="modal-pr__opts-list"><dt>Цвет:</dt><dd>'+offer.color+'</dd></dl>':'') + (offer.size?'<dl class="modal-pr__opts-list"><dt>Размер:</dt><dd>'+offer.size+'</dd></dl>':'');
		}
		modal.find('.pr-properties').html(insertHTML);
	}
	$('#modal-pr .slider-big').on('init', function (e, slick) {
		var slider = $(this),
			currentSlide = slider.find('.slick-slide[data-slick-index='+slick.currentSlide+']'),
			offer = {
				id: currentSlide.data('id'),
				title: currentSlide.data('title'),
				color: currentSlide.data('color'),
				size: currentSlide.data('size'),
				price: currentSlide.data('price')
			};
		setPropModalSKU (offer);
	});
	$('#modal-pr .slider-big').on('afterChange', function (e, slick, currentSlide) {
		var slider = $(this),
			currentSlide = slider.find('.slick-slide[data-slick-index='+currentSlide+']'),
			offer = {
				id: currentSlide.data('id'),
				title: currentSlide.data('title'),
				color: currentSlide.data('color'),
				size: currentSlide.data('size'),
				price: currentSlide.data('price')
			};
		setPropModalSKU (offer);
	});
	//кладем в корзину во всплывающем окне
	$('body').delegate('#modal-pr #add2bsk_popup', 'click', function(e) {
		e.preventDefault();
		var el = $(this);
		//если есть выбор размера
		if (el.closest('#modal-pr').find('select[name=offer-size]').length) {
			var product_id = el.closest('#modal-pr').find('select[name=offer-size] option:selected').val();
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

	/*** корзина ***/
	function basketRefresh() {
		var form = $('#basket-list form[name=basket_form]'),
			getData = form.serialize();
		$('#basket-list').append('<div class="loader"></div>');
		$.post('',getData,function(data) {
			//$('#basket-list').remove('.loader');
			$('#basket-list').html(data);
			$('#basket-list .selectpicker').selectpicker({
				noneResultsText: 'Не найдено',
				style: 'dropdown-toggle_lg btn-default'
			});
			bskSmallRefresh();
			//getDCV2P('','');
		});
	}
	//удаление товара из корзины
	$('body').delegate('form[name=basket_form] .js-b-basket-list-item-close', 'click', function(e) {
		e.preventDefault();
		var el = $(this),
			product_id = el.data('product-id');
		el.next('input').attr({checked:'checked'});
		basketRefresh();
		if (product_id)
			ecommerce (product_id, 'remove');
	});
	//очищаем корзину
	$('body').delegate('#clearBasket', 'click',function(e) {
		e.preventDefault();
		$('form[name=basket_form] .productDel').attr({checked:'checked'});
		$('form[name=basket_form] .js-b-basket-list-item-close').each(function () {
			var el = $(this),
				product_id = el.data('product-id');
			if (product_id)
				ecommerce (product_id, 'remove');
		});
		setTimeout(function() {
			basketRefresh();
		},1);
	});
	//изменяем количество товара
	$('body').delegate('form[name=basket_form] .quanSelect', 'change', function(e) {
		var select = $(this),
			value = select.find('option:selected').val(),
			inputQuantity = select.closest('.b-basket-list__quantity').find('input');
		inputQuantity.val(value);
		setTimeout(function() {
			basketRefresh();
		},1);
	});

	//показывает только те способы оплаты, которые доступны для выбранного способа доставки
	function getAvailablePay (deliv_id) {
		if (parseInt(deliv_id, 10) > 0) {
			var paySystems = window.D2P[deliv_id];
			$('input[name=PAY_SYSTEM_ID]').each(function() {
				var el = $(this),
					thisPayment = el.val();
				if (!in_array(thisPayment,paySystems)) {
					el.prop('checked',false);
					el.closest('.js-b-checker').hide();
				}
				else {
					el.closest('.js-b-checker').show();
				}
			});
			if ($('#payments-block .js-b-checker:visible').length == 1) {
				$('#payments-block .js-b-checker:visible input:radio').prop('checked', true);
			}
			else {
				$('#payments-block .js-b-checker input:radio').prop('checked', false);
			}
		}
	}

	$('input[name=DELIVERY_ID]:radio').on('change', function(e) {
		var el = $(this),
			deliveryID = el.val();
		getAvailablePay(deliveryID);
	});

	if ($('#payments-block').length) {
		var deliveryID = $('input[name=DELIVERY_ID]:radio:checked').val();
		getAvailablePay(deliveryID);
	}


	$('form[name=orderForm]').validate({
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
			DELIVERY_ID: 'required',
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
			DELIVERY_ID: 'Выберите службу доставки',
			PAY_SYSTEM_ID: 'Выберите способ оплаты'
		},
		//errorLabelContainer: '#messageBox',
		errorElement: 'div',
		errorClass: 'text-error',
		errorPlacement: function (error, element) {
			if (element.is(':radio')) {
				element.closest('#payments-block').append(error);
				//error.closest('#payments-block').append("#lastname");
			}
			else {
				error.insertAfter(element);
			}
			/*if (element.attr("name") == "fname" || element.attr("name") == "lname" ) {
				error.insertAfter("#lastname");
			} else {
				error.insertAfter(element);
			}*/
		}
	});

	//форма оплаты заказа
	$('form[name=payModal]').on('submit', function(e) {
		e.preventDefault();
		var form = $(this);
		form.find('#messageBoxPay').empty();
		form.validate({
			rules: {
				order: {
					required: true,
					number: true
				}
			},
			messages: {
				order: {
					required: 'Введите ваш номер заказа',
					number: 'Номер заказа должен состоять из цифр'
				}
			},
			errorLabelContainer: '#messageBoxPay',
			errorElement: 'p',
			errorClass: 'help-text help-text--error'
		});
		if (form.valid()) {
			var getData = {order: form.find('input[name=order]').val()};
			$.getJSON('/include/orderPay.php', getData, function(response) {
				if (response.STATUS == "OK") {
					window.location.href = "/basket/payment.php?order="+getData.order;
				}
				else if (response.STATUS == "ERROR") {
					form.find('#messageBoxPay').html('<p class="help-text help-text--error">'+response.MESSAGE+'</p>');
				}
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
			//errorLabelContainer: '#messageBox',
			errorElement: 'div',
			errorClass: 'text-error'
		});
		if (form.valid()) {
			//yaCounter15270730.reachGoal('sborka-mebel');
			var getData = form.serialize(),
				url = form.attr('action');
			$.getJSON(url, getData, function(data) {
				form.find('input[type=text],textarea').val("");
				info_alert(data.TITLE, data.MESSAGE);
			});
		};
	});

	//возврат товара
	$('form[name=returnPolicy] button[type=submit]').on('click', function (e) {
		e.preventDefault();
		var form = $(this).closest('form');
		form.validate({
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
			messages: {
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
			},
			errorElement: 'div',
			errorClass: 'text-error'
		});
		if (form.valid()) {
			//yaCounter15270730.reachGoal('brak-warranty');
			form.submit();
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
			errorElement: 'div',
			errorClass: 'text-error'
		});
		if (form.valid()) {
			//yaCounter15270730.reachGoal('opt-subscribe');
			var getData = form.serialize(),
				url = form.attr('action');
			$.getJSON(url, getData, function(data) {
				form.find(':text').val("");
				info_alert(data.TITLE, data.MESSAGE);
			});
		};
	});

	if ($('#payment-block input[name=payment]').length) {
		function setPayHref() {
			var activePayHref = $('#payment-block input[name=payment]:radio:checked').val();
			$('#payment-block .payment-method__footer a').attr('href', activePayHref);
		}
		setPayHref();

		$('#payment-block .payment-method__item').on('click', function(e) {
			//$(this).find('.payment-method__checkbox').prop("checked", true);
			setPayHref();
		});
	}

	//сохрание временных введенных данных в форме отзыва
	$('form[name=send-reviews] input[type=text], form[name=send-reviews] textarea').on('blur', function() {
		var form = $(this).closest('form'),
			getData = form.serialize();
		$.post('/include/saveTempReview.php', getData);
	});

	if (window.location.hash == '#add-review') {
		//$('#moreLinkReview').data('mode', 'add').click();
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
    	form.find('[name=UF_WORTH]').val(json.UF_WORTH);
    	form.find('[name=UF_LACK]').val(json.UF_LACK);
    	form.find('[name=UF_COMMENT]').val(json.UF_COMMENT);
		if (parseInt(json.UF_RATE,10) > 0) {
			form.find('input[name=UF_RATE]').val(json.UF_RATE);
			form.find('.stars a:eq('+(json.UF_RATE-1)+')').click();
		}
    }

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
			errorElement: 'div',
			errorClass: 'text-error'
		});
		if (form.valid()) {
			form.find('.my-review').append('<div class="loader"></div>');
			form.find('button[type=submit]').prop('disabled',true);
			var getData = form.serialize();
			form.find('input[type=text],input[type=email],textarea').val("");
			$.post('/include/reviewSend.php', getData, function(data) {
				$('html, body').stop().animate({scrollTop: $('body').offset().top}, 800);
				info_alert(data.TITLE, data.MESSAGE);
				form.find('.loader').remove();
				form.find('button[type=submit]').prop('disabled',false);
			},'json');
		};
	});

	$('form[name=makerFilter]').on('submit', function(e) {
		e.preventDefault();
		var form = $(this),
			getData = form.serialize();
		$.get('', getData, function(response) {
			$('#resultList').html(response);
			$('html, body').stop().animate({scrollTop: $('#resultList').offset().top}, 800);
		});
	});

	//при клике на банер проставлять куку
	$('body').delegate('.js-b-b-m', 'click', function(){
		$.cookie('counter_baner', '4', {
			path: '/'
		});
	});
});